<?php

$params = array_merge(
	require dirname(__FILE__) . '/params.php',
	require dirname(__FILE__) . '/params-local.php'
);

return array(
	'basePath' => APP_DIR,
	'name' => 'Cron',
	'sourceLanguage' => 'en_US',
	'language' => 'ru',
	'charset' => 'UTF-8',
	'import' => array(
		'application.models.*',
		'application.components.*',
	),
	'components' => array(
		'cache' => array(
			'class' => 'system.caching.CFileCache',
		),
		'email' => array(
			'class' => 'application.extensions.email.Email',
			'delivery' => 'php',
		),
		'db' => [
			'class' => 'system.db.CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'tablePrefix' => '',
			'schemaCachingDuration' => 86400,
		]
	),
	'params' => $params,
);
