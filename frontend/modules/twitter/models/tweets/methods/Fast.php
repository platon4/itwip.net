<?php

namespace twitter\models\tweets\methods;

use Yii;

/**
 * Class Fast
 * @package twitter\models\tweets\methods
 */
class Fast extends \FormModel
{
    public $_time;
    public $_tid;
    public $dropdown = [
        5  => ['txt' => '12 часов, цена: 5 руб.', 'price' => 5, 'time' => 12],
        10 => ['txt' => '9 часов, цена: 10 руб.', 'price' => 10, 'time' => 9],
        20 => ['txt' => '6 часов, цена: 20 руб.', 'price' => 20, 'time' => 6],
        40 => ['txt' => '3 часа, цена: 40 руб.', 'price' => 40, 'time' => 3],
        65 => ['txt' => '1 час, цена: 65 руб.', 'price' => 65, 'time' => 1],
    ];

    public $urlsExclude = [];
    public $pay = 'now';
    protected $uCount;
    protected $prices = [1 => 65, 3 => 40, 6 => 20, 9 => 10, 12 => 5];
    protected $orderID = 0;
    protected $sum;
    protected $count = 0;
    protected $taksRows = [];

    public function rules()
    {
        return [
            ['_tid', 'safe', 'on' => 'method'],
            ['urlsCount', 'urlsCount', 'on' => 'method'],

            ['_tid', 'ext.validators.hashValidator', 'min' => 7, 'max' => 20, 'on' => 'urls,order'],

            ['pay', 'in', 'range' => ['now', 'later'], 'allowEmpty' => false, 'on' => 'order'],
            ['urlsExclude', 'urlsExcludeValidate', 'on' => 'order'],
            ['_time', 'timeValidate', 'allowEmpty' => false, 'on' => 'order'],
            ['order', 'orderProcess', 'on' => 'order']
        ];
    }

    public function attributeLabels()
    {
        return [
            'pay' => 'Способ оплаты',
        ];
    }

    public function afterValidate()
    {
        $this->pay = $this->pay == 'now' ? 1 : 0;
    }

    public function timeValidate()
    {
        if(!array_key_exists($this->_time, $this->dropdown))
            $this->addError('order', 'Выбраный вами время индексаций, отсутствует в списке.');
    }

    public function urlsExcludeValidate()
    {
        if($this->urlsExclude !== array()) {
            foreach($this->urlsExclude as $key => $url) {
                if(!\CHelper::int($url))
                    unset($this->urlsExclude[$key]);
            }
        }
    }

    public function getTimeList()
    {
        $list = [];

        foreach($this->dropdown as $key => $value)
            $list[$key] = $value['txt'];

        return $list;
    }

    public function urlsCount()
    {
        if($this->uCount() <= 0)
            $this->addError('urlsCount', 'Чтоб воспользоватся выбранным способом, вам необходимо добавить хотя бы 1 уникальную ссылку.');
    }

    public function uCount()
    {
        if($this->uCount === null) {
            $this->uCount = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_tweetsRoster}} WHERE _key=:_key AND _placement=1 AND FIND_IN_SET('unique-url', _indexes)=0 AND _url IS NOT NULL")->queryScalar([':_key' => $this->_tid]);
        }

        return $this->uCount;
    }

    public function getUrls()
    {
        $urls = Yii::app()->db->createCommand("SELECT id,_url FROM {{twitter_tweetsRoster}} WHERE _key=:_key AND _placement=1 AND FIND_IN_SET('unique-url', _indexes)=0 AND _url IS NOT NULL")->queryAll(true, [':_key' => $this->_tid]);

        return $urls;
    }

    public function getPrices()
    {
        return $this->prices;
    }

    public function getViewFile()
    {
        return '/tweets/order/_fastMethod';
    }

    public function getRedirectUrl()
    {
        return '/twitter/orders/status';
    }

    public function getSum()
    {
        return $this->getCount() * $this->dropdown[$this->_time]['price'];
    }

    public function getPrice()
    {
        return $this->dropdown[$this->_time]['price'];
    }

    public function getCount()
    {
        return $this->count;
    }

    public function orderProcess()
    {
        if($this->urlsExclude !== array())
            $rows = Yii::app()->db->createCommand("SELECT * FROM {{twitter_tweetsRoster}} WHERE _key=:_key AND _placement=1 AND NOT id IN('" . implode("', '", $this->urlsExclude) . "') AND FIND_IN_SET('unique-url', _indexes)=0 AND _url IS NOT NULL")->queryAll(true, [':_key' => $this->_tid]);
        else
            $rows = Yii::app()->db->createCommand("SELECT * FROM {{twitter_tweetsRoster}} WHERE _key=:_key AND _placement=1 AND FIND_IN_SET('unique-url', _indexes)=0 AND _url IS NOT NULL")->queryAll(true, [':_key' => $this->_tid]);

        if($rows !== false) {
            foreach($rows as $row) {
                $this->count++;
                $this->taksRows[] = [
                    $this->_tid,
                    $row['tweet_hash'],
                    $row['_url'],
                    $row['_url_Hash'],
                    $this->getPrice() - 1,
                    $this->getPrice(),
                    0,
                    $row['tweet'],
                    json_encode(['time' => $this->_time])
                ];
            }
        } else
            $this->addError('order', 'test');
    }

    protected function getTaksRows()
    {
        return $this->taksRows;
    }

    /*
     * Создание заказа
     */
    public function create()
    {
        $db = Yii::app()->db;

        try {
            $t = $db->beginTransaction(); // Запускаем транзакцию

            /*
             * Создаем заказ
             */
            $db->createCommand("INSERT INTO {{twitter_orders}} (owner_id,type_order,order_hash,create_date,status,payment_type,_params) VALUES (:owner,:type_order,:order_hash,:create_date,:status,0,:_params)")
                ->execute([
                    ':owner'         => Yii::app()->user->id,
                    ':type_order'    => 'indexes',
                    ':order_hash'    => $this->_tid,
                    ':create_date'   => date('Y-m-d H:i:s'),
                    ':status'        => $this->pay,
                    ':_params'       => json_encode(['time' => $this->_time])
                ]);

            $order_id = $db->lastInsertId;

            /*
             * Проверяем если пользователь хочет оплатить заказ сразу, если да то списаваем с баланса
             */
            if($this->pay === 1) {
                /*
                 * Проверяем если у пользователя достаточно средств на балансе, если нет, выводим ошибку и отменяем транзакцию
                 */
                if(!\Finance::payment($this->getSum(), Yii::app()->user->id, 0, 0, $order_id)) {
                    $this->addError('order', Yii::t('twitterModule.tweets', '_errors_order_no_money', array('{typeBalance}' => '')));
                    $t->rollBack();

                    return false;
                }
            }

            \CHelper::batchInsert('twitter_ordersPerform', [
                'order_hash',
                'hash',
                'url',
                'url_hash',
                'cost',
                'return_amount',
                'status',
                'tweet',
                '_params'
            ], $this->getTaksRows()); // Добавляем список заданий для работа

            $t->commit(); // Завершаем транзакцию

            return true;
        } catch(Exception $e) {
            $this->addError('order', Yii::t('twitterModule.orders', '_orders_create_system_error'));
            $t->rollBack();

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
