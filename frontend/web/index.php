<?php
/*
=====================================================
 Copyright (c) iTwip.net 2013
=====================================================
*/

date_default_timezone_set('Europe/Moscow');

define('YII_DEBUG', true);

require_once(dirname(__DIR__) . '/framework/yii.php');
require_once(dirname(__DIR__) . '/config/aliases.php');
require_once(dirname(__DIR__) . '/components/ArrayHelper.php');

$config = ArrayHelper::merge(
	require(dirname(__DIR__) . '/config/main.php'),
	require(dirname(__DIR__) . '/config/main-local.php')
);

Yii::createWebApplication($config)->run();
