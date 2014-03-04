<?php

date_default_timezone_set('Europe/Moscow');

define('HOME_DIR', substr(dirname(__FILE__), 0, -12));

	define('APP_DIR', HOME_DIR . '/application');
	define('SYS_DIR', HOME_DIR . '/system');

$yiic= HOME_DIR . '/system/yiic.php';
$config=dirname(__FILE__).'/config/cron.php';
 
require_once($yiic);