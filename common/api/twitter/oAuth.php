<?php

namespace common\api\twitter;

class oAuth extends \common\api\twitter\libraries\tmhOAuth
{
    public function __construct($config = [])
    {
        if(!isset($config['ip']))
            $config['ip'] = null;

        parent::__construct($config);
    }

    public static function auth_request($data)
    {

    }
} 