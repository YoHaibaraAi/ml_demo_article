<?php
class Db_InvitationFriendTable
{
    private $db = null;
    private $tbl_name = 'ef_recruit_invitation_friend';
    public function __construct()
    {
        $this->db= Server::get(Server::mysql); 
    }
    /**
     * 保存被邀请用户的注册信息
     * @param unknown $inviter_u_id
     * @param unknown $register_u_id
     * @param unknown $inviter_code
     * @param unknown $invite_type
     */
    public function save($inviter_u_id,$register_u_id,$inviter_code,$invite_type)
    {
        $arr_insert_data = array();
        $arr_insert_data['inviter_u_id'] =   $inviter_u_id;
        $arr_insert_data['inviter_code']  =   $inviter_code;
        $arr_insert_data['create_time']    =date::get_date_time();
        $arr_insert_data['register_u_id'] =   $register_u_id;
        $arr_insert_data['invite_type']  =   intval($invite_type) ;  //1链接分享2引导分享3画像分享
        return $this->db->insert($this->tbl_name, $arr_insert_data);
    }
    /**
     * 根据注册者u_id获得该条信息
     * @param unknown $register_u_id
     */
    public function get_info_by_register_id($register_u_id)
    {
        $sql=" select * from $this->tbl_name where register_u_id= '".mysql_escape_string($register_u_id)."' ";
        return $this->db->rawQueryOne($sql);
    }
    /**
     * 取得所有邀请好友表中信息
     */
    public function get_all()
    {
        $sql=" select * from $this->tbl_name ";
        return $this->db->rawQuery($sql);
    }
    /**
     * 获得邀请人有效邀请好友总数
     * @param unknown $inviter_u_id
     */
    public  function get_count_friend($inviter_u_id)
    {
       $sql="select count(id) as count from $this->tbl_name where inviter_u_id= '".mysql_escape_string($inviter_u_id)."' ";
       $result=$this->db->rawQueryOne($sql);
       return $result['count'];
    }
    /**
     * 获取该用户邀请的所有用户
     * @param unknown $inviter_u_id
     */
    public function get_friend_by_inviter_id($inviter_u_id)
    {
        $sql="select * from $this->tbl_name where inviter_u_id= '".mysql_escape_string($inviter_u_id)."' ";
        return $result=$this->db->rawQuery($sql);
    }
    /**
     * 获取邀请成功的邀请者的数量
     * @return mixed
     */
    public function get_no_repeat_count()
    {
        $sql="SELECT COUNT(DISTINCT inviter_u_id)  as count FROM $this->tbl_name ";
        $result=$this->db->rawQueryOne($sql);
         return $result['count'];
    }
    /**
     * 获取分销列表
     * @param unknown $data
     */
    public function get_inviter_uid_list($data)
    {
        $page=empty($data['page'])?0:$data['page'];
        $pagelen=empty($data['page_count'])?0:$data['page_count'];
        $where=$this->get_recruit_list_where($data);
        $sql="select DISTINCT inviter_u_id, inviter_code,create_time from $this->tbl_name  where $where order by create_time desc limit $page,$pagelen";
        //return $sql;
        return $result=$this->db->rawQuery($sql);
    }
    /**
     * 获取分销列表
     * @param unknown $data
     */
    public function get_inviter_uid_lists($data)
    {
        $page=empty($data['page'])?1:$data['page'];
        $pagelen=empty($data['page_count'])?20:$data['page_count'];
        $start=($page-1)*$pagelen;
        $where=$this->get_recruit_list_where($data);
        $sql="select DISTINCT (a.inviter_u_id), a.inviter_code,b.create_time from $this->tbl_name as a ";
        $sql.="left join ef_recruit_invitation_code as b on a.inviter_u_id=b.inviter_u_id where $where order by b.create_time ";
        $sql.="desc limit $start,$pagelen";
        //where $where order by create_time desc limit $page,$pagelen";
        //return $sql;
        return $result=$this->db->rawQuery($sql);
    }
    /**
     * 拼装$where查询条件
     * @param unknown $data
     * @return string
     */
    public function get_recruit_list_where($data)
    {
        $where="1";
        if ($data['inviter_u_id'])
        {
            $inviter_u_id=$data['inviter_u_id'];
           $where.="inviter_u_id ='".mysql_escape_string($inviter_u_id)."'";
        }
        return $where;
    }
    
    
    
    
    
    
    
    
    
}