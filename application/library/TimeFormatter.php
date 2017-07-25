<?php
/**
 * 
 * 由base.class.php拆出
 * SPACE 时间值转换类
 *
 */
define("TIME_FORMAT_MINITE", "%s分钟前");
define("TIME_FORMAT_TODAY", "今天 %s");
define("TIME_FORMAT_YESTODAY", "昨天 %s");
define("TIME_FORMAT_HISTORY", "%s年%s月%s日");
define("TIME_FORMAT_HISTORY_VISITOR", "%s月%s日");
define('TIME_FORMAT_CAPTION_TODAY','今天');
define('TIME_FORMAT_CAPTION_YESTODAY','昨天');
define('TIME_FORMAT_CAPTION_YEAR','年');
define('TIME_FORMAT_CAPTION_MONTH','月');
define('TIME_FORMAT_CAPTION_DAY','日');
define('TIME_FORMAT_CAPTION_HOUR','点');
define('TIME_FORMAT_CAPTION_MINITE','分');
define('TIME_FORMAT_CAPTION_SECOND','秒');
class TimeFormatter {
	public static function timeFormat($time) {
		$now = time();
		if(($dur = $now - $time) < 3600) {
			$minutes = ceil($dur / 60);
			if ($minutes<=0){
				$minutes = 1;
			}
			$time = sprintf(TIME_FORMAT_MINITE, $minutes);
		}else
		if(date("Ymd", $now) == date("Ymd", $time)) {
			$time = sprintf(TIME_FORMAT_TODAY, date("H:i", $time));
		}else{
			$time = sprintf(TIME_FORMAT_HISTORY, date("Y",$time),date("n",$time),date("j",$time)) . " " . date("H:i",$time);
		}
		return $time;
	}

	public static function timeFormatVisitor($time) {
		$now = time();
		if(($dur = $now - $time) < 3600) {
			$minutes = ceil($dur / 60);
			if ($minutes<=0){
				$minutes = 1;
			}
			$time = sprintf(TIME_FORMAT_MINITE, $minutes);
		}else
		if(date("Ymd", $now) == date("Ymd", $time)) {
			$time = sprintf(TIME_FORMAT_TODAY, date("H:i", $time));
		}else{
			$time = sprintf(TIME_FORMAT_HISTORY_VISITOR, date("n",$time),date("j",$time));
		}
		return $time;
	}

	public static function timeFormatArr($time) {
		$retime = array();
		$now = time();
		if (strpos($time,'-')!==false) {
			$time = strtotime($time);
		}
		if(($dur = $now - $time) < 3600) {
			$minutes = ceil($dur / 60);
			if ($minutes<=0){
				$minutes = 1;
			}
			$retime['date'] = TIME_FORMAT_CAPTION_TODAY;
			$retime['time'] = sprintf(TIME_FORMAT_MINITE, $minutes);
		}else
		if(date("Ymd", $now) == date("Ymd", $time)) {
			$retime['date'] = TIME_FORMAT_CAPTION_TODAY;
			$retime['time'] = date("H:i", $time);
		}else{
			$retime['date'] = date("n", $time).TIME_FORMAT_CAPTION_MONTH.date("j", $time).TIME_FORMAT_CAPTION_DAY;
			$retime['time'] = date("H:i", $time);
		}
		return $retime;
	}
}