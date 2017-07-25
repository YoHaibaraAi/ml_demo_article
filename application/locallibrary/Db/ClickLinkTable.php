<?php
class Db_ClickLinkTable
{
    private $db = null;
    private $tbl_name = 'ef_recruit_link_click';
    public function __construct()
    {
        $this->db= Server::get(Server::mysql);
    }
    /**
     * 写入点击信息
     * @param unknown $data
     */
    public function save($data)
    {
        $arr_insert_data = array();
        $arr_insert_data['inviter_u_id'] = $data['inviter_u_id'];
        $arr_insert_data['inviter_code'] = $data['inviter_code'];
        $arr_insert_data['link_source_type'] = $data['link_source_type'];
        $arr_insert_data['is_mobile'] = intval($data['is_mobile']);
        $arr_insert_data['click_content'] = json_encode($data['click_content']) ;
        $arr_insert_data['click_time']    =date::get_date_time();
        return $this->db->insert($this->tbl_name, $arr_insert_data);
    }
    /**
     * 获得该用户分享链接的点击次数
     * @param unknown $inviter_u_id
     */
    public function get_count_click($inviter_u_id)
    {
        $sql="select count(id) as count from $this->tbl_name where inviter_u_id= '".mysql_escape_string($inviter_u_id)."' ";
        $result=$this->db->rawQueryOne($sql);
        return $result['count'];
        
    }
}
    