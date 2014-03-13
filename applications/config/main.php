<?php

$rootDir = __DIR__ . '/../..';

$params = array_merge(
    require($rootDir . '/common/config/params.php'),
    require($rootDir . '/common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'applications',
    'basePath' => dirname(__DIR__),
    'timeZone' => 'Europe/Moscow',
    'defaultRoute' => 'default',
    'modules' => [
        'twitter' => 'app\modules\twitter\Twitter',
    ],
    'components' => [
        'db' => $params['components.db'],
        'cache' => $params['components.cache'],
        'mail' => $params['components.mail'],
        'redis' => $params['components.redis'],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '/' => 'default',
                '<_m:\w+>/<_c:\w+>/<_a:\w+>' => '<_m>/<_c>/<_a>',
            ]
        ]
    ],
    'params' => $params,
];