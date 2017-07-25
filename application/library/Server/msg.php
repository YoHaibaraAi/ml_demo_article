<?php

/**
* @author kingfee
* @version 1.0
*
* 短信平台消息发送
* $option
* 
*/
class Server_msg
{
	var $send_url =array(
		"single" => "/msg/HttpSendSM",
		"multi"  => "/msg/HttpBatchSendSM",
		"var"    => "/msg/HttpVarSM",
		"pkg"    => "/msg/HttpPkgSM",
		"query"  => "/msg/QueryBalance"
	);

	var $error_msg = array();

	public $logcallback;

	public function __construct($options)
	{
		//
		$this->ip          = isset($options['ip'])?$options['ip']:'222.73.117.158';
		$this->account     = isset($options['account'])?$options['account']:'';
		$this->pswd        = isset($options['pswd'])?$options['pswd']:'';
		$this->logcallback = isset($options['logcallback'])?$options['logcallback']:false;

	}

	// 单发
	/**
	* 
	* $msg 短信内容长度不能超过585个字符。使用URL方式编码为UTF-8格式。短信内容超过70个字符（企信通是60个字符）时，会被拆分成多条，然后以长短信的格式发送。
	* 
	* 
	**/
	public function send_single_msg($mobile,$msg,$needstatus=true,$product="",$extno=""){
		$ip      = $this->ip;
		$account = $this->account;
		$pswd    = $this->pswd;

		if (empty($account)||empty($pswd)) {
			// $this->log("账号密码为空");
			throw new Exception("短信系统异常,请联系管理员", 1);
		}


		$post               = array();
		$post['account']    = $account;
		$post['pswd']       = $pswd;
		$post['mobile']     = $mobile;
		$post['msg']        = $msg;
		$post['needstatus'] = $needstatus;
		if (!empty($product)) {
			$post['product'] = $product;
		}
		if (!empty($extno)) {
			$post['extno'] 	 = $extno;	
		}

		$post_url = "http://".$ip.$this->send_url['single'];
		//echo($post_url);
		// exit();
		$result   = $this->http_post($post_url,$post);

		return $result;
	}
	/**
	* http://222.73.117.158/msg/HttpBatchSendSM?
	* account=test01&pswd=123456&
	* mobile=18900000000,13800138000&msg=test&needstatus=true&product=274463133
	**/
	// 群发
	public function send_multi_msg($mobile,$msg,$needstatus=true,$product="",$extno=""){
		$ip      = $this->ip;
		$account = $this->account;
		$pswd    = $this->pswd;

		if (empty($account)||empty($pswd)) {
			// $this->log("账号密码为空");
			throw new Exception("短信系统异常,请联系管理员", 1);
		}


		$post               = array();
		$post['account']    = $account;
		$post['pswd']       = $pswd;
		$post['mobile']     = $mobile;
		$post['msg']        = $msg;
		$post['needstatus'] = $needstatus;
		if (!empty($product)) {
			$post['product'] = $product;
		}
		if (!empty($extno)) {
			$post['extno'] 	 = $extno;	
		}

		$post_url = "http://".$ip.$this->send_url['multi'];
		// echo($post_url);
		// exit();
		$result   = $this->http_post($post_url,$post);

		return $result;

	}
	/**
	* 短信模板。其中的变量用“{$var}”来替代。例如：
	* “{$var},你好！，请你于{$var}日参加活动”，该短信中具有两个变量参数。
	* 编码为UTF-8格式。
	*
	* 每一组参数之间用英文“；”间隔
	* 每一组参数内部用英文“，”间隔，其中第一个参数为手机号码，
	* 第二个参数为模板中第一个变量，
	* 第三个参数为模板中第二个变量，以此类推。
	* 单次不能超过1000条
	**************** Demo ********************
	* http://192.168.168.168/msg/HttpVarSM?
	* account=111111&pswd=123456
	* &msg={$var},你好！，请你于{$var}日参加活动
	* &params=13800210000,李先生,2013-01-01;13500210000,王先生，2013-01-15
	* &needstatus=true&product=99999
	**/
	public function send_var_msg($msg,$params,$needstatus=true,$product="",$extno=""){
		$ip      = $this->ip;
		$account = $this->account;
		$pswd    = $this->pswd;

		if (empty($account)||empty($pswd)) {
			// $this->log("账号密码为空");
			throw new Exception("短信系统异常,请联系管理员", 1);
		}

		$post               = array();
		$post['account']    = $account;
		$post['pswd']       = $pswd;
		$post['msg']        = $msg;
		$post['params']     = $params;
		$post['needstatus'] = $needstatus;
		if (!empty($product)) {
			$post['product'] = $product;
		}
		if (!empty($extno)) {
			$post['extno'] 	 = $extno;	
		}

		$post_url = "http://".$ip.$this->send_url['var'];
		// echo($post_url);
		// exit();
		$result = $this->http_post($post_url,$post);

		return $result;

	}
	/**
	* 发送消息包
	**************** Demo ************************
	* http://192.168.168.168/msg/HttpPkgSM?
	* account=111111&pswd=123456
	* &msg=13800000001|测试短信1&msg=13800000002|测试短信2&msg=13800000003|测试短信3
	* &needstatus=true&product=99999&extno=123
	***/
	public function send_package_msg($msgs,$needstatus=true,$product="",$extno=""){
		$ip      = $this->ip;
		$account = $this->account;
		$pswd    = $this->pswd;
		
		// foreach ($msgs as $key => $value) {
		// 	$msg = $value;
		// }
		$post = array();
		$post['account']    = $account;
		$post['pswd']       = $pswd;
		$post['msg']        = $msgs;
		$post['needstatus'] = $needstatus;
		if (!empty($product)) {
			$post['product'] = $product;
		}
		if (!empty($extno)) {
			$post['extno'] 	 = $extno;	
		}

		$post_url = "http://".$ip.$this->send_url['pkg'];
		
		$result   = $this->http_post($post_url,$post);

		return $result;
	}
	/** 获取余额 **/
	public function get_querybalance(){
		$ip      = $this->ip;
		$account = $this->account;
		$pswd    = $this->pswd;

		$post            = array();
		$post['account'] = $account;
		$post['pswd']    = $pswd;

		$post_url = "http://".$ip.$this->send_url['query'];
		$result   = $this->http_post($post_url,$post);

		return $result;
	}


	public function split_respose($data){
		$data = split("/\n/", $data);

	}

  /**
   * 日志记录，可被重载。
   * @param mixed $log 输入日志
   * @return mixed
   */
  protected function log($log){
  		if ($this->debug && function_exists($this->logcallback)) {
  			if (is_array($log)) $log = print_r($log,true);
  			return call_user_func($this->logcallback,$log);
  		}
  }
	/**
	 * GET 请求
	 * @param string $url
	 */
	private function http_get($url){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($oCurl, CURLOPT_TIMEOUT, 15);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

	/**
	 * POST 请求
	 * @param string $url
	 * @param array $param
	 * @param boolean $post_file 是否文件上传
	 * @return string content
	 */
	private function http_post($url,$param,$post_file=false){
		$oCurl = curl_init();
		if(stripos($url,"https://")!==FALSE){
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
			curl_setopt($oCurl, CURLOPT_SSLVERSION, 1); //CURL_SSLVERSION_TLSv1
		}
		if (is_string($param) || $post_file) {
			$strPOST = $param;
		} else {
			$aPOST = array();
			// print_r($param);
			// exit();
			foreach($param as $key=>$val){
				// $aPOST[] = $key."=".urlencode($val);
				// 参数 数组传入 消息包
				if (!is_array($val)) {
					$aPOST[] = $key."=".urlencode($val);
					// echo("\n".$key."=".$val);
				}else{
					// echo("\n\nthis is ".$val."\n\n");
					foreach ($val as $k => $v) {
						$aPOST[] = $key."=".urlencode($v);
					}
				}

				// $aPOST[] = $key."=".$val;
			}
			$strPOST =  join("&", $aPOST);
		}
		// echo $strPOST;
		// exit();
		curl_setopt($oCurl, CURLOPT_URL, $url);
		curl_setopt($oCurl, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($oCurl, CURLOPT_TIMEOUT, 15);
		curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt($oCurl, CURLOPT_POST,true);
		curl_setopt($oCurl, CURLOPT_POSTFIELDS,$strPOST);
		$sContent = curl_exec($oCurl);
		$aStatus = curl_getinfo($oCurl);
		curl_close($oCurl);
		if(intval($aStatus["http_code"])==200){
			return $sContent;
		}else{
			return false;
		}
	}

}
?>