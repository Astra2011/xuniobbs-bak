<?php

 // 本地插件
//$plugin_srcfiles = array();
$plugin_paths = array();
$plugins = array(); // 跟官方插件合并

// 官方插件列表
$official_plugins = array();

define('PLUGIN_OFFICIAL_URL', DEBUG == 4 ? 'http://plugin.x.com/' : 'http://plugin.xiuno.com/');

// todo: 对路径进行处理 include _include(APP_PATH.'view/htm/header.inc.htm');
$g_include_slot_kv = array();
function _include($srcfile) {
	global $conf;
	// 合并插件，存入 tmp_path
	$len = strlen(APP_PATH);
	$tmpfile = $conf['tmp_path'].substr(str_replace('/', '_', $srcfile), $len);
	if(!is_file($tmpfile) || DEBUG > 1) {
		// 开始编译
		$s = plugin_compile_srcfile($srcfile);
		
		// 支持 <template> <slot>
		$g_include_slot_kv = array();
		for($i = 0; $i < 10; $i++) {
			$s = preg_replace_callback('#<template\sinclude="(.*?)">(.*?)</template>#is', '_include_callback_1', $s);
			if(strpos($s, '<template') === FALSE) break;
		}
		file_put_contents_try($tmpfile, $s);
		
		$s = plugin_compile_srcfile($tmpfile);
		file_put_contents_try($tmpfile, $s);
		
	}
	return $tmpfile;
}

function _include_callback_1($m) {
	global $g_include_slot_kv;
	$r = file_get_contents($m[1]);
	preg_match_all('#<slot\sname="(.*?)">(.*?)</slot>#is', $m[2], $m2);
	if(!empty($m2[1])) {
		$kv = array_combine($m2[1], $m2[2]);
		$g_include_slot_kv += $kv;
		foreach($g_include_slot_kv as $slot=>$content) {
			$r = preg_replace('#<slot\sname="'.$slot.'"\s*/>#is', $content, $r);
		}
	}
	return $r;
}

// 在安装、卸载插件的时候，需要先初始化
function plugin_init() {
	global $plugin_srcfiles, $plugin_paths, $plugins, $official_plugins;
	/*$plugin_srcfiles = array_merge(
		glob(APP_PATH.'model/*.php'), 
		glob(APP_PATH.'route/*.php'), 
		glob(APP_PATH.'view/htm/*.*'), 
		glob(ADMIN_PATH.'route/*.php'), 
		glob(ADMIN_PATH.'view/htm/*.*'),
		glob(APP_PATH.'lang/en-us/*.*'),
		glob(APP_PATH.'lang/zh-cn/*.*'),
		glob(APP_PATH.'lang/zh-tw/*.*'),
		array(APP_PATH.'model.inc.php')
	);
	foreach($plugin_srcfiles as $k=>$file) {
		$filename = file_name($file);
		if(is_backfile($filename)) {
			unset($plugin_srcfiles[$k]);
		}
	}*/
	
	$official_plugins = plugin_official_list_cache();
	empty($official_plugins) AND $official_plugins = array();
	
	$plugin_paths = glob(APP_PATH.'plugin/*', GLOB_ONLYDIR);
	if(is_array($plugin_paths)) {
		foreach($plugin_paths as $path) {
			$dir = file_name($path);
			$conffile = $path."/conf.json";
			if(!is_file($conffile)) continue;
			$arr = xo_json_decode(file_get_contents($conffile));
			if(empty($arr)) continue;
			$plugins[$dir] = $arr;
			
			// 额外的信息
			$plugins[$dir]['hooks'] = array();
			$hookpaths = glob(APP_PATH."plugin/$dir/hook/*.*"); // path
			if(is_array($hookpaths)) {
				foreach($hookpaths as $hookpath) {
					$hookname = file_name($hookpath);
					$plugins[$dir]['hooks'][$hookname] = $hookpath;
				}
			}
			
			// 本地 + 线上数据
			$plugins[$dir] = plugin_read_by_dir($dir);
		}
	}
}

// 插件依赖检测，返回依赖的插件列表，如果返回为空则表示不依赖
/*
	返回依赖的插件数组：
	array(
		'xo_ad'=>'1.0',
		'xo_umeditor'=>'1.0',
	);
*/
function plugin_dependencies($dir) {
	global $plugin_srcfiles, $plugin_paths, $plugins;
	$plugin = $plugins[$dir];
	$dependencies = $plugin['dependencies'];
	
	// 检查插件依赖关系
	$arr = array();
	foreach($dependencies as $_dir=>$version) {
		if(!isset($plugins[$_dir]) || !$plugins[$_dir]['enable']) {
			$arr[$_dir] = $version;
		}
	}
	return $arr;
}

/*
	返回被依赖的插件数组：
	array(
		'xo_ad'=>'1.0',
		'xo_umeditor'=>'1.0',
	);
*/
function plugin_by_dependencies($dir) {
	global $plugins;
	
	$arr = array();
	foreach($plugins as $_dir=>$plugin) {
		if(isset($plugin['dependencies'][$dir]) && $plugin['enable']) {
			$arr[$_dir] = $plugin['version'];
		}
	}
	return $arr;
}

function plugin_enable($dir) {
	global $plugins;
	
	if(!isset($plugins[$dir])) {
		return FALSE;
	}
	
	$plugins[$dir]['enable'] = 1;
	
	//plugin_overwrite($dir, 'install');
	//plugin_hook($dir, 'install');
	
	// 启用时若尚未分配排序序号，则自动分配（先来后到）
	plugin_hook_rank_assign($dir);
	
	file_replace_var(APP_PATH."plugin/$dir/conf.json", array('enable'=>1), TRUE);
	
	plugin_clear_tmp_dir();
	
	return TRUE;
}

// 清空插件的临时目录
function plugin_clear_tmp_dir() {
	global $conf;
	rmdir_recusive($conf['tmp_path'], TRUE);
	xo_unlink($conf['tmp_path'].'model.min.php');
}

// 分配插件级全局排序序号：返回当前所有插件中的最大 hook_rank，供新插件取 max+1
// 卸载/禁用时 hook_rank 保留，不回收，避免重装后序号漂移导致行为不稳定
function plugin_hook_rank_max() {
	global $plugins;
	$max = 0;
	foreach($plugins as $dir=>$pconf) {
		$r = isset($pconf['hook_rank']) ? intval($pconf['hook_rank']) : 0;
		$r > $max AND $max = $r;
	}
	return $max;
}

// 为尚未分配 hook_rank 的插件自动分配一个全局唯一递增序号（先来后到，顺序稳定）
function plugin_hook_rank_assign($dir) {
	global $plugins;
	if(!isset($plugins[$dir])) return 0;
	$cur = isset($plugins[$dir]['hook_rank']) ? intval($plugins[$dir]['hook_rank']) : 0;
	if($cur > 0) return $cur; // 已分配过，保持不变
	$rank = plugin_hook_rank_max() + 1;
	$plugins[$dir]['hook_rank'] = $rank;
	file_replace_var(APP_PATH."plugin/$dir/conf.json", array('hook_rank'=>$rank), TRUE);
	return $rank;
}

function plugin_disable($dir) {
	global $plugins;
	
	if(!isset($plugins[$dir])) {
		return FALSE;
	}
	
	$plugins[$dir]['enable'] = 0;
	
	//plugin_overwrite($dir, 'unstall');
	//plugin_hook($dir, 'unstall');
	
	// 注意：禁用时不回收 hook_rank，保留原序号，重新启用后顺序不变
	file_replace_var(APP_PATH."plugin/$dir/conf.json", array('enable'=>0), TRUE);
	
	plugin_clear_tmp_dir();
	
	return TRUE;
}

// 安装所有的本地插件
function plugin_install_all() {
	global $plugins;
	
	// 检查文件更新
	foreach ($plugins as $dir=>$plugin) {
		plugin_install($dir);
	}
}

// 卸载所有的本地插件
function plugin_unstall_all() {
	global $plugins;
	
	// 检查文件更新
	foreach ($plugins as $dir=>$plugin) {
		plugin_unstall($dir);
	}
}
/*
	插件安装：
		把所有的插件点合并，重新写入文件。如果没有备份文件，则备份一份。
		插件名可以为源文件名：view/header.htm
*/
function plugin_install($dir) {
	global $plugins, $conf;
	
	if(!isset($plugins[$dir])) {
		return FALSE;
	}
	
	$plugins[$dir]['installed'] = 1;
	$plugins[$dir]['enable'] = 1;
	
	// 1. 直接覆盖的方式
	//plugin_overwrite($dir, 'install');
	
	// 2. 钩子的方式
	//plugin_hook($dir, 'install');
	
	// 安装时若尚未分配排序序号，则自动分配（先来后到）
	plugin_hook_rank_assign($dir);
	
	// 写入配置文件
	file_replace_var(APP_PATH."plugin/$dir/conf.json", array('installed'=>1, 'enable'=>1), TRUE);
	
	plugin_clear_tmp_dir();
	
	return TRUE;
}

// copy from plugin_install 修改
function plugin_unstall($dir) {
	global $plugins;
	
	if(!isset($plugins[$dir])) {
		return TRUE;
	}
	
	$plugins[$dir]['installed'] = 0;
	$plugins[$dir]['enable'] = 0;
	
	// 1. 直接覆盖的方式
	//plugin_overwrite($dir, 'unstall');
	
	// 2. 钩子的方式
	//plugin_hook($dir, 'unstall');
	
	// 写入配置文件
	file_replace_var(APP_PATH."plugin/$dir/conf.json", array('installed'=>0, 'enable'=>0), TRUE);
	
	plugin_clear_tmp_dir();
	
	return TRUE;
}

function plugin_paths_enabled() {
	static $return_paths;
	if(empty($return_paths)) {
		$return_paths = array();
		$plugin_paths = glob(APP_PATH.'plugin/*', GLOB_ONLYDIR);
		if(empty($plugin_paths)) return array();
		foreach($plugin_paths as $path) {
			$conffile = $path."/conf.json";
			if(!is_file($conffile)) continue;
			$pconf = xo_json_decode(file_get_contents($conffile));
			if(empty($pconf)) continue;
			if(empty($pconf['enable']) || empty($pconf['installed'])) continue;
			$return_paths[$path] = $pconf;
		}
	}
	return $return_paths;
}

// 编译源文件，把插件合并到该文件，不需要递归，执行的过程中 include _include() 自动会递归。
function plugin_compile_srcfile($srcfile) {
	global $conf;
	// 判断是否开启插件
	if(!empty($conf['disabled_plugin'])) {
		$s = file_get_contents($srcfile);
		return $s;
	}
	
	// 如果有 overwrite，则用 overwrite 替换掉
	$srcfile = plugin_find_overwrite($srcfile);
	$s = file_get_contents($srcfile);
	
	// 最多支持 10 层
	for($i = 0; $i < 10; $i++) {
		if(strpos($s, '<!--{hook') !== FALSE || strpos($s, '// hook') !== FALSE) {
			$s = preg_replace('#<!--{hook\s+(.*?)}-->#', '// hook \\1', $s);
			$s = preg_replace_callback('#//\s*hook\s+(\S+)#is', 'plugin_compile_srcfile_callback', $s);
		} else {
			break;
		}
	}
	return $s;
}


// 只返回一个权重最高的文件名
function plugin_find_overwrite($srcfile) {
	//$plugin_paths = glob(APP_PATH.'plugin/*', GLOB_ONLYDIR);
	
	$plugin_paths = plugin_paths_enabled();
	
	$len = strlen(APP_PATH);
	/*
	// 如果发现插件目录，则尝试去掉插件目录前缀，避免新建的 overwrite 目录过深。
	if(strpos($srcfile, '/plugin/') !== FALSE) {
		preg_match('#'.preg_quote(APP_PATH).'plugin/\w+/#i', $srcfile, $m);
		if(!empty($m[0])) {
			$len = strlen($m[0]);
		}
	}*/
	
	$returnfile = $srcfile;
	$maxrank = 0;
	foreach($plugin_paths as $path=>$pconf) {
		
		// 文件路径后半部分
		$dir = file_name($path);
		$filepath_half = substr($srcfile, $len);
		$overwrite_file = APP_PATH."plugin/$dir/overwrite/$filepath_half";
		if(is_file($overwrite_file)) {
			$rank = isset($pconf['overwrites_rank'][$filepath_half]) ? $pconf['overwrites_rank'][$filepath_half] : 0;
			if($rank >= $maxrank) {
				$returnfile = $overwrite_file;
				$maxrank = $rank;
			}
		}
	}
	return $returnfile;
}

function plugin_compile_srcfile_callback($m) {
	static $hooks;
	if(empty($hooks)) {
		$hooks = array();
		$plugin_paths = plugin_paths_enabled();
		
		//$plugin_paths = glob(APP_PATH.'plugin/*', GLOB_ONLYDIR);
		foreach($plugin_paths as $path=>$pconf) {
			$dir = file_name($path);
			// 插件级全局排序序号：序号小=先安装=先执行；未分配的给 0（同 rank 时按目录名升序兜底）
			$rank = isset($pconf['hook_rank']) ? intval($pconf['hook_rank']) : 0;
			$hookpaths = glob(APP_PATH."plugin/$dir/hook/*.*"); // path
			if(is_array($hookpaths)) {
				foreach($hookpaths as $hookpath) {
					$hookname = file_name($hookpath);
					$hooks[$hookname][] = array('hookpath'=>$hookpath, 'rank'=>$rank, 'dir'=>$dir);
				}
			}
		}
		foreach ($hooks as $hookname=>$arrlist) {
			// 排序规则：hook_rank 升序（序号小=先安装=先执行），同 rank 时按插件目录名升序，保证顺序确定、可复现
			usort($arrlist, function($a, $b) {
				if($a['rank'] == $b['rank']) {
					return strcmp($a['dir'], $b['dir']);
				}
				return $a['rank'] > $b['rank'] ? 1 : -1;
			});
			$hooks[$hookname] = arrlist_values($arrlist, 'hookpath');
		}
		
	}
	
	$s = '';
	$hookname = $m[1];
	if(!empty($hooks[$hookname])) {
		$fileext = file_ext($hookname);
		foreach($hooks[$hookname] as $path) {
			// 从 hook 路径提取插件目录名，注入标记，供致命错误时反查「是哪个插件」
			$dir = '';
			if(preg_match('#/plugin/([^/]+)/hook/#', $path, $mm)) {
				$dir = $mm[1];
			}
			$t = file_get_contents($path);
			if($fileext == 'php' && preg_match('#^\s*<\?php\s+exit;#is', $t)) {
				// 正则表达式去除兼容性比较好。
				$t = preg_replace('#^\s*<\?php\s*exit;(.*?)(?:\?>)?\s*$#is', '\\1', $t);
			}
			if($fileext == 'php' && $dir !== '') {
				$t = "\n// @plugin:$dir\n".$t;
			}
			$s .= $t;
		}
	}
	return $s;
}

// 先下载，购买，付费，再安装
function plugin_online_install($dir) {

}



// -------------------> 官方插件列表缓存到本地。

// 条件满足的总数
function plugin_official_total($cond = array()) {
	global $official_plugins;
	$offlist = $official_plugins;
	$offlist = arrlist_cond_orderby($offlist, $cond, array(), 1, 1000);
	return count($offlist);
}

// 远程插件列表，从官方服务器获取插件列表，全部缓存到本地，定期更新
function plugin_official_list($cond = array(), $orderby = array('pluginid'=>-1), $page = 1, $pagesize = 20) {
	global $official_plugins;
	// 服务端插件信息，缓存起来
	$offlist = $official_plugins;
	$offlist = arrlist_cond_orderby($offlist, $cond, $orderby, $page, $pagesize);
	foreach($offlist as &$plugin) $plugin = plugin_read_by_dir($plugin['dir'], FALSE);
	return $offlist;
}

function plugin_official_list_cache() {
	$s = DEBUG == 3 ? NULL : cache_get('plugin_official_list');
	if($s === NULL) {
		$url = PLUGIN_OFFICIAL_URL."plugin-all-4.htm"; // 获取所有的插件，匹配到3.0以上的。
		$s = http_get($url);
		
		// 检查返回值是否正确
		if(empty($s)) return xo_error(-1, '从官方获取插件数据失败。');
		$r = xo_json_decode($s);
		if(empty($r)) return xo_error(-1, '从官方获取插件数据格式不对。');
		
		$s = $r;
		cache_set('plugin_official_list', $s, 3600); // 缓存时间 1 小时。
	}
	return $s;
}

function plugin_official_read($dir) {
	global $official_plugins;
	$offlist = $official_plugins;
	$plugin = isset($offlist[$dir]) ? $offlist[$dir] : array();
	return $plugin;
}

// -------------------> 本地插件列表缓存到本地。
// 安装，卸载，禁用，更新
function plugin_read_by_dir($dir, $local_first = TRUE) {
	global $plugins;

	$local = array_value($plugins, $dir, array());
	$official = plugin_official_read($dir);
	if(empty($local) && empty($official)) return array();
	if(empty($local)) $local_first = FALSE;
	
	// 本地插件信息
	//!isset($plugin['dir']) && $plugin['dir'] = '';
	!isset($local['name']) && $local['name'] = '';
	!isset($local['price']) && $local['price'] = 0;
	!isset($local['brief']) && $local['brief'] = '';
	!isset($local['version']) && $local['version'] = '1.0';
	!isset($local['bbs_version']) && $local['bbs_version'] = '4.0';
	!isset($local['installed']) && $local['installed'] = 0;
	!isset($local['enable']) && $local['enable'] = 0;
	!isset($local['hooks']) && $local['hooks'] = array();
	!isset($local['hooks_rank']) && $local['hooks_rank'] = array();
	!isset($local['hook_rank']) && $local['hook_rank'] = 0;
	!isset($local['dependencies']) && $local['dependencies'] = array();
	!isset($local['icon_url']) && $local['icon_url'] = '';
	!isset($local['have_setting']) && $local['have_setting'] = 0;
	!isset($local['setting_url']) && $local['setting_url'] = 0;
	
	// 加上官方插件的信息
	!isset($official['pluginid']) && $official['pluginid'] = 0;
	!isset($official['name']) && $official['name'] = '';
	!isset($official['price']) && $official['price'] = 0;
	!isset($official['brief']) && $official['brief'] = '';
	!isset($official['bbs_version']) && $official['bbs_version'] = '4.0';
	!isset($official['version']) && $official['version'] = '1.0';
	!isset($official['cateid']) && $official['cateid'] = 0;
	!isset($official['lastupdate']) && $official['lastupdate'] = 0;
	!isset($official['stars']) && $official['stars'] = 0;
	!isset($official['user_stars']) && $official['user_stars'] = 0;
	!isset($official['installs']) && $official['installs'] = 0;
	!isset($official['sells']) && $official['sells'] = 0;
	!isset($official['file_md5']) && $official['file_md5'] = '';
	!isset($official['filename']) && $official['filename'] = '';
	!isset($official['is_cert']) && $official['is_cert'] = 0;
	!isset($official['is_show']) && $official['is_show'] = 0;
	!isset($official['img1']) && $official['img1'] = 0;
	!isset($official['img2']) && $official['img2'] = 0;
	!isset($official['img3']) && $official['img3'] = 0;
	!isset($official['img4']) && $official['img4'] = 0;
	!isset($official['brief_url']) && $official['brief_url'] = '';
	!isset($official['qq']) && $official['qq'] = '';
	
	$local['official'] = $official;
	
	if($local_first) {
		$plugin = $local + $official;
	} else {
		$plugin = $official + $local;
	}
	// 额外的判断
	$plugin['icon_url'] = $plugin['pluginid'] ? PLUGIN_OFFICIAL_URL."upload/plugin/$plugin[pluginid]/icon.png" : "../plugin/$dir/icon.png";
	$plugin['setting_url'] = $plugin['installed'] && is_file("../plugin/$dir/setting.php") ? "plugin-setting-$dir.htm" : "";
	$plugin['downloaded'] = isset($plugins[$dir]);
	$plugin['stars_fmt'] = $plugin['pluginid'] ? str_repeat('<span class="ti star"></span>', $plugin['stars']) : '';
	$plugin['user_stars_fmt'] = $plugin['pluginid'] ? str_repeat('<span class="ti star"></span>', $plugin['user_stars']) : '';
	$plugin['is_cert_fmt'] = empty($plugin['is_cert']) ? '<span class="text-danger">'.lang('no').'</span>' : '<span class="text-success">'.lang('yes').'</span>';
	$plugin['have_upgrade'] = $plugin['installed'] && version_compare($official['version'], $local['version']) > 0 ? TRUE : FALSE;
	$plugin['official_version'] = $official['version']; // 官方版本
	$plugin['img1_url'] = $official['img1'] ? PLUGIN_OFFICIAL_URL.'upload/plugin/'.$plugin['pluginid'].'/img1.jpg' : ''; // 官方版本
	$plugin['img2_url'] = $official['img2'] ? PLUGIN_OFFICIAL_URL.'upload/plugin/'.$plugin['pluginid'].'/img2.jpg' : ''; // 官方版本
	$plugin['img3_url'] = $official['img3'] ? PLUGIN_OFFICIAL_URL.'upload/plugin/'.$plugin['pluginid'].'/img3.jpg' : ''; // 官方版本
	$plugin['img4_url'] = $official['img4'] ? PLUGIN_OFFICIAL_URL.'upload/plugin/'.$plugin['pluginid'].'/img4.jpg' : ''; // 官方版本
	return $plugin;
}

function plugin_siteid() {
	global $conf;
	// 站点指纹已固化在配置里，与 auth_key 解耦，换密钥后官方仍认得是同一个站点
	if(!empty($conf['site_id'])) return $conf['site_id'];
	// 兜底：配置里没有就用旧算法现算
	return md5($conf['auth_key']._SERVER('SERVER_ADDR'));
}

/*function plugin_outid($dir) {
	global $conf;
	$auth_key = $conf['auth_key'];
	$siteip = _SERVER('SERVER_ADDR')
	$outid = md5($auth_key.$siteip.$dir);
	return $outid;
}*/

// 启动早期处理「插件致命错误待禁用标记」。
// 由 xo_shutdown_handle() 在崩溃时写标记文件，这里在加载任何带 hook 的文件之前处理，保证进程干净。
// 返回被禁用的插件 dir（成功）或 ''（无待处理标记）。
function plugin_protect_handle() {
	global $conf;
	if(empty($conf['plugin_protect_on'])) return '';

	$tmp_path = empty($conf['tmp_path']) ? APP_PATH.'tmp/' : $conf['tmp_path'];
	$marks = glob($tmp_path.'plugin_error_*.php');
	if(empty($marks)) return '';

	$dir = '';
	$errmsg = '';
	foreach($marks as $mark) {
		$base = file_name($mark); // plugin_error_<dir>.php
		if(strpos($base, 'plugin_error_') !== 0) continue;
		$dir = substr($base, strlen('plugin_error_'), -4);
		$dir = preg_replace('#[^a-zA-Z0-9_-]#', '', $dir);
		if($dir === '') continue;

		// 读标记内容（首行是 php 退出标记 + tab 分隔的时间/目录/文件/行号/错误消息）
		$s = file_get_contents($mark);
		$s = preg_replace('#^<\?php\s*exit;\?#i', '', $s);
		$arr = explode("\t", trim($s, ">\t\r\n "));
		// arr: [time, dir, file, line, errmsg]
		$errmsg = isset($arr[4]) ? $arr[4] : '';
		$errmsg = trim($errmsg);
		break; // 一次只处理一个，避免连环禁用误伤
	}

	if($dir === '') return '';

	// 删除标记文件（处理完即清，不留垃圾）
	foreach($marks as $mark) xo_unlink($mark);

	// 禁用该插件：把 conf.json 的 enable 置 0
	$conffile = APP_PATH."plugin/$dir/conf.json";
	if(is_file($conffile)) {
		$pconf = xo_json_decode(file_get_contents($conffile));
		if(is_array($pconf)) {
			$pconf['enable'] = 0;
			file_replace_var($conffile, array('enable'=>0), TRUE);
		}
	}

	// 清空编译缓存，让禁用立即生效
	plugin_clear_tmp_dir();

	// 记录日志
	xo_log("plugin auto-disabled: $dir, reason: $errmsg", 'plugin_error');

	// 写一次性 flash，供后台弹窗报告
	plugin_protect_flash_set($dir, $errmsg);

	return $dir;
}

// 写一次性弹窗标记：内容仅存目录名 + 错误摘要，读一次即清，不残留。
function plugin_protect_flash_set($dir, $errmsg) {
	global $conf;
	$tmp_path = empty($conf['tmp_path']) ? APP_PATH.'tmp/' : $conf['tmp_path'];
	$flash = $tmp_path.'plugin_protect_flash.php';
	$s = "<?php exit;?>\t".time()."\t".$dir."\t".str_replace(array("\r", "\n", "\t"), ' ', $errmsg)."\r\n";
	@file_put_contents($flash, $s);
}

// 读并清除一次性弹窗标记。返回 array(dir, errmsg) 或 array()。
function plugin_protect_flash_get() {
	global $conf;
	$tmp_path = empty($conf['tmp_path']) ? APP_PATH.'tmp/' : $conf['tmp_path'];
	$flash = $tmp_path.'plugin_protect_flash.php';
	if(!is_file($flash)) return array();
	$s = file_get_contents($flash);
	xo_unlink($flash);
	$s = preg_replace('#^<\?php\s*exit;\?#i', '', $s);
	$arr = explode("\t", trim($s, ">\t\r\n "));
	if(count($arr) < 3) return array();
	return array('dir'=>$arr[1], 'errmsg'=>isset($arr[2]) ? $arr[2] : '');
}
?>