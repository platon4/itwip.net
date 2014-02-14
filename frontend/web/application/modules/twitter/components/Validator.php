<?php

namespace twitter\components;

use Yii;
use twitter\components\ValidatorModel;

class Validator extends ValidatorModel
{
	private $_errors = array(); // attribute name => array of errors
	private $_validators = array(2, 10, 9, 4, 3, 6, 1, 8, 5, 7);
	private $urlHash;
	private $url;
	protected $notPlacement = array(1, 2, 3, 4, 5, 6, 8, 9, 10);

	/**
	 * @var array list of built-in validators (name=>method)
	 */
	public $builtInValidators = array(
		1 => 'linkFilter',
		2 => 'TweetCharacter',
		3 => 'censorFilter',
		4 => 'adultFilter',
		5 => 'UniqueTweet',
		6 => 'domainFilter',
		7 => 'UniqueUrl',
		8 => 'wordsFilter',
		9 => 'exceededHash',
		10 => 'references'
	);

	public function validate($tweet)
	{
		$this->_clear();
		$this->tweet = trim($tweet);

		if($this->beforeValidate()) {
			foreach($this->getValidators() as $attribute => $validator)
				$this->$validator($attribute);

			return !$this->hasErrors();
		}
		else
			$this->addError(10, array('text' => '_errors_tweet_is_empty'));
	}

	public function addError($attribute, $error)
	{
		$this->_errors[$attribute][] = $error;
		$this->setIndexes($attribute);
	}

	public function hasErrors($attribute = NULL)
	{
		if($attribute === NULL)
			return $this->_errors !== array();
		else
			return isset($this->_errors[$attribute]);
	}

	public function _clear()
	{
		$this->_errors   = [];
		$this->indexes   = [];
		$this->tweet     = NULL;
		$this->urls      = NULL;
		$this->urlCount  = NULL;
		$this->words     = [];
		$this->urlHash   = NULL;
		$this->url       = NULL;
		$this->tweetHash = NULL;
	}

	public function getErrors($json = false)
	{
		if($this->hasErrors()) {
			if($json === true)
				return json_encode($this->_errors);
			else
				return $this->_errors;
		}
		else
			return NULL;
	}

	public function getValidators()
	{
		$methods = array();

		foreach($this->_validators as $key) {
			if(isset($this->builtInValidators[$key]))
				$methods[$key] = $this->builtInValidators[$key];
			else
				throw new \CException(Yii::t('yii', '{method} has an invalid validation method.', array('{method}' => $key)));
		}

		return $methods;
	}

	public function setValidators($validators)
	{
		if($validators !== array())
			$this->_validators = $validators;
		else
			throw new \CException(Yii::t('yii', '{class} has an invalid validators list.', array('{class}' => get_class($this))));
	}

	public function setIndexes($attribute)
	{
		$this->indexes[] = $attribute;
	}

	public function getTweetHash()
	{
		return $this->tweetHash;
	}

	public function getUrl()
	{
		return $this->url;
	}

	public function setUrl($url)
	{
		$this->url     = $url;
		$this->urlHash = md5($url);
		return $this;
	}

	public function getUrlHash()
	{
		return $this->urlHash;
	}

	public function getIndexes($r = false)
	{
		if($r === true)
			return $this->indexes;
		else
			return ($this->indexes !== array()) ? implode(',', $this->indexes) : '';
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function allowNext()
	{
		if(!\CHelper::isEmpty($this->getIndexes(true))) {
			foreach($this->getIndexes(true) as $index) {
				if(in_array($index, $this->notPlacement))
					return 0;
			}
		}

		return 1;
	}
}
