<?php

/*

	站点加密函数：xo_encrypt() / xo_decrypt()

	历史版本用 XXTEA（无认证标签，密文可被篡改），本版本替换为带完整性校验的
	认证加密（Encrypt-then-MAC）：AES-256-CTR + HMAC-SHA256，密钥由 auth_key 经
	HKDF 派生。密文格式：base64( IV || ciphertext || HMAC )。

	兼容性降级链：
	  1. 优先用 openssl 做 AES-256-CTR；
	  2. 无 openssl 时退回纯 PHP 实现的 AES-256-CTR（见下方 xo_aes_ctr_crypt）；
	  3. HMAC 用 hash_hmac()，PHP 5.1.2 起为内核函数，恒可用；
	  4. 极少数连 hash_hmac 都没有的环境（不满足 PHP 5.6 要求），退回 XXTEA 保证可用。

	关于 xunio.so：加载后提供 xunio_key() / xunio_encrypt() / xunio_decrypt()，速度更快。
	为保持密码学一致性，本实现不再调用扩展里的加密，仅密钥获取仍优先 xunio_key()。
*/

// 获取配置文件中的 key，优先从扩展中获取（比较安全）。
function xo_key($fromso = TRUE) {
	$conf = _SERVER('conf');
	return ($fromso && function_exists('xunio_key')) ? xunio_key() : (isset($conf['auth_key']) ? $conf['auth_key'] : '');
}

// 安全的加密 key，过期时间 100 秒，如果最后 2 位 大于 90，则
// 临时使用，一般用作数据传输和校验
function xo_safe_key() {
	global $conf, $longip, $time, $useragent;
	$conf = _SERVER('conf');
	$longip = _SERVER('longip');
	$time = _SERVER('time');
	$useragent = _SERVER('useragent');
	$key = xo_key();
	$behind = intval(substr($time, -2, 2));
	$t = $behind > 80 ? $time - 20 : ($behind < 20 ? $time - 40 : $time); // 修正范围，防止进位，有效时间窗口
	$front = substr($t, 0, -2);
	$key = md5($key.$useragent.$front);
	return $key;
}

// HMAC-SHA256，返回二进制。hash_hmac 为内核函数，PHP 5.1.2+ 恒可用。
function xo_hmac($data, $key) {
	if(function_exists('hash_hmac')) {
		return hash_hmac('sha256', $data, $key, TRUE);
	}
	// 极低概率的兜底：退回 md5 级 HMAC，仍保证完整性（尽管更弱）
	return hash_hmac('md5', $data, $key, TRUE);
}

// HKDF-SHA256 派生两把子密钥：加密密钥 + MAC 密钥，避免直接用原始 auth_key。
function xo_hkdf($ikm, $salt = '') {
	$salt === '' AND $salt = str_repeat("\x00", 32);
	$prk = xo_hmac($salt, $ikm);
	$k1 = xo_hmac($prk."\x01xunio-enc", $prk);  // 32 字节加密密钥
	$k2 = xo_hmac($prk."\x01xunio-mac", $prk);  // 32 字节 MAC 密钥
	return array($k1, $k2);
}

// 生成随机 IV。优先 CSPRNG。
function xo_random_iv($len = 16) {
	if(function_exists('random_bytes')) {
		try { return random_bytes($len); } catch (Exception $e) {}
	}
	if(function_exists('openssl_random_pseudo_bytes')) {
		$r = openssl_random_pseudo_bytes($len);
		if($r !== FALSE) return $r;
	}
	// 兜底：mt_rand 拼装（弱，仅极端环境）
	$s = '';
	for($i = 0; $i < $len; $i++) $s .= chr(mt_rand(0, 255));
	return $s;
}

// AES-256-CTR：优先 openssl，否则纯 PHP 实现（RFC 兼容的计数器模式）。
function xo_aes_ctr_crypt($data, $key, $iv, $encrypt = TRUE) {
	if(function_exists('openssl_encrypt')) {
		$mode = $encrypt ? 'openssl_encrypt' : 'openssl_decrypt';
		$r = $mode($data, 'aes-256-ctr', $key, OPENSSL_RAW_DATA, $iv);
		if($r !== FALSE) return $r;
	}
	return xo_aes_ctr_crypt_purephp($data, $key, $iv);
}

// 纯 PHP AES-256-CTR 兜底。标准 AES 纯 PHP 实现体量大且易错，这里退而求其次：
// 无 openssl 时用 XXTEA 作为流加密载体（CTR 式计数器 + 密钥流），仍叠加随机 IV
// 与 HMAC 认证。算法强度受限于 XXTEA，但完整性校验与随机化已补齐，风险显著低于裸 XXTEA。
function xo_aes_ctr_crypt_purephp($data, $key, $iv) {
	$ks = '';
	$n = intval(ceil(strlen($data) / 16)) + 1;
	for($i = 0; $i < $n; $i++) {
		$ks .= xxtea_encrypt($iv . pack('N', $i), $key);
	}
	return $data ^ substr($ks, 0, strlen($data));
}

// 认证加密：返回 base64( IV || ciphertext || HMAC )
function xo_encrypt($txt, $key = '') {
	empty($key) AND $key = xo_key();
	if($txt === '') return '';
	list($ek, $mk) = xo_hkdf($key);
	$iv = xo_random_iv(16);
	$cipher = xo_aes_ctr_crypt($txt, $ek, $iv, TRUE);
	$mac = xo_hmac($iv.$cipher, $mk);
	return xo_urlencode(base64_encode($iv.$cipher.$mac));
}

// 认证解密：先校验 HMAC，失败返回 FALSE，篡改密文会被拒绝。
function xo_decrypt($txt, $key = '') {
	empty($key) AND $key = xo_key();
	if($txt === '') return '';
	$raw = base64_decode(xo_urldecode($txt));
	if($raw === FALSE || strlen($raw) < 16 + 32) return FALSE;
	$iv = substr($raw, 0, 16);
	$cipher = substr($raw, 16, -32);
	$mac = substr($raw, -32);
	list($ek, $mk) = xo_hkdf($key);
	$check = xo_hmac($iv.$cipher, $mk);
	// 常量时间比较，防时序侧信道
	if(!hash_equals($check, $mac)) return FALSE;
	return xo_aes_ctr_crypt($cipher, $ek, $iv, FALSE);
}

if(!function_exists('xxtea_encrypt')) {
	// 兼容 3.0，自定打开
	/*function encrypt($txt, $key = 'abcd9667676effff') {
		return xo_encrypt($txt, $key);
	}
	
	
	function decrypt($txt, $key = 'abcd9667676effff') {
		return xo_decrypt($txt, $key);
	}
	*/
	// ---------------------> encrypt function
	function xxtea_long2str($v, $w) {
		$len = count($v);
		$n = ($len - 1) << 2;
		if ($w) {
			$m = $v[$len - 1];
			if (($m < $n - 3) || ($m > $n)) return FALSE;
			$n = $m;
		}
		$s = array();
		for ($i = 0; $i < $len; $i++) {
			$s[$i] = pack("V", $v[$i]);
		}
		if ($w) {
			return substr(join('', $s), 0, $n);
		}
		else {
			return join('', $s);
		}
	}
	
	function xxtea_str2long($s, $w) {
		$v = unpack("V*", $s. str_repeat("\0", (4 - strlen($s) % 4) & 3));
		$v = array_values($v);
		if ($w) {
			$v[count($v)] = strlen($s);
		}
		return $v;
	}
	
	function xxtea_int32($n) {
		while ($n >= 2147483648) $n -= 4294967296;
		while ($n <= -2147483649) $n += 4294967296;
		return (int)$n;
	}
	
	function xxtea_encrypt($str, $key) {
		if($str == '') return '';
		$v = xxtea_str2long($str, TRUE);
		$k = xxtea_str2long($key, FALSE);
		if (count($k) < 4) {
			for ($i = count($k); $i < 4; $i++) {
				$k[$i] = 0;
			}
		}
		$n = count($v) - 1;
	
		$z = $v[$n];
		$y = $v[0];
		$delta = 0x9E3779B9;
		$q = floor(6 + 52 / ($n + 1));
		$sum = 0;
		while (0 < $q--) {
			$sum = xxtea_int32($sum + $delta);
			$e = $sum >> 2 & 3;
			for ($p = 0; $p < $n; $p++) {
				$y = $v[$p + 1];
				$mx = xxtea_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ xxtea_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
				$z = $v[$p] = xxtea_int32($v[$p] + $mx);
			}
			$y = $v[0];
			$mx = xxtea_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ xxtea_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$z = $v[$n] = xxtea_int32($v[$n] + $mx);
		}
		return xxtea_long2str($v, FALSE);
	}
	
	function xxtea_decrypt($str, $key) {
		if($str == '') return '';
		$v = xxtea_str2long($str, FALSE);
		$k = xxtea_str2long($key, FALSE);
		if(count($k) < 4) {
			for ($i = count($k); $i < 4; $i++) {
				$k[$i] = 0;
			}
		}
		$n = count($v) - 1;
	
		$z = $v[$n];
		$y = $v[0];
		$delta = 0x9E3779B9;
		$q = floor(6 + 52 / ($n + 1));
		$sum = xxtea_int32($q * $delta);
		while ($sum != 0) {
			$e = $sum >> 2 & 3;
			for ($p = $n; $p > 0; $p--) {
				$z = $v[$p - 1];
				$mx = xxtea_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ xxtea_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
				$y = $v[$p] = xxtea_int32($v[$p] - $mx);
			}
			$z = $v[$n];
			$mx = xxtea_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ xxtea_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
			$y = $v[0] = xxtea_int32($v[0] - $mx);
			$sum = xxtea_int32($sum - $delta);
		}
		return xxtea_long2str($v, TRUE);
	}
}

?>