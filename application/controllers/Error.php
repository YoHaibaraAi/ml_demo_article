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
   
      /**
      switch ($config["error_api_json"])
      {
          case "json" :
             $this->toJson($e);
             break;
          case "text" :
             $this->toJsonText($e);
             break;
          default :
             $this->toHtml($e); 
             break;
      }
      **/
    }

    /**
     * 将错误信息输出到报错页面
     * @param  [type] $e [description]
     * @return [type]    [description]
     */
    private function toHtml($e)
    {
      $view = $this->getView();
      $view->assign('code', $e->getCode());
      $view->assign('message', $e->getMessage());
      $view->assign('file', $e->getFile());
      $view->assign('line', $e->getLine());
      $view->assign('debug', $e->xdebug_message);
      $view->display();
    }

    /**
     * 将错误信息以JSON结构返回
     * @param  [type] $e [description]
     * @return [type]    [description]
     */
    private function toJson($e)
    {
      $data['error'] = $e->getCode();
      $data['errmsg'] = $e->getMessage();
      $data['error_file'] = $e->getFile();
      $data['error_line'] = $e->getLine();
      ApiOutput::output($data);
    }
   
    /**
     * 将错误信息以JSON结构返回
     * @param  [type] $e [description]
     * @return [type]    [description]
     */
    private function toJsonText($e)
    {
      $data['error'] = $e->getCode();
      $data['errmsg'] = $e->getMessage();
      $data['error_file'] = $e->getFile();
      $data['error_line'] = $e->getLine();
      ApiOutput::output($data,"text");
    }
}
?>