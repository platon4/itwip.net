<?php

namespace console\modules\twitter\models;

use Yii;
use yii\base\Exception;
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
                    $e = new $this->_order_types[$order['type_order']]();
                    $this->appendOrder($e->process($order));
                    $e->clear();
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
     * Проверяем если список обновлений не пуст
     *
     * @return boolean
     */
    public function hasUpdates()
    {
        return !empty($this->_updates);
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
     * Получаем список для обновление заказов и задачи
     *
     * @return array
     */
    public function getUpdates()
    {
        return $this->_updates;
    }

    public function makeTaks()
    {

    }

    public function updateOrders()
    {
        if($this->hasUpdates()) {
            $rows = $this->getUpdates();

            $orderSQL = "UPDATE {{%twitter_orders}}";
            $taskSQL = "UPDATE {{%twitter_ordersPerform}}";

            foreach($rows as $row) {
                if(isset($row['order']) && !empty($row['order'])) {

                    foreach($row['order'] as $order) {
                        $orderSQL .= $this->buildUpdate($order);
                    }
                }

                if(isset($row['task']) && !empty($row['task'])) {
                    foreach($row['task'] as $task) {
                        $taskSQL .= $this->buildUpdate($task);
                    }
                }
            }

            echo $orderSQL . "\n";
            echo $taskSQL . "\n";
        }
    }

    public function buildUpdate($data)
    {

    }

    /*
     * Создаем задания для робота, и отправляем ему
     */
    public function makeOrders()
    {
        if($this->hasOrders() || $this->hasUpdates()) {
            try {
                $t = Yii::$app->db->beginTransaction();

                $this->makeTaks();
                $this->updateOrders();

                $t->commit();
            } catch(Exception $e) {
                $t->rollBack();
            }
        }
    }
}