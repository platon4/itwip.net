<?php

namespace twitter\components;

use Yii;

abstract class ValidatorModel
{
	public $indexes = array();
	public $patternUrl = "#(?:(https?|http)://)?(?:www\\.)?([a-z0-9-]+\.(com|ru|net|org|mil|edu|arpa|gov|biz|info|aero|inc|name|tv|mobi|com.ua|am|me|md|kg|kiev.ua|com.ua|in.ua|com.ua|org.ua|[a-z_-]{2,12}))(([^ \"'>\r\n\t]*)?)?#i";
	public $tweetLength = 140;
	public $lengthHttps = 23;
	public $lengthHttp = 22;
	protected $tweet;
	protected $urls;
	protected $urlCount = 0;
	protected $_words = array();
	protected $_adultWords = array();
	protected $_censorWords = array();
	protected $tweetHash;
	public $scenario;
	public $id;

	public function beforeValidate()
	{
		$this->extractUrls();
		$this->setTweetHash();

		return true;
	}

	public function UniqueTweet($attribute)
	{
		if(Yii::app()->redis->hExists('UniqueTweet:' . Yii::app()->user->id, $this->getTweetHash()))
			$this->addError($attribute, array('text' => '_errors_uniq_tweet'));
		else
			Yii::app()->redis->hSet('UniqueTweet:' . Yii::app()->user->id, $this->getTweetHash(), $this->tweet);
	}

	public function UniqueUrl($attribute)
	{
		if($this->urlCount == 1) {
			if(Yii::app()->redis->hExists('UniqueUrl:' . Yii::app()->user->id, $this->getUrlHash()))
				$this->addError($attribute, array('text' => '_errors_uniq_url', 'replace' => array('key' => '{url}', 'value' => $this->urls)));
			else
				Yii::app()->redis->hSet('UniqueUrl:' . Yii::app()->user->id, $this->getUrlHash(), $this->getUrl());
		}
	}

	public function linkFilter($attribute)
	{
		if($this->urlCount > 1)
			$this->addError($attribute, array('text' => '_errors_links_limited'));
	}

	public function TweetCharacter($attribute)
	{
		if($this->tweetLength() > $this->tweetLength)
			$this->addError($attribute, array('text' => '_errors_symbols_limited'));
	}

	public function domainFilter($attribute)
	{
		if($this->urlCount) {
			$foundMatch = array();

			if(is_array($this->urls)) {
				foreach($this->urls as $url) {
					$domain = \CHelper::_getDomen($url);

					if(Yii::app()->redis->hExists(md5('domainsBlackList'), $domain))
						$foundMatch[] = $domain;
				}
			}
			else {
				$domain = \CHelper::_getDomen($this->urls);

				if(Yii::app()->redis->hExists(md5('domainsBlackList'), $domain))
					$foundMatch[] = $domain;
			}

			if($foundMatch !== array())
				$this->addError($attribute, array('text' => '_errors_domen_blocked', 'replace' => array('key' => '{domens}', 'value' => implode(", ", $foundMatch))));
		}
	}

	public function censorFilter($attribute)
	{
		$strMatch = array();

		foreach($this->_censorWords() as $word) {
			if(preg_match("#(^|\b|\s|\<br \/\>|\#)" . $word . "#iu", $this->tweet))
				$strMatch[] = $word;
		}

		if($strMatch !== array())
			$this->addError($attribute, array('text' => '_errors_censor_match', 'replace' => array('key' => '{words}', 'value' => implode(', ', $strMatch))));
	}

	public function adultFilter($attribute)
	{
		$strMatch = array();

		foreach($this->_adultWords() as $word) {
			if(preg_match("#(^|\b|\s|\<br \/\>|\#)" . $word . "#iu", $this->tweet))
				$strMatch[] = $word;
		}

		if($strMatch !== array())
			$this->addError($attribute, array('text' => '_errors_adult_match', 'replace' => array('key' => '{words}', 'value' => implode(', ', $strMatch))));
	}

	public function wordsFilter($attribute)
	{
		$strMatch = array();

		foreach($this->_words() as $word) {
			if(preg_match("#(^|\b|\s|\<br \/\>|\#)" . $word . "#iu", $this->tweet))
				$strMatch[] = $word;
		}

		if($strMatch !== array())
			$this->addError($attribute, array('text' => '_errors_filter_match', 'replace' => array('key' => '{words}', 'value' => implode(', ', $strMatch))));
	}

	public function exceededHash($attribute)
	{
		preg_match_all('/#[^\s]*/i', $this->tweet, $matches);

		if(isset($matches) && count($matches[0]) > Yii::app()->params['twitter']['tweets']['hashCount'])
			$this->addError($attribute, array('text' => '_errors_hash_limited'));
	}

	public function references($attribute)
	{
		preg_match_all('/@[^\s]*/i', $this->tweet, $matches);

		if(isset($matches) && count($matches[0]) > 1)
			$this->addError($attribute, array('text' => '_errors_references_limited'));
	}

	public function tweetLength()
	{
		$urlsLentgh = 0;
		$lentgh     = 0;

		if($this->urlCount) {
			if(is_array($this->urls)) {
				foreach($this->urls as $url) {
					$urlsLentgh += $this->stringLength($url);

					if(substr($url, 0, 8) === 'https://')
						$lentgh += $this->lengthHttps;
					else
						$lentgh += $this->lengthHttp;
				}
			}
			else {
				if(substr($this->urls, 0, 8) === 'https://')
					$lentgh += $this->lengthHttps;
				else
					$lentgh += $this->lengthHttp;

				$urlsLentgh += $this->stringLength($this->urls);
			}
		}

		return (($this->stringLength($this->tweet) - $urlsLentgh) + $lentgh);
	}

	public function stringLength($str)
	{
		return iconv_strlen($str, "utf-8");
	}

	protected function extractUrls()
	{
		preg_match_all($this->patternUrl, strtolower($this->tweet), $urls);

		if(!\CHelper::isEmpty($urls[0])) {
			$this->urlCount = count($urls[0]);

			if($this->urlCount > 1) {
				$this->urls = array();

				foreach($urls[0] as $url) {
					$this->urls[] = trim($url);
				}
			}
			else {
				$this->urls = trim($urls[0][0]);
				$this->setUrl($this->urls);
			}

			$this->tweet = str_replace($this->urls, "", $this->tweet);
		}
	}

	protected function _words()
	{
		if($this->_words === array())
			$this->_words = Yii::app()->redis->lRange('BlockedWordsList', 0, -1);

		return $this->_words;
	}

	protected function _adultWords()
	{
		if($this->_adultWords === array())
			$this->_adultWords = Yii::app()->redis->lRange('adultWordsList', 0, -1);

		return $this->_adultWords;
	}

	protected function _censorWords()
	{
		if($this->_censorWords === array())
			$this->_censorWords = Yii::app()->redis->lRange('censorWordsList', 0, -1);

		return $this->_censorWords;
	}

	protected function setTweetHash()
	{
		$this->tweetHash = md5($this->tweet);
	}
}
