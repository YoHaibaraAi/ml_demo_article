<?php
/**
 * 临时缓存表
 */
class Db_CacheTable{
	
	private $db=null;
	private $tbl_name="ef_cache";
	
	public function __construct(){
		
		$this->db=Server::get(Server::mysql);
	}

	// 新增
	public function add($key,$value){
		if (empty($key)||empty($value)) return false;
		$insert_data = array();
		$insert_data['cache_key']		= $key;
		$insert_data['cache_content']	= $value;

		return $this->db->insert($this->tbl_name,$insert_data);
	}

	// 修改 
	public function update($key,$value){
		if (empty($key)||empty($value)) return false;
		$update_data = array();
		$update_data['cache_content']	= $value;

		$update_data['update_time']	=  Date::get_date_time();
		$str_where= "cache_key='".mysql_escape_string($key)."'";
		$this->db->where($str_where)->update($this->tbl_name, $update_data);
		return $this->db->count;
	}

	// 查询
	public function getData($key){
		if (empty($key)) return false;
		$sql="
			select cache_content
			from {$this->tbl_name}
			where 1
			and cache_key='".mysql_escape_string($key)."'";
		
		return $result = $this->db->rawQueryOne($sql);
	}
}