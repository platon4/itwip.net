<?php

namespace twitter\components\validators;

use Yii;

class TweetCollection extends Tweet
{
    public function preInit()
    {
        Yii::app()->redis->delete(['twitter:validators:url:' . Yii::app()->user->id, 'twitter:validators:tweets:' . Yii::app()->user->id]);
        Yii::app()->redis->set('twitter:collection:run:' . Yii::app()->user->id, true);
        Yii::app()->redis->expire('twitter:collection:run:' . Yii::app()->user->id, 60 * 60);
    }
} 