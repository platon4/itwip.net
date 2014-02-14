<?php

class CHelper
{

	/**
	 * Пакетная вставка в базу
	 *
	 * @param     $table
	 * @param     $columns
	 * @param     $rows
	 * @param int $breakRow
	 *
	 * @return bool
	 * @throws CDbException
	 */
	public static function batchInsert($table, $columns, $rows, $breakRow = 500)
	{
		if($rows === array()) return false;

		try {
			$values = [];
			$exe    = [];
			$count  = count($rows);
			$i      = 0;
			foreach($rows as $row) {
				$vs = [];
				$i++;

				foreach($row as $k => $value) {
					$exe[':v_' . $i . '_' . $k] = $value;
					$vs[]                       = ':v_' . $i . '_' . $k;
				}

				$values[] = '(' . implode(', ', $vs) . ')';

				if(($i % $breakRow == 0 AND $i > 0) OR $i == $count) {
					Yii::app()->db->createCommand("INSERT INTO {{" . $table . "}} (" . implode(', ', $columns) . ") VALUES " . implode(",", $values))->execute($exe);
					$values = [];
					$exe    = [];
				}
			}

			return true;
		} catch(Exception $e) {
			throw new CDbException(Yii::t('yii', 'CDbCommand failed to execute the SQL statement: {error}',
				array('{error}' => $e->getMessage())), (int)$e->getCode(), $e instanceof PDOException ? $e->errorInfo : NULL);
		}
	}


	/**
	 * @param $int
	 *
	 * @return int
	 */
	public static function int($int)
	{
		if(is_numeric($int))
			return $int;
		else
			return 0;
	}

	public static function _md5($_string)
	{
		$rpl = array(
			'q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p',
			'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l',
			'z', 'x', 'c', 'v', 'b', 'n', 'm', ','
		);

		$_to = array(
			'zq6', 'uw', 'c5', 'q9', 'b', 'n', 'm',
			'z6q', 'v5q', 'h6y', 'y6', 'cv6', 'ygh', 'l6', '9e', 'o', 'p',
			'c6w', 'GSq', 'dxqS6', '5w', 'g', 'Zq', 'j', 'k', 'l',
		);

		$_string = str_replace($rpl, $_to, $_string);
		$_string = md5(base64_encode(sha1($_string)));

		$md5 = md5($_string);

		return $md5;
	}

	public static function _strtolower($string)
	{
		$small = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й',
			'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф',
			'х', 'ч', 'ц', 'ш', 'щ', 'э', 'ю', 'я', 'ы', 'ъ', 'ь',
			'э', 'ю', 'я');
		$large = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й',
			'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф',
			'Х', 'Ч', 'Ц', 'Ш', 'Щ', 'Э', 'Ю', 'Я', 'Ы', 'Ъ', 'Ь',
			'Э', 'Ю', 'Я');

		return str_replace($large, $small, $string);
	}

	public static function _getIP()
	{
		if(!empty($_SERVER['HTTP_CLIENT_IP'])) //check ip from share internet
		{
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) //to check ip is pass from proxy
		{
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			$ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknow';
		}

		return $ip;
	}

	public static function _getBrowse()
	{
		return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
	}

	public static function _getURL($url, $method = "GET", $data = array(), $headers = array(), $cookie = false)
	{
		$curl = curl_init(); // инициализируем cURL

		switch($method) {
		case 'POST':
			break;
		default:

			if(count($data)) {

				$params = array();

				foreach($data as $k => $v) {
					$params[] = $k . '=' . $v;
				}

				$qs   = implode('&', $params);
				$url  = strlen($qs) > 0 ? $url . '?' . $qs : $url;
				$data = array();
			}
			break;
		}

		curl_setopt($curl, CURLOPT_URL, $url);

		if($cookie) {
			curl_setopt($curl, CURLOPT_COOKIEJAR, HOME_DIR . '/application/data/cookie_' . md5(self::_getIP() . self::_getDomen($url)) . '.txt'); //сохранить куки в файл
			curl_setopt($curl, CURLOPT_COOKIEFILE, HOME_DIR . '/application/data/cookie_' . md5(self::_getIP() . self::_getDomen($url)) . '.txt'); //считать куки из файла
		}

		//устанавливаем наш вариат клиента (браузера) и вид ОС
		if(!empty($_SERVER['HTTP_USER_AGENT'])) {
			curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		}
		else {
			curl_setopt($curl, CURLOPT_USERAGENT, "Opera/10.00 (Windows NT 5.1; U; ru) Presto/2.2.0");
		}

		if(count(Yii::app()->params['ips'])) {
			curl_setopt($curl, CURLOPT_INTERFACE, Yii::app()->params['ips'][rand(0, count(Yii::app()->params['ips']) - 1)]);
		}

		//Установите эту опцию в ненулевое значение, если вы хотите, чтобы PHP завершал работу скрыто, если возвращаемый HTTP-код имеет значение выше 300. По умолчанию страница возвращается нормально с игнорированием кода.
		curl_setopt($curl, CURLOPT_FAILONERROR, 1);

		//Максимальное время в секундах, которое вы отводите для работы CURL-функций.
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
		curl_setopt($curl, CURLOPT_TIMEOUT, 30);

		switch($method) {

		case 'GET':
			break;

		case 'POST':
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
			break;

		default:
			curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		}

		//Установите эту опцию в ненулевое значение, если вы хотите, чтобы шапка/header ответа включалась в вывод.
		curl_setopt($curl, CURLOPT_HEADER, 0);

		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // разрешаем редиректы
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		if(count($headers)) {

			$header = array();

			foreach($headers as $k => $v) {
				$header[] = trim($k . ': ' . $v);
			}

			curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		}

		$response = curl_exec($curl); // выполняем запрос и записываем в переменную

		$code       = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$info       = curl_getinfo($curl);
		$error      = curl_error($curl);
		$error_code = curl_errno($curl);

		curl_close($curl); // заканчиваем работу curl

		return array('response'   => $response, 'code' => $code, 'info' => $info, 'error' => $error,
					 'error_code' => $error_code);
	}

	public static function tObject($array, $name = "")
	{
		$object = new stdClass();

		foreach($array as $key => $value) {
			$object->{$key} = $value;
		}

		return $object;
	}

	public static function generateID()
	{
		if(function_exists('openssl_random_pseudo_bytes')) {

			$stronghash = md5(openssl_random_pseudo_bytes(15));
		}
		else
			$stronghash = md5(uniqid(mt_rand(), TRUE));

		$salt = sha1(str_shuffle("abchefghjkmnpqrstuvwxyz0123456789") . $stronghash);
		$hash = '';

		for($i = 0; $i < rand(10, 15); $i++) {
			$hash .= $salt{mt_rand(0, 39)};
		}

		return $hash;
	}

	public static function _gString($start = 0, $end = 32)
	{
		if(function_exists('openssl_random_pseudo_bytes')) {

			$stronghash = md5(openssl_random_pseudo_bytes(15));
		}
		else
			$stronghash = md5(uniqid(mt_rand(), TRUE));

		$salt = str_shuffle("sqblkFrD4U-7LmhBA_JdxfG9j3SanNwe_1KYWHtQzTiuZRPoC_E5V8Ig0vcXOMp26-y");
		$hash = '';

		for($i = 0; $i < rand($start, $end); $i++) {
			$hash .= $salt{mt_rand(0, 66)};
		}

		return $hash;
	}

	public static function validID($id)
	{
		return (preg_match("/^[a-zA-Z0-9]{10,15}+$/", $id)) ? true : false;
	}

	public static function validReferralCode($code)
	{
		return (preg_match("/^[a-zA-Z0-9_-]{5,15}+$/", $code)) ? true : false;
	}

	public static function toUnicode($string)
	{
		if(!mb_check_encoding($string, 'UTF-8') OR !($string === mb_convert_encoding(mb_convert_encoding($string, 'UTF-32', 'UTF-8'), 'UTF-8', 'UTF-32')))
			$string = mb_convert_encoding($string, 'UTF-8', 'Windows-1251');

		return $string;
	}

	public static function _getDomen($url)
	{
		if($url == '')
			return;

		$url = str_replace("http://", "", strtolower($url));
		$url = str_replace("https://", "", $url);
		if(substr($url, 0, 4) == 'www.')
			$url = substr($url, 4);
		$url = explode('/', $url);
		$url = reset($url);
		$url = explode(':', $url);
		$url = reset($url);

		return $url;
	}

	public static function wget($from, $background = true, $command = false)
	{
		$exec = "/usr/bin/wget ";

		if(!$command) {
			if($background)
				$exec .= "-O - -q ";

			$exec .= "$from > /dev/null";
		}
		else
			$exec .= $from;

		if($background)
			$exec .= " &";

		exec($exec, $output);
	}

	public static function createFile($name, $data, $dir = false, $w = false)
	{
		if(!$dir)
			$dir = Yii::app()->getRuntimePath();
		if(!$w)
			$w = 'a+';
		$fp = @fopen($dir . DIRECTORY_SEPARATOR . $name, $w);
		if($data)
			fwrite($fp, $data);
		fclose($fp);
	}

	public static function toICQ($id, $message)
	{
		$message = $id . '||' . $message;
		self::createFile(md5($id . time() . rand(0, 50)), $message, Yii::app()->getBasePath() . '/cron/icq/messages', 'wb+');

		return true;
	}

	public static function removeFile($name, $dir = false)
	{
		if(!$dir)
			$dir = Yii::app()->getRuntimePath();
		@unlink($dir . DIRECTORY_SEPARATOR . $name);
	}

	public static function strlen($str)
	{
		return iconv_strlen($str, "utf-8");
	}

	public static function substr($str, $a, $b)
	{
		return iconv_substr($str, $a, $b, "utf-8");
	}

	public static function isEmpty($value, $trim = false)
	{
		return $value === NULL || $value === array() || $value === '' || $trim && is_scalar($value) && trim($value) === '';
	}
}
