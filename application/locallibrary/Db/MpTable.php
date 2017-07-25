<?php
class Db_MpTable
{
    private $db = null;
    private $tbl_name = 'ef_ad_dispatch_search';
    public function __construct()
    {
        $this->db= Server::get(Server::mysql); 
    }
    /**
     * 获取微信号总数
     */
    public function get_mp_count()
    {
        $sql="select count(id) as count from $this->tbl_name";
        $result=$this->db->rawQueryOne($sql);
        return $result['count'];
    }
    /**
     * 随机生成公众号
     * @param unknown $array
     */
    public function get_mp($count)
    {
        //$count=intval($count);
        $sql=" select * from $this->tbl_name where mp_weixin_id='".mysql_escape_string($count)."' ";
        $result=$this->db->rawQueryOne($sql);
        return $result;
    }
}