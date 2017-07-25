<?php

/**
 * Server工厂
 * @author junzhong
 */
class Server{
	const mysql="mysql";
	
	private static $server_pool=array();
	
	/**
	 * 取得服务实例
	*/
	public static function get($server_name="",$options=array()){
		
		switch ($server_name){
			case self::mysql:
				//取得数据库配置
				$config_array=Yaf_Registry::get("config_mysql");
				$source=$config_array['source'];
				$options=$config_array[$source];
				//生成并返回实例
				return self::mysql_maker($options);
				break;
			default:
				return null;
		}
	}
	
	public static function destruct(){
		if(self::$server_pool[self::cache]){
			//关闭memcache
			self::$server_pool[self::cache]->close();
		}
	}
	
	public static function clean_server($instance)
	{
	    unset(self::$server_pool[$instance]); 
	}
	/**
	 * mysql数据库实例
	 */
	private static function mysql_maker($options=array()){
		//单例
		if(self::$server_pool[self::mysql]){
			return self::$server_pool[self::mysql];
		}
		else{
			$db=new Server_mysqli($options);
			self::$server_pool[self::mysql]=$db;
			return $db;
		}
	}
	
}