<?php
//解决JSON输出中文乱码
function arrayRecursive(&$array, $function, $apply_to_keys_also = false) {
	static $recursive_counter = 0;
	if (++$recursive_counter > 1000) {
		die('possible deep recursion attack');
	}
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			arrayRecursive($array[$key], $function, $apply_to_keys_also);
		} else {
			$array[$key] = $function($value);
		}
		if ($apply_to_keys_also && is_string($key)) {
			$new_key = $function($key);
			if ($new_key != $key) {
				$array[$new_key] = $array[$key];
				unset($array[$key]);
			}
		}
	}
	$recursive_counter--;
}
//解决JSON输出中文乱码
function JSON($array) {
	arrayRecursive($array, 'urlencode', true);
	$json = json_encode($array);
	return urldecode($json);
}
/**
 * 获得用户IP地址
 * @return [string] [IP地址]
 */
function getRealIp() {
	$ip = false;
	if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
		$ip = $_SERVER["HTTP_CLIENT_IP"];
	}
	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
		$ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
		if ($ip) {
			array_unshift($ips, $ip);
			$ip = false;}
		for ($i = 0; $i < count($ips); $i++) {
			if (!eregi("^(10│172.16│192.168).", $ips[$i])) {
				$ip = $ips[$i];
				break;
			}
		}
	}
	return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
}

//判断用户是否登录
//返回：bool/string
function is_login() {
	$user = \Lib\Session::get('userinfo');
	return $user != null ? $user['uid'] : false;
}
