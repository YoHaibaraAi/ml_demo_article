<?php
//错误输出控制
error_reporting(E_ERROR);

session_start();
define("APP_PATH",  realpath(dirname(__FILE__) ));

$app  = new Yaf_Application(APP_PATH . "/conf/application.ini");

//设定默认路径
$config = Yaf_Application::app()->getConfig();
define('APP_APPLICATION', $config->application->directory);
define('APP_LIBRARY', Yaf_Loader::getInstance()->getLibraryPath(true));

//加载通用类
include_once(APP_LIBRARY.'/Core/inc.php');

$app->bootstrap()->run();