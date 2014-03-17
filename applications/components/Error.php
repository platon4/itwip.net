<?php

namespace app\components;

use Yii;

class Error
{
    public static function e($name, $message, $url = null, $urlName = null)
    {
        echo Yii::$app->view->render('@app/views/error', ['name' => $name, 'message' => $message, 'url' => $url, 'urlName' => $urlName]);
        exit(-1);
    }
} 