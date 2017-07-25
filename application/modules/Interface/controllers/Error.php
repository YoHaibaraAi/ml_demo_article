<?php
/**
 * 统一的异常处理Controller
 * 如果该modules下没有ErrorController，则会调用默认的application/controllers/error.php
 */
class ErrorController extends Yaf_Controller_Abstract {
    /**
     * 这是一个示例。生成前台页面
     * @return [type] [description]
     */
    public function errorAction() 
    {
      global $config;
      $e = $this->getRequest()->getException();

      $this->toJson($e);
    }

    /**
     * 将错误信息以JSON结构返回
     * @param  [type] $e [description]
     * @return [type]    [description]
     */
    private function toJson($e)
    {      
        $msg = $e->getMessage();

      echo api::json(1,$msg,null,$e->getCode());
    }
   

}
?>