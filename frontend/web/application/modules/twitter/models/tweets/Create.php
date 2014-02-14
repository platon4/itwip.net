<?php

namespace twitter\models\tweets;

use Yii;
use twitter\components\Validator;

/**
 * Description of TweetsCreate
 *
 * @author eolitich
 */
class Create extends \FormModel
{
	public $_key;
	public $tweets = [];
	protected $tweetsRows = [];
	protected $validator;

	public function rules()
	{
		return array(
			array('_key', 'ext.validators.hashValidator', 'min' => 10, 'max' => 15),
			array('tweets', '_tweets')
		);
	}

	public function beforeValidate()
	{
		Yii::app()->redis->delete(array('Roster:' . Yii::app()->user->id, 'UniqueTweet:' . Yii::app()->user->id, 'UniqueUrl:' . Yii::app()->user->id));

		return true;
	}

	public function afterValidate()
	{
		if(!$this->tweetsAdd())
			$this->addError('_tweets', Yii::t('twitterModule.tweets', '_error_create_tweets_roster'));
	}

	public function _tweets()
	{
		$i = 0;

		if(is_array($this->tweets) AND count($this->tweets)) {
			foreach($this->tweets as $tweet) {
				if(trim($tweet) != '') {
					$t = explode("\n", $tweet);

					foreach($t as $l) {
						if(trim($l) != '') {
							$this->tweetProccess($l);
							$i++;
						}
					}
				}
			}
		}

		if(!$i)
			$this->addError('_tweets', Yii::t('twitterModule.tweets', '_error_no_tweets_add_edit'));
	}

	public function tweetProccess($tweet)
	{
		$validator = $this->loadValidator();

		$validator->validate($tweet);
		$this->tweetsRows[] = [$this->_key, Yii::app()->user->id, $tweet, $validator->getTweetHash(), $validator->getUrl(), $validator->getUrlHash(), $validator->getIndexes(), $validator->getErrors(true), $validator->allowNext(), date('Y-m-d H:i:s')];
	}

	protected function getInsertRows()
	{
		return $this->tweetsRows;
	}

	public function _getKey()
	{
		return $this->_key;
	}

	protected function tweetsAdd()
	{
		if($this->getInsertRows() !== array()) {

			Yii::app()->db->createCommand("DELETE FROM {{tw_tweets_roster}} WHERE owner_id=:owner")->execute(array(':owner' => Yii::app()->user->id));
			\CHelper::batchInsert('tw_tweets_roster', ['_key', 'owner_id', 'tweet', 'tweet_hash', '_url', '_url_hash', '_indexes', '_info', '_placement', '_date'], $this->getInsertRows());
			Yii::app()->redis->hSet('Roster:' . Yii::app()->user->id, $this->_key, $this->_key);

			return true;
		}
		else {
			$this->addError('tweets', Yii::t('twitterModule.tweets', '_invalid_tweets_list_to'));

			return false;
		}
	}

	protected function loadValidator()
	{
		if($this->validator === NULL) {
			$this->validator = new Validator();
		}

		return $this->validator;
	}
}
