<?php
function format_data($data) {
	//json不支持中文,使用前先转码
	foreach ($data as $key => $value) {
		foreach ($value as $k => $v) {
			$data[$key][$k] = urlencode($v);
		}
	}
	return urldecode(json_encode($data));
}
