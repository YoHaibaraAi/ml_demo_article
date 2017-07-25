<?php
/**
 * @author kasiss
 * 
 * @date 2016-04-13
 * 
 */


class Watcher_Sdk
{
    
    
    public $base_url = "http://watcher.lightcloudapps.com/yzapi/api-v1/3";
    public $ww_api_token = "";
    
    private function _http_request($url,$params=array())
    {
        if(strstr($url,'?s='))
        {
            $url = $url."&ww_api_token={$this->ww_api_token}";
        }else{
            $url = $url."?ww_api_token={$this->ww_api_token}";
        }
        
        if(is_array($params))
        {
            $query = '';
            foreach($params as $k => $v)
            {
                $query .= '&'.$k.'='.$v;
            }
            
            $url .= $query;
        }
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        $res = curl_exec($ch);
        $error = curl_error($ch);
        
        if(!$error)
        {
            return $res;
        }
        return null;
    }
    
    public function __construct($token)
    {
        $this->ww_api_token = $token;
    }
    
    /**
     * 账号搜索
     * @param $keyword 搜索关键词
     * @param $tags 搜索标签 数组 或tag1,tag2
     * 
     * @return array();
     * 
     * 原始json格式数据
     * {
     *       "errcode": 0,
     *       "errmsg": "",
     *       "timestamp": 12345678,
     *       "items": [{
     *           "title": "",
     *           "description": "",
     *           "thumb": "http://",
     *           "qrcode": "",
     *           "id": "",
     *           "username": "",
     *           "certified": "",
     *           "allow_comment": "",
     *           "allow_ad": "",
     *           "tags": [
     *               "tags1", "tag2"
     *           ]
     *       }, {}]
     *   }
     * 
     */
    public function accounts_search($keyword,$tags=array())
    {
        $act_url = $this->base_url."/accounts?s=$keyword";
        if($tags)
        {
            if(is_string($tags))
            {
                $act_url .= "&tags=$tags";
            }else if(is_array($tags))
            {
                $act_url .= "&tags=".trim(implode(',', $tags),',');
            }
        }
        
        $data = $this->_http_request($act_url);
        return is_null($data) ? array() :json_decode($data,1);
    }
    
    
    
    /**
     * 账号基础情况查询
     * @param unknown $guid 公众号原始id  gh_xxxxxxx
     * @return array()
     * 原始json格式数据
     * 
     * {
     *       "errcode": 0,
     *       "errmsg": "",
     *       "timestamp": 12345678,
     *       "item": {
     *           "title": "",
     *           "description": "",
     *           "thumb": "http://",
     *           "qrcode": "",
     *           "id": "",
     *           "username": "",
     *           "certified": "",
     *           "allow_comment": "",
     *           "allow_ad": "",
     *           "tags": [
     *               "tags1", "tag2"
     *           ]
     *       }
     *   }
     * 
     */
    public function accounts_basic($guid)
    {
       $act_url = $this->base_url."/accounts/{$guid}/basic";
       $data = $this->_http_request($act_url);
       return is_null($data) ? array() :json_decode($data,1);
    }
    
    
    /**
     * 账号品牌词分布清单云
     * @param unknown $guid公众号原始id  gh_xxxxxxx
     * 
     * @return array()
     * {
     *   	"data": {
     *   		"keywords": {
     *   			"7days": [
     *   				[
     *   					0.46249293819123,
     *   					"自媒体"
     *   				]
     *   			],
     *   			"update_time": 1461085237
     *   		}
     *   	},
     *   	"errcode": 0,
     *   	"errmsg": "",
     *   	"timestamp": 1461217312
     *  }
     */
    public function accounts_keywords($guid)
    {
        $act_url = $this->base_url."/accounts/{$guid}/keywords";
        $data = $this->_http_request($act_url);
        return is_null($data) ? array() :json_decode($data,1);
    }
    
    
    /**
     * 账号品牌词云分布
     * @param unknown $guid
     * @return  array()
     *
     *
     * {
     *   	"data": {
     *   		"ner": {
     *   			"7days": {
     *   				"company_name": [
     *   					"TCL多媒体"
     *   				],
     *   				"job_title": [
     *   					"CTO"
     *   				],
     *   				"location": [
     *   					"深圳南山"
     *   				],
     *   				"org_name": [
     *   					"WeMedia自媒体联盟"
     *   				],
     *   				"person_name": [
     *   					"陈光郎"
     *   				],
     *   				"product_name": [
     *   					"微信"
     *   				],
     *   				"time": [
     *   					"4月7日",
     *   					"春季"
     *   				]
     *   			},
     *   			"update_time": 1461085237
     *   		}
     *   	},
     *   	"errcode": 0,
     *   	"errmsg": "",
     *   	"timestamp": 1461217497
     *   }
     */
    public function accounts_brand($guid)
    {
        $act_url = $this->base_url."/accounts/{$guid}/brand";
        $data = $this->_http_request($act_url);
        return is_null($data) ? array() :json_decode($data,1);
    }
    
    /**
     * 账号统计数据
     * @param unknown $guid 公众号原始id gh_xxxxxx
     * @param string $from_date  统计开始日期  默认为当前日期前一天 格式 YYYY-MM-DD
     * @param string $to_date   统计结束日期 默认为当前日期 格式 YYYY-MM-DD
     * @param int $daily      是否按日期分割 是 1 否 0
     * 
     * @return array();
     * 
     * 原始json格式数据
     * {
     *       errcode: 0,
     *       errmsg: "",
     *       timestamp: 1459880074,
     *       data:{
     *           status: {
     *               from_date: "2016-04-04",
     *               to_date: "2016-04-05",
     *               daily:true,
     *               publish_num: 5,
     *               read_num_total: 150954,
     *               read_num_avg: 75477,
     *               read_num_array: {
     *                   1197: 85704,
     *                   1198: 65250
     *               },
     *               read_num_total_line1: 85704,
     *               read_num_avg_line1: 85704,
     *               read_num_line1_array: {
     *                   1197: 85704
     *               },
     *               read_num_total_line2: 65250,
     *               read_num_avg_line2: 65250,
     *               read_num_line2_array: {
     *                   1198: 65250
     *               },
     *               read_num_total_other: 0,
     *               read_num_avg_other: 0,
     *               read_num_other_array: [],
     *               fav_num_total: 451,
     *               fav_num_avg: 225.5,
     *               fav_num_array: {
     *                   1197: 344,
     *                   1198: 107
     *               },
     *               all_ids:[3211397,3211387,3211377,3211367,3211358,3211348,3211341,3211332],
     *               2016-04-04:
     *                  {
     *                      read_num_total:800000,
     *                      fav_num_total:7446,
     *                      read_num_array:
     *                          {
     *                              3211397:100000,
     *                              3211387:100000,
     *                              3211377:100000,
     *                              3211367:100000,
     *                              3211358:100000,
     *                              3211348:100000,
     *                              3211341:100000,
     *                              3211332::100000
     *                           },
     *                       fav_num_array:
     *                           {
     *                               3211397:666,
     *                               3211387:302,
     *                               3211377:888,
     *                               3211367:1013,
     *                               3211358:1953,
     *                               3211348:677,
     *                               3211341:363,
     *                               3211332:1584
     *                            },
     *                        content_10wp_num:8,
     *                        read_num_total_other:600000,
     *                        read_num_other_array:
     *                            {
     *                              3211397:100000,
     *                              3211387:100000,
     *                              3211377:100000,
     *                              3211367:100000,
     *                              3211358:100000,
     *                              3211348:100000
     *                            },
     *                         read_num_total_line2:100000,
     *                         read_num_line2_array:{"3211341":100000},
     *                         content_line1_num:1,
     *                         read_num_total_line1:100000,
     *                         read_num_line1_array:{"3211332":100000},
     *                         read_num_avg:100000,
     *                         read_num_avg_line1:100000,
     *                         read_num_avg_line2:100000,
     *                         read_num_avg_other:100000,
     *                         fav_num_avg:930.75
     *                 }
     *               
     *           }
     *      }
     *   }
     */
    public function accounts_status($guid,$from_date='',$to_date='',$daily=0)
    {
        $act_url = $this->base_url."/accounts/{$guid}/status";
        
        if($from_date == '')
        {
            $from_date = date('Y-m-d',strtotime('-1 day'));
        }
        if($to_date == '')
        {
            $to_date =  date('Y-m-d');
        }
        
        $params = array(
            'from_date' => $from_date,
            'to_date' => $to_date,
            'daily' => $daily
        );
        
        $data = $this->_http_request($act_url,$params);
        return is_null($data) ? array() :json_decode($data,1);
    }
    
    
    /**
     * 账号内容数据
     * @param string $guid 公众号原始id gh_xxxxxx
     * @param number $num   返回内容数量 默认为50 最大值1000
     * @param number $page  当前读取数据的页数，默认为0
     * @param string $from_date 获取起始日期，格式为YYYY-MM-DD，默认为当前时间前一天
     * @param string $to_date   获取结束日期，格式为YYYY-MM-DD，默认为今天
     * @param string $orderby 排序依据，默认为date(创建日期),取值范围为date(创建日期),read_num(阅读量),fav_num(点赞量)
     * @param string $order 排序顺序，取值范围为 DESC(默认) 或 ASC
     * @param number $index 图文位置，取值为整数，0(默认)或不输入则不限制位置,1为头条
     * 
     * @return array();
     * 
     * {
     *       errcode: 0,
     *       errmsg: "",
     *       timestamp: 1459877016,
     *       list: {
     *           items: [{
     *               title: "穿上红色高跟鞋，去征服世界吧",
     *               link: "http://mp.weixin.qq.com/s?__biz=MzAwODA5MTM4Mg==&mid=431005418&idx=1&sn=1c142c3c67f3f52e38d019939db2655d#rd",
     *               idx: "1",
     *               thumb: "http://mmbiz.qpic.cn/mmbiz/tXW6WVRLvkfLNsfumcqH4v84MIStPPasdnDBWJEic2dulPYVpFiaPhLHYFbwpTHW4baAEjxribZ6Gohc7ZDflkQPQ/0?wx_fmt=jpeg",
     *               account_uid: "gh_062c099d6323",
     *               account_name: "黎贝卡的异想世界",
     *               username: "Miss_shopping_li",
     *               publish_date: "2016-04-05",
     *               source_url: "黎贝卡的异想世界",
     *               read_num: 85704,
     *               fav_num: 344
     *           }, {
     *               title: "推广&nbsp;||波点经典，斑点却不经典",
     *               link: "http://mp.weixin.qq.com/s?__biz=MzAwODA5MTM4Mg==&mid=431005418&idx=2&sn=ad7cc312e38506a150c98cfb2be757d3#rd",
     *               idx: "2",
     *               thumb: "http://mmbiz.qpic.cn/mmbiz/tXW6WVRLvkfLNsfumcqH4v84MIStPPasmicLuqOPCDnFkhayu0LibgITzjiap30RviaicRoXYtia0JDwzDDdqFtV0iblg/0?wx_fmt=jpeg",
     *               account_uid: "gh_062c099d6323",
     *               account_name: "黎贝卡的异想世界",
     *               username: "Miss_shopping_li",
     *               publish_date: "2016-04-05",
     *               source_url: "黎贝卡的异想世界",
     *               read_num: 65250,
     *               fav_num: 107
     *           }],
     *           query: {
     *               num: 50,
     *               page: 0,
     *               from_date: "2016-04-04",
     *               to_date: "2016-04-05",
     *               order: "DESC",
     *               orderby: "read_num"
     *           },
     *           total_num: "2"
     *       }
     *   }
     */
   
    public function accounts_list($guid,$num=50,$page=0,$from_date='',$to_date='',$orderby='date',$order="DESC",$index=0)
    {
        $act_url = $this->base_url."/accounts/{$guid}/list";
    
        if($from_date == '')
        {
            $from_date = date('Y-m-d',strtotime('-1 day'));
        }
        if($to_date == '')
        {
            $to_date =  date('Y-m-d');
        }
    
        $params = array(
            'from_date' => $from_date,
            'to_date' => $to_date,
            'num'=>$num,
            'orderby'=>$orderby,
            'order'=>$order,
            'index'=>$index
        );
    
        $data = $this->_http_request($act_url,$params);
        return is_null($data) ? array() :json_decode($data,1);
    }
    
    /**
     * 向watcher添加账号 传入历史图文链接
     * @param unknown $url
     * @return mixed
     * 
     * 
     * {
     *   errcode: 0,    //account_exists
     *   errmsg: "账号 新晚报 添加完成", // 账号已经存在
     *   timestamp: 1460907439,
     *   data: {
     *       item: {
     *           account_id: 1270,
     *           wechat_username: "gh_5aa41f572ba1",
     *           wechat_nickname: "新晚报",
     *           wechat_biz: "MjM5NTU1MTY5Mg=="
     *       }
     *   }
     *  }
     *  {
     *   	"data": null,
     *   	"errcode": "invalid_content",
     *   	"errmsg": "页面内容不合法",
     *   	"timestamp": 1462347231
     *   }
     *  
     */
    
    public function create($url)
    {
        $url = urlencode($url);
        $act_url =  $this->base_url."/accounts/create?s=&content_url=".$url;
        $data = $this->_http_request($act_url);
        return is_null($data) ? array() :json_decode($data,1);
    }
    
    
}