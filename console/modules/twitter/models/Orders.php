<?php

namespace console\modules\twitter\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\Query;
use console\components\Logger;

class Orders extends Model
{
    protected $_data;
    protected $_orders = [];
    protected $_updates = [];
    protected $_tasks = [];
    protected $_order_types = [
        'manual'  => 'console\modules\twitter\models\orders\Manual',
        'indexes' => 'console\modules\twitter\models\orders\Indexes'
    ];

    public function __construct()
    {
        parent::__construct();

        register_shutdown_function([$this, 'flush'], true);
    }

    /**
     * Регистрируем запуск скрипта, в избежание двух запусков одновременно
     *
     * в случае ошибки, пометка автоматом удаляется через 2 минуты
     */
    public function registerRun()
    {
        Yii::$app->redis->set('console:run:twitter:orders:create', true, 120);
    }

    public function isRun()
    {
        return Yii::$app->redis->exists('console:run:twitter:orders:create');
    }

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

    /**
     * Берем 15 заказов в минуту (15 * 720 = 10800 заказов в сутку) заказов для оброботки
     */
    public function createOrders()
    {
        if($this->isRun() === false) {
            /** Регистрируем запуск комманды, для избижание 2 запуска */
            $this->registerRun();

            $query = new Query();

            /* Берем список заказов из базы */
            $orders = $query
                ->select('id,owner_id,type_order,order_hash,payment_type,_params')
                ->from('{{%twitter_orders}}')
                ->where(['and', 'status=:status', 'process_date<=:date', 'is_process=0', 'type_order=:type'], [':type' => 'indexes', ':status' => 1, ':date' => date('Y-m-d')])
                ->orderBy(['id' => SORT_ASC])
                ->limit(10)
                ->all();

            /** Если заказы есть обрабатаваем дальше */
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
        } else {
            Logger::log('Double launch commands "twitter/orders/create"', 1);
        }
    }

    /**
     * Добовляем заказ в список
     *
     * @var $data array
     */
    public function appendOrder($data)
    {
        if(isset($data['task']) && !empty($data['task'])) {
            foreach($data['task'] as $task) {
                $this->_orders[] = $task;
            }
        }

        if(isset($data['update']) && !empty($data['update'])) {
            foreach($data['update'] as $key => $update) {
                foreach($update as $k => $v)
                    $this->_updates[$key][$k] = $update;
            }
        }
        $this->_updates[] = $data['update'];
    }

    /**
     * Проверяем если список заказов не пуст
     *
     * @return boolean
     */
    public function hasOrders()
    {
        return !empty($this->_orders);
    }

    /**
     * Проверяем если список обновлений не пуст
     *
     * @return boolean
     */
    public function hasUpdates()
    {
        return !empty($this->_updates);
    }

    /**
     * Получаем список созданных заказов
     *
     * @param string $key
     * @return array
     */
    public function getOrders($key = '')
    {
        if($key == 'columns')
            return ['order_id', 'order_hash', 'sbuorder_id', 'orderType', 'tweet_hash', 'url_hash', 'process_time', 'params', 'daemon'];
        else
            return $this->_orders;
    }

    /**
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
        if($this->hasOrders()) {

            $rows = [];

            foreach($this->getOrders() as $key => $values) {
                foreach($values as $k => $v) {
                    $rows[$key][] = $v;
                }
            }

            Yii::$app->db->createCommand()->batchInsert('{{%twitter_tweeting}}', $this->getOrders('columns'), $rows)->execute();
        }
    }

    public function updateOrders()
    {
        if($this->hasUpdates()) {
            $rows = $this->getUpdates();

            foreach($rows as $row) {
                $this->buildUpdate("{{%twitter_orders}}", $row, 'order');
                $this->buildUpdate("{{%twitter_ordersPerform}}", $row, 'task');
            }
        }
    }

    public function buildUpdate($table, $data, $t)
    {
        if(isset($data[$t]) && !empty($data[$t])) {
            foreach($data[$t] as $id => $fields) {
                if(is_array($fields)) {
                    $columns = [];
                    foreach($fields as $field => $value) {
                        $columns[$field] = $value;
                    }

                    Yii::$app->db->createCommand()->update($table, $columns, ['id' => $id])->execute();
                }
            }
        }
    }

    /**
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
                echo $e;
                $t->rollBack();
            }
        }
    }

    /**
     * Удалить мусор, по завершения выполненой комманды
     */
    public function flush()
    {
        Yii::$app->redis->delete('console:run:twitter:orders:create');
    }
}