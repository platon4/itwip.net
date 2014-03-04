<?php

namespace twitter\models\tweets;

use Yii;
use \twitter\components\validators;

class Create extends \FormModel
{
    public $tweets = [];
    public $h;
    protected $hash;
    protected $_tweets = [];

    public function rules()
    {
        return [
            ['h', 'ext.validators.hashValidator', 'min' => 10, 'max' => 15, 'on' => 'recreate'],
            ['tweets', 'preInit'],
            ['tweets', 'makeTweets']
        ];
    }

    public function afterValidate()
    {
        if(!$this->hasErrors()) {
            if(\CHelper::isEmpty($this->getTweets()) || !$this->createRoster())
                $this->addError('_tweets', Yii::t('twitterModule.tweets', '_error_create_tweets_roster'));
        }
    }

    public function preInit()
    {
        if(Yii::app()->redis->exists('twitter:collection:run:' . Yii::app()->user->id))
            $this->addError('tweets', 'В данный момент обрабатывается другой список твитов, пожалуйста дождитесь окончание оброботки.');
    }

    /*
     * Проверяем если список твиттов не пуст
     */
    public function makeTweets()
    {
        if($this->getScenario() === 'recreate') {
            $rows = Yii::app()->db->createCommand("SELECT * FROM {{twitter_tweetsListsRows}} WHERE _hash=:hash")->queryAll(true, [':hash' => $this->h]);

            foreach($rows as $row) {
                if(trim($row['tweet']) != '')
                    $this->pushTweet($row['tweet']);
            }
        } else {
            if(!\CHelper::isEmpty($this->tweets) && count($this->tweets)) {
                foreach($this->tweets as $tweet) {
                    if(trim($tweet) != '') {
                        $t = explode("\n", $tweet);
                        foreach($t as $v) {
                            if(trim($v) != '')
                                $this->pushTweet($v);
                        }
                    }
                }
            } else {
                $this->addError('_tweets', Yii::t('twitterModule.tweets', '_error_no_tweets_add_edit'));
            }
        }
    }

    /*
     * Добавляем твит в общий список твитов на добавление
     */
    public function pushTweet($tweet)
    {
        $this->_tweets[] = $tweet;
    }

    public function getTweets()
    {
        return $this->_tweets;
    }

    public function getHash()
    {
        if($this->hash === null)
            $this->hash = \CHelper::generateID();

        return $this->hash;
    }

    public function makeRows()
    {
        $validator = new validators\TweetCollection($this->getHash());
        $rows = [];

        foreach($this->getTweets() as $tweet) {
            $validator->validate($tweet);

            $rows[] = [
                $this->getHash(),
                Yii::app()->user->id,
                $tweet,
                $validator->getHash(),
                $validator->getUrl(),
                $validator->getUrlHash(),
                $validator->getIndexes(),
                $validator->getInfo(),
                $validator->allowNext(),
                date('Y-m-d')
            ];
        }

        $validator->afterInit();

        return $rows;
    }

    public function createRoster()
    {
        try {
            $t = Yii::app()->db->beginTransaction();

            \CHelper::batchInsert('twitter_tweetsRoster', ['_key', 'owner_id', 'tweet', 'tweet_hash', '_url', '_url_hash', '_indexes', '_info', '_placement', '_date'], $this->makeRows());

            $t->commit();
            return true;
        } catch(Exception $e) {

            $t->rollBack();
            return false;
        }
    }
}
