<?php

namespace console\components;

use Yii;

class Logger
{
    public static function log($message, $category = '', $name = '')
    {
        self::save($name, $message, $params = [], $category);
    }

    public static function error($message, $params = [], $category = '', $name = '')
    {
        self::save($name, $message, $params, $category);
    }

    public static function message($message)
    {
        if(is_string($message)) {
            return (string) $message;
        } else {
            return (string) var_export($message, true);
        }
    }

    public static function save($name, $message, $params, $category)
    {
        $category = 'logs/' . ltrim($category, '/');

        $dirs = explode('/', $category);
        $path = Yii::$app->getRuntimePath();

        foreach($dirs as $k => $dir) {
            $path .= DIRECTORY_SEPARATOR . $dir;
            if(!is_dir($path)) {
                @mkdir($path);
                @chmod($path, 0777);
            }

            if($dir == 'logs')
                unset($dirs[$k]);
        }

        $file = rtrim($path, '/') . DIRECTORY_SEPARATOR . (trim($name) != '' ? $name : implode('-', $dirs)) . '_' . date('Y-m-d H');

        if(!is_string($params))
            $params = !empty($params) ? var_export($params, true) : '';

        $hr = '';
        for($i = 0 ; $i <= 30 ; $i++) {
            $hr .= '-';
        }

        $fp = fopen($file . '.log', 'a+');
        fwrite($fp, self::message($message) . PHP_EOL . $params . PHP_EOL . $hr . PHP_EOL);
        fclose($fp);
    }
}