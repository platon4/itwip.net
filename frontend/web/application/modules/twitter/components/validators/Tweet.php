<?php

namespace twitter\components\validators;

use Yii;

abstract class Tweet
{
    protected $hash;
    protected $config = [
        'patternUrl'    => "#(?:(https?|http)://)?(?:www\\.)?([a-z0-9-]+\.(com|ru|net|org|mil|edu|arpa|gov|biz|info|aero|inc|name|tv|mobi|com.ua|am|me|md|kg|kiev.ua|com.ua|in.ua|com.ua|org.ua|[a-z_-]{2,12}))(([^ \"'>\r\n\t]*)?)?#i",
        'tweetLength'   => 140,
        'lengthHttps'   => 23,
        'lengthHttp'    => 22,
        'hashTagsCount' => 3
    ];

    /*
     * Список валидаторов
     * @var array
     */
    protected $_validators = [
        'unique-tweet',
        'unique-url',
        'links',
        'character',
        'domain',
        'hash-tags',
        'references',
        'censor',
        'politics'
    ];

    /*
     * Список валидаторов которые которые допускаются к размещению
     */
    protected $skipPassValidators = [
        'politics',
        'unique-url'
    ];

    protected $tweets = [];
    protected $_errors = [];

    protected $url;
    protected $urlHash;
    protected $tweet;
    protected $_filters;

    public function __construct($hash, $config = [])
    {
        $this->preInit();
        $this->hash = $hash;
        $this->config = array_merge($this->config, $config);
    }

    public function getFilters($key)
    {
        if(!isset($this->_filter[$key])) {
            if(Yii::app()->redis->exists('twitter:filters:' . $key) === false) {
                $rows = Yii::app()->db->createCommand("SELECT * FROM {{twitter_tweetsFilters}} WHERE _type=:type")->queryAll(true, [':type' => $key]);

                $this->_filter[$key] = [];

                if(!empty($rows)) {
                    foreach($rows as $row)
                        $this->_filter[$key][$row['_key']] = $row['reason'];

                    Yii::app()->redis->hMset('twitter:filters:' . $key, $this->_filter[$key]);
                }
            } else {
                $this->_filter[$key] = Yii::app()->redis->hGetAll('twitter:filters:' . $key);
            }
        }

        return $this->_filter[$key];
    }

    public function validate($tweet)
    {
        $this->_clear();
        $this->tweet = trim($tweet);

        if($this->beforeValidate()) {
            foreach($this->getValidators() as $attribute => $validator)
                $this->$validator($attribute);
        }

        return !$this->hasErrors();
    }

    public function addError($attribute, $error)
    {
        $this->_errors[$attribute] = $error;
    }

    public function hasErrors($attribute = null)
    {
        if($attribute === null)
            return $this->_errors !== array();
        else
            return isset($this->_errors[$attribute]);
    }

    public function _clear()
    {
        $this->_errors = [];
        $this->tweet = null;
        $this->urls = null;
        $this->urlCount = null;
        $this->urlHash = null;
        $this->url = null;
        $this->tweetHash = null;
    }

    public function preInit()
    {
        Yii::app()->redis->delete(['twitter:validators:url:' . Yii::app()->user->id, 'twitter:validators:tweets:' . Yii::app()->user->id]);
        Yii::app()->redis->set('twitter:collection:run:' . Yii::app()->user->id, true);
        Yii::app()->redis->expire('twitter:collection:run:' . Yii::app()->user->id, 60 * 60);
    }

    public function afterInit()
    {
        Yii::app()->redis->delete(['twitter:collection:run:' . Yii::app()->user->id]);
    }

    public function getErrors()
    {
        if($this->hasErrors())
            return $this->_errors;
        else
            return null;
    }

    public function getValidators()
    {
        $methods = [];

        foreach($this->_validators as $key) {
            $validateName = 'validate' . str_replace(' ', '', ucwords(implode(' ', explode('-', $key))));
            if(method_exists($this, $validateName))
                $methods[$key] = $validateName;
            else
                throw new \CException(Yii::t('yii', '{method} has an invalid validation method.', ['{method}' => $validateName]));
        }

        return $methods;
    }

    public function setValidators($validators)
    {
        if(is_array($validators) && !\CHelper::isEmpty($validators))
            $this->_validators = $validators;
        else
            throw new \CException(Yii::t('yii', '{class} has an invalid validators list.', array('{class}' => get_class($this))));
    }

    public function getHash()
    {
        if($this->tweetHash === null) {
            $this->tweetHash = md5($this->tweet);
        }

        return $this->tweetHash;
    }

    public function getUrl()
    {
        if($this->url === null && $this->urlCount === 1)
            $this->url = current($this->urls);

        return $this->url;
    }

    public function getUrlHash()
    {
        if($this->getUrl() !== null && $this->urlCount === 1)
            return md5($this->getUrl());

        return '';
    }

    public function allowNext()
    {
        if($this->hasErrors()) {
            foreach($this->getErrors() as $index => $v) {
                if(!in_array($index, $this->skipPassValidators))
                    return 0;
            }
        }

        return 1;
    }

    /*
     * Извлекаем все ссылки из твита
     */
    protected function extractUrls()
    {
        preg_match_all($this->config['patternUrl'], strtolower($this->tweet), $urls);

        if(!\CHelper::isEmpty($urls[0])) {
            $this->urlCount = count($urls[0]);

            if($this->urlCount) {
                foreach($urls[0] as $url)
                    $this->urls[] = trim($url);
            }

            $this->tweet = str_replace($this->urls, "", $this->tweet);
        }
    }

    public function getIndexes()
    {
        $keys = '';
        if($this->hasErrors())
            $keys = implode(',', array_keys($this->getErrors()));

        return $keys;
    }

    public function getAllIndexes()
    {
        return $this->_validators;
    }

    public function getInfo()
    {
        if($this->hasErrors())
            return json_encode($this->getErrors());

        return '';
    }

    /*
     * Обработка твита до валидации
     */
    protected function beforeValidate()
    {
        $this->extractUrls();

        return true;
    }

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

    /*
     * Проверка на коло-го ссылок в твите
     */
    protected function validateLinks($attribute)
    {
        if($this->urlCount > 1)
            $this->addError($attribute, array());
    }

    /*
     * Проверка коло-го символов в твите
     */
    protected function validateCharacter($attribute)
    {
        $urlsLentgh = 0;
        $lentgh = 0;

        if($this->urlCount) {
            if(is_array($this->urls)) {
                foreach($this->urls as $url) {
                    $urlsLentgh += \CHelper::strlen($url);

                    if(substr($url, 0, 8) === 'https://')
                        $lentgh += $this->config['lengthHttps'];
                    else
                        $lentgh += $this->config['lengthHttp'];
                }
            } else {
                if(substr($this->urls, 0, 8) === 'https://')
                    $lentgh += $this->config['lengthHttps'];
                else
                    $lentgh += $this->config['lengthHttp'];

                $urlsLentgh += \CHelper::strlen($this->urls);
            }
        }

        $count = ((\CHelper::strlen($this->tweet) - $urlsLentgh) + $lentgh);

        if($count > $this->config['tweetLength'])
            $this->addError($attribute, array());

        return $count;
    }

    /*
     * Проверяем если твит содержит ссылки, если содержит, проверяем если ссылки не забанены
     */
    protected function validateDomain($attribute)
    {
        if($this->urlCount) {
            $foundMatch = array();
            $this->getFilters('domains');

            if(is_array($this->urls)) {
                foreach($this->urls as $url) {
                    $domain = \CHelper::_getDomen($url);

                    if(Yii::app()->redis->hExists('twitter:filters:domain', $domain))
                        $foundMatch[] = $domain;
                }
            } else {
                $domain = \CHelper::_getDomen($this->urls);

                if(Yii::app()->redis->hExists('twitter:filters:domain', $domain))
                    $foundMatch[] = $domain;
            }

            if($foundMatch !== array())
                $this->addError($attribute, array('replace' => array('{domens}' => implode(", ", $foundMatch))));
        }
    }

    /*
     * Проверяем коло-го хэштегов
     */
    protected function validateHashTags($attribute)
    {
        preg_match_all('/#[^\s]*/i', $this->tweet, $matches);

        if(isset($matches) && count($matches[0]) > $this->config['hashTagsCount'])
            $this->addError($attribute, array());
    }

    /*
     * Проверяем если есть упоминаний в твите, и не превышает коло-го
     */
    protected function validateReferences($attribute)
    {
        preg_match_all('/@[^\s]*/i', $this->tweet, $matches);

        if(isset($matches) && count($matches[0]) > 1)
            $this->addError($attribute, array());
    }

    /*
     * Проверяем твит на запрешеные слова
     */
    protected function validateCensor($attribute)
    {
        $strMatch = array();

        foreach($this->getFilters('censor') as $word => $reason) {
            if(preg_match("#(^|\b|\s|\<br \/\>|\#)" . $word . "#iu", $this->tweet))
                $strMatch[] = $word;
        }

        if($strMatch !== array())
            $this->addError($attribute, array('replace' => array('{words}' => implode(', ', $strMatch))));
    }

    /*
     * Проверяем твит на запрешеные слова
     */
    protected function validatePolitics($attribute)
    {
        $strMatch = array();

        foreach($this->getFilters('politics') as $word) {
            if(preg_match("#(^|\b|\s|\<br \/\>|\#)" . $word . "#iu", $this->tweet))
                $strMatch[] = $word;
        }

        if($strMatch !== array())
            $this->addError($attribute, array('replace' => array('{words}' => implode(', ', $strMatch))));
    }
}