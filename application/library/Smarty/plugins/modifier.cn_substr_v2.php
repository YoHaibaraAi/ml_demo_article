<?php
/**
 * 截取utf-8中文字符
 * 以len=10（5个字）为例，如果长度小于等于5个字，那么直接返回；否则截取4个字再加后缀，此时如果后缀为空串，结果则会看起来有点诡异
 * 按照微博字数规则，半角字符数为1，汉字为2
 * 与foodv2项目 include/mod_status_core::substr_utf8_cn相同
 * @param unknown_type $str
 * @param unknown_type $len 截取的字符数
 * @param unknown_type $suffix
 * @return unknown|Ambigous <string, unknown>
 */
function smarty_modifier_cn_substr_v2($str, $len, $suffix = '') {
	$ori_str = $str;
	$length = strlen(@iconv('UTF-8', 'GBK', $str));
	if ($length <= $len) {
		return $str;
	}else {
		$len -= 2;
	}
	
	$result_str = '';
	for($i = 0; $i < $len; $i ++) {
		$temp_str = substr ( $str, 0, 1 );
		if (ord ( $temp_str ) > 127) {
			if ($i + 1 < $len) {
				$result_str .= substr ( $str, 0, 3 );
				$str = substr ( $str, 3 );
			}
			$i ++;
		} else {
			$result_str .= substr ( $str, 0, 1 );
			$str = substr ( $str, 1 );
		}
	}
	
	if (strcmp ( $ori_str, $result_str ) > 0 && $suffix != '') {
		$result_str .= $suffix;
	}
	return $result_str;
}
?>