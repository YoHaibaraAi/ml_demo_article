<?php

/**
 * 
 * 由base.class.php拆出
 *
 * SESSION处理类
 * @package baseLib
 * @author 刘程辉 <chenghui@staff.sina.com.cn>
 * @version 1.00
 * @copyright (c) 2004, 新浪网研发中心 All rights reserved.
 * @Description 封装session的操作处理
 */

/**
 * History:
 * 2005-06-20 创建类1.0
 */


class session
{
	/**
	 * 从序列化数组中解析出来的session值
	 */
	var $session_val;

	/**
	 * session中值是否被取出并被unserialize解码过的标志
	 */
	var $flag_parse;

	/**
	 * 构造函数
	 */
	function session($name = "SessionID")
	{
		global $db_myuser_conf, $tbl_myuser_struct;
		global $SinaClub_Session_Array;
		global $db_public_conf,$db_myuser_conf_r,$db_myuser_conf_w;

		$session_val	= array();
		$this->is_parse = false;
		$this->_name($name);
		$this->_start();
		if ($unipro_cookie = $this->get_online_state())			//	add by zhanglin @2006-11-03
		{
			if ((strlen(trim($unipro_cookie['id'])) >= 5 && strlen(trim($unipro_cookie['id'])) <= 10) && ($this->get_val("SINACLUB_UID") != $unipro_cookie['id']))
			{
				include_once ($_SERVER['DOCUMENT_ROOT'] . '/include/function_lib.php');
				include_once ($_SERVER['DOCUMENT_ROOT'] . '/include/my_dbfairy.2.0.class.php');
				include_once ($_SERVER['DOCUMENT_ROOT'] . '/config/my_db_config.php');
				include_once ($_SERVER['DOCUMENT_ROOT'] . '/config/blog_define.php');
				if (check_kill_ip(DEF_KILL_IP_PATH))
				{
					//-- 从会员中心数据库中得到数据 ---
					$db_public_conf['host'] = calc_hash_db($unipro_cookie['id'], $db_myuser_conf_r);
					$tb_name = MYUSER_TBL_PRE_NAME . calc_hash_tbl($unipro_cookie['id'],128);

					$mydb = new my_DbFairy($tbl_myuser_struct);
					//	$mydb->debug();
					$mydb->open($db_public_conf);

					$mydb->table($tb_name, "select where uid='" . $unipro_cookie['id'] . "'");
					$mydb->show_one();
					$rows = $mydb->my_rows();
					if (!$mydb->isok)
					{
						$mydb->close();
						return false;
					}
					elseif ($rows != 1)
					{
						$mydb->close();
						return false;
					}
					else
					{
						$mydb->goto(0);

						if ($mydb->getval('status') == MEMBER_STATUS_DENY)
						{
							$mydb->close();
							return false;
						}

						// --　种植session -------------
						$this->setval('SINACLUB_BLOG', '1');
						$this->setval('SINACLUB_UID', $unipro_cookie['id'] . '');
						$this->setval('SINACLUB_NICKNAME', string::esc_user_input($mydb->getval('uname')) . '');
						$this->setval('SINACLUB_VCODE', '');
						$this->setval('SINACLUB_UHOST', $mydb->getval('uhost') . '');
						$this->setval('SINACLUB_MOBILE', $mydb->getval('mobile') . '');
						$this->setval('SINACLUB_ICON', $mydb->getval('m_icon') . '');
						$this->setval('SINACLUB_VIPUSER', $mydb->getval('m_vipuser') + 0);
						$this->setval('SINACLUB_PUBUSER', $mydb->getval('m_pubuser') + 0);
						$this->setval('SINACLUB_STATUS', $mydb->getval('status') + 0);
						$this->dump();
						setcookie("BLOG_NICKNAME", string::esc_user_input($mydb->getval('uname')) . '', null, "/", '.sina.com.cn');
						setCookieCn(__COOKIES_NICKNAME_JS, cookies_str_replace(string::esc_user_input($mydb->getval('uname'))), null, "/", '.blog.sina.com.cn');
						Log::write('login', "成功登录:" . $unipro_cookie['id']);
						if (SHOW_LOGIN_MESSAGE == 1)			// 显示公告信息
						{
							if ($mydb->getval('uhost') == '')
							{
								$url = '/u/' . $unipro_cookie['id'];
							}
							else
							{
								if($mydb->getval('m_vipuser') == 2)
								{
									$url = '/' . $mydb->getval('uhost');
								}
								elseif( $mydb->getval('m_vipuser') == 3)
								{
									$url = '/m/' . $mydb->getval('uhost');
								}
							}
							show_login_message($url);
							exit;
						}
					}
					$mydb->close();
					$mydb = false;
				}
				return true;
			}
		}
		elseif ($this->get_val("SINACLUB_UID") != "")
		{
			$this->setval("SINACLUB_UID", "");
			$this->destroy();
			setcookie("BLOG_NICKNAME", null , time() - 3600, "/", '.sina.com.cn');
			setcookie(__COOKIES_NICKNAME_JS, null , time() - 3600, "/", '.blog.sina.com.cn');
		}
	}


	/**
	 * 打开session
	 * 通过服务器全局变量$GLOBALS['__SINA_Session_Flag']作为session是否打开的标志
	 * @param string $name 名称
	 */
	function _start()
	{
		if (!$GLOBALS['__SINA_Session_Flag'])
		{
			session_start();
			$GLOBALS['__SINA_Session_Flag'] = true;
		}
	}

	/**
	 * 设置session的名称前缀
	 * @param string $name 名称
	 * @return string
	 */
	function _name($name = null)
	{
		return isset($name) ? session_name($name) : session_name();
	}

	/**
	 * 将序列化的session数组还原
	 */
	function _parse()
	{
		$this->_start();
		if ($GLOBALS['__SINA_Session_Flag'])
		{
			$this->session_val  = unserialize($_SESSION['__SINA_Session_Val']);
			$this->flag_parse 	= true;
		}
	}

	/**
	 * 给session赋值,将session数组以序列化方式存?	 *
	 */
	function dump()
	{
		$this->_start();
		if( ! empty($this->session_val))
		{
			$_SESSION['__SINA_Session_Val'] = serialize($this->session_val);
		}
	}

	/**
	 * 从session中取某个字段值
	 * @param string $key session KEY值
	 * @return mixed
	 */
	function get_val($key)
	{
		if ( ! $this->flag_parse)
		{
			$this->_parse();
		}
		return isset($this->session_val[$key]) ? $this->session_val[$key] : false;
	}

	/**
	 * 从session中取某个字段值
	 * @param string $key session KEY值
	 * @return mixed
	 */
	function get_all()
	{
		if ( ! $this->flag_parse)
		{
			$this->_parse();
		}
		return $this->session_val;
	}

	/**
	 * 设置session的key和val值
	 * @param string $key session KEY值
	 * 		  mixed	 $val session VAL值
	 */
	function setval($key, $val)
	{
		if ( ! $this->flag_parse)
		{
			$this->_parse();
		}
		$this->session_val[$key] = $val;
	}

	/**
	 * 以数组方式设置session的key和val值
	 * @param array $ary 包含sessionKEY和VAL的数组
	 */
	function set_array($ary)
	{
		$cnt 	= count($ary);
		$keys 	= array_keys($ary);
		for($i = 0; $i < $cnt ; $i++)
		{
			$this->session_val[$keys[$i]] = $ary[$keys[$i]];
		}
	}

	/**
	 * 从session中释放掉某个变量
	 * @param string $key session KEY值
	 * @return boolean
	 */
	function unregister($key)
	{
		if(isset($this->session_val[$key]))
		{
			unset($this->session_val[$key]);
		}
	}

	/**
	 * 释放SESSION
	 */
	function destroy()
	{
		session_unset();
		session_destroy();
		unset($GLOBALS['__SINA_Session_Flag']);
	}
	// add by zhanglin @2006-11-03
	/**
	* 解析$_SERVER['HTTP_COOKIE']串为数组元素
	*/
	function _parse_cookie_str()
	{
		$str = $_SERVER['HTTP_COOKIE'];
		$tmp = array();
		$myarray = array();
		$tmp = split(";", $str);
		foreach($tmp as $k => $v)
		{
			$tk = "";
			$tv = "";
			$tt = 0;
			$tt = strpos($v,"=");
			$tk = substr($v, 0, $tt);
			$tv = substr($v, $tt + 1);
			$myarray[trim($tk)] = trim($tv);
		}
		return $myarray;
	}

	/**
	 * 解密统一cookie字串，并返回数组值
	 *
	 * @author 李业
	 * @access private
	 * @return array
	 */
	function _parse_sina_cookie()
	{
		$sina_cookie = array (
				'uid'     	=> '',     //登录名
				'pc'      	=> '',     //登录密码
				'id'      	=> '',     //新浪唯一号
				'ag'      	=> '',     //用户类别 1-手机 2-免费 3-vip 4-668
				'gender'  	=> '',     //性别
				'birthday'	=> '',     //生日
				'nickname'	=> '',     //昵称
				'appmask' 	=> ''      //应用掩码
		);

		$my_cookies = $this->_parse_cookie_str();
		if (!isset($my_cookies['SINAPRO']) || $my_cookies['SINAPRO'] == '')
		{
			return false;
		}
		// 做url解码 by 刘德洪 @ 2005年11月24日
		$ck_sinapro = urldecode($my_cookies['SINAPRO']);

		// 解密cookie串

		$sinapro = sina_cookie_decrypt($ck_sinapro);
		$vlist  = array();
		$vlist  = split("&", $sinapro);

		foreach ($vlist as $k => $v)
		{
			$rlist  = split('=', $v);
			if (isset($rlist[0]) && isset($rlist[1]) && $rlist[0] != '' && $rlist[1] != '')
			{
				$sina_cookie[$rlist[0]] = urldecode($rlist[1]);
			}
		}

		$matches = array();

		$ck_nickname = $my_cookies['nick'];
		if ($ck_nickname == '')
		{
			return false;
		}
		if (preg_match("/^(.*)\(\d{10}\)$/", $ck_nickname, $matches))
		{
			$sina_cookie['nickname'] = $matches[1];
		}


		return $sina_cookie;
	}
	/**
	 * 登录状态检测函数
	 * 检测统一cookie是否存在（即用户是否登录）
	 *
	 * @access public
	 * @return boolean
	 */
	function get_online_state()
	{
		if(extension_loaded('sina'))
		{
			if ($sina_cookie = $this->_parse_sina_cookie())
			{
				if (($sina_cookie['ag'] == 1) || ($sina_cookie['ag'] == 2) || ($sina_cookie['ag'] == 3) || ($sina_cookie['ag'] == 4)  || ($sina_cookie['ag'] == 5)  || ($sina_cookie['ag'] == 6))
				{
					return $sina_cookie;
				}
			}
		}
		return false;
	}
}