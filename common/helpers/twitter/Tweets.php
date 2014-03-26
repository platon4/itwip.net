<?php

namespace common\helpers\twitter;

class Tweets
{
    public static function getUrl($tweet)
    {
        preg_match_all("#(?:(https?|http)://)?(?:www\\.)?([a-z0-9-]+\.(com|ru|net|org|mil|edu|arpa|gov|biz|info|aero|inc|name|tv|mobi|com.ua|am|me|md|kg|kiev.ua|com.ua|in.ua|com.ua|org.ua|[a-z_-]{2,12}))(([^ \"'>\r\n\t]*)?)?#i", strtolower($tweet), $urls);

        if(!empty($urls[0])) {
            $count = count($urls[0]);

            if($count) {
                foreach($urls[0] as $url) {
                    return trim($url);
                }
            }
        }

        return null;
    }
} 