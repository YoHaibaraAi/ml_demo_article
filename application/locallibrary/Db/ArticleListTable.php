<?php
class Db_ArticleListTable
{

    private $db = null;
    private $tbl_name = 'ef_article_list';

    public function __construct()
    {
        $this->db = Server::get(Server::mysql);
    }

    public function get_history_list($mp_id)
    {
      $where=" id='$mp_id' ";
      return $result=$this->db->where($where)->get($this->tbl_name) ;
    }
    public function change_pre_result($id)
    {
       $where=" id = '$id' ";
        $date=Date::get_date_time();
        return $result=$this->db->where($where)->update($this->tbl_name,array('pre_result'=>2,'update_time'=>$date));
    }
    public function change_pre_one($id)
    {
        $where=" id = '$id' ";
        $date=Date::get_date_time();
        return $result=$this->db->where($where)->update($this->tbl_name,array('pre_result'=>1,'update_time'=>$date));
    }
    public function change_result($id)
    {
        $where=" id = '$id' ";
        $date=Date::get_date_time();
        return $result=$this->db->where($where)->update($this->tbl_name,array('result'=>2,'update_time'=>$date));
    }
    public function change_overdue_result($id)
    {
        $where=" id = '$id' ";
        $date=Date::get_date_time();
        return $result=$this->db->where($where)->update($this->tbl_name,array('pre_result'=>3,'update_time'=>$date));
    }
    public function get_ad_list()
    {
        $where="pre_result=2";
        return $result=$this->db->where($where)->get($this->tbl_name,array(0,1));
    }
    public function get_no_txt()
    {
        $where="txt_status=1";
        return $result=$this->db->where($where)->get($this->tbl_name);
    }
    public function change_txt_status($id)
    {
        $where=" id = '$id' ";
        $date=Date::get_date_time();
        return $result=$this->db->where($where)->update($this->tbl_name,array('txt_status'=>2,'update_time'=>$date));
    }
    public function change_pre_ad($id)
    {
        $where=" id = '$id' ";
        $date=Date::get_date_time();
        return $result=$this->db->where($where)->update($this->tbl_name,array('pre_result'=>0,'update_time'=>$date));
    }
    public function check_ad_status($id)
    {
        $where=" id = '$id' ";
        return $result=$this->db->where($where)->getOne($this->tbl_name);
    }
    public function change_words_status($id)
    {
        $where=" id = '$id' ";
        $date=Date::get_date_time();
        return $result=$this->db->where($where)->update($this->tbl_name,array('words_status'=>1,'update_time'=>$date));
    }
}