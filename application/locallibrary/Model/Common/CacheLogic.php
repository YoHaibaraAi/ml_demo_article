<?php

class Model_Common_CacheLogic{
	
	/**
	 * 取得Cache数据源
	 */
	public static function getCacheDB(){
		return new Db_CacheTable();
	}
	
	/**
	 * 记录缓存
	 */
	public static function set($key,$value)
	{
		$cache_db = self::getCacheDB();
		
		$cache_info = self::get($key);
		// print_r($cache_info);
		// 根据缓存内容是否已经存在 进行新增或更新
		if (empty($cache_info)) {
			$ret = $cache_db->add($key,json_encode($value));
		}else{
			$ret = $cache_db->update($key,json_encode($value));
		}

		return $ret;
	}

	/**
	* 获取缓存
	***/
	public static function get($key)
	{
		$cache_info = self::getCacheDB()->getData($key);
		return json_decode($cache_info['cache_content'],1);
	}

	
	
}