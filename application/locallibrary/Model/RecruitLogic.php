<?php
include_once (APP_LIBRARY . "/Core/base/pageView.class.php");
class Model_RecruitLogic
{
    const default_count = 20;
    public static function getInvitationCodeTable()
    {
        return new Db_InvitationCodeTable();
    }
    public static function getClickLinkTable()
    {
        return new Db_ClickLinkTable();
    }
    public static function getInvitationFriendTable()
    {
        return new Db_InvitationFriendTable();
    }
    public static function getInComeTable()
    {
        return new Db_InComeTable();
    }
    public static function getUserTable()
    {
        return new Db_UserTable();
    }
    public static function getMpTable()
    {
        return new Db_MpTable();
    }
    /**
     * 生成用户专属邀请码
     * @param unknown $uid
     */
    public static function createCode($inviter_u_id)
    {
        $inviter_code=md5($inviter_u_id);    
        $res=self::getInvitationCodeTable()->check($inviter_u_id);   //判断该用户是否已经生成邀请码
        if(!count($res))                                            //如果该用户已经生成过邀请码则直接返回
        {
            $result=self::getInvitationCodeTable()->save($inviter_u_id,$inviter_code);
        }
        
        return $inviter_code;
    }
    public static  function checkCode($inviter_code)
    {
        $result=self::getInvitationCodeTable()->get_info_by_code($inviter_code);
        return count($result);
    }
    /**
     * 批量产生邀请码
     * @param unknown $data
     * @return number
     */
    public static function createCodes($data)
    {
        $count=0;
        foreach ($data as $key =>$value)
        {
            $inviter_code=md5($value['u_id']);
            $res=self::getInvitationCodeTable()->check($value['u_id']);
            if (!count($res))
            {
                $result=self::getInvitationCodeTable()->save($value['u_id'],$inviter_code);
                if ($result)
                {
                    $count++;
                }  
            }
   
        }
        return $count;
    }
    /**
     * 被邀请者点击链接
     * @param unknown $inviter_code
     * @param unknown $inviter_u_id
     * @param unknown $link_source_type
     */
    public static function clickLink($inviter_code,$inviter_u_id,$link_source_type,$ip,$user_agent,$refer,$is_mobile)
    {
        $info['ip'] = $ip;
        $info['user_agent'] = $user_agent;
        $info['refer'] = $refer;
        $data=array('inviter_code'=>$inviter_code,'inviter_u_id'=>$inviter_u_id,'link_source_type'=>$link_source_type,'is_mobile'=>$is_mobile,'click_content'=>json_encode($info));
        return $result=self::getClickLinkTable()->save($data);
    }
    /**
     * 根据邀请码获得信息
     * @param unknown $inviter_code
     */
    public static function getInfoByCode($inviter_code)
    {
        $result=self::getInvitationCodeTable()->get_info_by_code($inviter_code);
        return $result;
    }
    /**
     * 被邀请用户注册
     * @param unknown $inviter_u_id
     * @param unknown $register_u_id
     * @param unknown $inviter_code
     * @param unknown $invite_type
     */
    public static function inviterRegister($inviter_u_id,$register_u_id,$inviter_code,$invite_type)
    {
        return $result=self::getInvitationFriendTable()->save($inviter_u_id,$register_u_id,$inviter_code,$invite_type);
    }
    /**
     * 根据被邀请用户register_u_id查询该用户邀请关系
     * @param unknown $register_u_id
     */
    public static function getRegisterInfo($register_u_id)
    {
        return $result=self::getInvitationFriendTable()->get_info_by_register_id($register_u_id);
    }
    /**
     * 通过$register_u_id查询该用户在income表中信息
     * @param unknown $register_u_id
     */
    public static function getRegisterBussinessInfo($register_u_id)
    {
        return $result=self::getInComeTable()->get_info_by_register_id($register_u_id);
    }

    /**
     * 每天通过脚本刷入新的订单
     */
    public static function  entryIncome($lists)
    {
        $count=0;
        foreach ($lists as $value)
        {
            $res=self::checkBusinessId($value['business_id']);
            if (!$res)
            {
                $result=self::getInComeTable()->save($value);
            }else 
            {
                continue;
            }
            if ($result)
            {
                $count++;
            }
        }
        return $count;
    }
    /**
     * 向income表中写入漏掉的信息
     * @param unknown $lists
     */
    public static function entryIncomeMiss($lists)
    {
        $count=0;
        foreach ($lists as $value)
        {
            $res=self::checkBusinessId($value['business_id']);
            if (!$res)
            {
                $result=self::getInComeTable()->save($value);
                if ($result)
                {
                    $count++;
                }  
            }
         
        }
        return $count;
    }
    /**
     * 根据business_id判断订单是否已经写入
     * @param unknown $business_id
     * @return number
     */
    public static function checkBusinessId($business_id)
    {
        $result=self::getInComeTable()->check_business_id($business_id);
        return count($result);
    }
    /**
     * 向income表中写入单条数据
     * @param unknown $list
     */
    public static function entryIncomeOne($list)
    {
        $result=self::getInComeTable()->save($list);
        return $result;
    }
    /**
     * 获得用户历史总红包数
     * @param unknown $inviter_u_id
     */
    public static function countMoney($inviter_u_id)
    {
       return  $result=self::getInComeTable()->count_money_by_inviter_id($inviter_u_id);
    }
    /**
     * 根据邀请者u_id获得各项信息
     * @param unknown $inviter_u_id
     */
    public static function getInfoByInviterId($inviter_u_id)
    {
        $role=4;
        $data['current_money']=Model_Common_WalletHandleLogic::getUserAmount($inviter_u_id,$role);//调用钱包接口
        $data['hope_money']=doubleval(self::getInComeTable()->count_hopemoney_by_inviter_id($inviter_u_id)/100);//获取期望的红包数;
        $data['count_money']=doubleval(self::getInComeTable()->count_money_by_inviter_id($inviter_u_id)/100);//历史总红包
        //获得有效推广好友数
        $data['recruit_friend']=self::getInvitationFriendTable()->get_count_friend($inviter_u_id);
        //获得分享的链接的点击数
        $data['link_click']=self::getClickLinkTable()->get_count_click($inviter_u_id);
        //获得该用户邀请者成交的订单总量
        $data['finish_order']=self::getInComeTable()->get_count_finish_order($inviter_u_id);
        //获得该用户邀请者正在进行的订单总量
        $data['unfinish_order']=self::getInComeTable()->get_count_unfinish_order($inviter_u_id);
        return $data;
    }
    /**
     * 获取被邀请人注册信息
     * @param unknown $inviter_u_id
     */
    public static function getUserInfo($inviter_u_id)
    {
        $data=self::getUserTable()->get_user_info($inviter_u_id);
    }
    /**
     * 获取该用户邀请的好友  后台也用
     * @param unknown $inviter_u_id
     */
    public static function getFriendByInviterId($inviter_u_id)
    {
        $i=0;
        $data=self::getInvitationFriendTable()->get_friend_by_inviter_id($inviter_u_id);
        foreach ($data as $value)
        {
            $u_id=$value['register_u_id']; //得到一个用户的id 然后取得该用户的注册信息
            $info=self::getUserTable()->get_user_info($u_id);
            $list[$i]['u_name']=preg_replace('/(\d{3})\d{4}(\d{1,})/','$1****$2',$info['u_phone']);
            $list[$i]['create_time']=$info['create_time'];
            $list[$i]['status']=1;           //不知道这个状态指的什么
            $info1=self::getInvitationFriendTable()->get_info_by_register_id($u_id);//获取该好友注册来源
            $list[$i]['source']=$info1['invite_type'];
            $i++;
        }
        return $list;
    }
    /**
     * 后台随机生成公众号
     */
    public static function getMp()
    {
//        $count=self::getMpTable()->get_mp_count();
        //生成不重复的随机数
//        $array=array();
 //       $count=0;
 //       while (count<3)
 //       {
  //          $count=mt_rand(1,$count);
 //           $array=array_unique($array);
            //$lists[$count]=self::getMpTable()->get_mp($count);
//             if (count($lists[$count]))
//             {
//                 $count++;
//             }
 //       }
  //      $array=array(1,2,3);
 //      $data=array("myproduct91","shoumeibai","bizhiboss");
        $key="recruit_show_mp";
        $result=Model_Common_CacheLogic::get($key);
        foreach ($result as $key =>$value)
        {
            $lists[$key]['mp_weixin_id']=$value['mp_weixin_id'];
            $lists[$key]['mp_avatar']=$value['mp_avatar'];
            $lists[$key]['mp_name']=$value['mp_name'];
            $lists[$key]['top_line_read_num']=$value['top_line_read_num'];
        }
        //$lists=self::getMpTable()->get_mp($count);
        return $lists;
    }
    /**
     * 将后台选定的公众号写入cache表
     * @param unknown $data
     * @return boolean
     */
    public static function saveMp($data)
    {
        for ($i=0;$i<3;$i++)
        {
            $lists[$i]=self::getMpTable()->get_mp($data[$i]);
        }
        $value=json_encode($lists);
        $key="recruit_show_mp";
        $result=Model_Common_CacheLogic::set($key, $value);
        if ($result)
        {
            return true;
        }
    }
    //===========================后台相关===============

    /**
     * 获取分销列表
     * @param unknown $data
     */
    public static function getList($data)
    {
        if (! $data['page'])
        {
            $data['page'] = 1;
        }
            $data['page_count'] = self::default_count;
            if ($data['inviter_phone'])
            {
                $info=self::getUserTable()->get_user_info_by_uphone($data['inviter_phone']);
                $data['inviter_u_id']=$info['u_id'];
            }
            $count = self::getInvitationFriendTable()->get_no_repeat_count();//取得该用户邀请成功者的数量
    
            $pageView = new PageView($count, $data['page_count'], $data['page']);
            $pageHtml=$pageView->echoPageAsDiv();
            //$lists=self::getInvitationCodeTable()->get_inviter_uid_list($data);
            $lists=self::getInvitationFriendTable()->get_inviter_uid_lists($data);
            //$lists=self::getIncomeTable()->get_recruit_list($data);
            //补足数据
            $i=0;
            foreach($lists as $value)
            {
                //首先判断该用户是否有邀请成功的好友
                $list[$i]['count_friend']=self::getInvitationFriendTable()->get_count_friend($value['inviter_u_id']);
//                 if (!$list[$i]['count_friend'])
//                 {
//                     continue;
//                 }
                $list[$i]['inviter_u_id']=$value['inviter_u_id'];
                $list[$i]['inviter_code']=$value['inviter_code'];
                $list[$i]['create_time']=$value['create_time'];
                $inviterinfo=Model_UserLogic::getUserInfo($value['inviter_u_id']);
                $total_income=doubleval(self::getInComeTable()->count_money_by_inviter_id($value['inviter_u_id'])/100);
                $list[$i]['total_income']=$total_income;
                if($inviterinfo)
                {
                    $list[$i]['u_phone']=$inviterinfo['u_phone'];
                }else
                {
                    $list[$i]['u_phone']='';
                }
               $i++;
            }
            //判断最后一个进入循环的用户是否有成功邀请的用户
//             if ($list[$i]['count_friend']==0)
//             {
//                 unset($list[$i]);
//             }
            return array(
                'pagehtml'=>$pageHtml,
                'count'=>$count,
                'list'=>$list,
    
            );
    }
    /**
     * 获取用户账单详情页面
     * @param unknown $inviter_id
     */
    public static function getOrderDetail($inviter_id)
    {
       return $lists=self::getIncomeTable()->get_order_by_inviter_uid($inviter_id);
    }
  //=================结算============  
    /**
     * 对未结算的订单进行结算
     * @param unknown $data
     */
    public static function payToInviter($lists)
    {
        $count=array();
       $count['pay']=0;  //打款计数
       $count['nopay']=0;  //已经打款计数
      foreach ($lists as $key =>$value)
      {
          switch ($value['business_type'])
          {
              case 1:
                  $bussiness_id="rec"."gift".$value['business_id'];
                  break;
               case 2:
                  $bussiness_id="rec"."ad".$value['business_id']; 
                  break;
               case 3:
                  $bussiness_id="rec"."dis".$value['business_id'];
                  break;
               case 3:
                   $bussiness_id="rec"."manu".$value['business_id'];
                   break;
          }
         $u_id=$value['inviter_u_id'];
         $role=4;
         switch ($value['business_type'])
         {
             case 1:
                 //$bussiness_id="rec"."gift".$value['business_id'];
             case 2:
                 $bussiness_code="003008";
                 break;
             case 3:
                 $bussiness_code="004009";
                 break;
              case 4:
                 $bussiness_code="004009";
                 break;
         }
         $money=floatval($value['in_come']);
         $remark="好友交易成功奖励";
         //判断是否已经打过款
         $res=Model_Common_WalletHandleLogic::checkPayRecord($u_id,$role,$bussiness_id);
         if(!$res)
         {
             //打款
             $result=Model_Common_WalletHandleLogic::payToInviter($u_id,$role,$bussiness_code,$bussiness_id,$money,$remark);
             //如果打款成功则更改用户结算状态
             if ($result)
             {
                 $count['pay']++;
                 $time=date::get_date_time();
                 $flag=self::getInComeTable()->settle_success($value['business_id'],$value['business_type'],$time);
             }
         }else 
         {
             //如果该用户已经打款 就更改用户结算状态
             $count['nopay']++;
             $time=date::get_date_time();
             $flag=self::getInComeTable()->settle_success($value['business_id'],$value['business_type'],$time);
             continue;
         }
      }
      return $count;
    }
    
    
    
    
    
    
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
