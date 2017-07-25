<?php
class InitSmartyPlugin extends Yaf_Plugin_Abstract {

    /**
     * 在路由成功后，初始化Smarty，并将Smarty设置为默认的模板引擎。
     * 为何要做插件而不是在bootstrap中进行：
     * 如果当前Module不是默认Module，则模板文件应该为/application/modules/{模板名}/views/{controller}/{action}.html，但在路由完成之前是无法得知当前Module名的
     * @param  Yaf_Request_Abstract  $request  [description]
     * @param  Yaf_Response_Abstract $response [description]
     * @return [type]                          [description]
     */
    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        //检查当前模块是否是默认模块
        $isDefaultModule = true;
        $config = Yaf_Application::app()->getConfig();
        $defaultModule = $config->application->dispatcher->defaultModule ? $config->application->dispatcher->defaultModule : 'Index';
        if(strcasecmp($defaultModule, $request->module)!=0)
        {
            $isDefaultModule = false;
        }
        $module_str = $isDefaultModule ? '' : '/modules/'.$request->module;
        
        $template_dir=APP_APPLICATION .$module_str. "/views";
        
        //cache区
        
        $smarty= new Yaf_Config_Ini(APP_PATH . '/conf/application.ini', 'smarty');
        $compile_root=$smarty->get('compiles_root');
        $cache_root=$smarty->get('cache_root');
        
        if(empty($compile_root))$compile_root=APP_PATH;
        if(empty($cache_root))$cache_root=APP_PATH;
        
        $compile_dir=$compile_root."/".$request->module;
        $cache_dir=$cache_root."/".$request->module;
        
     	if (!file_exists($compile_dir)) mkdir($compile_dir,0755,true);
     	if (!file_exists($cache_dir)) mkdir($cache_dir,0755,true);
       
        $smarty_conf['left_delimiter'] = "{%";
        $smarty_conf['right_delimiter'] = "%}";
        $smarty_conf['template_dir'] =$template_dir;
        $smarty_conf['compile_dir'] = $compile_dir;
        $smarty_conf['cache_dir'] = $cache_dir;
        $smarty_conf['caching'] = false;
        $smarty = new Smarty_Adapter(null, $smarty_conf);
        
        Yaf_Dispatcher::getInstance()->setView($smarty)->autoRender(false);
    }
    
}

