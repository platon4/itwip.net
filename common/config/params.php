<?php

Yii::setAlias('common', realpath(__DIR__ . '/../'));
Yii::setAlias('frontend', realpath(__DIR__ . '/../../frontend'));
Yii::setAlias('console', realpath(__DIR__ . '/../../console'));

return [
	'adminEmail' => 'admin@itwip.net',
	'supportEmail' => 'support@itwip.net',
	'components.cache' => [
		'class' => 'yii\caching\FileCache',
	],
	'components.mail' => [
		'class' => 'yii\swiftmailer\Mailer',
		'viewPath' => '@common/mails',
	],
	'components.db' => [
		'class' => 'yii\db\Connection',
		'dsn' => 'mysql:host=localhost;dbname=itwip_prod',
		'username' => 'root',
		'password' => '',
		'charset' => 'utf8',
		'tablePrefix' => 'it_',
	],
	'components.redis' => [
		'class' => 'common\components\Redis',
		'server' => [
			'host' => '127.0.0.1',
			'port' => 6379,
		],
	]
];