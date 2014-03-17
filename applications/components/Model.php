<?php

namespace app\components;

class Model extends \yii\base\Model
{
    public function getError()
    {
        if (parent::hasErrors()) {
            return current(current(parent::getErrors()));
        } else {
            return '';
        }
    }
} 