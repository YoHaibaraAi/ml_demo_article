<?php
class Model_ArticleLogic
{
    public static function getArticleListTable()
    {
        return new Db_ArticleListTable();
    }
    public static function getArticleWordsTable()
    {
        return new Db_ArticleWordsTable();
    }
    public static function getArticleUserTable()
    {
        return new Db_ArticleUserTable();
    }
    public static function saveArticle()
    {
        $a=self::getArticleListTable()->get_no_txt();
        foreach ($a as $key =>$value)
        {
            $mp_ids[]=$value['id'];
        }
        $i=0;
        foreach ($mp_ids as $key =>$mp_id)
        {
            $lists=self::getArticleListTable()->get_history_list($mp_id);
            foreach ($lists as $key =>$value)
            {
                $result=self::getUrlContent($value['content_url']);
                $regex="/<div class=\"rich_media_content \" id=\"js_content\">([\s\S]*?)<\/div>/i";
                if (preg_match_all($regex, $result, $matches))
                {
                    $text=trim(strip_tags($matches[1][0]));
                    $text=str_replace("\n","",$text);
                    $text=str_replace("\r","",$text);
                    $text=str_replace("\r\n","",$text);
                    $result_info=self::saveTxt($text,$value['id']);
                    if ($result_info)
                    {
                        $i++;
                    }
                }else
                {
                    return "error";
                }

            }
        }
        return $i;

    }
    public static function getUrlContent($url)
    {
        $header = array (
            'CLIENT-IP:58.68.44.61','X-FORWARDED-FOR:58.68.44.61',
            'User-Agent:Mozilla/5.0 (Windows NT 5.1; rv:34.0) Gecko/20100101 Firefox/34.0',
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        $content = curl_exec ( $ch );
        if ($content == FALSE) {
            $result= "error:" . curl_error ( $ch );
        }
        curl_close ( $ch );
        return $content;
    }
    public static function saveTxt($content,$n)
    {
        $test=dirname(dirname(dirname(dirname(__FILE__))));
        $path=$test."/data/content/";



        $file=$path.$n.'.'.txt;
        $save_result=file_put_contents($file,$content,FILE_APPEND);
        if ($save_result)
        {
            return self::getArticleListTable()->change_txt_status($n);
        }
    }
    public static function ad_search()
    {
        $test=dirname(dirname(dirname(dirname(__FILE__))));
        $dir=$test."/data/ad/";
        $dir1=$test."/data/cut/";
        $a=scandir($dir);
        $b=scandir($dir1);
        $i=0;
        foreach ($a as $key =>$value)
        {
            if(pathinfo($value,PATHINFO_EXTENSION)=="txt")
            {
                $pre_status=self::getArticleListTable()->check_ad_status(basename($value,'.txt'));
                if ($pre_status['pre_result']==5)
                {
                    $result=self::getArticleListTable()->change_pre_result(basename($value,'.txt'));
                    if ($result)
                    {
                        $i++;
                    }
                }

            }

        }
        foreach ($b as $k =>$val)
        {
            if(pathinfo($val,PATHINFO_EXTENSION)=="txt")
            {
                $pre_status=self::getArticleListTable()->check_ad_status(basename($val,'.txt'));
                if ($pre_status['pre_result']==5)
                {
                    $result=self::getArticleListTable()->change_pre_one(basename($val,'.txt'));

                }

            }

        }
        return $i;
    }
    public static function ad_judge()
    {
       return $lists=self::getArticleListTable()->get_ad_list();

    }
    public static function ad_confirm($id,$status)
    {
        if ($status==3)
        {
           return  $result=self::getArticleListTable()->change_overdue_result($id);
        }
        if ($status==2)
        {
            $result[]=self::getArticleListTable()->change_result($id);
            $result[]=self::getArticleListTable()->change_pre_ad($id);
            return $result;
        }

    }
    public static function makePreData()
    {
        $path=dirname(dirname(dirname(dirname(__FILE__))));
        $word_path=$path.'/data/words/';
        $ad_path=$path.'/data/ad/';
        $i=0;
        if (is_dir($word_path)) {
            if ($dh = opendir($word_path)) {

                while (($file = readdir($dh)) !== false) {
                    if(pathinfo($file,PATHINFO_EXTENSION)=="txt")
                    {
                         $id= basename($file,'.txt');
                         $ad_status=self::getArticleListTable()->check_ad_status($id);

                             //$fp=fopen($word_path.$id,'.txt','r');
                             $str=file_get_contents($word_path.$id.'.txt');
                             if ($ad_status['pre_result']==0)
                             {
                                 $is_ad=1;
                             }elseif ($ad_status['pre_result']==3)
                             {
                                 $is_ad=0;
                             }
                             $add_result=self::getArticleWordsTable()->add($id,$str,$is_ad);
                             if ($add_result)
                             {
                                 $result=self::getArticleListTable()->change_words_status($id);
                                 $i++;
                             }




                    }
                }
                closedir($dh);
            }
        }
        return $i;
    }
    public static function getCsv()
    {
        $path=dirname(dirname(dirname(dirname(__FILE__))));
        $dir=$path.'/data/';

        $lists=self::getArticleWordsTable()->get_no_words();
        $fp=fopen($dir.'data.csv','a+');
//        $data=fgetcsv($fp);

//            $header="list_id,is_ad,words";
//            fputcsv($fp,split(',',$header));






        $i=0;
        foreach ($lists as $key =>$value)
        {
            $line=$value['list_id'].','.$value['is_ad'].','.$value['words'];
//            $encode=mb_detect_encoding($line,array("ASCII",'UTF-8',"GB2312","GBK",'BIG5'));
//            var_dump($encode);exit();
//            iconv("UTF-8","GBK",$line);
            $res=fputcsv($fp,split(',',$line));
            if ($res)
            {
                $result=self::getArticleWordsTable()->change_words_status($value['id']);
                $i++;
            }
        }
        fclose($fp);
        return $i;
    }
}














