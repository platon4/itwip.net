<?php

namespace console\modules\twitter\components;

use yii\base\Exception;

class Tweeting
{
    public function process($task)
    {
        $this->init($task);
    }

    protected function init($task)
    {

    }
}