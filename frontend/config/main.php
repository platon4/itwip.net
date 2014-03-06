<?php

$params = array_merge(
	require(__DIR__ . '/params.php'),
	require(__DIR__ . '/params-local.php')
);

return [
	'basePath' => dirname(__DIR__),
    'preload' => ['underconstruct'],
	'name' => 'iTwip',
	'homeUrl' => 'https://itwip.net/',
	'sourceLanguage' => 'en_US',
	'language' => 'ru',
	'charset' => 'UTF-8',
	'import' => [
		'application.models.*',
		'application.components.*',
	],
	'defaultController' => 'index',
	'modules' => [
		'accounts',
		'twitter',
		'finance',
		'office',
		'shop',
	],
	'onBeginRequest' => function ($event) {
			$route  = Yii::app()->getRequest()->getPathInfo();
			$module = substr($route, 0, strpos($route, '/'));

			if(Yii::app()->hasModule($module)) {
				$module = Yii::app()->getModule($module);
				if(isset($module->urlRules)) {
					$urlManager = Yii::app()->getUrlManager();
					$urlManager->addRules($module->urlRules);
				}
			}

			return true;
		},
	'components' => [
        'underconstruct' => [
            'class' => 'Underconstruct'
        ],
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
		'email' => [
			'class' => 'application.extensions.email.Email',
			'delivery' => 'php',
		],
		'request' => [
			'class' => 'HttpRequest',
			'noCsrfValidationRoutes' => [
				'^finance/pay/robaresult$',
				'^finance/pay/webmoneyresult$',
				'^finance/pay/success$',
			],
			'enableCookieValidation' => true,
			'enableCsrfValidation' => true,
			'csrfTokenName' => '_token',
		],
		'user' => [
			'allowAutoLogin' => true,
			'class' => 'WebUser',
			'loginUrl' => '/accounts/auth',
		],
		'session' => [
			'cookieMode' => 'allow',
			'cookieParams' => [
				'path' => '/',
				'domain' => '.itwip.net',
				'httpOnly' => true,
			],
		],
		'db' => [
			'class' => 'system.db.CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'tablePrefix' => '',
			'schemaCachingDuration' => 86400,
		],
		/*
		 * База данных сообщества, для вывода блоков с новостями
		 */
		'fdb' => [
			'class' => 'system.db.CDbConnection',
			'connectionString' => 'mysql:host=localhost;dbname=',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => '',
			'charset' => 'utf8',
			'tablePrefix' => '',
			'schemaCachingDuration' => 86400,
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
				'twitter/bwlist' => 'twitter/default/bwlist',
				'twitter/resetParams' => 'twitter/default/resetParams',
				'js/www-lang-core.js' => 'main/jslang',
				'accounts/auth' => 'accounts/default/auth',
				'accounts/lost' => 'accounts/default/lost',
				'accounts/new' => 'accounts/default/new',
				'accounts/created' => 'accounts/default/created',
				'accounts/confirm_change' => 'accounts/default/confirm_change',
				'finance/output' => 'finance/default/output',
				'finance/replenishment' => 'finance/default/replenishment',
				'regulations' => 'main/regulations',
				'help' => 'main/help',
				'incompatibility' => 'main/incompatibility',
				'support' => 'accounts/default/support',

				'ajax/<action:\w+>' => 'ajax/<action>',
				'test/<action:\w+>' => 'test/<action>',
				'<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
				'<module:\w+>' => '<module>',
				'<module:\w+>/<controller:\w+>' => '<module>/<controller>',
				'<controller:\w+>/<action:\w+>' => '<controller>/<action>',
				'<action:\w+>' => 'main/<action>',
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