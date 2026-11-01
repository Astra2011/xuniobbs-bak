<?php

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1);

include _include(APP_PATH.'model/smtp.func.php');
$smtplist = smtp_init(APP_PATH.'conf/smtp.conf.php');
// hook admin_setting_start.php

if($action == 'base') {
	
	// hook admin_setting_base_get_post.php
	
	if($method == 'GET') {
		
		// hook admin_setting_base_get_start.php
		
		$input = array();
		$input['sitename'] = form_text('sitename', $conf['sitename']);
		$input['sitebrief'] = form_textarea('sitebrief', $conf['sitebrief'], '100%', 100);
		$input['runlevel'] = form_radio('runlevel', array(0=>lang('runlevel_0'), 1=>lang('runlevel_1'), 2=>lang('runlevel_2'), 3=>lang('runlevel_3'), 4=>lang('runlevel_4'), 5=>lang('runlevel_5')), $conf['runlevel']);
		$input['user_create_on'] = form_radio_yes_no('user_create_on', $conf['user_create_on']);
		$input['user_create_email_on'] = form_radio_yes_no('user_create_email_on', $conf['user_create_email_on']);
		$input['user_resetpw_on'] = form_radio_yes_no('user_resetpw_on', $conf['user_resetpw_on']);
		$input['lang'] = form_select('lang', lang_list(), $conf['lang']);
		$input['timezone'] = form_text('timezone', xo_timezone_name_to_offset($conf['timezone']), '120px');
		
		$header['title'] = lang('admin_site_setting');
		$header['mobile_title'] =lang('admin_site_setting');
		
		// hook admin_setting_base_get_end.php
		
		include _include(ADMIN_PATH.'view/htm/setting_base.htm');
		
	} else {
		
		$sitebrief = param('sitebrief', '', FALSE);
		$sitename = param('sitename', '', FALSE);
		$runlevel = param('runlevel', 0);
		$user_create_on = param('user_create_on', 0);
		$user_create_email_on = param('user_create_email_on', 0);
		$user_resetpw_on = param('user_resetpw_on', 0);
		
		$_lang = param('lang');
		$_langs = lang_list();
		!isset($_langs[$_lang]) AND $_lang = $conf['lang'];
		
		// 时区：GMT 偏移量，整数 -12 ~ +14；非法则回落到当前配置
		$_timezone_offset = param('timezone', '');
		$_timezone = xo_timezone_offset_to_name($_timezone_offset);
		
		// hook admin_setting_base_post_start.php
		
		$replace = array();
		$replace['sitename'] = $sitename;
		$replace['sitebrief'] = $sitebrief;
		$replace['runlevel'] = $runlevel;
		$replace['user_create_on'] = $user_create_on;
		$replace['user_create_email_on'] = $user_create_email_on;
		$replace['user_resetpw_on'] = $user_resetpw_on;
		$replace['lang'] = $_lang;
		$replace['timezone'] = $_timezone;
		
		file_replace_var(APP_PATH.'conf/conf.php', $replace);
	
		// hook admin_setting_base_post_end.php
		
		message(0, lang('modify_successfully'));
	}

} elseif($action == 'smtp') {

	// hook admin_setting_smtp_get_post.php
	
	if($method == 'GET') {
		
		// hook admin_setting_smtp_get_start.php
		
		$header['title'] = lang('admin_setting_smtp');
		$header['mobile_title'] = lang('admin_setting_smtp');
	
		$smtplist = smtp_find();
		$maxid = smtp_maxid();
		
		// hook admin_setting_smtp_get_end.php
		
		include _include(ADMIN_PATH."view/htm/setting_smtp.htm");
	
	} else {
		
		// hook admin_setting_smtp_post_start.php
		
		$email = param('email', array(''));
		$host = param('host', array(''));
		$port = param('port', array(0));
		$user = param('user', array(''));
		$pass = param('pass', array(''));
		
		$smtplist = array();
		foreach ($email as $k=>$v) {
			$smtplist[$k] = array(
				'email'=>$email[$k],
				'host'=>$host[$k],
				'port'=>$port[$k],
				'user'=>$user[$k],
				'pass'=>$pass[$k],
			);
		}
		$r = file_put_contents_try(APP_PATH.'conf/smtp.conf.php', "<?php\r\nreturn ".var_export($smtplist,true).";\r\n?>");
		!$r AND message(-1, lang('conf/smtp.conf.php', array('file'=>'conf/smtp.conf.php')));
		
		// hook admin_setting_smtp_post_end.php
		
		message(0, lang('save_successfully'));
	}
} elseif($action == 'smtp_test') {

	// hook admin_setting_smtp_test_get_post.php

	if($method == 'POST') {

		// hook admin_setting_smtp_test_start.php

		$email = param('email', '', FALSE);
		$host = param('host', '', FALSE);
		$port = param('port', 0);
		$user = param('user', '', FALSE);
		$pass = param('pass', '', FALSE);
		$to = param('to', '', FALSE);

		if(empty($host) || empty($port)) {
			message(-1, lang('smtp_test_fill_config'));
		}
		if(empty($to)) {
			message(-1, lang('smtp_test_fill_to'));
		}
		if(!preg_match('#^[\w\.\-\+]+@[\w\.\-]+\.\w+$#', $to)) {
			message(-1, lang('smtp_test_invalid_to'));
		}

		include _include(XUNIOPHP_PATH.'xo_send_mail.func.php');

		$smtp = array(
			'email'=>$email,
			'host'=>$host,
			'port'=>$port,
			'user'=>$user,
			'pass'=>$pass,
		);

		$subject = lang('smtp_test_subject');
		$body = lang('smtp_test_body');

		// hook admin_setting_smtp_test_before.php
		$r = xo_send_mail($smtp, $email, $to, $subject, $body);
		// hook admin_setting_smtp_test_after.php

		if($r === TRUE) {
			message(0, lang('smtp_test_success', array('to'=>$to)));
		} else {
			global $errstr;
			message(-1, lang('smtp_test_fail', array('errmsg'=>$errstr)));
		}

		// hook admin_setting_smtp_test_end.php
	}

} elseif($action == 'plugin_protect') {

	// hook admin_setting_plugin_protect_get_post.php

	if($method == 'GET') {

		// hook admin_setting_plugin_protect_get_start.php

		$header['title'] = lang('admin_setting_plugin_protect');
		$header['mobile_title'] = lang('admin_setting_plugin_protect');

		$input = array();
		$input['plugin_protect_on'] = form_radio_yes_no('plugin_protect_on', $conf['plugin_protect_on']);

		// hook admin_setting_plugin_protect_get_end.php

		include _include(ADMIN_PATH."view/htm/setting_plugin_protect.htm");

	} else {

		$plugin_protect_on = param('plugin_protect_on', 0);

		// hook admin_setting_plugin_protect_post_start.php

		file_replace_var(APP_PATH.'conf/conf.php', array('plugin_protect_on'=>$plugin_protect_on));

		// hook admin_setting_plugin_protect_post_end.php

		message(0, lang('save_successfully'));
	}
} elseif($action == 'hook_rank') {

	// hook admin_setting_hook_rank_get_post.php

	if($method == 'GET') {

		// hook admin_setting_hook_rank_get_start.php

		$header['title'] = lang('admin_setting_hook_rank');
		$header['mobile_title'] = lang('admin_setting_hook_rank');

		plugin_init();

		// 收集所有已启用（installed && enable）插件，以及每个插件挂载的 hook 点
		$enabled = array();
		foreach($plugins as $dir=>$pconf) {
			if(empty($pconf['installed']) || empty($pconf['enable'])) continue;
			$enabled[$dir] = $pconf;
		}

		// 汇总所有 hook 点（去重、按名排序），并统计每个 hook 点下注册的插件数
		$hooklist = array(); // hookname => array(dir, name, rank)
		foreach($enabled as $dir=>$pconf) {
			$hooks = isset($pconf['hooks']) ? $pconf['hooks'] : array();
			foreach($hooks as $hookname=>$hookpath) {
				if(!isset($hooklist[$hookname])) $hooklist[$hookname] = array();
				$hr = isset($pconf['hooks_rank']) ? $pconf['hooks_rank'] : array();
				if(!is_array($hr)) $hr = array();
				$rank = isset($hr[$hookname]) ? intval($hr[$hookname]) : 0;
				$hooklist[$hookname][$dir] = array(
					'dir'=>$dir,
					'name'=>isset($pconf['name']) ? $pconf['name'] : $dir,
					'rank'=>$rank,
				);
			}
		}
		// 每个 hook 点内按序号升序（小先执行），同序号按目录名升序
		foreach($hooklist as $hookname=>&$arr) {
			uasort($arr, function($a, $b) {
				if($a['rank'] == $b['rank']) return strcmp($a['dir'], $b['dir']);
				return $a['rank'] > $b['rank'] ? 1 : -1;
			});
		}
		unset($arr);
		// hook 点名排序（字母序，稳定展示）
		ksort($hooklist);

		// hook admin_setting_hook_rank_get_end.php

		include _include(ADMIN_PATH."view/htm/setting_hook_rank.htm");

	} else {

		// hook admin_setting_hook_rank_post_start.php

		$hookname = param('hookname', '', FALSE);
		$dir = param('dir', '', FALSE);
		$dir2 = param('dir2', '', FALSE);
		if(empty($hookname) || empty($dir) || empty($dir2)) {
			message(-1, lang('hook_rank_invalid'));
		}
		if(!is_word($dir) || !is_word($dir2)) {
			message(-1, lang('hook_rank_invalid'));
		}

		plugin_init();

		if(!isset($plugins[$dir]) || !isset($plugins[$dir2])) {
			message(-1, lang('plugin_not_exists'));
		}

		// 校验两个插件确实都挂载了该 hook 点
		if(!isset($plugins[$dir]['hooks'][$hookname]) || !isset($plugins[$dir2]['hooks'][$hookname])) {
			message(-1, lang('hook_rank_invalid'));
		}

		// 读取两个插件在该 hook 点的序号（缺失按 0 处理）
		$hr1 = isset($plugins[$dir]['hooks_rank']) ? $plugins[$dir]['hooks_rank'] : array();
		$hr2 = isset($plugins[$dir2]['hooks_rank']) ? $plugins[$dir2]['hooks_rank'] : array();
		if(!is_array($hr1)) $hr1 = array();
		if(!is_array($hr2)) $hr2 = array();
		$rank1 = isset($hr1[$hookname]) ? intval($hr1[$hookname]) : 0;
		$rank2 = isset($hr2[$hookname]) ? intval($hr2[$hookname]) : 0;

		// 交换两个插件在该 hook 点的序号
		$hr1[$hookname] = $rank2;
		$hr2[$hookname] = $rank1;
		file_replace_var(APP_PATH."plugin/$dir/conf.json", array('hooks_rank'=>$hr1), TRUE);
		file_replace_var(APP_PATH."plugin/$dir2/conf.json", array('hooks_rank'=>$hr2), TRUE);

		plugin_clear_tmp_dir();

		// hook admin_setting_hook_rank_post_end.php

		message(0, lang('save_successfully'));
	}
}

// hook admin_setting_end.php

?>