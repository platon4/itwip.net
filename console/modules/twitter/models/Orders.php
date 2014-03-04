<?php

namespace console\modules\twitter\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

class Orders extends Model
{
    protected $_data;
    protected $_orders = [];
    protected $_order_types = [
        'manual'  => 'console\modules\twitter\models\orders\Manual',
        'indexes' => 'console\modules\twitter\models\orders\Indexes'
    ];

    public function rules()
    {
        return [
            ['order', 'createOrders', 'skipOnEmpty' => FALSE, 'on' => 'create']
        ];
    }

    public function afterValidate()
    {
        if(!$this->hasErrors()) {
            /* Если указан сценарий создание заказа, после успешной валидаций, отсылаем созданый список заказов роботу */
            if($this->getScenario() == 'create')
                $this->makeOrders();
        }
    }

    /*
     * Берем 10 заказов в минуту (100 заказов в 10 мин) заказов для оброботки
     */
    public function createOrders()
    {
        /* Берем список заказов из базы */
        $_data = (new Query())
            ->select('id,owner_id,type_order,order_hash,order_cost,return_amount,payment_type,_params')
            ->from('it_twitter_orders')
            ->where(['and', 'status=:status', 'process_date<=:date'], [':date' => date('Y-m-d'), ':status' => 0])
            ->orderBy(['id' => SORT_ASC])
            ->limit(10)
            ->all();

        /* Если заказы есть обрабатаваем дальше */
        if(is_array($_data) && $_data !== []) {
            $hashs = [];

            /* Создаем список хэшей заказов для дальнейшей выборки заданий заказа */
            foreach($_data as $row)
                $hashs[] = $row['order_hash'];

            /* Выбираем список заданий заказа по хэшу заказа */
            $_tasks = (new Query())
                ->select('id,order_hash,hash,url,url_hash,cost,return_amount,status,_params')
                ->from('it_twitter_ordersPerform')
                ->where(['order_hash' => $hashs, 'status' => 1, 'is_process' => 0])
                ->limit(100)
                ->all();

            foreach($_data as $row) {
                $tasks = [];
                foreach($_tasks as $_t)
                    if($_t['order_hash'] == $row['order_hash'])
                        $tasks[] = $_t;

                if(array_key_exists($row['type_order'], $this->_order_types)) {
                    $this->appendOrder((new $this->_order_types[$row['type_order']]())->create(['order' => $row, 'tasks' => $tasks]));
                }
            }
        }

        /* Если список заказов пуст, добавляем ошибку */
        if(!$this->hasOrders())
            $this->addError('orders', 'Not found order process.');
    }

    /*
     * Добовляем заказ в список
     *
     * @var $data array
     */
    public function appendOrder($data)
    {
        $this->_orders[] = $data;
    }

    /*
     * Проверяем если список заказов не пуст
     *
     * @return boolean
     */
    public function hasOrders()
    {
        return !empty($this->_orders);
    }

    /*
     * Получаем список созданных заказов
     *
     * @return array
     */
    public function getOrders()
    {
        return $this->_orders;
    }

    /*
     * Создаем задания для робота, и отправляем ему
     */
    public function makeOrders()
    {
        print_r($this->getOrders());
    }
}