<?php

function get_env(&$env, &$write) {
	$env['os']['name'] = lang('os');
	$env['os']['must'] = TRUE;
	$env['os']['current'] = PHP_OS;
	$env['os']['need'] = lang('unix_like');
	$env['os']['status'] = 1;
	// glob gzip
	//$env['os']['disable'] = 1;
	
	$env['php_version']['name'] = lang('php_version');
	$env['php_version']['must'] = TRUE;
	$env['php_version']['current'] = PHP_VERSION;
	// password_hash() 为 5.5+，hash_equals() 为 5.6+，两者都是运行必需的
	$env['php_version']['need'] = '5.6';
	$env['php_version']['status'] = version_compare(PHP_VERSION, '5.6', '>=') ? 1 : 0;

	// 目录可写
	$writedir = array(
		'../conf/',
		'../log/',
		'../tmp/',
		'../upload/',
		'../plugin/'
	);

	$write = array();
	foreach($writedir as &$dir) {
		$write[$dir] = xo_is_writable('./'.$dir);
	}
}

// 是否已经安装过：conf.php 或安装锁存在，均视为已安装
// 不能再用 DEBUG 做判断，本目录入口文件里 DEBUG 恒为 2，条件永远不成立
function install_locked() {
	return is_file(APP_PATH.'conf/conf.php') || is_file(INSTALL_PATH.'install.lock');
}

// 数据库名 / 用户名白名单，只允许 MySQL 标识符常用字符，杜绝 SQL 注入
function install_check_name($s) {
	return preg_match('/^[A-Za-z0-9_\-]{1,64}$/', $s) === 1;
}

function install_check_user($s) {
	return preg_match('/^[A-Za-z0-9_\-@.]{1,32}$/', $s) === 1;
}

// 主机白名单，允许 IPv4 / 域名 / IPv6 方括号，可选带端口，杜绝 DSN 注入
function install_check_host($s) {
	// IPv6 形如 [::1] 或 [::1]:3306
	if(preg_match('/^\[[0-9a-fA-F:]+\](:[0-9]{1,5})?$/', $s) === 1) return TRUE;
	return preg_match('/^[A-Za-z0-9._\-]{1,128}(:[0-9]{1,5})?$/', $s) === 1;
}

function install_sql_file($sqlfile) {
	global $errno, $errstr;
	$s = file_get_contents($sqlfile);
	$s = str_replace(";\r\n", ";\n", $s);
	//$s = preg_replace('/#(.*?)\r\n/i', "", $s);
	$arr = explode(";\n", $s);
	foreach ($arr as $sql) {
		$sql = trim($sql);
		if(empty($sql)) continue;
		$arr = explode(";\n", $s);
		db_exec($sql) === FALSE AND message(-1, "sql: $sql, errno: $errno, errstr: $errstr");
	}
}



?>