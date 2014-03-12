<?php

namespace twitter\models;

use Yii;

class Twitter extends \FormModel
{
    public $pageLimits = [
        10 => ['title' => '10', 'value' => '10'],
        20 => ['title' => '20', 'value' => '20'],
        30 => ['title' => '30', 'value' => '30'],
        40 => ['title' => '40', 'value' => '40'],
        50 => ['title' => '50', 'value' => '50']
    ];
    public $id;
    public $ot_itr = 1;
    public $do_itr = 100;
    public $price_post_ot = 1;
    public $price_post_do = 10000;
    public $ya_r_ot = 0;
    public $ya_r_do = 5000000;
    public $googl_rang_ot = 0;
    public $googl_rang_do = 10;
    public $_age;
    public $blogging_topics = [];
    public $age_blog_ot = 1;
    public $age_blog_do = 90;
    public $gender = 0;
    public $in_yandex = 'matter';
    public $in_google = 0;
    public $language_blog = 'matter';
    public $added_system = 'all';
    public $pType = ['manual' => 1, 'auto' => 1];
    public $payMethod = ['rv' => 1, 'bv' => 1];
    public $bw;

    public $_q;

    public $limit = 10;
    public $fbw;
    public $_a = 'DESC';
    public $_o = 'date';

    public $tape;
    public $followers_ot = 500;
    public $followers_do = 5000000;

    protected $_ages;
    protected $_subjects;
    protected $_count;
    protected $_stats;

    /**
     * @var объект класса \CPagination
     */
    protected $_pages;

    protected $filters = [];
    protected $_orders = [
        'date'      => '`tw`.`date_add`',
        'itr'       => '`tw`.`itr`',
        'wlist'     => '`tw`.`whitelisted`',
        'blist'     => '`tw`.`blacklisted`',
        'cpost'     => '`tw`.`_posts_count`',
        'price'     => '`st`.`_price`',
        'tape'      => '`tw`.`tape`',
        'followers' => '`tw`.`followers`',
        'group'     => 'tw._group'
    ];

    protected $pTypes;
    protected $payMethods;
    protected $_where;
    protected $bwList;
    protected $_rowsCount;

    public function rules()
    {
        return [
            ['limit', 'in', 'range' => array_keys($this->pageLimits), 'on' => 'get'],
            ['added_system', 'in', 'range' => ['all', 'today', 'three_days', 'seven_days', 'month'], 'on' => 'get'],
            ['age_blog_ot,_age_blog_do,age,blogging_topics,tape,_age', 'safe', 'on' => 'get'],

            ['language_blog', 'in', 'range' => ['matter', 'ru', 'en'], 'on' => 'get'],

            ['_a', 'in', 'range' => ['DESC', 'ASC'], 'allowEmpty' => FALSE, 'on' => 'get'],
            ['_o', 'in', 'range' => array_keys($this->_orders), 'allowEmpty' => FALSE, 'on' => 'get'],

            /*
             * iTr
             */
            ['ot_itr,do_itr', 'numerical', 'min' => 1, 'max' => 100, 'on' => 'get'],
            ['ot_itr', 'compare', 'compareAttribute' => 'do_itr', 'operator' => '<=', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_itr'), 'on' => 'get'],

            /*
             * Цена твита
             */
            ['price_post_ot,price_post_do', 'numerical', 'min' => 1, 'max' => 10000, 'on' => 'get'],
            ['price_post_ot', 'compare', 'compareAttribute' => 'price_post_do', 'operator' => '<=', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_price_post'), 'on' => 'get'],

            ['pType', 'ConfirmTypeValidate', 'on' => 'get'],
            ['payMethod', 'payMethod', 'on' => 'get'],

            /*
             * Yandex
             */
            ['in_yandex', 'in', 'range' => ['matter', 'yes', 'no'], 'on' => 'get'],
            ['ya_r_ot', 'compare', 'compareAttribute' => 'ya_r_do', 'operator' => '<', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_ya_r'), 'on' => 'get'],

            /*
             * Google
             */
            ['googl_rang_ot,googl_rang_do', 'numerical', 'integerOnly' => TRUE, 'min' => 0, 'max' => 10, 'on' => 'get'],
            ['googl_rang_ot', 'compare', 'compareAttribute' => 'googl_rang_do', 'operator' => '<', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_googl_rang'), 'on' => 'get'],

            /*
             * Возраст
             */
            ['age_blog_ot', 'compare', 'compareAttribute' => 'age_blog_do', 'operator' => '<', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_age_blog'), 'on' => 'get'],

            ['bw', 'in', 'range' => [0, 1, 2], 'message' => 'Параметр "Черно-белый список" указан неправильно.', 'on' => 'get'],

            /*
             * Читатели
             */
            ['followers_ot,followers_do', 'numerical', 'integerOnly' => TRUE, 'min' => 500, 'max' => 99999999999, 'on' => 'get,rows'],
            ['followers_ot', 'compare', 'compareAttribute' => 'followers_do', 'operator' => '<=', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_followers'), 'on' => 'get'],

            ['gender', 'in', 'range' => [0, 1, 2], 'on' => 'get'],

            ['fbw', 'in', 'range' => ['black', 'white'], 'on' => 'get'],
            ['_q', 'length', 'max' => 55, 'on' => 'get'],

            /* Сценари добавление в черно белый список пользователя */
            ['id', 'numerical', 'integerOnly' => TRUE, 'allowEmpty' => FALSE, 'message' => 'Неправильно указан идентификатор запроса.', 'on' => 'bw'],
            ['bw', 'in', 'range' => ['black', 'white'], 'message' => 'Не удалось обработать запрос, пожалуйста попробуйте еще раз.', 'on' => 'bw'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'in_yandex'     => Yii::t('twitterModule.tweets', '_in_yandex'),
            'language_blog' => Yii::t('twitterModule.tweets', '_language_blog'),
            'added_system'  => Yii::t('twitterModule.tweets', '_added_system'),
            'age_blog_ot'   => Yii::t('twitterModule.tweets', '_age_blog'),
            'gender'        => Yii::t('twitterModule.tweets', '_floor_blogger'),
            'ot_itr'        => 'iTr "от"',
            'do_itr'        => 'iTr "до"',
            'price_post_ot' => 'Цена твита "от"',
            'price_post_do' => 'Цена твита "до"',
            'ya_r_ot'       => 'Яндекс авторитет "от"',
            'ya_r_do'       => 'Яндекс авторитет "до"',
            'googl_rang_ot' => 'Google PR "от"',
            'googl_rang_do' => 'Google PR "до"',
            'followers_ot'  => 'Читателей "от"',
            'followers_do'  => 'Читателей "до"',
            '_ping'         => 'Weblogs.Ping',
            'interval'      => 'Интервал',
            '_q'            => 'Поиск твита',
            '_o'            => 'Параметр сортировки',
            '_a'            => 'Параметр сортировки',
            'sDate'         => 'размещать с',
            '_q'            => 'Аккаунт'
        ];
    }

    public function init()
    {
        $this->age_blog_do = (int) round(((time() - strtotime('15.07.2006 00:00:00')) / 86400) / 31);
    }

    /*
     * Проверка способа размещение
     */
    public function ConfirmTypeValidate()
    {
        $type = [];
        if((!isset($this->pType['auto']) || $this->pType['auto'] == 0) && (!isset($this->pType['manual']) || $this->pType['manual'] == 0))
            $this->addError('confirm', Yii::t('twitterModule.tweets', '_no_sustem_confirm'));
        else {
            if(isset($this->pType['manual']) && $this->pType['manual'] == 1)
                $type[] = 0;

            if(isset($this->pType['auto']) && $this->pType['auto'] == 1)
                $type[] = 1;
        }

        $this->pTypes = $type;
    }

    /*
     * Способ оплаты, проверяем выбраные валюты оплаты заказа, и создаем список валют
     *
     * @return array
     */
    public function payMethod()
    {
        $pay = [];
        if(!isset($this->payMethod['rv']) || !isset($this->payMethod['bv'])) {
            $this->addError('payMethod', 'Параметр "Способ оплаты" не указан, или указан неверно.');
        } else if($this->payMethod['rv'] == 0 && $this->payMethod['bv'] == 0) {
            $this->addError('payMethod', 'Пожалуйста укажите способ оплаты.');
        } else {
            if($this->payMethod['rv'] == 1)
                $pay[] = 0;

            if($this->payMethod['bv'] == 1)
                $pay[] = 1;
        }

        $this->payMethods = $pay;
    }

    public function getStat()
    {
        if($this->_stats === NULL)
            $this->_stats = Yii::app()->db->createCommand("SELECT blacklisted, whitelisted FROM {{tw_accounts}} WHERE id=:id")->queryRow(TRUE, [':id' => $this->id]);

        return $this->_stats;
    }

    public function getRows()
    {
        $p = $this->where()['params'];
        $e = NULL;

        if($this->fbw !== NULL) {

            if($this->fbw == 'black') {
                $t = '0';
                $this->_rowsCount = $this->bwList('black');
            } else {
                $t = '1';
                $this->_rowsCount = $this->bwList('white');
            }

            $p[] = 'EXISTS (SELECT `id` FROM {{twitter_bwList}} WHERE tw_id = `tw`.`id` AND _type=' . $t . ' AND owner_id=' . Yii::app()->user->id . ')';
            $e = 'bw';

        }

        $rows = Yii::app()->db->createCommand("SELECT
        `tw`.`id`, `tw`.`screen_name`, `tw`.`name`,
        `tw`.`avatar`, `tw`.`date_add`, `tw`.`itr`,
        `tw`.`followers`, `tw`.`whitelisted`, `tw`.`blacklisted`,
        `tw`.`_posts_count`, `st`.`_price`,
        `tw`.`tape`,`tw`.`in_yandex`,
        (SELECT _type FROM {{twitter_bwList}} WHERE owner_id='" . Yii::app()->user->id . "' AND tw_id=tw.id) as bw
        FROM {{tw_accounts_settings}} st INNER JOIN {{tw_accounts}} tw ON st.tid=tw.id WHERE " . $this->implode(" AND ", $p, $e) . "
        ORDER BY " . $this->_orders[$this->_o] . ' ' . $this->_a . "
        LIMIT " . $this->getPages()->getOffset() . ", " . $this->getPages()->getLimit())
            ->queryAll(TRUE, $this->where()['values']);

        return $rows;
    }

    protected function where()
    {
        if($this->_where === NULL) {
            $fileds = ['`tw`.`_status`=\'1\''];

            if(in_array(0, $this->payMethod))
                $fileds[] = '`tw`.`_group`=\'0\'';

            $values = [];

            $params = [
                'ot_itr'          => ['c' => '`tw`.`itr`', 'w' => '>=', 't' => 'int'], //itr начало
                'do_itr'          => ['c' => '`tw`.`itr`', 'w' => '<=', 't' => 'int'], //itr конец
                'followers_ot'    => ['c' => '`tw`.`followers`', 'w' => '>=', 't' => 'int'], //читатели начало
                'followers_do'    => ['c' => '`tw`.`followers`', 'w' => '<=', 't' => 'int'], //читатели конец
                'price_post_ot'   => ['c' => '`st`.`_price`', 'w' => '>=', 't' => 'int'],
                'price_post_do'   => ['c' => '`st`.`_price`', 'w' => '<=', 't' => 'int'],
                'ya_r_ot'         => ['c' => '`tw`.`yandex_rank`', 'w' => '>=', 't' => 'int'],
                'ya_r_do'         => ['c' => '`tw`.`yandex_rank`', 'w' => '<=', 't' => 'int'],
                'googl_rang_ot'   => ['c' => '`tw`.`google_pr`', 'w' => '>=', 't' => 'int'],
                'googl_rang_do'   => ['c' => '`tw`.`google_pr`', 'w' => '<=', 't' => 'int'],
                'age_blog_ot'     => ['c' => '`tw`.`created_at`', 'w' => '<=', 't' => 'days'],
                'age_blog_do'     => ['c' => '`tw`.`created_at`', 'w' => '>=', 't' => 'days'],
                'blogging_topics' => ['c' => '`st`.`_subjects`', 'w' => '', 't' => 'in'],
                '_age'            => ['c' => '`st`.`_age`', 'w' => '=', 't' => 'int'],
                'gender'          => ['c' => '`st`.`_gender`', 'w' => '=', 't' => 'int'],
                'in_yandex'       => ['c' => '`tw`.`in_yandex`', 'w' => '=', 't' => 'enum'],
                'language_blog'   => ['c' => '`tw`.`_lang`', 'w' => '=', 't' => 'value'],
                'added_system'    => ['c' => '`tw`.`date_add`', 'w' => '>=', 't' => 'days'],
                'pTypes'          => ['c' => '`st`.`working_in`', 'w' => '', 't' => 'in'],
                'payMethods'      => ['c' => '`st`.`_allow_bonus_pay`', 'w' => '', 't' => 'in'],
                'tape'            => ['c' => '`tw`.`tape`', 'w' => '=', 't' => 'int'],
            ];

            if(isset($this->blogging_topics[0]) && $this->blogging_topics[0] == 0)
                $this->blogging_topics = [];

            /*
             * Форматируем запрос к базе
             * array
             */
            foreach(\THelper::setParams(array_merge($this->attributes, ['payMethods' => $this->payMethods, 'pTypes' => $this->pTypes]), $params) as $rows) {
                if(isset($rows['fields']))
                    $fileds[] = $rows['fields'];

                if(isset($rows['values']))
                    $values[':' . $rows['values'][0]] = $rows['values'][1];
            }

            if($this->bw == 1)
                $fileds['bw'][] = 'EXISTS (SELECT `id` FROM {{twitter_bwList}} WHERE tw_id = `tw`.`id` AND _type=1 AND owner_id=' . Yii::app()->user->id . ')';
            elseif($this->bw == 2)
                $fileds['bw'][] = 'NOT EXISTS (SELECT `id` FROM {{twitter_bwList}} WHERE tw_id = `tw`.`id` AND _type=0 AND owner_id=' . Yii::app()->user->id . ')';

            if($this->_q) {
                $fileds[] = "(`tw`.`screen_name` LIKE :account OR `tw`.`name` LIKE :account)";
                $values[':account'] = '%' . $this->_q . '%';
            }

            $this->_where = ['params' => $fileds, 'values' => $values];
        }

        return $this->_where;
    }

    public function getPages()
    {
        if($this->_pages === NULL) {
            $this->_pages = new \CPagination($this->getRowsCount());
            $this->_pages->pageSize = $this->getLimit();
        }

        return $this->_pages;
    }

    public function getRowsCount()
    {
        if($this->_rowsCount === NULL)
            $this->_rowsCount = $this->getCount();

        return $this->_rowsCount;
    }

    public function getCount()
    {
        if($this->_count === NULL)
            $this->_count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{tw_accounts_settings}} st INNER JOIN {{tw_accounts}} tw ON st.tid=tw.id WHERE " . $this->implode(" AND ", $this->where()['params']) . "")->queryScalar($this->where()['values']);

        return $this->_count;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getPageLimits()
    {
        return $this->pageLimits;
    }

    /*
     * @return array
     */
    public function getAges()
    {
        if($this->_ages === NULL)
            $this->_ages = require \Yii::app()->getModulePath() . '/twitter/data/_age.php';

        return $this->_ages;
    }

    /*
     * @return array
     */
    public function getSubjects()
    {
        if($this->_subjects === NULL)
            $this->_subjects = \Html::groupByKey(\Subjects::model()->findALl(array('order' => 'sort')), 'id', '_key', 'parrent');

        return $this->_subjects;
    }

    public function bwList($k)
    {
        if($this->bwList === NULL && $this->getCount()) {
            $p = $this->where()['params'];
            $sql = "SELECT COUNT(*) FROM {{tw_accounts_settings}} st INNER JOIN {{tw_accounts}} tw ON st.tid=tw.id WHERE ";

            $b = Yii::app()->db->createCommand($sql . $this->implode(" AND ", array_merge($p, ['EXISTS (SELECT `id` FROM {{twitter_bwList}} WHERE tw_id = `tw`.`id` AND _type=0 AND owner_id=' . Yii::app()->user->id . ')'])) . "", 'bw')->queryScalar($this->where()['values']);
            $w = Yii::app()->db->createCommand($sql . $this->implode(" AND ", array_merge($p, ['EXISTS (SELECT `id` FROM {{twitter_bwList}} WHERE tw_id = `tw`.`id` AND _type=1 AND owner_id=' . Yii::app()->user->id . ')'])) . "", 'bw')->queryScalar($this->where()['values']);

            $this->bwList = ['black' => $b, 'white' => $w];
        }

        return isset($this->bwList[$k]) ? $this->bwList[$k] : 0;
    }

    protected function implode($s, $data, $exclude = [], $i = 0)
    {
        if(is_array($data)) {
            $str = '';
            foreach($data as $k => $v) {
                if(!\CHelper::isEmpty($exclude)) {
                    if((is_array($exclude) && in_array($k, $exclude) || ($k == $exclude))) {
                        continue;
                    }
                }

                $i++;

                if($i !== 1)
                    $str .= $s;

                if(is_array($v)) {
                    $str .= $this->implode($s, $v, $exclude, 0);
                } else {
                    $str .= $v;
                }
            }

            return $str;
        }

        return '';
    }

    /*
     * Добавление твиттер аккаунта в черный список пользователя
     *
     * @return boolean
     */
    public function bwToggle()
    {
        $columns = NULL;

        try {
            $t = Yii::app()->db->beginTransaction();

            $exe = Yii::app()->db->createCommand("INSERT INTO {{twitter_bwList}} (owner_id,tw_id,_type) VALUES (:owner,:tw,:type) ON DUPLICATE KEY UPDATE _type=:type")->execute([':owner' => Yii::app()->user->id, ':tw' => $this->id, ':type' => ($this->bw == 'white' ? 1 : 0)]);

            if($exe === 1) {
                $columns = $this->bw == 'white' ? 'whitelisted=whitelisted+1' : 'blacklisted=blacklisted+1';
            } else if($exe === 2) {
                $columns = $this->bw == 'white' ? 'whitelisted=whitelisted+1, blacklisted=blacklisted-1' : 'whitelisted=whitelisted-1, blacklisted=blacklisted+1';
            } else {
                $columns = $this->bw == 'white' ? 'whitelisted=whitelisted-1' : 'blacklisted=blacklisted-1';
                Yii::app()->db->createCommand()->delete('{{twitter_bwList}}', 'owner_id=:owner AND tw_id=:tw', [':owner' => Yii::app()->user->id, ':tw' => $this->id]);
            }

            if($columns !== NULL)
                Yii::app()->db->createCommand("UPDATE {{tw_accounts}} SET " . $columns . " WHERE id=:id")->execute([':id' => $this->id]);

            $t->commit();
            return TRUE;
        } catch(Exception $e) {
            $this->addError('bw', 'Не удалось обработать запрос, пожалуйста попробуйте еще раз.');
            $t->rollBack();
            return FALSE;
        }
    }
}