<?php
// 合并 XunioPHP

$dir = '../xuniophp/';


$s = php_strip_whitespace($dir.'db_mysql.class.php');
$s .= php_strip_whitespace($dir.'db_pdo_mysql.class.php');
$s .= php_strip_whitespace($dir.'db_pdo_sqlite.class.php');
$s .= php_strip_whitespace($dir.'cache_apc.class.php');
$s .= php_strip_whitespace($dir.'cache_memcached.class.php');
$s .= php_strip_whitespace($dir.'cache_mysql.class.php');
$s .= php_strip_whitespace($dir.'cache_redis.class.php');
$s .= php_strip_whitespace($dir.'cache_xcache.class.php');
$s .= php_strip_whitespace($dir.'cache_yac.class.php');

$s .= php_strip_whitespace($dir.'db.func.php');
$s .= php_strip_whitespace($dir.'cache.func.php');
$s .= php_strip_whitespace($dir.'image.func.php');
$s .= php_strip_whitespace($dir.'array.func.php');
$s .= php_strip_whitespace($dir.'xo_encrypt.func.php');
$s .= php_strip_whitespace($dir.'misc.func.php');

$s = substr($s, 8, -2);

$xuniophp = file_get_contents($dir.'xuniophp.php');
$before = '// hook xuniophp_include_before.php';
$after = '// hook xuniophp_include_after.php';
$pre = substr($xuniophp, 0, strpos($xuniophp, $before) + 1 + strlen($before));
$suffix = substr($xuniophp, strpos($xuniophp, $after));
$xuniophp_min = trim($pre)."\r\n\r\n".trim($s)."\r\n\r\n".trim($suffix);

//echo $xuniophp_min;exit;
/*
$p = '#//\shook\sxuniophp_include_before\.php(.*?)//\shook\sxuniophp_include_after\.php#ism';
$xuniophp_min = preg_replace($p, $s, $xuniophp);
*/

/*
$xuniophp_min = preg_replace(
'#//\shook\sxuniophp_include_before\.php(.*)//\shook\sxuniophp_include_after\.php#ism', 
'//\shook\sxuniophp_include_before.php'.$s.'//\shook\sxuniophp_include_after.php', 
$xuniophp);*/

file_put_contents($dir.'xuniophp.min.php', $xuniophp_min);

echo 'ok';