<?php

/**
 * Description of Logs
 *
 * @author Александр
 */
class Logs
{
	public static function save($name, $string, $dir = false, $w = 'wb+')
	{
		$patch = Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . 'logs';

		if(!is_dir($patch)) {
			@mkdir($patch, 0777);
			@chmod($patch, 0777);
		}

		if($dir)
			$patch .= DIRECTORY_SEPARATOR . $dir;

		if(!is_dir($patch)) {
			@mkdir($patch, 0777);
			@chmod($patch, 0777);
		}

		$logFile = $name . '-' . date("d-m-Y") . '.txt';

		$fp = fopen($patch . DIRECTORY_SEPARATOR . $logFile, $w);
		fwrite($fp, $string);
		fclose($fp);
	}
}
