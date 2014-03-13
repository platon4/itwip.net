<?php

namespace common\api\twitter;

use Yii;
use yii\base\Exception;
use yii\db\Query;

class Apps
{
    private static $apps;

    public static function get($id, $key)
    {
        if(self::$apps === null || self::$apps[$id] === null) {
            self::$apps[$id] = self::_get($id, $key);

            if(self::$apps[$id] === false) {
                self::reloadCache();
                self::$apps[$id] = self::_get($id, $key);
            }
        }

        return isset(self::$apps[$id][$key]) ? self::$apps[$id][$key] : false;
    }

    public static function _get($id, $key)
    {
        $app = Yii::$app->redis->get('twitter:apps:' . $id);

        if($app !== false)
            $app = json_decode($app, true);

        return $app;
    }

    public static function reloadCache()
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