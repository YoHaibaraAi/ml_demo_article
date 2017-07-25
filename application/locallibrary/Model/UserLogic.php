<?php
class Model_UserLogic
{
    public static function getUserTable()
    {
        return new Db_UserTable();
    }
    /**
     * 根据用户u_id获取用户信息
     * @param unknown $u_id
     */
    public static function getUserInfo($u_id)
    {
       return self::getUserTable()->get_user_info($u_id); 
    }
    /**
     * 根据用户手机号取得用户信息
     * @param unknown $u_phone
     */
    public  static function getUserInfoByUphone($u_phone)
    {
        return self::getUserTable()->getUserInfoByUphone($u_phone);
    }
}