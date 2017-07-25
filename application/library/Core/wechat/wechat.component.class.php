<?php

class WechatComponent
{
	// const AppID="wxb0d867b0bd27ac57";
	// const AppSecret="0c79e1fa963cd80cc0be99b20a18faeb";
	// const Redirect_URI="http://wxz.wemedia.cn/back_api.php";
	// const EncodingAesKey = "QBcl2Vd5PhtEJB65eYXgYDlCJJeXfGpoc1emQzJu81g";
	// const Token = "OTg4NDkz";
	const API_URL_PREFIX = 'https://api.weixin.qq.com/cgi-bin';
	const COMPONENT_LOGIN_URL = 'https://mp.weixin.qq.com/cgi-bin/componentloginpage';
	const COMPONENT_TOKEN_URL = '/component/api_component_token';
	const COMPONENT_PREAUTHCODE_URL = '/component/api_create_preauthcode';
	const COMPONENT_QUERY_AUTH_URL ='/component/api_query_auth';
	const COMPONENT_GET_AUTHORIZER_INFO_URL = '/component/api_get_authorizer_info';
	const COMPONENT_AUTHORIZER_TOKEN_URL = '/component/api_authorizer_token';
	// const COMPONENT_TOKEN_URL = 'component/api_component_token';
	// const COMPONENT_TOKEN_URL = 'component/api_component_token';

	public function __construct($options)
	{
		$this->appid          = isset($options['appid'])?$options['appid']:'';
		$this->appsecret      = isset($options['appsecret'])?$options['appsecret']:'';
		$this->redirect_uri   = isset($options['redirect_uri'])?$options['redirect_uri']:'';
		$this->encodingAesKey = isset($options['encodingaeskey'])?$options['encodingaeskey']:'';
		$this->token          = isset($options['token'])?$options['token']:'';

		$this->mc=Server::get(Server::cache);

	}

	/*
	获取第三方平台令牌  过期时间 7200
	[component_access_token] => lJg8zZM2i7gogvD7xH63u_8ZJZy0XJ1VCTan0OXGg7XfSQtn1rzdhWIG3BcDEFHW-xZUf00Q-96umFggxtKnKBOSQ8di2EvgrURK021h9jQ
    [expires_in] => 7200*/
	function get_api_component_token($verify_ticket){
		$appid     = $this->appid;
		$appsecret = $this->appsecret;

		$data  = "";
		$key   = "mc4easyfans_get_api_component_token_{$appid}_{$appsecret}_{$verify_ticket}";
		$cache = $this->mc->get($key);
		if (empty($cache))
		{
			$content   = "";
			$url       = self::API_URL_PREFIX.self::COMPONENT_TOKEN_URL;
			// 'https://api.weixin.qq.com/cgi-bin/component/api_component_token';
			
			$post_file = '{
			"component_appid":"'.$appid.'" ,
			"component_appsecret": "'.$appsecret.'", 
			"component_verify_ticket": "'.$verify_ticket.'" 
			}';

			$content    = self::http_post($url,$post_file,true);
			$data       = json_decode($content,true);
			
			$expires_in = $data['expires_in'];
			$expires_in = intval($expires_in);
			if ($expires_in>200) {
				// 保存MC
				$mc_timeout = $expires_in -200;
				$this->mc->set($key, serialize($data), 0, $mc_timeout);
			}
		}else{
			// echo("get get_api_component_token mc\n");
			$data = unserialize($cache);
			
		}
		// print_r($data);

		return $data;
	}


	/*
	获取预授权码
    [pre_auth_code] => pV0Ek1ebQWNF476KcshTblCbjFmArM72qr9ohpmIDl-XXSGz7ZZDcSISRhkVoNla
    [expires_in] => 1800
	*/
	function get_api_create_preauthcode($component_access_token){
		$appid = $this->appid;

		$data  = "";

		$url       = self::API_URL_PREFIX.self::COMPONENT_PREAUTHCODE_URL.'?component_access_token='.$component_access_token;
		// 'https://api.weixin.qq.com/cgi-bin/component/api_create_preauthcode?component_access_token='.$component_access_token;
		$post_file = '{
			"component_appid":"'.$appid.'" 
		}';

		$content    = self::http_post($url,$post_file,true);
		$data       = json_decode($content,true);

		return $data;
	}

	// 拼接微信公众号授权页面地址
	function join_auth_url($pre_auth_code,$redirect_uri){
		$appid = $this->appid;
		$url   = self::COMPONENT_LOGIN_URL;
		// "https://mp.weixin.qq.com/cgi-bin/componentloginpage";
		$url   .="?component_appid=".$appid;
		$url   .="&pre_auth_code=".$pre_auth_code;
		$url   .="&redirect_uri=".$redirect_uri;
		return $url;
	}

	// 获取公众号授权信息
	public function get_api_query_auth($auth_code,$component_access_token){
		$appid = $this->appid;

		$data  = "";
		$key   = "mc4easyfans_get_api_query_auth_{$appid}_{$auth_code}_{$component_access_token}";
		$cache = $this->mc->get($key);
		if (empty($cache))
		{	
			$url = self::API_URL_PREFIX.self::COMPONENT_QUERY_AUTH_URL.'?component_access_token='.$component_access_token;
			// 'https://api.weixin.qq.com/cgi-bin/component/api_query_auth?component_access_token='.$component_access_token;
			$post_file='{
				"component_appid":"'.$appid.'",
				"authorization_code": "'.$auth_code.'"
			}';
			// echo($url);
			// echo($post_file."\n<br>");

			$content = self::http_post($url,$post_file,true);
			$data = json_decode($content,true);

			$expires_in = $data['authorization_info']['expires_in'];
			$expires_in = intval($expires_in);
			if ($expires_in>200) {
				// 保存MC
				$mc_timeout = $expires_in -200;
				$this->mc->set($key, serialize($data), 0, $mc_timeout);
			}
		}else{
			// echo("get get_api_query_auth mc\n");
			$data = unserialize($cache);
		}
		// print_r($data);
		return $data;
	}

	// 获取公众号详细信息
	public function get_api_get_authorizer_info($auth_appid,$component_access_token){
		$appid = $this->appid;

		$url = self::API_URL_PREFIX.self::COMPONENT_GET_AUTHORIZER_INFO_URL.'?component_access_token='.$component_access_token;
		// 'https://api.weixin.qq.com/cgi-bin/component/api_get_authorizer_info?component_access_token='.$component_access_token;
		$post_file='{
			"component_appid":"'.$appid.'",
			"authorizer_appid":"'.$auth_appid.'"
		}';
		// echo($url);
		// echo($post_file."\n<br>");
		$content = self::http_post($url,$post_file,true);
		// echo($content);

		$data = json_decode($content,true);
		// echo("======公众号信息===\n");
		// print_r($data);
		return $data;
	}

	// 获取刷新token
	public function get_api_authorizer_token($auth_appid,$refresh_token,$component_access_token){
		$appid = $this->appid;

		$data  = "";
		$key   = "mc4easyfans_get_api_authorizer_token_{$appid}_{$auth_appid}_{$refresh_token}_{$component_access_token}";
		$cache = $this->mc->get($key);
		if (empty($cache))
		{
			$url = self::API_URL_PREFIX.self::COMPONENT_AUTHORIZER_TOKEN_URL.'?component_access_token='.$component_access_token;
			// 'https://api.weixin.qq.com/cgi-bin/component/api_authorizer_token?component_access_token='.$component_access_token;
			$post_file='{
				"component_appid":"'.$appid.'",
				"authorizer_appid":"'.$auth_appid.'",
				"authorizer_refresh_token": "'.$refresh_token.'"
			}';
			$content = self::http_post($url,$post_file,true);
			$data = json_decode($content,true);

			$expires_in = $data['authorization_info']['expires_in'];
			$expires_in = intval($expires_in);
			if ($expires_in>200) {
				// 保存MC
				$mc_timeout = $expires_in -200;
				$this->mc->set($key, serialize($data), 0, $mc_timeout);
			}
		}else{
			// echo("get get_api_authorizer_token mc\n");
			$data = unserialize($cache);
		}
		// print_r($data);
		return $data;
	}

	/**
	====依赖于wechat.class.php中的 解密类==
	* 消息解密
	*/
	public function get_decrypt_msg($xml){
		$appid = $this->appid;
		$aeskey = $this->encodingAesKey;

		$result ="";

		$msg_data = (array)simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		$msg_appid   = $msg_data['AppId'];
		$msg_encrypt = $msg_data['Encrypt'];

		if ($msg_appid==$appid) {
			// 解密数据
			$prpc = new Prpcrypt($aeskey);
			$data = $prpc->decrypt($msg_encrypt,$msg_appid);

			$result = is_array($data)?$data[1]:$data;
		}
		// print_r($result);
		// exit();
		return $result;
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

	// post请求
	// public function http_post($url,$fields,$userAgent = '', $httpHeaders = '', $username = '', $password = ''){
	// 	$curl = new Curl_Class();
	// 	return $curl->post($url, $fields, $userAgent, $httpHeaders, $username, $password);
	// }
	// get请求
	// public function http_get($url, $userAgent = '', $httpHeaders = '', $username = '', $password = '')   {
	// 	$curl = new Curl_Class();
	// 	return $curl->get($url, $userAgent, $httpHeaders, $username, $password);
	// }



}