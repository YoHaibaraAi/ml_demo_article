<?php
/**
* 钱包操作
* @date: 2016年8月29日
*/
class Model_Common_WalletHandleLogic
{
    const MISSION_TYPE_ADVERTISE = 1;   //任务类型 广告
    const MISSION_TYPE_LUCKYDRAW = 2;   //任务类型 红包
    const MISSION_TYPE_DISPATCH  = 3;   //任务类型 精采

    const ROLE_TYPE_WEMEDIA     = 1;    //角色 自媒体
    const ROLE_TYPE_ADVERTISER  = 2;    //角色 广告主
    const ROLE_TYPE_RECEIPT    = 3;    //角色 发票账户
    const ROLE_TYPE_RECRUIT    = 4;    //角色 分销账户

    private static $act_url = '';
    private static $key = 'rewrxsw2eydf';
    private static $param = array();
    public static function getDrawLogTable()
    {
        return new Db_DrawLogTable();
    }
    private static $test = false;
    private static $testResult = array(
        'getBalance' => '{"error":0,"msg":"success","data":{"money":"10000.0000"},"code":""}',
        'toRecharge' => '{"error":0 ,"msg":"success","data": { "bill_no":"es2342334adnws3a","recharge_url":"http://www.baidu.com"},"code":""}',
        'toDrawmoney' => '{"error":0 ,"msg":"success","data": { "bill_no":"es2342334adnws3a"},"code":""}',
        'getAccountList'=>'{"error":0,"msg":"success","data":{"total":10,"list":[{"id":17,"bill_no":"yz201512071113095664f9457335e73","u_id":"1","role":1,"bussiness_type":"U01C003001","money":"1.0000","new_total_money":"2.0000","remark":"test \u5e7f\u544a\u6253\u6b3e","create_time":"2015-12-07 11:13:09","pay_time":"2015-12-07 11:13:09"},{"id":15,"bill_no":"yz2015120621422356643b3f1fa9631","u_id":"1","role":1,"bussiness_type":"U01C003001","money":"1.0000","new_total_money":"3.0000","remark":"test \u5e7f\u544a\u6253\u6b3e","create_time":"2015-12-06 21:42:23","pay_time":"2015-12-06 21:42:23"},{"id":14,"bill_no":"yz20151206213633566439e174db885","u_id":"1","role":1,"bussiness_type":"U01C003001","money":"1.0000","new_total_money":"4.0000","remark":"test \u5e7f\u544a\u6253\u6b3e","create_time":"2015-12-06 21:36:33","pay_time":"2015-12-06 21:36:33"}]},"code":""}',
        'checkDrawMoney'=>'{"error":0 ,"msg":"success","data":"","code":""}'
    );
    private static function execute($stream=false)
    {
        $host = strstr($_SERVER['WALLET_HOST'],'http://') ? $_SERVER['WALLET_HOST'] : ('http://'.$_SERVER['WALLET_HOST']);
        $host = "http://wallet.yeezan.com";
        $url = $host.self::$act_url;
        //         var_dump($url);
        //接口安全性认证
        $key=self::$key; //key

        $param=self::$param;
        ksort($param);//排序参数
        $param['token']=md5($key.implode(':',$param));
        include_once APP_LIBRARY."/Curl.php";

        $curl=new Curl_Class();
        $res=  $curl->post($url,$param);

        if(json_decode($res))
        {
            return json_decode($res);
        }else{

            throw  new Exception('支付系统异常');
        }
         
    }
    /**
     * 获取用户账户总额
     * @param unknown $u_id
     * @param unknown $role
     */
    public static function getUserAmount($u_id,$role)
    {
        self::$act_url = '/interface/user/account';
        $data = array();
        $data['u_id'] = $u_id;
        $data['role'] = $role;
        
        self::$param = $data;
        
     
            $result = json_decode(self::$testResult['personAmount']);
       
           
            $result = self::execute();
            //$result = json_decode(json_encode($result),1);
        //return $result;
        if($result && $result->error == 0)
        {
            $money = $result->data->money;
            $money = doubleval($money/100);
            return $money;
        }else
        {
            return false;
        }
    }
    /**
     * 获取用户账单明细
     * @param unknown $u_id
     * @param unknown $role
     */
    public static function getUserBill($u_id,$role,$page,$page_num)
    {
        self::$act_url = '/interface/user/bill_detail';
        $data = array();
        $data['u_id'] = $u_id;
        $data['role'] = $role;
        $data['page'] = $page;
        $data['page_num'] = $page_num;
        self::$param = $data;
         $result = self::execute();//return $result->data->list;
        
         $result = json_decode(json_encode($result),1);
        if($result && $result['error'] == 0)
        {
            $lists = $result['data']['list'];
//             return $lists;
            foreach ($lists as $key => $value)
            
            {
                $info[$key]['bill_time']=$value['pay_time'];
                $info[$key]['bill_id']=$value['bill_no'];
                $info[$key]['bill_type']=$value['pay_type'];//1入账2出账
                $info[$key]['bill_count']=doubleval($value['money']/100);
                $info[$key]['bill_cash']=doubleval($value['new_total_money']/100);
                $info[$key]['status']=$value['status'];//待处理0 完成1 驳回2
                $info[$key]['comment']=$value['remark'];
            }
            return $info;
        }else
        {
            return false;
        }
    }
    /**
     * 平台向用户打款
     * @param unknown $u_id
     * @param unknown $role
     * @param unknown $bussioness_code
     * @param unknown $bussiness_id
     * @param unknown $money
     * @param unknown $remark
     */
    public static function payToInviter($u_id,$role,$bussiness_code,$bussiness_id,$money,$remark)
    {
        self::$act_url = '/interface/platform/user_transfer';
        $data = array();
        $data['u_id'] = $u_id;
        $data['role'] = $role;
        $data['bussiness_code'] = $bussiness_code;
        $data['bussiness_id'] = $bussiness_id;
        $data['money'] = $money;
        $data['remark'] = $remark;
        self::$param = $data;
        $result = self::execute();
        if($result && $result->error == 0)
        {
            return true;
        }else
        {
            return false;
        }
    }
    /**
     * 查询是否向用户打过款
     * @param unknown $u_id
     * @param unknown $role
     * @param unknown $bussiness_id
     * @return boolean
     */
    public static function checkPayRecord($u_id,$role,$bussiness_id)
    {        
        self::$act_url = '/interface/user/bill_search_by_bussiness_id';
        $data = array();
        $data['u_id'] = $u_id;
        $data['role'] = $role;
        $data['bussiness_id'] = $bussiness_id;
        self::$param = $data;
        $result = self::execute();
        if($result && $result->error == 0)
        {
            return true;
        }else
        {
            return false;
        }
    }
    /**
     * 分销用户提现申请
     * @param unknown $info
     */
    public static function recruitBankDrawRequest($info)
    {
        self::$act_url = '/interface/user/distribution_draw_request';
        $data = array();
        $data['u_id'] = $info['u_id'];
        $data['money'] = $info['money'];
        $data['role'] = $info['role'];
        $data['bussiness_id'] = $info['business_id'];
        $data['remark'] = "用户提现";
        $data['alipay'] = $info['alipay'];
        $data['real_name'] = $info['real_name'];
        $data['id_number'] = $info['id_number'];
        $data['id_pic'] = $info['id_pic'];
        $data['phone'] = $info['phone'];
        $data['type']=1;
        self::$param = $data;
        $result = self::execute();
        //$result='{"error":0,"msg":"success","data":{"in_bill_no":"yz2016083009560657c4e7b66ccac79","out_bill_no":"yz2016083009560657c4e7b66c52f26"},"code":""}';
        //$result=json_decode($result);
        if($result && $result->error == 0)
        {
            $result = json_decode(json_encode($result),1);
            $data['in_bill_no']=$result['data']['in_bill_no'];
            $data['out_bill_no']=$result['data']['out_bill_no'];
            $res=self::getDrawLogTable()->save($data);
            return $res;
        }else
        {
            return $result;
        }
    }
    /**
     * 分销对公提现申请
     * @param unknown $info
     */
    public static function recruitDrawRequest($info)
    {
        self::$act_url = '/interface/user/distribution_bank_draw_request';
        $data = array();
        $data['u_id'] = $info['u_id'];
        $data['role'] = $info['role'];
        $data['money'] = $info['money'];
        $data['bussiness_id'] = $info['business_id'];
        $data['remark'] = "用户对公提现";
        $data['company_name'] = $info['company_name'];
        $data['bank_name'] = $info['bank_name'];
        $data['bank_card_number'] = $info['bank_card_number'];
        $data['bank_address'] = $info['bank_address'];
        $data['phone'] = $info['phone'];
        $data['express_company'] = $info['express_company'];
        $data['express_number'] = $info['express_number'];
        $data['type']=2;
        self::$param = $data;
        $result = self::execute();//var_dump($result);
        if($result && $result->error == 0)
        {
            
            $result = json_decode(json_encode($result),1);
            $data['in_bill_no']=$result['data']['in_bill_no'];
            $data['out_bill_no']=$result['data']['out_bill_no'];
            $res=self::getDrawLogTable()->save($data);
            return true;
        }else
        {
            return false;
        }
      }
      /**
       * 分销账户提现权限验证
       * @param unknown $u_id
       */
  public static function recruitDrawCheck($u_id)
  {
      self::$act_url = '/interface/user/distribution_draw_check';
      $data = array();
      $data['u_id'] = $u_id;
      self::$param = $data;
      $result = self::execute();
      if($result && $result->error == 0)
      {
          return true;
      }else
      {
          return false;
      }
      
  }
        
        
        
    }
    
    
    
    
    
    
    
    
    
    
    
