<?php

namespace console\modules\twitter\components;

use console\components\Logger;

class Errors
{
    use ErrorsTreit;

    private $_errors = [
        188 => 'removeMalwareTweet'
    ];

    private $_code = 0;
    private $_error;

    public function errorTweetPost($model, $tweeting)
    {
        $this->processResponse($tweeting->getResult());

        if(isset($this->_errors[$this->getErrorCode()]) && method_exists($this, $this->_errors[$this->getErrorCode()])) {
            $this->_errors[$this->getErrorCode()]($model);
        } else {
            $this->unknownError($model);
        }

        Logger::error($tweeting->getResult(), $model->getTask(), 'daemons/tweeting/tweets', 'errorPostTweet');
    }

    public function getErrorCode()
    {
        return $this->_code;
    }

    public function getErrorMessage()
    {
        return $this->_error;
    }

    public function processResponse($response)
    {
        if(isset($response['errors'][0])) {
            if(isset($response['errors'][0]['code']))
                $this->_code = $response['errors'][0]['code'];

            if(isset($response['errors'][0]['code']))
                $this->_error = $response['errors'][0]['message'];
            else
                $this->_error = 'Unknown response from twitter';
        }
    }
}