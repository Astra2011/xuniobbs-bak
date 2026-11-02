<?php

// 重置指定用户（默认 uid=1）的密码，改完请立即删除本文件
// 用法：命令行 php tool/resetpw.php uid=1 password=新密码
// 安全：仅允许命令行（IN_CMD）运行，禁止 Web 匿名访问，防止任意人重置管理员密码

define('SKIP_ROUTE', 1);
include dirname(__FILE__).'/../index.php';

// 仅命令行可执行；Web 访问一律拒绝
!IN_CMD AND exit('Access Denied: 此脚本仅允许命令行执行。');

$uid = intval(param('uid', 1));
$newpw = param('password', '', FALSE);
$newpw === '' AND exit('请指定新密码');

$r = user_update($uid, array('password'=>user_password_hash($newpw), 'salt'=>''));
$r === FALSE AND exit('重置失败');

$user = user_read($uid);
exit($user['username'].' 的密码已经重设为：'.$newpw);

?>