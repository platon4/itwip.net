<?php

namespace console\modules\twitter\components\tweeting;

use console\modules\twitter\components\Tweeting;

class Errors
{
    public function __construct($task, $tweeting = null)
    {
        if($tweeting !== null && $tweeting instanceof Tweeting) {
            echo 'Error 1' . PHP_EOL;
        } else {
            echo 'Error 1' . PHP_EOL;
        }
    }
} 