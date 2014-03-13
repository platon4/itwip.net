<?php

$params = array_merge(
	require(__DIR__ . '/params.php'),
	require(__DIR__ . '/params-local.php')
);

return [
	'basePath' => dirname(__DIR__),
	'name' => 'iTwip - Applications',
	'sourceLanguage' => 'en_US',
	'language' => 'ru',
	'charset' => 'UTF-8',
	'import' => [
		'application.models.*',
		'application.components.*',
	],
	'defaultController' => 'index',
	'modules' => [
		'twitter',
	],
	'components' => [
		'cache' => [
			'class' => 'system.caching.CFileCache',
		],
		'redis' => [
			'class' => 'application.extensions.redis.extRedis',
			'server' => [
				'host' => '127.0.0.1',
				'port' => 6379,
			],
		],
		'authManager' => [
			'class' => 'PhpAuthManager',
			'defaultRoles' => ['guest'],
		],
		'errorHandler' => [
			'errorAction' => 'main/error',
		],
		'urlManager' => [
			'urlFormat' => 'path',
			'rules' => [
				'/' => 'main/index',
			],
			'showScriptName' => false,
		],
		'log' => [
			'class' => 'CLogRouter',
			'routes' => [],
		],
	],
	'params' => $params,
];