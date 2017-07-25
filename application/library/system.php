<?php

/**
 * 由base.class.php拆出
 * 后台采集抓取、统计分析需要用到的公共函数
 * @author sunny
 *
 */
class system
{
	/**
	 * 日期格式有效性校验
	 *
	 * @param date $date
	 * @param string $format
	 * @return boolean
	 */
	public static function valid_date($date, $format = 'YYYY-MM-DD')
	{
		if (strlen ( $date ) >= 8 && strlen ( $date ) <= 10) {
			$separator_only = str_replace ( array (
					'M',
					'D',
					'Y'
			), '', $format );
			$separator = $separator_only [0];
			if ($separator) {
				$regexp = str_replace ( $separator, "\\" . $separator, $format );
				$regexp = str_replace ( 'MM', '(0[1-9]|1[0-2])', $regexp );
				$regexp = str_replace ( 'M', '(0?[1-9]|1[0-2])', $regexp );
				$regexp = str_replace ( 'DD', '(0[1-9]|[1-2][0-9]|3[0-1])', $regexp );
				$regexp = str_replace ( 'D', '(0?[1-9]|[1-2][0-9]|3[0-1])', $regexp );
				$regexp = str_replace ( 'YYYY', '\d{4}', $regexp );
				$regexp = str_replace ( 'YY', '\d{2}', $regexp );
				if ($regexp != $date && preg_match ( '/' . $regexp . '$/', $date )) {
					foreach ( array_combine ( explode ( $separator, $format ), explode ( $separator, $date ) ) as $key => $value ) {
						if ($key == 'YY')
							$year = '20' . $value;
						if ($key == 'YYYY')
							$year = $value;
						if ($key [0] == 'M')
							$month = $value;
						if ($key [0] == 'D')
							$day = $value;
					}
					if (checkdate ( $month, $day, $year ))
						return true;
				}
			}
		}
		return false;
	}
	/**
	 *
	 *
	 **/
	function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

	/**
	 * mid转mid_62链接
	 * @param unknown_type $mid
	 * @return string
	 */
	public static function midToStr($mid)
	{
		settype($mid, 'string');
		$mid_length = strlen($mid);
		$url = '';
		$str = strrev($mid);
		$str = str_split($str, 7);

		foreach ($str as $v) {
			$char = self::intTo62(strrev($v));
			$char = str_pad($char, 4, "0");
			$url .= $char;
		}

		$url_str = strrev($url);

		return ltrim($url_str, '0');
	}

	/**
	 * 62进制字典
	 * @param string $key
	 * @return Ambigous <string>
	 */
	public static function str62keys_int_62($key)
	{
		$str62keys = array (
				"0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q",
				"r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q",
				"R","S","T","U","V","W","X","Y","Z"
		);
		return $str62keys[$key];
	}



	/**
	 * url 10 进制 转62进制
	 * @param unknown_type $int10
	 * @return Ambigous <string, Ambigous>
	 */
	public static function intTo62($int10)
	{
		$s62 = '';
		$r = 0;
		while ($int10 != 0) {
			$r = $int10 % 62;
			$s62 .= self::str62keys_int_62($r);
			$int10 = floor($int10 / 62);
		}

		return $s62;
	}


	public static function sinaWburl2ID($url)
	{
		$surl[2] = self::str62to10(substr($url, strlen($url) - 4, 4));
		$surl[1] = self::str62to10(substr($url, strlen($url) - 8, 4));
		$surl[0] = self::str62to10(substr($url, 0, strlen($url) - 8));
		$int10 = $surl[0] . $surl[1] . $surl[2];
		return ltrim($int10, '0');
	}

	/**
	 * 62进制到10进制
	 * @param string $str62
	 */
	public static function str62to10($str62)
	{
		$strarry = str_split($str62);
		$str = 0;
		for ($i = 0; $i < strlen($str62); $i++) {
			$vi = Pow(62, (strlen($str62) - $i -1));

			$str += $vi * self::str62keys($strarry[$i]);
		}
		$str = str_pad($str, 7, "0", STR_PAD_LEFT);
		return $str;
	}

	/**
	 * //62进制字典
	 * @param unknown_type $ks
	 */
	public static function str62keys($ks)
	{
		$str62keys = array (
				"0","1","2","3","4","5","6","7","8","9","a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q",
				"r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q",
				"R","S","T","U","V","W","X","Y","Z"
		);
		return array_search($ks, $str62keys);
	}

	/**
	 * 较验接口返回值,如果错误打印错误并返回false,否则返回true
	 * @param array $resInfo
	 * @return boolean
	 */
	public static function __check_api_return_res($resInfo)
	{
		if(isset($resInfo['data'])) {//使用mod_get_mblogv2接口
			if ($resInfo['data']['error'] > 1) {//报错
				self::__print ( 'errno:'.$resInfo['data']['errno'].',errmsg:'.$resInfo['data']['errmsg'], 'waring' );
				return false;
			}elseif((isset($resInfo['data']['count']) && $resInfo['data']['count'] <= 0) || count($resInfo['data']['result']) <= 0){//没有数据
				return false;
			} else {
				return true;
			}
		}else{//新浪官方
			if (!is_array($resInfo) || isset ( $resInfo ['error'] )) {
				self::__print ( is_array($resInfo) ? implode ( '||', $resInfo ) : '', 'waring' );
				return false;
			} else {
				return true;
			}
		}
	}

	/**
	 * @param string $msg
	 * @param string $level 级别  普通消息：info 警告:warning记日志
	 */
	public static function __print($msg, $level='info')
	{
		echo "\n------------------header---------------\n";
		if (defined ( 'ISDOS' ) && ISDOS == TRUE) {
			echo "\nbody\t" . iconv ( 'UTF-8', 'GBK', $msg ) . "\n\n";
		} else {
			echo "\nbody\t" . $msg . "\n\n";
		}
		echo "\n------------------footer---------------\n";

		if($level == 'warning') {
			file_put_contents(CRAWL_LOG_DIR . '/'. $level .date('Y-m-d').'.log', $msg."\n", FILE_APPEND);
		}
	}

	/**
	 * 返回当前 Unix 时间戳和微秒数
	 * @return number
	 */
	public static function get_microtime()
	{
		list($usec, $sec) = explode(' ', microtime());
		return ((float)$usec + (float)$sec);
	}

	/**
	 * 根据字段排序
	 */
	public static function array_orderby()
	{
		$args = func_get_args();
		$data = array_shift($args);
		foreach ($args as $n => $field) {
			if (is_string($field)) {
				$tmp = array();
				foreach ($data as $key => $row)
					$tmp[$key] = $row[$field];
				$args[$n] = $tmp;
			}
		}
		$args[] = &$data;
		call_user_func_array('array_multisort', $args);
		return array_pop($args);
	}

	public static function get_week_day($str)
	{
		$s_arr = array('Sunday', 'Saturday', 'Friday', 'Thursday', 'Wednesday', 'Tuesday', 'Monday');
		$r_arr = array('星期日', '星期六', '星期五', '星期四', '星期三', '星期二', '星期一');
		return str_replace($s_arr, $r_arr, $str);
	}

	/**
	 * 转换字节
	 * @param int $size
	 * @return string
	 */
	public static function convert($size, $dec=2)
	{
		$a = array("B", "KB", "MB", "GB", "TB", "PB");
		$pos = 0;
		while ($size >= 1024) {
			$size /= 1024;
			$pos++;
		}
		return round($size,$dec)." ".$a[$pos];
	}

	/**
	 * Make an HTTP request
	 *
	 * @return string API results
	 * @ignore
	 */
	public static function http($url, $method, $postfields = NULL, $headers = array())
	{
		$http_info = array();
		$ci = curl_init();
		$useragent = 'mblog.city';
		$connecttimeout = 30;
		$timeout = 30;
		$ssl_verifypeer = false;


		/* Curl settings */
		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ci, CURLOPT_USERAGENT, $useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $connecttimeout);
		curl_setopt($ci, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ci, CURLOPT_ENCODING, "");
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $ssl_verifypeer);
		//curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);

		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($postfields)) {
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
					$this->postdata = $postfields;
				}
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (!empty($postfields)) {
					$url = "{$url}?{$postfields}";
				}
		}


		$headers[] = "API-RemoteIP: " . $_SERVER['REMOTE_ADDR'];
		curl_setopt($ci, CURLOPT_URL, $url );
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

		$response = curl_exec($ci);
		$http_code = curl_getinfo($ci, CURLINFO_HTTP_CODE);
		$http_info = array_merge($http_info, curl_getinfo($ci));
		//$this->url = $url;

		if ($_GET['debug']) {
			echo "=====post data======\r\n";
			var_dump($postfields);

			echo '=====info====='."\r\n";
			print_r( curl_getinfo($ci) );

			echo '=====$response====='."\r\n";
			print_r( $response );
		}
		curl_close ($ci);
		return $response;
	}

	/**
	 * 字符串截取
	 * @param string $str
	 * @param int $start
	 * @param int $lenth
	 */
	public static function subString($str, $start, $lenth)
	{
		$len = strlen($str);
		$r = array();
		$n = 0;
		$m = 0;
		for($i = 0; $i < $len; $i++) {
			$x = substr($str, $i, 1);
			$a  = base_convert(ord($x), 10, 2);
			$a = substr('00000000'.$a, -8);
			if ($n < $start){
				if (substr($a, 0, 1) == 0) {
				}elseif (substr($a, 0, 3) == 110) {
					$i += 1;
				}elseif (substr($a, 0, 4) == 1110) {
					$i += 2;
				}
				$n++;
			}else{
				if (substr($a, 0, 1) == 0) {
					$r[ ] = substr($str, $i, 1);
				}elseif (substr($a, 0, 3) == 110) {
					$r[ ] = substr($str, $i, 2);
					$i += 1;
				}elseif (substr($a, 0, 4) == 1110) {
					$r[ ] = substr($str, $i, 3);
					$i += 2;
				}else{
					$r[ ] = '';
				}
				if (++$m >= $lenth){
					break;
				}
			}
		}
		return $r;
	}

	/**
	 * 邮件发送接口
	 */
	public static function sendMail($from,$to,$nickname,$title,$body,$sendtype=0,$tag="",$username="webmaster@dfz_weibo.senderline.com",$password="IxYWUwMGRkZmQ"){

		if(empty($from)||empty($to)||empty($nickname)||empty($title)||empty($body))return false;

		$to_arr=explode(';', $to);
		if(is_array($to_arr)){
			foreach($to_arr as $sub_to){
				self::sendOneMail($from, $sub_to, $nickname, $title, $body,$sendtype,$tag,$username,$password);
			}
		}

		return true;
	}

	/**
	 * 邮件发送接口(单条)
	 */
	public static function sendOneMail($from,$to,$nickname,$title,$body,$sendtype=0,$tag="",$username="webmaster@dfz_weibo.senderline.com",$password="IxYWUwMGRkZmQ")
	{
		if(empty($from)||empty($to)||empty($nickname)||empty($title)||empty($body))return false;
		$curl=new Curl_Class();

		$url="http://edsapi.mail.sina.com/edsapi/api/sendmail.php";

		$fields=array();
		$fields['username']=$username;
		$fields['password']=$password;
		$fields['from']=$from;
		$fields['to']=$to;
		$fields['nickname']=$nickname;
		$fields['title']=$title;
		$fields['body']=$body;
		$fields['sendtype']=$sendtype;
		$fields['tag']=$tag;

		$result= $curl->post($url, $fields);

		if($result){
			$result_arr=json_decode($result,true);

			if($result_arr['errcode']=="1000")return true;
		}

		return array('status'=>false, 'error'=>$result_arr['errcode']. ": " . $result_arr['message']);

	}

	/**
	 * 邮件发送接口(大量邮件发送的情况才才使用)
	 */
	public static function sendMoreMail($from,$to,$nickname,$title,$body,$sendtype=0,$tag="",$username="webmaster@dfz_weibo.senderline.com",$password="IxYWUwMGRkZmQ"){

		if(empty($from)||empty($to)||empty($nickname)||empty($title)||empty($body))return false;
		$curl=new Curl_Class();

		$url="http://edsapi.mail.sina.com/edsapi/api/sendmoremail.php";

		$fields=array();
		$fields['username']=$username;
		$fields['password']=$password;
		$fields['from']=$from;
		$fields['to']=$to;
		$fields['nickname']=$nickname;
		$fields['title']=$title;
		$fields['body']=$body;
		$fields['sendtype']=$sendtype;
		$fields['tag']=$tag;

		$result= $curl->post($url, $fields);

		if($result){
			$result_arr=json_decode($result,true);

			if($result_arr['errcode']=="1000")return true;
		}

		return false;
	}

}
