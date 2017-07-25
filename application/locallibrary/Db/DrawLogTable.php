<?php
class Db_DrawLogTable
{
    private $db = null;
    private $tbl_name = 'ef_recruit_draw_log';
    public function __construct()
    {
        $this->db= Server::get(Server::mysql);
    }
    /**
     * 将提现日志录入
     * @param unknown $data
     */
    public function save($data)
    {
        $arr_insert_data = array();
        $arr_insert_data['u_id'] = $data['u_id'];
        $arr_insert_data['business_id'] = $data['bussiness_id'];
        $arr_insert_data['type'] = intval($data['type']);
        $arr_insert_data['money'] = $data['money'];
        $arr_insert_data['remark'] = $data['remark'];
        $arr_insert_data['in_bill_no'] = $data['in_bill_no'];
        $arr_insert_data['out_bill_no'] = $data['out_bill_no'];
        $arr_insert_data['create_time'] = date::get_date_time();
        return $this->db->insert($this->tbl_name, $arr_insert_data);
    }
}