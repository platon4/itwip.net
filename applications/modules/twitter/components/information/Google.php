<?php

namespace app\modules\twitter\components\information;

use Yii;
use common\helpers\Url;

class Google
{
    public static function stringToNumber($string, $check, $magic)
    {
        $int32 = 4294967296; // 2^32
        $length = strlen($string);

        for ($i = 0; $i < $length; $i++) {
            $check *= $magic;

            if ($check >= $int32) {
                $check = ($check - $int32 * (int)($check / $int32));
                //if the check less than -2^31
                $check = ($check < -($int32 / 2)) ? ($check + $int32) : $check;
            }
            $check += ord($string{$i});
        }
        return $check;
    }

    public static function createHash($string)
    {
        $check1 = self::stringToNumber($string, 0x1505, 0x21);
        $check2 = self::stringToNumber($string, 0, 0x1003F);

        $factor = 4;
        $halfFactor = $factor / 2;

        $check1 >>= $halfFactor;
        $check1 = (($check1 >> $factor) & 0x3FFFFC0) | ($check1 & 0x3F);
        $check1 = (($check1 >> $factor) & 0x3FFC00) | ($check1 & 0x3FF);
        $check1 = (($check1 >> $factor) & 0x3C000) | ($check1 & 0x3FFF);

        $calc1 = (((($check1 & 0x3C0) << $factor) | ($check1 & 0x3C)) << $halfFactor) | ($check2 & 0xF0F);
        $calc2 = (((($check1 & 0xFFFFC000) << $factor) | ($check1 & 0x3C00)) << 0xA) | ($check2 & 0xF0F0000);

        return ($calc1 | $calc2);
    }

    public static function checkHash($hashNumber)
    {
        $check = 0;
        $flag = 0;

        $hashString = sprintf('%u', $hashNumber);
        $length = strlen($hashString);

        for ($i = $length - 1; $i >= 0; $i--) {
            $r = $hashString{$i};
            if (1 === ($flag % 2)) {
                $r += $r;
                $r = (int)($r / 10) + ($r % 10);
            }
            $check += $r;
            $flag++;
        }

        $check %= 10;
        if (0 !== $check) {
            $check = 10 - $check;
            if (1 === ($flag % 2)) {
                if (1 === ($check % 2)) {
                    $check += 9;
                }
                $check >>= 1;
            }
        }

        return '7' . $check . $hashString;
    }

    public static function getPR($url)
    {
        if (strpos($url, 'http://') === false && strpos($url, 'https://') === false)
            $url = 'http://' . $url;

        $request = Url::get("http://toolbarqueries.google.com/tbr?client=navclient-auto&ch=" . self::checkHash(self::createHash($url)) . "&features=Rank&q=info:" . $url . "&num=100&filter=0");
        $html = $request['response'];

        if ($request['code'] == 200) {
            $pos = strpos($html, "Rank_");

            if ($pos !== false)
                return substr($html, $pos + 9);

            return 0;
        }

        return false;
    }

    public static function twitterGetPR($screen_name)
    {
        return self::getPR('https://twitter.com/' . $screen_name);
    }
} 