<?php

namespace common\helpers;

class Url
{
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
} 