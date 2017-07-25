<?php


/**
 * 由base.class.php拆出
 *
 */
class php_to_perl
{
	//php数组转perl hash,生成字符串，需要eval
	//调用方法，直接调用php_array_to_perl_hash($array)
	static function php_array_to_perl_hash($php_array,$in_array=0)
	{

		$return_val = "";
		$return_arr = "";
		if(!is_array($php_array)) return false;
		$return_val =  "{";
		foreach($php_array as $key=> $value)
		{
			$key = str_replace("@","\\@",$key);
				
			if(is_array($value))
			{

				$return_arr[] .= "'{$key}',". php_to_perl::php_array_to_perl_hash($value,1);
			}
			else
			{
				$value = str_replace("@","\\@",$value);
				$value = str_replace("'","",$value);
				$value = str_replace("\"","",$value);
				$return_arr[] .= "'{$key}','{$value}'";

			}
		}

		$return_val .= implode(" , ",$return_arr);
		$return_val .=  "}";

		return $return_val;
	}

}