<?php

namespace twitter\components\validators;

use Yii;

class TweetEdit extends Tweet
{
    private $id;

    public function __construct($id, $hash)
    {
        parent::__construct($hash);

        $this->id = $id;
    }

    /*
      * Проверка твита на уникальность
      */
    protected function validateUniqueTweet($attribute)
    {
        if(Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_tweetsRoster}} WHERE id!=:id AND tweet_hash=:hash")->queryScalar([':id' => $this->id, ':hash' => $this->getHash()])) {
            $this->addError($attribute, array());
        }
    }

    /*
     * Проверка если ссылка в твите не повторяется
     */
    protected function validateUniqueUrl($attribute)
    {
        if($this->urlCount === 1 && Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_tweetsRoster}} WHERE id!=:id AND _url_Hash=:hash")->queryScalar([':id' => $this->id, ':hash' => $this->getUrlHash()])) {
            $this->addError($attribute, array('replace' => array('{url}' => $this->getUrl())));
        }
    }
} 