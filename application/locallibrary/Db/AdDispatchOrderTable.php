<?php
/**
 * 广告dispatch 订单
 */

class Db_AdDispatchOrderTable
{
    
    private $db = null;
    private $tbl_name = 'ef_ad_dispatch_order';
    
    public function __construct()
    {
        $this->db = Server::get(Server::mysql);
    }
    /**
     * 获取被邀请用户新增加的精彩订单
     * @param unknown $now
     * @param unknown $line_time
     * @param unknown $register_u_id
     */
    public function get_new_dispatch($info)
    {
        $where="status in (1,2,3,4,5,6)";
        if ($info['register_u_id'])
        {
            $register_u_id=mysql_escape_string($info['register_u_id']);
            $where.="and u_id= '$register_u_id' " ;
        }
      
      if ($info['line_time'])
      {
          $line_time=mysql_escape_string($info['line_time']);
          $now=mysql_escape_string($info['now']);
          $where.="and create_time > '$line_time' and create_time <= '$now' ";
      }
      $sql="select * from $this->tbl_name where $where";
      return $result=$this->db->rawQuery($sql);
    }
    /**
     * 根据dis_id取得订单信息
     * @param unknown $dis_id
     */
    public function get_order_info_by_disid($dis_id)
    {
        $sql=" select * from $this->tbl_name where dis_id = '".mysql_escape_string($dis_id)."'";
        return $result=$this->db->rawQueryOne($sql);
    }
}