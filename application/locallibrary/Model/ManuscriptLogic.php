<?php
class Model_ManuscriptLogic
{
    public static function getManuscriptTable()
    {
        return new Db_ManuscriptTable();
    }
    public static function getNewManu($info)
    {
        $result=self::getManuscriptTable()->get_new_manu($info);
        if (count($result))
        {
            $i=0;
            foreach ($result as $value)
            {
                $data[$i]['register_u_id'] =$value['u_id'];
                $data[$i]['inviter_u_id'] =$info['inviter_u_id'];
                $data[$i]['business_id'] =$value['order_id'];
                $data[$i]['business_type'] =intval(4);
                $data[$i]['total_money'] =$value['ad_cost'];
                $data[$i]['ratio'] =0.02;
                $data[$i]['in_come'] =doubleval($value['ad_cost'])*doubleval(0.02);
                if ($value['status']!=6)
                {
                    $data[$i]['status'] =1;
                }else
                {
                    $data[$i]['status'] =2;
                }
                //$data[$i]['create_time'] =date::get_date_time();
                $i++;
            }
            return $data;
        }else 
        {
            return array();
        }
    }
    /**
     * 根据$manu_id查看未完成订单状态
     * @param unknown $ad_id
     */
    public function checkOrderStatus($manu_id)
    {
        $result=self::getManuscriptTable()->get_manu_info_by_manuid($manu_id);
        if (count($result))
        {
            if($result['status']==5)
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