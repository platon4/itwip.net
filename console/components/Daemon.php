<?php

namespace console\components;

use Yii;
use yii\base\Exception;

class Daemon
{
    public static function processID()
    {
        return getmypid();
    }

    public static function setProcess($name)
    {
        $pid = self::processID();

        if($pid) {
            Yii::$app->redis->set('console:processID:' . $name, $pid);
            Yii::$app->redis->expire('console:processID:' . $name, 60 * 60);
        } else
            throw new Exception('Invalid process ID.');
    }

    public static function isSetProcess($name)
    {
        if(Yii::$app->redis->exists('console:processID:' . $name) === true)
            return true;
        else
            return false;
    }

    public static function isRunning($name)
    {
        $pid = Yii::$app->redis->get('console:processID:' . $name);

        if($pid !== false && posix_kill((int) $pid, 0)) {
            return true;
        } else {
            return false;
        }
    }

    public static function stopDaemon($id, $code = 0, $message = '')
    {
        Yii::$app->redis->delete('console:processID:' . $id);

        if(!empty($message))
            echo $message . "\n";

        exit($code);
    }
}