<?php

namespace console\modules\twitter\components;

use console\components\Logger;

class Errors
{
    private $_errors = [

    ];

    public function errorTweetPost($model, $tweeting)
    {

        Logger::error($tweeting->getResult(), $model->getTask(), 'daemon/tweeting/tweets', 'errorPostTweet');
    }

    public function getErrorCode()
    {

    }

    public function getErrorMessage()
    {

    }
} 