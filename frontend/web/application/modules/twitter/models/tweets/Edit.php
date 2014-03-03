<?php

namespace twitter\models\tweets;

use Yii;
use twitter\components\validators\TweetEdit;

class Edit extends \FormModel
{
    protected $validator;
    public $id;
    public $tweet;
    public $_key;

    public function rules()
    {
        return [
            ['id', 'numerical', 'integerOnly' => true, 'allowEmpty' => false],
            ['_key', 'ext.validators.hashValidator', 'min' => 10, 'max' => 15],
            ['tweet', 'safe']
        ];
    }

    public function afterValidate()
    {
        $tw = new TweetEdit();

        $this->initTweets();

        $tw->validate($this->tweet);
        if($tw->allowNext()) {
            $this->refactoring($tw);

            Yii::app()->db->createCommand("UPDATE {{twitter_tweetsRoster}} SET tweet=:tweet,tweet_hash=:tweet_hash,_url=:url,_url_hash=:url_hash,_indexes=:indexes,_info=:info,_placement=:next WHERE id=:id")
                ->execute(array(
                    ':id'         => $this->id,
                    ':tweet'      => $this->tweet,
                    ':tweet_hash' => $tw->getTweetHash(),
                    ':url'        => $tw->getUrl(),
                    ':url_hash'   => $tw->getUrlHash(),
                    ':indexes'    => $tw->getIndexes(),
                    ':info'       => $tw->getErrors(true),
                    ':next'       => $tw->allowNext()
                ));

            Yii::app()->redis->delete($this->_key . ':counts');
        } else {
            foreach($tw->getErrors() as $error) {
                if(isset($error[0]['replace']))
                    $replace = array($error[0]['replace']['key'] => $error[0]['replace']['value']);
                else
                    $replace = array();

                $this->addError('tweet', Yii::t('twitterModule.tweets', $error[0]['text'], $replace));

                return;
            }
        }
    }

    protected function refactoring($model)
    {
        $row = Yii::app()->db->createCommand("SELECT tweet_hash, _url_Hash FROM {{twitter_tweetsRoster}} WHERE id=:id")->queryRow(true, [':id' => $this->id]);
    }

    public function initTweets()
    {

    }
}
