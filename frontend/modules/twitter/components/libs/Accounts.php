<?php

namespace twitter\components\libs;

use Yii;

class Accounts
{
    /*
     * @var integer
     */
    protected $accounts;
    protected $_load;
    protected $_errors = [];

    public function __construct($accounts)
    {
        if(\CHelper::isEmpty($accounts))
            throw new \CException(Yii::t('yii', '{component} invalid initial ids.', ['{component}' => get_class($this)]));

        $this->accounts = $accounts;
        $this->load();
    }

    protected function load()
    {
        if($this->_load === null) {
            if(is_array($this->accounts)) {
                $ids = [];
                $accountsRows = Yii::app()->redis->hmGet('twitterAccounts', $this->accounts);

                foreach($this->accounts as $id) {
                    if(isset($accountsRows[$id]) && $accountsRows[$id] !== false) {
                        $this->_load[$id] = unserialize($accountsRows[$id]);
                    } else
                        $ids[] = $id;
                }

                if(is_array($ids) && $ids !== array()) {
                    $accounts = Yii::app()->db->createCommand("SELECT tw.*,st.working_in,st.fast_posting,st.allow_retweet,st.allow_following,st._subjects,st._gender,st._age,st._price,st._timeout,st._stop,st.filterWords,st._allow_bonus_pay FROM {{tw_accounts}} tw INNER JOIN {{tw_accounts_settings}} st ON tw.id=st.tid WHERE tw.id IN('" . implode("', '", $ids) . "')")->queryAll();
                    $cache = [];

                    foreach($accounts as $row) {
                        $this->_load[$row['id']] = $row;
                        $cache[$row['id']] = serialize($row);
                    }

                    if($cache !== array()) {
                        Yii::app()->redis->hmSet('twitterAccounts', $cache);
                    }
                }
            } else {
                $cache = Yii::app()->redis->hGet('twitterAccounts', $this->accounts);

                if($cache !== false && !\CHelper::isEmpty($cache)) {
                    $this->_load[$this->accounts] = unserialize($cache);
                } else {
                    $row = Yii::app()->db->createCommand("SELECT tw.*,st.working_in,st.fast_posting,st.allow_retweet,st.allow_following,st._subjects,st._gender,st._age,st._price,st._timeout,st._stop,st.filterWords,st._allow_bonus_pay FROM {{tw_accounts}} tw INNER JOIN {{tw_accounts_settings}} st ON tw.id=st.tid WHERE tw.id=:id")->queryRow(true, [':id' => $this->accounts]);
                    $this->_load[$this->accounts] = $row;

                    if($row !== false)
                        Yii::app()->redis->hSet('twitterAccounts', $this->accounts, serialize($row));
                }
            }
        }

        return $this->_load;
    }

    public function get($key, $id = 0)
    {
        if($id)
            $data = isset($this->load()[$id]) ? $this->load()[$id] : [];
        else if(!$id && !is_array($this->accounts))
            $data = isset($this->load()[$this->accounts]) ? $this->load()[$this->accounts] : [];
        else
            throw new \CException(Yii::t('yii', '{component} invalid params "id" in method get().', ['{component}' => get_class($this)]));

        return isset($data[$key]) ? $data[$key] : [];
    }

    public function getAll($id = 0)
    {
        if($id)
            return isset($this->load()[$id]) ? $this->load()[$id] : [];
        else if(!$id && !is_array($this->accounts))
            return isset($this->load()[$this->accounts]) ? $this->load()[$this->accounts] : [];
        elseif(is_array($this->accounts))
            return $this->load();
        else
            throw new \CException(Yii::t('yii', '{component} invalid params "id" in method getAll().', ['{component}' => get_class($this)]));
    }

    public function tweetPasses($tweet, $id = 0)
    {
        if($this->personalFilter()) {
            $words = explode(",", $this->get('_filter', $id));

            if($words !== array()) {
                $strMatch = array();

                foreach($words as $word) {
                    if(trim($word) != '' && preg_match("#(^|\b|\s|\<br \/\>|\#)" . $word . "#iu", $tweet))
                        $strMatch[] = $word;
                }

                if($strMatch !== array())
                    $this->addError($id, 'Аккаунт <b>' . \Html::encode('"' . $this->get('screen_name', $id) . '"') . '</b> запретил следующие слова для размещение: ' . implode(", ", $strMatch));
            }
        }

        return !$this->hasErrors();
    }

    public function personalFilter($id = 0)
    {
        $id = ($id) ? $id : $this->accounts;

        if($id) {
            if(isset($this->_filter[$id])) {
                return $this->_filter[$id];
            } else {
                $stops = explode(",", $this->get('_stop', $id));

                if(in_array(1, $stops)) {
                    $this->_filter[$id] = true;

                    return $this->_filter[$id];
                }
            }
        }

        return false;
    }

    public function hasErrors()
    {
        return $this->_errors !== array() ? true : false;
    }

    public function getErrors($id = 0)
    {
        if($this->_errors === array())
            return 'System Error';

        reset($this->_errors);

        if(!$id || !isset($this->_errors[$id])) {
            $id = key($this->_errors);
        }

        return $this->_errors[$id] !== array() ? current($this->_errors[$id]) : [];
    }

    public function isLoad()
    {
        return is_array($this->_load) && $this->_load !== array() ? true : false;
    }

    protected function addError($aid, $error = '')
    {
        $this->_errors[$aid][] = $error;
    }

    /*
     * Очищаем память
     */
    public function __destruct()
    {
        $this->_load = null;
        $this->accounts = null;
    }
} 