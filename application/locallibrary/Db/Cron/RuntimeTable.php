<?php
/**
 * crontab的运行时状态的记录类
 * (为后期构造监控系统准备)
 * @author junzhong
 */

class Db_Cron_RuntimeTable{
	
	
	private $tbl_name="ef_company_cron_runtime";
	
	/**
	 * 构造函数
	 */
	public function __construct(){
	
		$this->db=Server::get(Server::mysql);
	}
	
	/**
	 * 创建一条cron运行记录
	 * @param $class	//类型
	 * @param $subclass //子类型
	 * @param $num      //（多脚本时的进程区分）
	 * @param $tag		//标签
	 * @param $content	//内容
	 */
	public function create($class,$subclass=0,$num=0,$content="",$tag=""){
		
		$arr_insert_data=array();
		$arr_insert_data['date']=date('Y-m-d');
		$arr_insert_data['class']=$class;
		$arr_insert_data['subclass']=$subclass;
		$arr_insert_data['num']=$num;
		$arr_insert_data['tag']=$tag;
		$arr_insert_data['content']=$content;
		$arr_insert_data['create_time']=date::get_date_time();
		
		return $this->db->insert($this->tbl_name, $arr_insert_data);
	
	}
	
	/**
	 * 更新运行中的content
	 * @param $id
	 * @param $content
	 */
	public function setContent($id,$content){
	
	    return $this->db->where(" id=".intval($id))->update($this->tbl_name, array('content'=>$content,'update_time'=>date::get_date_time()));
	}
	
	/**
	 * 取得运行记录
	 * @param $date 		//时间
	 * @param $class		//类型
	 * @param $subClass 	//子类型
	 * @param $num		    //（多脚本时的进程区分）	
	 * @return mixed
	 */
	public function getRuntime($date,$class,$subClass,$num=0){
		
		$sql="
			select *
			from {$this->tbl_name}
			where 1
			and date='{$date}'
			and class={$class}
			and subclass={$subClass}
			and num={$num}
			order by id desc
		";
		
		return $this->db->rawQueryOne($sql);
	}
	
	/**
	 * 开始监控
	 */
	public function start($id,$content=""){
		
		$upd_arr=array();
		$upd_arr['start_time']=date::get_date_time();
		if($content)$upd_arr['content']=$content;
		
		return $this->db->where(" id=".intval($id))->update($this->tbl_name, $upd_arr);
	}
	
	/**
	 * 结束监控
	 */
	public function end($id,$content=""){
	    
		$upd_arr=array();
		$upd_arr['end_time']=date::get_date_time();
		if($content)$upd_arr['content']=$content;
		
		return $this->db->where(" id=".intval($id))->update($this->tbl_name, $upd_arr);
	}
	
}