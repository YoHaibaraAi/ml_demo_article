<?php
error_reporting(E_ERROR);

define("APP_PATH",  realpath(dirname(__FILE__) . '/../')); /* 指向public的上一级 */

$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");

//设定默认路径
$config = Yaf_Application::app()->getConfig();
define('APP_APPLICATION', $config->application->directory);
define('APP_LIBRARY', Yaf_Loader::getInstance()->getLibraryPath(true));

//加载通用类
include_once(APP_LIBRARY.'/Core/inc.php');
include_once('cron_base.class.php');
if(is_file(APP_PATH.'/system/server.inc.php')){ 
	include_once('server.inc.php');
}

