<?php

namespace twitter\models\tweets;

use Yii;
use twitter\components\Validator;

class Edit extends \FormModel
{
	protected $validator;
	public $id;
	public $tweet;
	public $_key;

	public function rules()
	{
		return array(
			array('id', 'numerical', 'integerOnly' => true, 'allowEmpty' => false),
			array('_key,tweet', 'safe')
		);
	}

	public function afterValidate()
	{
		$tw = new Validator();
		$tw->setId($this->id);

		$this->initTweets();

		$tw->validate($this->tweet);

		if($tw->allowNext()) {
			$this->refactoring($tw);

			Yii::app()->db->createCommand("UPDATE {{tw_tweets_roster}} SET tweet=:tweet,tweet_hash=:tweet_hash,_url=:url,_url_hash=:url_hash,_indexes=:indexes,_info=:info,_placement=:next WHERE id=:id")
				->execute(array(
					':id' => $this->id,
					':tweet' => $this->tweet,
					':tweet_hash' => $tw->getTweetHash(),
					':url' => $tw->getUrl(),
					':url_hash' => $tw->getUrlHash(),
					':indexes' => $tw->getIndexes(),
					':info' => $tw->getErrors(true),
					':next' => $tw->allowNext()
				));

			Yii::app()->redis->delete($this->_key . ':counts');
		}
		else {
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
		$row = Yii::app()->db->createCommand("SELECT tweet_hash, _url_Hash FROM {{tw_tweets_roster}} WHERE id=:id")->queryRow(true, [':id' => $this->id]);
		Yii::app()->redis->hDel('UniqueTweet:' . Yii::app()->user->id, $row['tweet_hash']);
		Yii::app()->redis->hDel('UniqueUrl:' . Yii::app()->user->id, $row['_url_Hash']);


	}

	public function initTweets()
	{
		if(!Yii::app()->redis->exists('Roster:' . Yii::app()->user->id . ':' . $this->_key)) {
			Yii::app()->redis->delete(array('Roster:' . Yii::app()->user->id . ':' . $this->_key, 'UniqueTweet:' . Yii::app()->user->id, 'UniqueUrl:' . Yii::app()->user->id));
			$rows = Yii::app()->db->createCommand("SELECT * FROM {{tw_tweets_roster}} WHERE _key=:key")->queryAll(true, [':key' => $this->_key]);

			foreach($rows as $row) {
				$indexes = explode($row['_indexes']);
				if(!in_array(7, $indexes) && Yii::app()->redis->hExists('UniqueTweet:' . Yii::app()->user->id, $row['tweet_hash']))
					Yii::app()->redis->hSet('UniqueTweet:' . Yii::app()->user->id, $row['tweet_hash'], $row['tweet_hash']);

				if(!in_array(5, $indexes) && Yii::app()->redis->hExists('UniqueUrl:' . Yii::app()->user->id, $row['_url_Hash']))
					Yii::app()->redis->hSet('UniqueUrl:' . Yii::app()->user->id, $row['_url_Hash'], $row['_url_Hash']);
			}

			Yii::app()->redis->set('Roster:' . Yii::app()->user->id . ':' . $this->_key, $this->_key);
			Yii::app()->redis->expire('Roster:' . Yii::app()->user->id . ':' . $this->_key, 60 * 60);
			unset($rows);
		}
	}
}
