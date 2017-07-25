<?php
class Db_ArticleUserTable
{

    private $db = null;
    private $tbl_name = 'ef_article_User';

    public function __construct()
    {
        $this->db = Server::get(Server::mysql);
    }
}