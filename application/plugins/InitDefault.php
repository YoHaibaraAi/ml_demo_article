<?php
/**
 * 默认路由及跳转行为组件
* @author junzhong
*
*/
class InitDefaultPlugin extends Yaf_Plugin_Abstract {

	public function routerStartup(Yaf_Request_Abstract $request,Yaf_Response_Abstract $response ){
//       
        // 启动默认路由设置
        preg_match('/(?<=\/{1})[\w\W]*(?=\?)/', $_SERVER['REQUEST_URI'],$matches);
	    if(!$matches[0])
	    {
	        $config_app = Yaf_Registry::get('config_app')->toArray();
	        $request->setModuleName($config_app['defaultModule']);
	        $request->setControllerName($config_app['defaultController']);
	        $request->setActionName($config_app['defaultAction']);
	    }
	}

	/**
	 * login check
	 * @param Yaf_Request_Abstract $request
	 * @param Yaf_Response_Abstract $response
	 */
	public function routerShutdown( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
	    //uri白名单
	    $module_name	=	strtolower($request->getModuleName());
	    $controller_name=	strtolower($request->getControllerName());
	    $action_name	=	strtolower($request->getActionName());
	       
	     
// 	    if($module_name=="interface"||$module_name=="interfaceadmin"){
	         
// 	        if($controller_name!="test"){
// 	            //接口安全性认证
//  	            $key="23qe2eydw"; //key
//             $parameters=$_POST;
	             
// 	            $token=$parameters['token'];
// 	            unset($parameters['token']);
//  	            ksort($parameters);//排序参数
// 	            if($token!=md5($key.implode(':',$parameters))){
//  	                exit(api::json(1,"认证失败"));
// 	            }
	            
//  	        }
// 	    }
	    
	    
	}

	/**
	 * dispathLoop分发结束后
	 * @param Yaf_Request_Abstract $request
	 * @param Yaf_Response_Abstract $response
	 */
	public function dispatchLoopShutdown( Yaf_Request_Abstract $request , Yaf_Response_Abstract $response ){
		//销毁未释放的服务
	}


}