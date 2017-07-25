<?php

class cron_base{
	
	protected $max_runtime=0;
	protected $start_time=0;
	
	protected $runtime=null;
	
	public function __construct(){
		
		//初始化开始时间
		$this->start_time=time();
		
		//初始化数据库 进程运行环境
		$this->runtime=new Db_Cron_RuntimeTable();
	}
	
	public function setMaxRuntime($maxruntime=0){
		$this->max_runtime=$maxruntime;
	}
	
	public function check_runtime(){
		return (time()-$this->start_time)>$this->max_runtime;
	}
	
	
	
	
	
}