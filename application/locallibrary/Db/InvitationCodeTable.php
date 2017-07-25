<?php
class Db_InvitationCodeTable
{
    private $db = null;
    private $tbl_name = 'ef_recruit_invitation_code';
    public function __construct()
    {
        $this->db= Server::get(Server::mysql); 
    }

     /**
      * 保存生成的邀请码
      * @param unknown $uid
      * @param unknown $code
      * @return string
      */
    public function save($inviter_u_id,$inviter_code)
    {
       $arr_insert_data=array();
       $arr_insert_data['inviter_u_id']           =$inviter_u_id;
       $arr_insert_data['inviter_code']           =$inviter_code;
       $arr_insert_data['create_time']    =date::get_date_time();
       $arr_insert_data['create_ip']	  = IP::get_client_ip_long();
       return $this->db->insert($this->tbl_name, $arr_insert_data);
    }
    /**
     * 根据用户u_id获取邀请码信息以作校验
     * @param unknown $inviter_u_id
     */
     public function check($inviter_u_id)
     {   
         $sql="select * from $this->tbl_name where inviter_u_id= '".mysql_escape_string($inviter_u_id)."' ";
         return $this->db->rawQuery($sql);
     }
     /**
      * 根据邀请码获得相关信息
      * @param unknown $inviter_u_id
      */
     public function get_info_by_code($inviter_code)
     {
         $sql="select * from $this->tbl_name where inviter_code= '".mysql_escape_string($inviter_code)."' ";
         return $this->db->rawQueryOne($sql);
         
     }
     /**
      * 获取分销邀请人列表
      * @param unknown $data
      * @return string
      */
     public function get_inviter_uid_list($data)
     {
         $page=empty($data['page'])?1:$data['page'];
         $pagelen=empty($data['page_count'])?20:$data['page_count'];
         $start=($page-1)*$pagelen;
         $where=$this->get_recruit_list_where($data);
         $sql="select  inviter_u_id, inviter_code,create_time from $this->tbl_name  where $where order by create_time desc limit $start,$pagelen";
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
             $where.=" and inviter_u_id ='".mysql_escape_string($inviter_u_id)."'";
         }
         return $where;
     }
     
     
     
     
     
     
}