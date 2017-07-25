<?php
class Db_ArticleWordsTable
{

    private $db = null;
    private $tbl_name = 'ef_article_words';

    public function __construct()
    {
        $this->db = Server::get(Server::mysql);
    }

    public function add($id,$words,$is_ad)
    {
       $tableData=array();
        $tableData['list_id']=$id;
        $tableData['is_ad']=$is_ad;
        $tableData['words']=$words;
        $tableData['create_time']=Date::get_date_time();
       return  $this->db->insert($this->tbl_name,$tableData);
    }
    public function get_no_words()
    {
        $where="is_csv=0";
        return $result=$this->db->where($where)->get($this->tbl_name);
    }
    public function change_words_status($id)
    {
        $where=" id = '$id' ";
        $date=Date::get_date_time();
        return $result=$this->db->where($where)->update($this->tbl_name,array('is_csv'=>1,'update_time'=>$date));
    }
}