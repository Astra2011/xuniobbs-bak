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

		// 收集已启用（installed && enable）插件，按 hook_rank 升序
		$pluginlist = array();
		foreach($plugins as $dir=>$pconf) {
			if(empty($pconf['installed']) || empty($pconf['enable'])) continue;
			$pluginlist[$dir] = array(
				'dir'=>$dir,
				'name'=>isset($pconf['name']) ? $pconf['name'] : $dir,
				'hook_rank'=>isset($pconf['hook_rank']) ? intval($pconf['hook_rank']) : 0,
				'hooks'=>isset($pconf['hooks']) ? array_keys($pconf['hooks']) : array(),
			);
		}
		// 按 hook_rank 升序（序号小先执行），同 rank 按 dir 升序
		uasort($pluginlist, function($a, $b) {
			if($a['hook_rank'] == $b['hook_rank']) {
				return strcmp($a['dir'], $b['dir']);
			}
			return $a['hook_rank'] > $b['hook_rank'] ? 1 : -1;
		});
		$pluginlist = array_values($pluginlist);
		$plugincount = count($pluginlist);

		// hook admin_setting_hook_rank_get_end.php

		include _include(ADMIN_PATH."view/htm/setting_hook_rank.htm");

	} else {

		// hook admin_setting_hook_rank_post_start.php

		$dir = param('dir', '', FALSE);
		$dir2 = param('dir2', '', FALSE);
		if(empty($dir) || empty($dir2)) {
			message(-1, lang('hook_rank_invalid'));
		}
		if(!is_word($dir) || !is_word($dir2)) {
			message(-1, lang('hook_rank_invalid'));
		}

		plugin_init();

		if(!isset($plugins[$dir]) || !isset($plugins[$dir2])) {
			message(-1, lang('plugin_not_exists'));
		}

		// 交换两个插件的 hook_rank（缺失的按 0 处理，再统一补齐）
		$rank1 = isset($plugins[$dir]['hook_rank']) ? intval($plugins[$dir]['hook_rank']) : 0;
		$rank2 = isset($plugins[$dir2]['hook_rank']) ? intval($plugins[$dir2]['hook_rank']) : 0;

		file_replace_var(APP_PATH."plugin/$dir/conf.json", array('hook_rank'=>$rank2), TRUE);
		file_replace_var(APP_PATH."plugin/$dir2/conf.json", array('hook_rank'=>$rank1), TRUE);

		plugin_clear_tmp_dir();

		// hook admin_setting_hook_rank_post_end.php

		message(0, lang('save_successfully'));
	}
}

// hook admin_setting_end.php

?>