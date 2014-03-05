<?php

namespace console\modules\twitter\models;

use Yii;
use yii\base\Model;
use yii\db\Query;

class Orders extends Model
{
    protected $_data;
    protected $_orders = [];
    protected $_updates = [];
    protected $_order_types = [
        'manual'  => 'console\modules\twitter\models\orders\Manual',
        'indexes' => 'console\modules\twitter\models\orders\Indexes'
    ];

    public function rules()
    {
        return [
            ['order', 'createOrders', 'skipOnEmpty' => false, 'on' => 'create']
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
        $query = new Query();

        /* Берем список заказов из базы */
        $orders = $query
            ->select('id,owner_id,type_order,order_hash,order_cost,return_amount,payment_type,_params')
            ->from('{{%twitter_orders}}')
            ->where(['and', 'status=:status', 'process_date<=:date'], [':status' => 0, ':date' => date('Y-m-d')])
            ->orderBy(['id' => SORT_ASC])
            ->limit(10)
            ->all();

        /* Если заказы есть обрабатаваем дальше */
        if(!empty($orders)) {
            foreach($orders as $order) {
                if(array_key_exists($order['type_order'], $this->_order_types)) {
                    $this->appendOrder((new $this->_order_types[$order['type_order']]())->process($order));
                }
            }
        } else
            $this->addError('orders', 'Not found order process.');
    }

    /*
     * Добовляем заказ в список
     *
     * @var $data array
     */
    public function appendOrder($data)
    {
        if(!empty($data['taks']))
            $this->_orders[] = $data['taks'];

        if(!empty($data['update']))
            $this->_updates[] = $data['update'];
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

    public function getUpdates()
    {
        return $this->_updates;
    }

    /*
     * Создаем задания для робота, и отправляем ему
     */
    public function makeOrders()
    {
        if($this->hasOrders()) {
            print_r($this->getOrders());
            print_r($this->getUpdates());
        }
    }
}