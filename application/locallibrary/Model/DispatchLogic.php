<?php
class Model_DispatchLogic
{
    public static function getAdDispatchTable()
    {
        return new Db_AdDispatchOrderTable();
    }
    /**
     * 取得新新精彩订单并组成可以插入到income表中的数组
     * @param unknown $now
     * @param unknown $line_time
     * @param unknown $register_u_id
     * @param unknown $inviter_u_id
     */
    public static function getNewDispatch($info)
    {
        $result=self::getAdDispatchTable()->get_new_dispatch($info);
        if (count($result))
        {
            $i=0;
            foreach ($result as $value)
            {
                $data[$i]['register_u_id'] =$value['u_id'];
                $data[$i]['inviter_u_id'] =$info['inviter_u_id'];
                $data[$i]['business_id'] =$value['dis_id'];
                $data[$i]['business_type'] =intval(3);
                $data[$i]['total_money'] =$value['ad_cost'];
                $data[$i]['ratio'] =0.02;
                $data[$i]['in_come'] =round($value['ad_cost'],2)*doubleval(0.02);
                if ($value['status']!=5)
                {
                    $data[$i]['status'] =1;
                }else 
                {
                    $data[$i]['status'] =2;
                }
                $i++;
                //$data[$i]['create_time'] =date::get_date_time();
            } 
            return $data;
        }else
        {
            return array();
        }
    }
    /**
     * 根据$dis_id查看未完成订单状态
     * @param unknown $dis_id
     */
    public function checkOrderStatus($dis_id)
    {
        $result=self::getAdDispatchTable()->get_order_info_by_disid($dis_id);
        if (count($result))
        {
            if($result['status']==6)
            {
                return true;
            }else 
            {
                return false;
            }
        }else 
        {
            return "系统错误";
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
}