<?php

namespace twitter\models\tweets\methods;

use Yii;
use twitter\components\Twitter;

class Manual extends \FormModel
{
    public $_tid;
    public $rid;
    public $act;
    public $_q;
    public $tweets = [];
    public $ot_itr = 1;
    public $do_itr = 100;
    public $_price_post_ot = 1;
    public $_price_post_do = 10000;
    public $_ya_r_ot = 0;
    public $_ya_r_do = 5000000;
    public $_googl_rang_ot = 0;
    public $_googl_rang_do = 10;
    public $_age;
    public $blogging_topics = [];
    public $_age_blog_ot = 1;
    public $_age_blog_do = 90;
    public $_gender = 0;
    public $_in_yandex = 0;
    public $_in_google = 0;
    public $_language_blog = 0;
    public $_added_system;
    public $pay_method = 0;
    public $pType = ['manual' => 1, 'auto' => 1];
    public $_show_only_white_list;
    public $_not_black_list;
    public $limit = 10;

    public $_a = 'DESC';
    public $_o = 'group';

    public $tape;
    public $followers_ot = 500;
    public $followers_do = 5000000;
    public $pay = 'now';
    public $t = [];
    public $sDate;
    public $_ping = 0;
    public $interval;
    public $accounts = [];

    /**
     * @var количество аккаунтов
     */
    protected $_accountsCount;

    /**
     * @var параметры поиска аккаунтов
     */
    protected $_where;

    /**
     * @var объект класса \CPagination
     */
    protected $_pages;

    protected $_tweetsListCount = 0;
    protected $taksRows = [];
    protected $_orders = [
        'followers' => 'tw.followers',
        'tape'      => 'tw.tape',
        'itr'       => 'tw.itr',
        'yrk'       => 'tw.yandex_rank',
        'gpr'       => 'tw.google_pr',
        'yin'       => 'tw.in_yandex',
        'work'      => 'st.working_in',
        'price'     => 'st._price',
        'group'     => 'tw._group'
    ];

    /*
     * @var integer
     */
    protected $_sum = 0;

    /*
     * @var integer
     */
    protected $_count = 0;

    /*
     * Иницализация фильтра
     */
    public function init()
    {
        if($this->getScenario() == 'method') {
            $row = Yii::app()->db->createCommand("SELECT _filter FROM {{tw_filters}} WHERE id=:tid")->queryRow(true, [':tid' => $this->_tid]);

            $this->attributes = unserialize($row['_filter']);
            $this->_age_blog_do = (int) round(((time() - strtotime('15.07.2006 00:00:00')) / 86400) / 31);

            if(Yii::app()->user->_get('money_amount') < Yii::app()->user->_get('bonus_money'))
                $this->pay_method = 1;
        }
    }

    /*
     * Правила валидаций
     *
     * @return array
     */
    public function rules()
    {
        return [
            /*
             * Сценари method (иницализация фильтра)
             */
            ['_tid', 'safe', 'on' => 'method'],

            ['_tid', 'ext.validators.hashValidator', 'min' => 7, 'max' => 20, 'on' => 'tweets,get,order,rows,save'], //проверяем хэш заказа
            ['rid', 'numerical', 'integerOnly' => true, 'allowEmpty' => false, 'message' => Yii::t('yii', 'Your request is invalid.'), 'on' => 'tweets,save'],
            ['act', 'in', 'range' => ['no_use', 'all'], 'message' => Yii::t('yii', 'Your request is invalid.'), 'on' => 'tweets'],
            ['_q', 'length', 'max' => 140, 'on' => 'tweets'],

            ['tweets', 'tweetsListValidate', 'on' => 'save'],

            /*
             * Сценари rows (список аккаунтов)
             */
            ['limit', 'in', 'range' => [10, 20, 30, 40, 50, 100], 'on' => 'get,rows'],
            ['_gender', 'in', 'range' => [0, 1, 2], 'on' => 'get,rows'],
            ['pay_method', 'in', 'range' => [0, 1], 'message' => Yii::t('twitterModule.tweets', '_no_pay_system'), 'on' => 'get,rows,order'],
            ['_ya_r_ot,_age_blog_ot,_googl_rang_ot,_ya_r_do', 'numerical', 'integerOnly' => true, 'min' => 0, 'tooSmall' => Yii::t('twitterModule.tweets', '_error_numerical_min_0'), 'on' => 'get,rows'],

            /*
             * iTr
             */
            ['ot_itr,do_itr', 'numerical', 'min' => 1, 'max' => 100, 'on' => 'get,rows'],
            ['ot_itr', 'compare', 'compareAttribute' => 'do_itr', 'operator' => '<=', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_itr'), 'on' => 'get,rows'],

            /*
             * Цена твита
             */
            ['_price_post_ot,_price_post_do', 'numerical', 'min' => 1, 'max' => 10000, 'on' => 'get,rows'],
            ['_price_post_ot', 'compare', 'compareAttribute' => '_price_post_do', 'operator' => '<=', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_price_post'), 'on' => 'get,rows'],

            /*
             * Yandex
             */
            ['_in_yandex', 'in', 'range' => ['matter', 'yes', 'no'], 'on' => 'get,rows'],
            ['_ya_r_ot', 'compare', 'compareAttribute' => '_ya_r_do', 'operator' => '<', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_ya_r'), 'on' => 'get,rows'],

            /*
             * Google
             */
            ['_googl_rang_ot,_googl_rang_do', 'numerical', 'integerOnly' => true, 'min' => 0, 'max' => 10, 'on' => 'get,rows'],
            ['_googl_rang_ot', 'compare', 'compareAttribute' => '_googl_rang_do', 'operator' => '<', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_googl_rang'), 'on' => 'get,rows'],

            /*
             * Возраст
             */
            ['_age_blog_ot', 'compare', 'compareAttribute' => '_age_blog_do', 'operator' => '<', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_age_blog'), 'on' => 'get,rows'],

            /*
             * Читатели
             */
            ['followers_ot,followers_do', 'numerical', 'integerOnly' => true, 'min' => 500, 'max' => 99999999999, 'on' => 'get,rows'],
            ['followers_ot', 'compare', 'compareAttribute' => 'followers_do', 'operator' => '<=', 'message' => Yii::t('twitterModule.tweets', '_error_comapare_followers'), 'on' => 'get,rows'],

            ['pType', 'ConfirmTypeValidate', 'on' => 'get,rows'],

            ['_language_blog', 'in', 'range' => ['matter', 'ru', 'en'], 'on' => 'get,rows'],
            ['_added_system', 'in', 'range' => ['all', 'today', 'three_days', 'seven_days', 'month'], 'on' => 'get,rows'],
            ['_age_blog_ot,_age_blog_do,_show_only_white_list,_not_black_list,_age,blogging_topics,tape', 'safe', 'on' => 'get,rows'],

            ['_a', 'in', 'range' => ['DESC', 'ASC'], 'allowEmpty' => false, 'on' => 'rows'],
            ['_o', 'in', 'range' => array_keys($this->_orders), 'allowEmpty' => false, 'on' => 'rows'],

            /*
             * Сценари order (создание заказа)
             */
            ['_ping', 'in', 'range' => [0, 1], 'on' => 'order'],
            ['sDate', 'date', 'format' => 'dd.MM.yyyy'],
            ['t', 'TargetingValidate', 'on' => 'order'],
            ['pay', 'in', 'range' => ['now', 'later'], 'message' => 'Выбранный вами способ оплаты не поддерживается', 'on' => 'order'],
            ['interval', 'numerical', 'min' => 30, 'max' => 1440, 'tooSmall' => 'Интервал слишком маленьки', 'tooBig' => 'Интервал слишком большой', 'on' => 'order'],
            ['accounts', 'AccountsValidate', 'on' => 'order'],
            ['accounts', 'orderValidate', 'on' => 'order']
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            '_in_yandex'     => Yii::t('twitterModule.tweets', '_in_yandex'),
            '_language_blog' => Yii::t('twitterModule.tweets', '_language_blog'),
            '_added_system'  => Yii::t('twitterModule.tweets', '_added_system'),
            '_age_blog_ot'   => Yii::t('twitterModule.tweets', '_age_blog'),
            '_gender'        => Yii::t('twitterModule.tweets', '_floor_blogger'),
            'ot_itr'         => 'iTr "от"',
            'do_itr'         => 'iTr "до"',
            '_price_post_ot' => 'Цена твита "от"',
            '_price_post_do' => 'Цена твита "до"',
            '_ya_r_ot'       => 'Яндекс авторитет "от"',
            '_ya_r_do'       => 'Яндекс авторитет "до"',
            '_googl_rang_ot' => 'Google PR "от"',
            '_googl_rang_do' => 'Google PR "до"',
            'followers_ot'   => 'Читателей "от"',
            'followers_do'   => 'Читателей "до"',
            '_ping'          => 'Weblogs.Ping',
            'interval'       => 'Интервал',
            '_q'             => 'Поиск твита',
            '_o'             => 'Параметр сортировки',
            '_a'             => 'Параметр сортировки',
            'sDate'          => 'размещать с'
        ];
    }

    /*
     * Устанавливаем нужные переменые или запускаем нужную функцию после валидации
     */
    public function afterValidate()
    {
        $this->pay = $this->pay == 'now' ? 1 : 0;

        if($this->getScenario() == 'get') {
            Yii::app()->redis->delete('twitter:o:m:' . $this->_tid . ':tweets');
        }
    }

    /*
     * Валидация выброных аккаунтов
     */
    public function AccountsValidate()
    {
        $ids = [];
        if(!\CHelper::isEmpty($this->accounts) && !is_array($this->accounts)) {
            $accounts = explode(":", $this->accounts);
            if($accounts !== array()) {
                foreach($accounts as $id) {
                    if(\CHelper::int($id))
                        $ids[] = $id;
                }
            }
        }

        if($ids === array())
            $this->addError('accounts', 'Для создание заказа, нужно выбрать хотя бы 1 аккаунт.');
        else
            $this->accounts = $ids;
    }

    /*
     * Валидация временного таргетинга
     */
    public function TargetingValidate()
    {
        if(is_array($this->t) && $this->t !== array()) {
            $d = 0;
            $h = 0;

            foreach($this->t as $day) {
                if(is_array($day)) {
                    foreach($day as $hour) {
                        if(!intval($hour) || $hour < 0 || $hour > 24) {
                            $this->addError('t', 'Параметр временного таргетинга указан неверно.');
                            return false;
                        }
                        $h++;
                    }

                    $d++;
                } else {
                    $this->addError('t', 'Параметр временного таргетинга указан неверно.');
                    return false;
                }
            }

            if($d === 7 && $h === 168)
                $this->t = 'all';
        } else {
            $this->addError('t', 'Чтоб создать заказ, необходимо выбрать хотя бы один день или час в разделе "Настройки временного таргетинга и размещения".');
        }
    }

    /*
     * Проверка способа размещение
     */
    public function ConfirmTypeValidate()
    {
        if($this->pType['auto'] == 0 AND $this->pType['manual'] == 0)
            $this->addError('confirm', Yii::t('twitterModule.tweets', '_no_sustem_confirm'));

        $type = [];
        if($this->pType['manual'] == 1)
            $type[] = 0;

        if($this->pType['auto'] == 1)
            $type[] = 1;

        $this->pType = $type;
    }

    /*
     * Параметры пойска аккаунтов
     *
     * @return array
     */
    public function where()
    {
        if($this->_where === null) {
            $fileds = ['`tw`.`_status`=\'1\''];

            if($this->pay_method == 0)
                $fileds[] = '`tw`.`_group`=\'0\'';

            $values = [];
            $params = [
                'ot_itr'                => ['c' => '`tw`.`itr`', 'w' => '>=', 't' => 'int'], //itr начало
                'do_itr'                => ['c' => '`tw`.`itr`', 'w' => '<=', 't' => 'int'], //itr конец
                'followers_ot'          => ['c' => '`tw`.`followers`', 'w' => '>=', 't' => 'int'], //читатели начало
                'followers_do'          => ['c' => '`tw`.`followers`', 'w' => '<=', 't' => 'int'], //читатели конец
                '_price_post_ot'        => ['c' => '`st`.`_price`', 'w' => '>=', 't' => 'int'],
                '_price_post_do'        => ['c' => '`st`.`_price`', 'w' => '<=', 't' => 'int'],
                '_ya_r_ot'              => ['c' => '`tw`.`yandex_rank`', 'w' => '>=', 't' => 'int'],
                '_ya_r_do'              => ['c' => '`tw`.`yandex_rank`', 'w' => '<=', 't' => 'int'],
                '_googl_rang_ot'        => ['c' => '`tw`.`google_pr`', 'w' => '>=', 't' => 'int'],
                '_googl_rang_do'        => ['c' => '`tw`.`google_pr`', 'w' => '<=', 't' => 'int'],
                '_age_blog_ot'          => ['c' => '`tw`.`created_at`', 'w' => '<=', 't' => 'days'],
                '_age_blog_do'          => ['c' => '`tw`.`created_at`', 'w' => '>=', 't' => 'days'],
                'blogging_topics'       => ['c' => '`st`.`_subjects`', 'w' => '', 't' => 'in'],
                '_age'                  => ['c' => '`st`.`_age`', 'w' => '=', 't' => 'int'],
                '_gender'               => ['c' => '`st`.`_gender`', 'w' => '=', 't' => 'int'],
                '_in_yandex'            => ['c' => '`tw`.`in_yandex`', 'w' => '=', 't' => 'enum'],
                '_language_blog'        => ['c' => '`tw`.`_lang`', 'w' => '=', 't' => 'value'],
                '_added_system'         => ['c' => '`tw`.`date_add`', 'w' => '>=', 't' => 'days'],
                'pType'                 => ['c' => '`st`.`working_in`', 'w' => '', 't' => 'in'],
                'pay_method'            => ['c' => '`st`.`_allow_bonus_pay`', 'w' => '0', 't' => 'not'],
                'tape'                  => ['c' => '`tw`.`tape`', 'w' => '=', 't' => 'int'],

                /*
                 * Черный-Белый список
                 */
                '_show_only_white_list' => ['c' => 'EXISTS (SELECT `id` FROM {{twitter_bwList}} WHERE tw_id = `tw`.`id` AND _type=1 AND owner_id=' . Yii::app()->user->id . ')', 'w' => '=', 't' => 'sql'],
                '_not_black_list'       => ['c' => 'NOT EXISTS (SELECT `id` FROM {{twitter_bwList}} WHERE tw_id = `tw`.`id` AND _type=0 AND owner_id=' . Yii::app()->user->id . ')', 'w' => '=', 't' => 'sql'],
            ];

            if(isset($this->blogging_topics[0]) && $this->blogging_topics[0] == 0)
                $this->blogging_topics = [];

            if($this->_show_only_white_list)
                unset($params['_not_black_list']);

            /*
             * Форматируем запрос к базе
             * array
             */
            foreach(\THelper::setParams($this->attributes, $params) as $rows) {
                if(isset($rows['fields']))
                    $fileds[] = $rows['fields'];

                if(isset($rows['values']))
                    $values[':' . $rows['values'][0]] = $rows['values'][1];
            }

            /*
             * Если какието параметры указаны, форматируем строку запроса
             * string
             */
            if($fileds !== array())
                $where = " WHERE " . implode(" AND ", $fileds) . " ";
            else
                $where = ' ';

            $this->_where = ['where' => $where, 'values' => $values];
        }

        return $this->_where;
    }

    /*
     * Список аккаунтов подходящих по параметрам
     */
    public function getAccounts()
    {
        $accountsRows = Yii::app()->db->createCommand("SELECT st.working_in,st._price,st._stop,tw.id,tw.screen_name,tw.name,tw.avatar,tw.itr,tw.yandex_rank, tw.google_pr,tw.in_yandex,tw.followers,tw.tape
                                                                                        FROM {{tw_accounts_settings}} st INNER JOIN {{tw_accounts}} tw ON st.tid=tw.id
                                                                                            " . $this->where()['where'] . "ORDER BY " . $this->_orders[$this->_o] . ' ' . $this->_a . " LIMIT " . $this->getPages()->getOffset() . ", " . $this->getPages()->getLimit())
            ->queryAll(true, $this->where()['values']);

        $rows = [];
        $keys = Yii::app()->redis->hKeys('twitter:o:m:' . $this->_tid . ':tweets');

        foreach($accountsRows as $row) {
            $row['tweetsCount'] = 0;

            foreach($keys as $h)
                if(substr($h, 0, strlen('tweet:' . $row['id'])) === 'tweet:' . $row['id'])
                    $row['tweetsCount']++;

            $rows[] = $row;
        }

        return $rows;
    }

    /*
     * Количество аккаунтов
     *
     * @return integer
     */
    public function getAccountsCount()
    {
        if($this->_accountsCount === null)
            $this->_accountsCount = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{tw_accounts_settings}} st INNER JOIN {{tw_accounts}} tw ON st.tid=tw.id" . $this->where()['where'] . "")->queryScalar($this->where()['values']);

        return $this->_accountsCount;
    }

    /*
     * Проверяем список сохраненых твитов если ID валидны, если нет, выводим ошибку
     *
     * @return array
     */
    public function tweetsListValidate()
    {
        if(!\CHelper::isEmpty($this->tweets)) {
            foreach($this->tweets as $id) {
                if(!\CHelper::int($id)) {
                    $this->addError('tweets', 'Некорректный список твитов');
                    return false;
                }
            }
        } else {
            $this->tweets = [];
        }
    }

    /*
     * Сохраняем выбранный список твитов в выбраный аккаунт
     *
     * @return boolean
     */
    public function tweetsListSave()
    {
        $this->_tweetsListCount = count($this->tweets);

        if($this->_tweetsListCount > 0) {
            $account = Twitter::accounts($this->rid);
            $rows = Yii::app()->db->createCommand("SELECT id,tweet FROM {{twitter_tweetsRoster}} WHERE _key=:key AND owner_id=:owner AND id IN ('" . implode("', '", $this->tweets) . "')")->queryAll(true, [':key' => $this->_tid, ':owner' => Yii::app()->user->id]);

            if($rows !== array()) {
                $tweets = [];
                foreach($rows as $row) {
                    if($account->tweetPasses($row['tweet'])) {
                        $tweets[] = $row['id'];
                    } else {
                        $this->addError('tweets', $account->getErrors());
                    }
                }

                if($tweets !== array() && !$this->hasErrors()) {
                    Yii::app()->redis->hDelete('twitter:o:m:' . $this->_tid . ':tweets', 'tweet:' . $this->rid);
                    foreach($tweets as $tweetID) {
                        Yii::app()->redis->hSet('twitter:o:m:' . $this->_tid . ':tweets', 'tweet:' . $this->rid . ':' . $tweetID, $tweetID);
                    }
                } else {
                    return false;
                }
            } else {
                $this->addError('tweets', 'Ошибка при сохранение твитов, список не найден, или не верные параметры, пожалуйста попробуйте еще раз.');
                return false;
            }
        } else {
            Yii::app()->redis->hDelete('twitter:o:m:' . $this->_tid . ':tweets', 'tweet:' . $this->rid);
        }

        return true;
    }

    /*
     * Список твитов для выпадающего списка
     *
     * @return array
     */
    public function getTweetsList()
    {
        $tweets = [];
        $fields = ['_key=:key', 'owner_id=:owner', '_placement=1'];
        $values = [':key' => $this->_tid, ':owner' => Yii::app()->user->id];

        if($this->_q !== null) {
            $fields[] = 'tweet LIKE :like';
            $values[':like'] = '%' . $this->_q . '%';
        }

        $rows = Yii::app()->redis->hGetAll('twitter:o:m:' . $this->_tid . ':tweets');
        $sltdc = [];
        $exclude = [];

        if($rows !== false) {
            if(!\CHelper::isEmpty($rows)) {
                foreach($rows as $k => $e) {
                    if(substr($k, 0, strlen('tweet:' . $this->rid)) == 'tweet:' . $this->rid)
                        $sltdc[$k] = $e;

                    $exclude[$k] = $e;
                }
            }
        }

        if($this->act == 'no_use' && $exclude !== array()) {
            $fields[] = 'NOT id IN(\'' . implode("', '", $exclude) . '\')';
        }

        if($sltdc !== array())
            $order = " ORDER BY FIELD(id," . implode(",", $sltdc) . ") DESC";
        else
            $order = '';

        $tweetsRows = Yii::app()->db->createCommand("SELECT * FROM {{twitter_tweetsRoster}} WHERE " . implode(" AND ", $fields) . $order)->queryAll(true, $values);

        foreach($tweetsRows as $row) {
            if(isset($sltdc['tweet:' . $this->rid . ':' . $row['id']]) && $sltdc['tweet:' . $this->rid . ':' . $row['id']] == $row['id'])
                $row['tweet_active'] = 1;
            else
                $row['tweet_active'] = 0;

            $tweets[] = $row;
        }

        return $tweets;
    }

    /*
     * Количество твитов сохраненных в списке
     *
     * @return integer
     */
    public function getTweetsListCount()
    {
        return $this->_tweetsListCount;
    }

    /**
     * Количество аккаунтов на странице
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @return \CPagination (object)
     */
    public function getPages()
    {
        if($this->_pages === null) {
            $this->_pages = new \CPagination($this->getAccountsCount());
            $this->_pages->pageSize = $this->getLimit();
        }

        return $this->_pages;
    }

    /*
     * Файл представления
     */
    public function getViewFile()
    {
        if($this->getScenario() == 'method')
            return '/tweets/order/_manualMethod';
        else
            return $this->getScenario() == 'rows' ? '/tweets/order/_manualAccountsRows' : '/tweets/order/_manualAccounts';
    }

    /*
     * Ссылка для перенаправление пользователя после создание заказа
     */
    public function getRedirectUrl()
    {
        return '/twitter/orders/status';
    }

    /*
     * Вычисляем сумму заказа
     *
     * @return integer
     */
    public function getSum()
    {
        return $this->_sum;
    }

    /*
     * Сумма которая будет списана с баланса пользователя
     *
     * @return integer
     */
    public function getExtractSum()
    {
        return $this->_ping == 1 ? $this->getSum() + ($this->getTaksCount() * 0.5) : $this->getSum();
    }

    /*
     * Создаем параметры заказа и загоняем все в json
     *
     * @return string (json_encode)
     */
    public function getOrderParams()
    {
        return json_encode([
            'targeting' => [
                'interval' => $this->interval,
                't'        => $this->t,
            ],
            'ping'      => $this->_ping
        ]);
    }

    /*
     * Количество заданий в заказе
     *
     * @return integer
     */
    public function getTaksCount()
    {
        return $this->_count;
    }

    /*
     * Дана начало оброботки заказа
     *
     * @return bool|string
     */
    public function getStartDate()
    {
        return $this->sDate === null ? date('Y-m-d') : $this->sDate;
    }

    /*
     * Создание списка заказов, для вставки в базу
     *
     * @retunr array
     */
    public function setTaksRows($accounts, $tweets, $selected)
    {
        if(!$this->hasErrors()) {
            $rows = $accounts->getAll();

            /*
             * Проверяем если есть выбранные твиты по списку, если есть, создаем задания
             */
            if(is_array($selected) && $selected !== array()) {
                foreach($selected as $k => $s) {
                    $acc_id = str_replace(':' . $s, '', str_replace('tweet:', '', $k));
                    $tweet = isset($tweets['tweets'][$s]) ? $tweets['tweets'][$s] : [];

                    if($accounts->get('id', $acc_id) === $acc_id && $tweet !== array()) {
                        $price = $accounts->get('_price', $acc_id);
                        $extract = $this->_ping == 1 ? $price + 0.5 : $price;
                        $this->taksRows[] = [
                            $this->_tid,
                            $tweet['tweet_hash'],
                            $price,
                            $extract,
                            $accounts->get('working_in', $acc_id),
                            $tweet['tweet'],
                            $accounts->get('owner_id', $acc_id),
                            $accounts->get('id', $acc_id),
                        ];

                        foreach($tweets['sort'] as $skey => $sort) {
                            if($sort['id'] === $tweet['id'])
                                $tweets['sort'][$skey]['r']++;
                        }

                        $this->_sum += $price;
                        $this->_count++;
                        if(isset($rows[$acc_id]))
                            unset($rows[$acc_id]);
                    }
                }
            }

            \ArrayHelper::orderBy($tweets['sort'], 'r', SORT_ASC);
            $tweetSortCount = count($tweets['sort']) - 1;

            $c = 0;
            foreach($rows as $row) {
                for($i = $c ; $i <= $tweetSortCount ; $i++) {
                    $tweet = isset($tweets['tweets'][$tweets['sort'][$i]['id']]) ? $tweets['tweets'][$tweets['sort'][$i]['id']] : [];

                    if($tweet !== array()) {
                        $extract = $this->_ping == 1 ? $row['_price'] + 0.5 : $row['_price'];
                        $this->taksRows[] = [
                            $this->_tid,
                            $tweet['tweet_hash'],
                            $row['_price'],
                            $extract,
                            $row['working_in'],
                            $tweet['tweet'],
                            $row['owner_id'],
                            $row['id']
                        ];

                        $this->_sum += $row['_price'];
                        $this->_count++;
                        break;
                    }
                }
                if($c >= $tweetSortCount)
                    $c = 0;
                else
                    $c++;
            }
        }
    }

    public function getTaksRows()
    {
        return $this->taksRows;
    }

    /*
     * Проверка выбраных аккаунтов и твитов
     */
    public function orderValidate()
    {
        $accounts = Twitter::accounts($this->accounts); //Загружаем данные выбраных аккаунтов

        /*
         * Проверяем если удалось загрузить список аккаунтов выбраных пользователям
         */
        if($accounts->isLoad()) {
            $selectedTweets = Yii::app()->redis->hGetAll('twitter:o:m:' . $this->_tid . ':tweets');
            $tweetsRows = Yii::app()->db->createCommand("SELECT id,tweet,tweet_hash FROM {{twitter_tweetsRoster}} WHERE _key=:key AND owner_id=:owner AND _placement=1")->queryAll(true, [':key' => $this->_tid, ':owner' => Yii::app()->user->id]);
            $tweets = [];

            foreach($tweetsRows as $row) {
                $tweets['tweets'][$row['id']] = $row;
                $tweets['sort'][] = ['id' => $row['id'], 'r' => 0];
            }

            $this->setTaksRows($accounts, $tweets, $selectedTweets);
        } else {
            $this->addError('accounts', 'Не удалось создать заказ, так как выбранные вами аккаунты были удалены, или не находятся в работе.');
        }
    }

    /*
     * Создание заказа
     *
     * @return boolean
     */
    public function create()
    {
        $db = Yii::app()->db;

        try {
            $t = $db->beginTransaction(); // Запускаем транзакцию

            /*
             * Создаем заказ
             */
            $db->createCommand("INSERT INTO {{twitter_orders}} (owner_id,type_order,order_hash,process_date,create_date,status,payment_type,_params) VALUES (:owner,:type_order,:order_hash,:process_date,:create_date,:status,:payment,:_params)")
                ->execute([
                    ':owner'        => Yii::app()->user->id,
                    ':type_order'   => 'manual',
                    ':order_hash'   => $this->_tid,
                    ':process_date' => $this->getStartDate(),
                    ':create_date'  => date('Y-m-d H:i:s'),
                    ':status'       => $this->pay,
                    ':payment'      => $this->pay_method,
                    ':_params'      => $this->getOrderParams()
                ]);

            $order_id = $db->lastInsertId;

            /*
             * Проверяем если пользователь хочет оплатить заказ сразу, если да то списаваем с баланса
             */
            if($this->pay === 1) {
                /*
                 * Проверяем если у пользователя достаточно средств на балансе, если нет, выводим ошибку и отменяем транзакцию
                 */
                if(!\Finance::payment($this->getExtractSum(), Yii::app()->user->id, $this->pay_method, 0, $order_id)) {
                    $this->addError('order', Yii::t('twitterModule.tweets', '_errors_order_no_money', array('{typeBalance}' => '')));
                    $t->rollBack();

                    return false;
                }
            }

            \CHelper::batchInsert('twitter_ordersPerform', [
                'order_hash',
                'hash',
                'cost',
                'return_amount',
                'status',
                'tweet',
                'bloger_id',
                'tw_account'
            ], $this->getTaksRows()); // Добавляем список заданий для работа

            $t->commit(); // Завершаем транзакцию

            return true;
        } catch(Exception $e) {
            $this->addError('order', Yii::t('twitterModule.orders', '_orders_create_system_error')); //Выводим ошибку транзакций
            $t->rollBack(); //Откатывает транзакцию

            return false;
        }
    }

    /*
     * Удаляем все не нужные данные, после создание заказа.
     */
    public function clear()
    {
        Yii::app()->db->createCommand("DELETE FROM {{twitter_tweetsRoster}} WHERE _key=:key")->execute([':key' => $this->_tid]); // Удаляем список твитов
    }
}