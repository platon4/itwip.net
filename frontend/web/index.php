<?php
/*
=====================================================
 Copyright (c) iTwip.net 2013
=====================================================
*/

date_default_timezone_set('Europe/Moscow');

define('YII_DEBUG', true);

require_once(dirname(__DIR__) . '/../vendor/yiisoft/yii/yii.php');
require_once(__DIR__ . '/application/config/aliases.php');
require_once(__DIR__ . '/application/components/ArrayHelper.php');

$config = ArrayHelper::merge(
	require(__DIR__ . '/application/config/main.php'),
	require(__DIR__ . '/application/config/main-local.php')
);

Yii::createWebApplication($config)->run();
