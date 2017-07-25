<?php

class Model_Common_MsgLogic
{
    
    private static $base_url='http://msg.yeezan.com/';
    private static $sec_url = '';
    private static $act_url ='';
    private static $param = array();
    const key="13qw2eydf"; //key
    
    
    /**
     *
     * @param string $stream
     * @return Ambigous <boolean, multitype:string number , mixed>|Ambigous <boolean, mixed>|boolean
     */
    private static function sendMsg($stream=false){
    
        //接口安全性认证
        $key=self::key; //key
    
        $url=self::$base_url.self::$sec_url.self::$act_url;
    
        $param=self::$param;
        ksort($param);//排序参数
        $param['token']=md5($key.implode(':',$param));
         
        include_once APP_LIBRARY."/Curl.php";
    
        $curl=new Curl_Class();
        $res=  $curl->post($url,$param);
        var_dump($res);
        if($stream)
        {
            return $res;
        }
    
        if($res)
        {
            return json_decode($res)?json_decode($res):false;
        }else{
            return false;
        }
    }
    
    
 /**
   * 推广者申请提现通过审核通知
   */
  public static function distribution_draw_success($u_id,$time,$money,$url){
      
      self::$act_url = 'distribution_draw_success';
      self::$sec_url = 'interface/wallet/';
      
      $param=array();
      $param['u_id']=$u_id;
      $param['time']=$time;
      $param['money']=$money;
      $param['url']=$url;
      
      self::$param = $param;
      return self::sendMsg();  
  }
  /**
   * 推广者申请提现未通过审核通知
   */
  public static function distribution_draw_fail($u_id,$time,$money,$refuse_reason,$url){

      self::$act_url = 'distribution_draw_fail';
      self::$sec_url = 'interface/wallet/';
      
      $param=array();
      $param['u_id']=$u_id;
      $param['time']=$time;
      $param['money']=$money;
      $param['refuse_reason']=$refuse_reason;
      $param['url']=$url;
      
      self::$param = $param;
      return self::sendMsg();
      
  }
  
  /**
   * 推广者红包到账通知
   */
  public static function distribution_reward($u_id,$money,$url){
      self::$act_url = 'distribution_reward';
      self::$sec_url = 'interface/wallet/';
      
      $param=array();
      $param['u_id']=$u_id;
      $param['money']=$money;
      $param['url']=$url;
      
      self::$param = $param;
      return self::sendMsg();
      
  }
    
  
}