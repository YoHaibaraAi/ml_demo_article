<?php

/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */
class Bootstrap extends Yaf_Bootstrap_Abstract{   
	/**
	 * 定义本地类库路径，并注册本地类前缀以实现自动加载
	 * 由于文档不完整，目前只找到这唯一一种定义本地类库的方法
	 * @return [type] [description]
	 */
	public function _initRegisterLocalNamespace(Yaf_Dispatcher $dispatcher)
	{
		$loader = Yaf_Loader::getInstance();
		$loader->setLibraryPath(APP_APPLICATION.'/locallibrary');
		$loader->registerLocalNamespace(array("Model","Db"));
		 
	}
	
	/**
	 * 注册插件
	 * @param Yaf_Dispatcher $dispatcher
	 */
    public function _initPlugin(Yaf_Dispatcher $dispatcher)
    {   
    	
        //注册Smarty插件
        $InitSmartyPlugin = new InitSmartyPlugin();
        $dispatcher->registerPlugin($InitSmartyPlugin);
        
        //注册默认行为插件
        $InitDefaultPlugin = new InitDefaultPlugin();
        $dispatcher->registerPlugin($InitDefaultPlugin);
        
    }
    
    /**
     * 注册配置文件 
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initConfig(Yaf_Dispatcher $dispatcher)
    {   
    	//base配置
    	$config_base = new Yaf_Config_Ini(APP_PATH . '/conf/application.ini', 'base');
    	Yaf_Registry::set('config_base', $config_base);
    	
    	$config_app = new Yaf_Config_Ini(APP_PATH . '/conf/application.ini','product');
    	Yaf_Registry::set('config_app', $config_app->application);
    	
    	$config_mysql = new Yaf_Config_Ini(APP_PATH . '/conf/db.ini', 'mysql');
    	Yaf_Registry::set('config_mysql', $config_mysql->toArray());
    	 
   
    }
    
    
    public static function _initServerConfig(){

        if($_SERVER['MAIN_HOST']){
            //主站域名
            Yaf_Registry::set('config_main_host', $_SERVER['MAIN_HOST']);
        }
        
        if($_SERVER['DATA_HOST']){
            //数据中心
            Yaf_Registry::set('config_data_host', $_SERVER['DATA_HOST']);
        }
        //mysql 配置
        if($_SERVER['DB_MYSQL_HOST']){
            $config_mysql['source']='mysql';
            $config_mysql['mysql']['host']=$_SERVER['DB_MYSQL_HOST'];
            $config_mysql['mysql']['port']=$_SERVER['DB_MYSQL_PORT'];
            $config_mysql['mysql']['username']=$_SERVER['DB_MYSQL_USER'];
            $config_mysql['mysql']['password']=$_SERVER['DB_MYSQL_PASSWD'];
            $config_mysql['mysql']['db']=$_SERVER['DB_MYSQL_DBNAME'];
            $config_mysql['mysql']['charset']=$_SERVER['DB_MYSQL_CHARSET'];
            
            $options = Yaf_Registry::set("config_mysql",$config_mysql);
        }
        
    }  
    
    //设置默认路由
    public function _initDefaultModule(Yaf_Dispatcher $dispatcher)
    {
//         $dispatcher->setDefaultModule("sample")->setDefaultController("index")->setDefaultAction("index");
    }
}

