<?php

/**
 *文章处理控制器
 */


class ArticleController extends Yaf_Controller_Abstract
{
    public function ad_saveAction()
    {

        $result=Model_ArticleLogic::saveArticle();
        //$result=Model_ArticleLogic::saveTxt();
        if ($result)
        {
            echo api::json(0,'成功',$result,1);
        }else{
            echo api::json(0,'失败',$result,1);
        }
    }
    public function indexAction()
    {

        $view=$this->getview();
        $view->display();
    }
    public function ad_searchAction()
    {
        $result=Model_ArticleLogic::ad_search();
        if ($result)
        {
            echo api::json(0,'广告搜索完成',array(),1);
        }else{
            echo api::json(0,'没有广告',array(),1);
        }

        //echo "hello";
    }
    public function ad_judgeAction()
    {
        $result=Model_ArticleLogic::ad_judge();
        if ($result)
        {
            echo api::json(0,'成功',$result,1);
        }else{
            echo api::json(0,'失败',array(),1);
        }
    }
    public function ad_confirmAction()
    {
        cgi::post($id,'list_id','');
        cgi::post($status,'status','');
        $result=Model_ArticleLogic::ad_confirm($id,$status);
        if ($result)
        {
            echo api::json(0,'成功',$result,1);
        }else{
            echo api::json(0,'失败',array(),1);
        }
    }
    public function ad_dataAction()
    {
        $result=Model_ArticleLogic::makePreData();
        //var_dump($result);
        if ($result)
        {
            echo api::json(0,'成功',$result,1);
        }else{
            echo api::json(0,'没有新的数据',array(),1);
        }
    }
    public function ad_csvAction()
    {
       $result=Model_ArticleLogic::getCsv();
        if ($result)
        {
            echo api::json(0,'成功',$result,1);
        }else{
            echo api::json(0,'没有新的数据',array(),1);
        }
    }
}