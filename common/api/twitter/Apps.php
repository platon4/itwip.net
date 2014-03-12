<?php

namespace common\components\twitter;

use Yii;
use yii\base\Exception;

class Apps
{
    private static $apps;

    public static function get($id, $key)
    {
        echo 1;
        die();
        if(self::$apps === null || self::$apps[$id] === null) {
            $app = Yii::$app->redis->get('twitter:apps:' . $id);

            if($app !== false)
                self::$apps[$id] = json_decode($app, true);
            else
                throw new Exception('App id is not found.');
        }

        return isset(self::$apps[$id][$key]) ? self::$apps[$id][$key] : false;
    }

    public static function clearCache()
    {
        Yii::$app->redis->delete(Yii::$app->redis->keys('twitter:apps:*'));

        $apps = (new Query())->from('{{%twitter_apps}}')->all();

        if(!empty($apps)) {
            $cache = [];
            foreach($apps as $app) {
                $cache['twitter:apps:' . $app['id']] = json_encode($app);
            }

            if(!empty($cache))
                Yii::$app->redis->mSet($cache);
        }
    }
} 