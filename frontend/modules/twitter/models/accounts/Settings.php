<?php

namespace twitter\models\accounts;

use Yii;

class Settings extends \ActiveRecord
{
    public $filter;
    public $subject;

    protected $words;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return '{{tw_accounts_settings}}';
    }

    public function rules()
    {
        return [
            ['_age,filter,_subjects', 'safe'],
            ['working_in, fast_posting, allow_retweet, allow_following,_allow_bonus_pay,in_indexses', 'in', 'range' => [0, 1]],
            ['_gender', 'in', 'range' => [0, 1, 3]],

            ['_age', 'numerical', 'integerOnly' => true],
            ['_price', 'numerical', 'integerOnly' => false, 'min' => \CMoney::_c(1), 'max' => \CMoney::_c(100000), 'tooSmall' => Yii::t('twitterModule.accounts', '_price_is_small', array('{price}' => \CMoney::_c(1, true))), 'tooBig' => Yii::t('twitterModule.accounts', '_price_is_big', array('{price}' => \CMoney::_c(100000, true)))],
            ['_timeout', 'numerical', 'integerOnly' => true, 'min' => Yii::app()->params['twitter']['posting_timeout'], 'max' => Yii::app()->params['twitter']['posting_timeout_max'], 'tooSmall' => Yii::t('twitterModule.accounts', '_timeout_is_small', array('{time}' => Yii::app()->params['twitter']['posting_timeout'])), 'tooBig' => Yii::t('twitterModule.accounts', '_timeout_is_big', array('{time}' => Yii::app()->params['twitter']['posting_timeout_max']))],

            ['words', 'filterWords'],
            ['subject', 'validateSubject']
        ];
    }

    public function attributeLabes()
    {
        return [
            'working_in'      => '"Работать в ручном или автоматическом режиме"',
            'fast_posting'    => '"Участвовать в быстрой индексации"',
            'allow_retweet'   => '"Участвовать в фолловинге"',
            'allow_following' => '"Участвовать в ретвитах"'
        ];
    }

    public function relations()
    {
        return [
            'accounts' => [self::HAS_ONE, 'twitter\models\accounts\Accounts', 'id']
        ];
    }

    public function validateSubject()
    {
        if(is_array($this->subject)) {
            $ids = array();

            foreach($this->subject as $_k) {
                if(in_array($_k, $ids)) {
                    $this->addError('_subject', Yii::t('twitterModule.accounts', '_subject_add_dublicat'));
                }

                $ids[] = $_k;
            }

            if(count($ids) > 1 AND in_array(0, $ids))
                $this->addError('_subject', Yii::t('twitterModule.accounts', '_subject_add_need_select'));

            $this->_subjects = implode(",", $ids);
        }
    }

    public function filterWords()
    {
        $stops = [];
        if(!empty($this->filter) && is_array($this->filter)) {
            $filtres = ['policy', 'personal'];

            if(array_key_exists('personal', $this->filter)) {
                if(trim($this->words) == '' || strlen($this->words) > 150)
                    $this->addError('filterWords', 'Стоп слова отсутствуют, или слишком много символов.');
                else
                    $this->filterWords = $this->words;
            }

            foreach($this->filter as $k => $v) {
                if(in_array($k, $filtres)) {
                    $stops[] = $k;
                }
            }
        }

        if(!$this->hasErrors())
            $this->_stop = implode(',', $stops);
    }
}
