<?php

namespace console\modules\twitter\components;

use console\components\Logger;

class Errors
{
    private $_errors = [

    ];

    public function errorTweetPost($model, $tweeting)
    {
        if(isset($this->_errors[$this->getErrorCode($tweeting)]) && method_exists($this, $this->_errors[$this->getErrorCode($tweeting)])) {
            $this->_errors[$this->getErrorCode($tweeting)]($model, $tweeting);
        } else {
            $this->unknownError($model, $tweeting);
        }

        Logger::error($tweeting->getResult(), $model->getTask(), 'daemons/tweeting/tweets', 'errorPostTweet');
    }

    public function getErrorCode()
    {

    }

    public function getErrorMessage()
    {
        return 0;
    }

    protected function removeTask()
    {

    }

    protected function unknownError($model, $tweeting)
    {

    }
}