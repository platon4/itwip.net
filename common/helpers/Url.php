<?php

namespace common\helpers;

use Yii;

class Url
{
    public static $_hostInfo;

    public static function getDomen($url)
    {
        if($url == '')
            return;

        $url = str_replace("http://", "", strtolower($url));
        $url = str_replace("https://", "", $url);
        if(substr($url, 0, 4) == 'www.')
            $url = substr($url, 4);

        $url = explode('/', $url);
        $url = reset($url);
        $url = explode(':', $url);
        $url = reset($url);

        return $url;
    }

    public static function getHostUrl()
    {
        if(self::$_hostInfo  === null) {
            $secure = Yii::$app->request->getIsSecureConnection();
            $http = $secure ? 'https' : 'http';

            if(isset($_SERVER['HTTP_APP_IP'])) {
                self::$_hostInfo = $http . '://' . $_SERVER['HTTP_APP_IP'];
            }
        }

        return self::$_hostInfo;
    }
} 