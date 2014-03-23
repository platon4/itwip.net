<?php

namespace common\helpers;

use Yii;

class String
{
    public static function generateHash()
    {
        if(function_exists('openssl_random_pseudo_bytes')) {

            $stronghash = md5(openssl_random_pseudo_bytes(15));
        } else
            $stronghash = md5(uniqid(mt_rand(), true));

        $salt = str_shuffle('QAZPOIWSXCDERTYUIMNBBVFGHJC' . sha1(str_shuffle("abchefghjkmnpqrstuvwxyz0123456789") . $stronghash));

        $hash = '';

        for($i = 0 ; $i < rand(7, 20) ; $i++) {
            $hash .= $salt{mt_rand(0, 66)};
        }

        return $hash;
    }

    public static function varExport($variable, $return = false)
    {
        if($variable instanceof stdClass) {
            $result = '(object) ' . self::improved_var_export(get_object_vars($variable), true);
        } else if(is_array($variable)) {
            $array = array();
            foreach($variable as $key => $value) {
                $array[] = var_export($key, true) . ' => ' . self::improved_var_export($value, true);
            }
            $result = 'array (' . implode(', ', $array) . ')';
        } else {
            $result = var_export($variable, true);
        }

        if(!$return) {
            print $result;
            return null;
        } else {
            return $result;
        }
    }
} 