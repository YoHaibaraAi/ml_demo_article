<?php
/**
 */
chdir(dirname(__FILE__));
include_once '../inc.php'; 
include_once 'Log.php';

Class cron {
    

    public function run(){
       
        $subject="test_subject";

        

    }
    
}


$app->bootstrap()->execute(array(new cron,'run'),$option);