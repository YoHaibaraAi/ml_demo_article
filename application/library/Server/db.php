<?php

/**
 * 使用说明：
 * * 从db_base继承，也可以直接使用。使用时需要在$_SERVER数组中配置当前DB服务器的详细配置，同时也要注意本类是读写分离的，读库和写库的信息都需要有。
 *
 * 安全问题：
 * * 本类一律不处理where和order参数中的数据，所以在使用本类的函数之前，对于这两个参数，一律用mysql_escape_string()处理。
 * * 本类中的insert()和update()函数，会自动处理插入和更新字段的值，但不会处理字段名称，一般字段名是写死在程序里的，
 *   如果是字段名采用用户输入的值，请自己先用mysql_escape_string()函数正理。
 *
 * 性能问题：
 * * 本类不记录SQL执行的时间。
 * * 本类会记录SQL执行的个数，存储在全局变量$db_base_performance_sql_count中，使用db_base::perf_get_count()可以读取其中的值。
 *
 */


$db_base_performance_sql_count = array();

class Server_db
{
	var $db_obj;
	var $debug=false;
	var $pref_debug=false;
	var $wait_timeout = 25;
	
	private $db_config_master=null;
	private $db_config_slave=null;
	private $db_base_obj_master=null;
	private $db_base_obj_slave=null;
	private $db_base_last_master_time=0;
	private $db_base_last_slave_time=0;
	
	
	function __construct($options=array())
	{
		
		if(isset($_GET["no_pref_debug"]))
		{
			$this->pref_debug = false;
		}
		else
		{
			$this->pref_debug = true;
		}
		
		if (isset($_GET["db_debug"]))
		{
			$config["db_debug"] = true;
		}
		if ($config["db_debug"] == true)
		{
			$this->debug = true;
		}
		else
		{
			$this->debug = false;
		}
			// $this->debug = true;

		$this->db_config_master = $options;
		$this->db_config_slave  = $options;
	}

	
####################################################################################
// 分库分表相关函数
	
	public static function get_dbname($id, $prefix="")
	{
		$db_id = abs(crc32($id)%16);
		$db_id = sprintf("%x", $db_id);
		return $prefix . $db_id;
	}

	public static function get_tblname($id, $prefix="")
	{
		$db_id = md5($id);
		$db_id = substr($db_id, 0, 2);
		return $prefix . $db_id;
	}
	
####################################################################################
// 性能分析
	public function perf_add_count($sql, $time=0)
	{
		if($this->pref_debug )
		{
			global $db_base_performance_sql_count;
			$db_base_performance_sql_count[$sql] = $time;
		}
		
	}
	
	public function perf_get_count()
	{
		global $db_base_performance_sql_count;
		return count($db_base_performance_sql_count);
	}
	
	public function perf_get_sql_list()
	{
		global $db_base_performance_sql_count;
		return $db_base_performance_sql_count;
	}
	
	
####################################################################################
// DEBUG	

	function debug_echo($sql)
	{
		if ($this->debug)
		{
			$sql_count = self::perf_get_count();
			$str = "<!-- NO:{$sql_count} {$sql} -->"; 
			echo $str . "\n";
		}
		
		
		$this->debug_explain($sql);
		$this->debug_echo_notice();
	}

	function debug_echo_notice()
	{
		if ($this->debug)
		{
			$sql_count = self::perf_get_count();
			if ($sql_count == 20)
			{
				echo <<<ZZ
<!-- 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	本次页面查询SQL数超过20个，建议优化。
	优化方法:
	1 对页面进行拆解，分成必须一次性显示的数据和需用户行为后数据或可异步读取数据。
		对于需要用户行为触发后才显示的数据，一般用AJAX模式读取。
		对于可异步读取的数据，一般在页面onready()后用json或jsonp方式读取。
	2 对数据表结构进行优化，对于关联类的表，如活动、奖品相关联肯定需要一个活动奖品关联表，可以在关联表中添加相应的冗余字段，省去查询奖品表。
	3 对SQL进行合并，如查询一组餐厅的名称，属于查询多条属于同一张表的数据，如果数量过多，不宜一条一条获取，则合并为一个SQL都查出来，并缓存到CACHE中，这个CACHE可以不主动更新。
	4 动静分离，类似于1，但比1做得彻底，直接把不变的内容生成HTML静态页，动态内容全部用异步读取再JS渲染。
	5 生成主动型缓存，即利用发布系统生成静态页面，然后在页面展示时network::file__get__contents输出。适用于整个页面更新周期不大的或需要编辑参与的页面。
-->\n
ZZ;
			}
		}
	}
	
	function debug_explain($sql)
	{
		if(isset($_GET["e_debug"]))
		{
			global $explain_sql_count;
			$explain_sql_count ++;
			$explain_sql  = "explain ". $sql;
			
			echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
			echo  "<!-- explain::get_listcount:() {$explain_sql} -->\n";
			$sth=$this->db_obj->prepare($explain_sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
			$res=$sth->execute();
			$lists=$sth->fetchAll(2);
			echo "<table  style='float:left' width=100% border=1><td style='width:20px'>{$explain_sql_count}</td><td colspan=9><b>{$sql}</b></td><tr>";
			foreach($lists[0] as $key => $value)
			{
				echo "<td>{$key}</td>";
				 
			}
			echo "</tr>";
			foreach($lists as $k=> $v)
			{
				"<tr>";
			
				foreach($v as $key => $value)
				{
					if ($key == "type" || $key == "Extra")
					{
						echo "<td style='background-color:red'>{$value}</td>";
					}
					else
					{
						echo "<td>{$value}</td>";
					}
					 
				}
			
				echo "</tr>";
				
			}	
			
			echo "</table><br>&nbsp;<br><br/>";
			
		}		
	}
	

####################################################################################
// DB	

	function init_db($is_master_db=true, $dbname=false)
	{
		if ($is_master_db)				//is master
		{
			if (!$dbname)
			{
				$dbname = $this->db_config_master['dbname'];
			}
			$dsn="mysql:host={$this->db_config_master['host']};port={$this->db_config_master['port']};dbname={$dbname}" ;
			$user=$this->db_config_master['user'];
			$passwd=$this->db_config_master['passwd'];
		}
		else							//is slave
		{
			if (!$dbname)
			{
				$dbname = $this->db_config_slave['dbname'];
			}
			$dsn="mysql:host={$this->db_config_slave['host']};port={$this->db_config_slave['port']};dbname={$dbname}" ;
			$user=$this->db_config_slave['user'];
			$passwd=$this->db_config_slave['passwd'];
		}
		
		
		if($is_master_db)
		{
			if(!$this->db_base_obj_master || ( time()- $this->db_base_last_master_time > $this->wait_timeout) )
			{
				$this->db_base_obj_master = new PDO($dsn,$user,$passwd);
				$this->db_base_last_master_time = time();//更新主库连接时间
				
				$this->db_base_obj_master->exec("SET NAMES '{$this->db_config_master['charset']}' ");
				
				if ($this->debug)
				{
					$str = "<!is_master_db : $is_master_db -- {$dsn} -->"; 
					echo $str . "\n";
				}
				
			}

			$this->db_obj = $this->db_base_obj_master;
		}
		else
		{
			
			if(!$this->db_base_obj_slave || (time()- $this->db_base_last_slave_time > $this->wait_timeout) )
			{
				$this->db_base_obj_slave = new PDO($dsn,$user,$passwd);
				$this->db_base_last_slave_time = time(); //更新辅库连接时间
				
				$this->db_base_obj_slave->exec("SET NAMES '{$this->db_config_slave['charset']}' ");
				
				if ($this->debug)
				{
					$str = "<!is_slave_db : $is_slave_db -- {$dsn} -->"; 
					echo $str . "\n";
				}	
			}
			
			$this->db_obj = $this->db_base_obj_slave;
        }

		if ($this->db_obj->errorCode() !== "00000")
		{
			$error = var_export($this->db_obj->errorInfo(), true);
			throw new Exception("Can't Connect DB - {$error}");
		}
	}
	

	function insert($tblname, $arr_insert_data, $dbname = false, $is_replace = false)
	{
		global $program_sql_call_time;
		$time_start = microtime_float();
		
		$this->init_db(true, $dbname);

		if (!is_array($arr_insert_data) || count($arr_insert_data) < 1)
		{
			return false;
		}

		$str_insert_keys = "";
		$str_insert_values = "";
		$b_isfirst = true;
		foreach($arr_insert_data as $db_key=> $db_value)
		{
			if ($b_isfirst)
			$b_isfirst = false;
			else
			{
				$str_insert_keys .= ",";
				$str_insert_values .= ",";
			}

			$str_insert_keys .= "`{$db_key}`";
			if ($db_key == "ip")
			{
				$str_insert_values .= $db_value;
			}
			else
			{
				$str_insert_values .= "'" . mysql_escape_string($db_value) ."'";
			}
		}

		if ($is_replace)
		{
			$sql ="replace into $tblname ({$str_insert_keys}) values ({$str_insert_values})";
		}
		else
		{
			$sql ="insert into $tblname ({$str_insert_keys}) values ({$str_insert_values})";
		}
		

		// DEBUG
		$this->debug_echo($sql);
		
		// 执行
		$sth=$this->db_obj->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$res=$sth->execute();
		
		
		$time_using = microtime_float() - $time_start;
		$program_sql_call_time = $program_sql_call_time + $time_using;
		
		// 性能监控
		$this->perf_add_count($sql, $time_using);
		
		if ($res > 0)
		{
			return true;
		}

		return false;
	}

	function update($tblname, $arr_update_data, $str_where, $dbname = false)
	{
		global $program_sql_call_time;
		$time_start = microtime_float();
		
		$this->init_db(true, $dbname);

		if (!is_array($arr_update_data) || count($arr_update_data) < 1)
		{
			return false;
		}
		
		foreach($arr_update_data as $db_key=> $db_value)
		{
			if (preg_match("/^$db_key\s*([\+\-\*\/])\s*(\d+)\s*$/",$db_value,$matchs) ) {
				$arr_set [] = " `{$db_key}`=`{$db_key}`" . $matchs[1] . $matchs[2];
			//------------------------------------------------------------------//

			}
			else
			{
				$arr_set[] = " `{$db_key}`='" . mysql_escape_string($db_value) ."'";
			}
		}
		$str_set = "set " . implode(",", $arr_set);

		$sql ="update $tblname {$str_set} where {$str_where}";
		
		
		
		// DEBUG
		$this->debug_echo($sql);
		
		// 执行
		$sth=$this->db_obj->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		
		
		
		$res=$sth->execute();
		$time_using = microtime_float() - $time_start;
		$program_sql_call_time = $program_sql_call_time + $time_using;
		
		// 性能监控
		$this->perf_add_count($sql, $time_using);

		
		$af_rows = $sth->rowCount();

		return $res;
	}

	function delete($tblname, $str_where, $dbname = false)
	{
		$this->init_db(true, $dbname);

		if (!$str_where)
		{
			return false;
		}

		$sql ="delete from $tblname where {$str_where}";
		
				

		// DEBUG
		$this->debug_echo($sql);
		
		// 执行
		$sth=$this->db_obj->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$res=$sth->execute();
 		$af_rows = $sth->rowCount();

 		// 性能监控
 		$this->perf_add_count($sql, 0);
 		
 		
		return $res;
	}

	function select($sql, $is_ok = false, $is_master_db=false, $dbname = false)
	{
		global $program_sql_call_time;
		$time_start = microtime_float();
		
		$this->init_db($is_master_db, $dbname);

		

		// DEBUG
		$this->debug_echo($sql);
		
		// 执行
		$sth=$this->db_obj->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		
		$res=$sth->execute();
		
		if ($is_ok)
		{
			return $res;
		}
		$lists=$sth->fetchAll(2);
		
		
		$time_using = microtime_float() - $time_start;
		$program_sql_call_time = $program_sql_call_time + $time_using;
		
		// 性能监控
		$this->perf_add_count($sql, $time_using);

		
		return $lists;

	}

	
	function query($sql, $is_ok = true, $dbname = false)
	{
		global $program_sql_call_time;
		$time_start = microtime_float();
		
		$this->init_db(true, $dbname);
		

		// DEBUG
		$this->debug_echo($sql);
		
		// 执行
		$sth=$this->db_obj->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		$res=$sth->execute();
		if($this->debug && $sth->errorCode() != '00000')
		{
			print_r($sth->errorInfo()); 
			exit;
		}
		if ($is_ok)
		{
			return $res;
		}
		$lists=$sth->fetchAll(2);
		
		$time_using = microtime_float() - $time_start;
		$program_sql_call_time = $program_sql_call_time + $time_using;
		
		// 性能监控
		$this->perf_add_count($sql, $time_using);

		return $lists;

	}
	
	function get_lastinsertid($dbname = false)
	{
		$this->init_db(true, $dbname);
		return $this->db_obj->lastInsertId();
	}
	
	function get_listcount($tblname, $where="", $groupby="", $dbname = false)
	{
		global $program_sql_call_time;
		$time_start = microtime_float();
		
		
		$this->init_db(false, $dbname);

		if ($where)
		{
			$where = "WHERE ".$where;
		}		
		
		$sql ="SELECT COUNT(id) AS `count` FROM $tblname $where $groupby";
		
		
		// DEBUG
		$this->debug_echo($sql);
		
		// 执行
		$sth=$this->db_obj->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
		
		$res=$sth->execute();
		
		
		
		$lists=$sth->fetchAll();
		
		$time_using = microtime_float() - $time_start;
		$program_sql_call_time = $program_sql_call_time + $time_using;
		
		// 性能监控
		$this->perf_add_count($sql, $time_using);

		
		if ($lists)
		{
			if ($groupby)
			{
				return count($lists);
			}
			return $lists[0]["count"];
		}
		return 0;
	}

	
	function get_alllist($tblname, $pageid, $pagelen, $order="", $where="", $dbname = false, $field=false, $force_index = array())
	{
		global $program_sql_call_time;
		$time_start = microtime_float();

		$this->init_db(false, $dbname);
		if ($pageid > 0 && $pagelen > 0)
		{
			$sqllimit = " LIMIT " . ($pageid - 1) * $pagelen . "," . $pagelen . " ";		// 鍒嗛?1?7
		}
		else
		{
			$sqllimit = "";
		}
		if ($order)
		{
			$order = "ORDER BY ".$order;
		}
		if ($where)
		{
			$where = "WHERE ".$where;
		}
		if ($force_index)
		{
			$force_index = "FORCE INDEX (`".implode('`,`', $force_index)."`)";
		}
		else
		{
			$force_index = '';
		}
		if (!$field)
		{
			$sql ="SELECT * FROM {$tblname} {$force_index} {$where} {$order}" . $sqllimit;
		}
		else
		{
			$sql ="SELECT {$field} FROM {$tblname} {$force_index} {$where} {$order}" . $sqllimit;
		}

		
		// DEBUG
		$this->debug_echo($sql);
		
		// 执行
		$sth=$this->db_obj->prepare($sql,array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

		$res=$sth->execute();
		$lists=$sth->fetchAll(2);
		$time_using = microtime_float() - $time_start;
		$program_sql_call_time = $program_sql_call_time + $time_using;
		
		// 性能监控
		$this->perf_add_count($sql, $time_using);
				
		return $lists;

	}

}

/*
$db = new db_base();
echo $db->get_dbname(rand(1, 1000000), "shenghuo_") . "\n";
echo $db->get_tblname(rand(1, 1000000), "shenghuo_") . "\n";
*/
