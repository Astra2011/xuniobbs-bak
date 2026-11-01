<?php

// 重置指定用户（默认 uid=1）的密码，改完请立即删除本文件
// 用法：浏览器访问 tool/resetpw.php?uid=1&password=新密码
// 或命令行 php tool/resetpw.php

define('SKIP_ROUTE', 1);
include dirname(__FILE__).'/../index.php';

$uid = intval(param('uid', 1));
$newpw = param('password', '', FALSE);
empty($newpw) AND message(-1, '请指定新密码');

$r = user_update($uid, array('password'=>user_password_hash($newpw), 'salt'=>''));
$r === FALSE AND message(-1, '重置失败');

$user = user_read($uid);
message(0, $user['username'].' 的密码已经重设为：'.$newpw);

?>