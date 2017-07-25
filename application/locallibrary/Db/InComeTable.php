<?php
class Db_InComeTable
{
    private $db = null;
    private $tbl_name = 'ef_recruit_income';
    public function __construct()
    {
        $this->db= Server::get(Server::mysql); 
    }
    /**
     * 根据被邀请者u_id取得相应信息
     * @param unknown $register_u_id
     */
    public function get_info_by_register_id($register_u_id)
    {
        $sql=" select * from $this->tbl_name where register_u_id='".mysql_escape_string($register_u_id)."' ";
        return $this->db->rawQueryOne($sql);
    }
    /**
     * 根据邀请者u_id取得相应信息
     * @param unknown $register_u_id
     */
    public function get_order_by_inviter_uid($inviter_u_id)
    {
        $sql=" select * from $this->tbl_name where inviter_u_id='".mysql_escape_string($inviter_u_id)."' ";
        return $this->db->rawQuery($sql);
    }

    /**
     * 将被邀请用户新增订单写入income表中
     * @param unknown $data
     */
    public function save($data)
    {
       $arr_insert_data = array();
       $arr_insert_data['inviter_u_id'] = $data['inviter_u_id'];
       $arr_insert_data['business_type'] = intval($data['business_type']);
       $arr_insert_data['business_id'] =    $data['business_id'];
       $arr_insert_data['register_u_id']    =$data['register_u_id'];
       $arr_insert_data['status']           =intval($data['status']);
       $arr_insert_data['total_money']    =round($data['total_money'],2);
       $arr_insert_data['in_come']    =round($data['in_come'],2);
       $arr_insert_data['ratio']    =doubleval($data['ratio']);
       $arr_insert_data['money_settle_status']    =intval(1);
       $arr_insert_data['create_time']    =date::get_date_time();
       return $this->db->insert($this->tbl_name, $arr_insert_data);
    }
    /**
     * 取得所有未完成的订单的信息
     */
    public function get_unfinish_order()
    {
       $sql=" select * from $this->tbl_name where status = 1 ";
       return $result=$this->db->rawQuery($sql);
    }
    public function check_business_id($business_id)
    {
        $sql=" select * from $this->tbl_name where business_id = '".mysql_escape_string($business_id)."' ";
        return $result=$this->db->rawQuery($sql);
    }
    /**
     * 将相关业务表中已经完成的订单在income表中做状态的修改
     * @param unknown $dis_id
     */
    public function update_status($business_id)
    {   
        $update_time=date::get_date_time();
        $sql="update $this->tbl_name set status=2,update_time='".mysql_escape_string($update_time)."' where business_id= '".mysql_escape_string($business_id)."'";
        return $result=$this->db->query($sql);
    }
    /**
     * 根据邀请者id计算历史红包总数
     * @param unknown $inviter_u_id
     */
    public function count_money_by_inviter_id($inviter_u_id)
    {
        $sql= " select sum(in_come) as sum from $this->tbl_name where inviter_u_id= '".mysql_escape_string($inviter_u_id)."' and money_settle_status= 2 ";
        $result=$this->db->rawQueryOne($sql);
        if($result['sum']) 
        {
            return $result['sum'];
        }else 
        {
            return 0;
        }
    }
    /**
     * 根据邀请者id计算期望红包总数
     * @param unknown $inviter_u_id
     */
    public function count_hopemoney_by_inviter_id($inviter_u_id)
    {
        $sql= "select sum(in_come) as sum from $this->tbl_name where inviter_u_id= '".mysql_escape_string($inviter_u_id)."' ";
        $result=$this->db->rawQueryOne($sql);
        return $result['sum'];
    }
    /**
     * 获取用户邀请者完成的订单总数
     * @param unknown $inviter_u_id
     */
    public function get_count_finish_order($inviter_u_id)
    {
        $sql= "select count(id) as count from $this->tbl_name where inviter_u_id='".mysql_escape_string($inviter_u_id)."' and status=2";
        $result=$this->db->rawQueryOne($sql);
        return $result['count'];
    }
    /**
     * 获取用户邀请者正在进行的订单总数
     * @param unknown $inviter_u_id
     */
    public function get_count_unfinish_order($inviter_u_id)
    {
        $sql= "select count(id) as count from $this->tbl_name where inviter_u_id= '".mysql_escape_string($inviter_u_id)."' and status=1";
        $result=$this->db->rawQueryOne($sql);
        return $result['count'];
    }
    /**
     * 获取income订单状态为2且结算状态为1的订单信息
     */
    public function get_finish_order_info()
    {
        $sql="select * from $this->tbl_name where status=2 and money_settle_status=1 ";
        return $result=$this->db->rawQuery($sql);
    }
    /**
     * 取得分销列表
     */
    public function get_recruit_list($data){
        $page=empty($data['page'])?0:$data['page'];
        $pagelen=empty($data['page_count'])?0:$data['page_count'];
    
        $where=$this->get_recruit_list_where($data);
    
        $order="create_time desc";
        if($data['recruit_order']==1)         //创建时间倒序
        {
            $order=" create_time desc ";
        }
    
        $page=($page-1)*$pagelen;
        $sql="select * from {$this->tbl_name} where $where order by $order limit $page,$pagelen";
        $list=$this->db->rawQuery($sql);
        return $list;
    }
    /**
     * 获取分销总数
     * @param unknown $data
     */
    public function get_recruit_list_count($data)
    {
        $where=$this->get_recruit_list_where($data);
    
        $sql = "select count(id) as num from {$this->tbl_name} where $where";
    
        $res=$this->db->rawQueryOne($sql);
        return intval($res['num']);
    }
    /**
     * 拼装$where查询条件
     * @param unknown $data
     * @return string
     */
    public function get_recruit_list_where($data)
    {
        $where="1";
        if ($data['business_id'])
        {
    
        }
        return $where;
    }
    /**
     * 更新用户结算状态
     * @param unknown $business_id
     * @param unknown $business_type
     */
    public function settle_success($business_id,$business_type,$time)
    {
//        $sql=" update $this->tbl_name set money_settle_status=2 
//            where business_id='".mysql_escape_string($business_id)."' 
//            and business_type='".mysql_escape_string($business_type)."'
//            and settle_time='".mysql_escape_string($time)."'";
       $tableData = array();
       $tableData['money_settle_status'] = 2;
       $tableData['settle_time'] = $time;
       //$tableData['update_time'] = Date::get_date_time();
       $where = "business_id='".mysql_escape_string($business_id)."' 
           and business_type='".mysql_escape_string($business_type)."'";
         
        $this->db->where($where)->update($this->tbl_name, $tableData);
        return $this->db->count;
    }
    /**
     * 根据邀请者uid分组获得结算信息
     */
    public function get_settle_info($now,$line_time)
    {
        $sql="select inviter_u_id,sum(in_come) as income from $this->tbl_name 
        where money_settle_status =2
        and  settle_time<= '$line_time'
        and  settle_time> '$now'
        group by inviter_u_id";
        return $result=$this->db->rawQuery($sql);
    }
    
    
    
    
    
    
    
    
}
