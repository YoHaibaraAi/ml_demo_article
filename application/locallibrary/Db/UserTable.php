<?php
class Db_UserTable
{
    private $db = null;
    private $tbl_name = 'ef_user';
    public function __construct()
    {
        $this->db= Server::get(Server::mysql); 
    }
    /**
     * 根据用户u_id查询用户数据
     * @param unknown $u_id
     */
    public function get_user_info($u_id)
    {
        $sql="select * from $this->tbl_name where u_id= '".mysql_escape_string($u_id)."' ";
        $result=$this->db->rawQueryOne($sql);
        return $result;
    }
    /**
     * 根据用户手机号码取得信息
     * @param unknown $u_phone
     * @return boolean
     */
    public function get_user_info_by_uphone($u_phone)
    {
        if (empty($u_phone))
            return false;
    
            $sql = " select * from {$this->tbl_name} where u_phone='" . mysql_escape_string($u_phone) . "'";
    
            return $result = $this->db->rawQueryOne($sql);
    }
    /**
     * 获取所有用户的u_id
     */
    public function get_all()
    {
        $sql=" select u_id from $this->tbl_name ";
       return $result = $this->db->rawQuery($sql);
    }
}