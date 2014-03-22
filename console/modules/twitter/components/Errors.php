<?php

namespace console\modules\twitter\components;

class Errors
{
    public function __construct()
    {

    }

    public function errorTweetPost($model, $tweeting)
    {
        print_r($tweeting);
    }
} 