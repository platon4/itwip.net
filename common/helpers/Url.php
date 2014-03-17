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
            if(isset($_SERVER['HTTP_HOST'])) {
                self::$_hostInfo = $http . '://' . $_SERVER['HTTP_HOST'];
            } else {
                self::$_hostInfo = $http . '://' . $_SERVER['SERVER_NAME'];
                $port = $secure ? Yii::$app->request->getSecurePort() : Yii::$app->request->getPort();
                if(($port !== 80 && !$secure) || ($port !== 443 && $secure)) {
                    self::$_hostInfo .= ':' . $port;
                }
            }
        }

        return self::$_hostInfo;
    }
} 