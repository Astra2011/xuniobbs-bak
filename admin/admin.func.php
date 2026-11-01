<?php

// hook admin_func_start.php

// 有部分用户
define('XN_ADMIN_BIND_IP', array_value($conf, 'admin_bind_ip'));

// 跨站请求伪造令牌，与会话绑定，不落库
function admin_csrf_token() {
	return substr(md5(session_id().xo_key()), 0, 32);
}

// 校验令牌，$force 为 TRUE 时 GET 请求也要校验（用于 GET 方式的写操作）
function admin_csrf_check($force = FALSE) {
	global $method;
	if(!$force && ($method == 'GET' || $method == 'HEAD')) return;
	$token = _SERVER('HTTP_X_XO_CSRF');
	empty($token) AND $token = param('xo_csrf', '', FALSE);
	if(!is_string($token) || $token === '' || !hash_equals(admin_csrf_token(), $token)) {
		message(-1, lang('csrf_token_invalid'));
	}
}

// 给 URL 追加令牌，用于 <a> 直接跳转的写操作
function admin_url_csrf($url) {
	$sep = strpos($url, '?') === FALSE ? '?' : '&';
	return $url.$sep.'xo_csrf='.admin_csrf_token();
}

function admin_token_check() {
	global $longip, $time, $useragent, $conf;
	$useragent_md5 = md5($useragent);
	
	//$key = md5($longip.$useragent_md5.$conf['auth_key']); // 有些环境是动态 IP
	$key = md5((XN_ADMIN_BIND_IP ? $longip : '').$useragent_md5.xo_key());
	
	// hook admin_token_check_start.php
	
	$admin_token = param('bbs_admin_token');
	if(empty($admin_token)) {
		$_REQUEST[0] = 'index';
		$_REQUEST[1] = 'login';
	} else {
		$s = xo_decrypt($admin_token, $key);
		if(empty($s)) {
			xo_cookie_set('bbs_admin_token', '', 0);
			//message(-1, lang('admin_token_error'));
			message(-1, lang('admin_token_expiry'));
		}
		list($_ip, $_time) = explode("\t", $s);
		
		// 后台超过 3600 自动退出。
		// Background / more than 3600 automatic withdrawal.
		//if($_ip != $longip || $time - $_time > 3600) {
		if((XN_ADMIN_BIND_IP && $_ip != $longip || !XN_ADMIN_BIND_IP) && $time - $_time > 3600) {
			xo_cookie_set('bbs_admin_token', '', 0);
			message(-1, lang('admin_token_expiry'));
		}
		
		// 超过半小时，重新发新令牌，防止过期
		// More than half an hour, reset a new token, prevent expired
		if($time - $_time > 1800) {
			admin_token_set();
		}
	}
	// hook admin_token_check_end.php
}

function admin_token_set() {
	global $longip, $time, $useragent, $conf;
	$useragent_md5 = md5($useragent);
	//$key = md5($longip.$useragent_md5.$conf['auth_key']);
	$key = md5((XN_ADMIN_BIND_IP ? $longip : '').$useragent_md5.xo_key());
	
	// hook admin_token_set_start.php
	
	$admin_token = param('bbs_admin_token');
	$s = "$longip	$time";
	
	$admin_token = xo_encrypt($s, $key);
	xo_cookie_set('bbs_admin_token', $admin_token, $time + 3600);
	
	// hook admin_token_set_end.php
}

function admin_token_clean() {
	global $time;
	xo_cookie_set('bbs_admin_token', '', $time - 86400);
	
	// hook admin_token_clean_start.php
}

// bootstrap style
function admin_tab_active($arr, $active) {
	// hook admin_tab_active_start.php
	$s = '';
	foreach ($arr as $k=>$v) {
		$s .= '<a role="button" class="btn btn-secondary'.($active == $k ? ' active' : '').'" href="'.$v['url'].'">'.$v['text'].'</a>';
	}
	// hook admin_tab_active_end.php
	return $s;
}

// hook admin_func_end.php

?>