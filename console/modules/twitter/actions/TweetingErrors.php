<?php

namespace console\modules\twitter\actions;

class TweetingErrors
{
    public function __construct($task, $tweeting)
    {
        if(is_string($tweeting)) {
            echo $tweeting;
        } else {
            print_r($tweeting->getResult());
        }
    }
} 