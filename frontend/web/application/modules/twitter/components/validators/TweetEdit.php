<?php

namespace twitter\components\validators;

use Yii;

class TweetEdit extends Tweet
{
    /*
      * Проверка твита на уникальность
      */
    protected function validateUniqueTweet($attribute)
    {
        if(Yii::app()->redis->hExists('twitter:validators:tweets:' . Yii::app()->user->id, $this->getHash()))
            $this->addError($attribute, array());
        else
            Yii::app()->redis->hSet('twitter:validators:tweets:' . Yii::app()->user->id, $this->getHash(), $this->tweet);
    }

    /*
     * Проверка если ссылка в твите не повторяется
     */
    protected function validateUniqueUrl($attribute)
    {
        if($this->urlCount == 1) {
            if(Yii::app()->redis->hExists('twitter:validators:url:' . Yii::app()->user->id, $this->getUrlHash()))
                $this->addError($attribute, array('replace' => array('{url}' => $this->urls)));
            else
                Yii::app()->redis->hSet('twitter:validators:url:' . Yii::app()->user->id, $this->getUrlHash(), $this->getUrl());
        }
    }
} 