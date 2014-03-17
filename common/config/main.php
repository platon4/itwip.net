<?php
$rootDir = __DIR__ . '/../..';

$params = array_merge(
    require($rootDir . '/common/config/params.php'),
    require($rootDir . '/common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'homeUrl'    => 'http://www.itwip.net/',
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
    'components' => [
        'db'    => $params['components.db'],
        'cache' => $params['components.cache'],
        'mail'  => $params['components.mail'],
        'redis' => $params['components.redis'],
    ]
];