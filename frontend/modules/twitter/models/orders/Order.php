<?php

namespace twitter\models\orders;

use Yii;

class Order extends \FormModel
{
    public $id;
    public $s;

    protected $_order;
    protected $_message;
    protected $returnAmount = [
        'manual'  => [0, 1, 2],
        'indexes' => [0]
    ];
    protected $_params;

    public function rules()
    {
        return [
            ['id', 'numerical', 'integerOnly' => true, 'allowEmpty' => false],
            ['id', 'getOrder'],
            ['id', 'payment', 'on' => 'paid'],
            ['id', 'remove', 'on' => 'remove'],
            ['s', 'safe']
        ];
    }

    /*
     * Оплата заказа
     */
    public function payment()
    {
        $order = $this->getOrder();
        $db = Yii::app()->db;

        if($order['status'] == 0) {
            try {
                $t = $db->beginTransaction(); // Запускаем транзакцию

                if(!\Finance::payment($order['return_amount'], Yii::app()->user->id, $order['payment_type'], 0, $order['id'])) {
                    $this->addError('order', Yii::t('twitterModule.tweets', 'У вас недостаточно средств на балансе, для оплаты данного заказа.', array('{typeBalance}' => '')));
                }

                Yii::app()->db->createCommand("UPDATE {{twitter_orders}} SET status=1 WHERE id=:id")->execute([':id' => $order['id']]);

                $t->commit();
            } catch(Exception $e) {
                $this->addError('order', Yii::t('twitterModule.orders', 'Не удалось оплатить заказ, пожалуйста, обратитесь в службу поддержки.')); //Выводим ошибку транзакций
                $t->rollBack(); //Откатывает транзакцию
            }
        } else {
            $this->addError('paid', 'Данный заказ уже оплачен.');
        }
    }

    /*
     * Загружаем данные заказа
     *
     * @return array
     */
    public function getOrder()
    {
        if($this->_order === null) {
            if($this->s == 1)
                $this->_order = Yii::app()->db->createCommand("SELECT t.*, o.id as order_id, o.owner_id, o.type_order, o.payment_type, o.status as order_status FROM {{twitter_ordersPerform}} t INNER JOIN {{twitter_orders}} o ON t.order_hash=o.order_hash WHERE t.id=:id AND o.owner_id=:owner")->queryRow(true, [':owner' => Yii::app()->user->id, ':id' => $this->id]);
            else
                $this->_order = Yii::app()->db->createCommand("SELECT o.*, (SELECT SUM(return_amount) FROM {{twitter_ordersPerform}} WHERE order_hash=o.order_hash) as return_amount FROM {{twitter_orders}} o WHERE id=:id AND owner_id=:owner")->queryRow(true, [':id' => $this->id, ':owner' => Yii::app()->user->id]);
        }

        if($this->_order === false)
            $this->addError('id', 'Заказ не найден, возможно вы указали неправильный ID, или заказ был удален.');

        return $this->_order;
    }

    public function getMessage()
    {
        return $this->_message;
    }

    public function setMessage($msg)
    {
        $this->_message = $msg;
    }

    public function remove()
    {
        if($this->s == 1)
            $this->removeTask();
        else
            $this->removeOrder();
    }

    protected function removeOrder()
    {
        $row = $this->getOrder();

        if(Yii::app()->redis->exists('orders:in_process:0:' . $row['id']) === false) {
            try {
                $t = Yii::app()->db->beginTransaction();

                if($row['status'] > 0 && isset($this->returnAmount[$row['type_order']])) {
                    $returnAmount = Yii::app()->db->createCommand("SELECT SUM(return_amount) FROM {{twitter_ordersPerform}} WHERE order_hash=:hash AND status IN('" . implode("', '", $this->returnAmount[$row['type_order']]) . "')")->queryScalar([':hash' => $row['order_hash']]);

                    if($returnAmount > 0)
                        \Finance::rePayment($returnAmount, Yii::app()->user->id, $row['payment_type'], 0, $row['id']);
                }

                Yii::app()->db->createCommand("DELETE FROM {{twitter_orders}} WHERE id=:id")->execute([':id' => $row['id']]);
                Yii::app()->db->createCommand("DELETE FROM {{twitter_ordersPerform}} WHERE order_hash=:hash")->execute([':hash' => $row['order_hash']]);

                $this->removeFromTweeting($row['id']);

                $this->setCode(200)->setMessage('Заказ № ' . $this->id . ' успешно удален.');
                $t->commit();

            } catch(\Exception $e) {
                $this->addError('id', 'Не удалось удалить выбранный вами заказ, пожалуйста попробуйте еще раз.');
                $t->rollBack();
            }
        } else {
            $this->addError('id', 'В данный момент ваш заказ обрабатывается, удаление невозможно.');
        }
    }

    protected function getParams($key)
    {
        if($this->_params === null) {
            $this->_params = json_decode($this->getOrder()['_params'], true);
        }

        return isset($this->_params[$key]) ? $this->_params[$key] : null;
    }

    protected function removeTask()
    {
        $row = $this->getOrder();

        if(Yii::app()->redis->exists('orders:in_process:1:' . $row['id']) === false) {
            try {
                $t = Yii::app()->db->beginTransaction();

                if($row['order_status'] > 0 && isset($this->returnAmount[$row['type_order']]) && in_array($row['status'], $this->returnAmount[$row['type_order']])) {
                    $twAccountLogin = Yii::app()->db->createCommand("SELECT screen_name FROM {{tw_accounts}} WHERE id=:id")->queryScalar([':id' => $this->getParams('account')]);
                    \Finance::rePayment($row['return_amount'], Yii::app()->user->id, $row['payment_type'], 1, $row['order_id'], $twAccountLogin);
                }

                Yii::app()->db->createCommand("DELETE FROM {{twitter_ordersPerform}} WHERE id=:id")->execute([':id' => $row['id']]);
                $tcount = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_ordersPerform}} WHERE order_hash=:hash")->queryScalar([':hash' => $row['order_hash']]);

                if($tcount <= 0 || $tcount === false) {
                    Yii::app()->db->createCommand("DELETE FROM {{twitter_orders}} WHERE order_hash=:hash")->execute([':hash' => $row['order_hash']]);
                    $this->setCode(199);
                } else {
                    $this->setCode(200)->setMessage('Твит успешно удален.');
                }

                $this->removeFromTweeting($row['id'], 1);
                $t->commit();

            } catch(\Exception $e) {
                $this->addError('id', 'Не удалось удалить выбранный вами заказ, пожалуйста попробуйте еще раз.');
                $t->rollBack();
            }
        } else {
            $this->addError('id', 'В данный момент ваш заказ обрабатывается, удаление невозможно.');
        }
    }

    protected function removeFromTweeting($id, $t = 0)
    {
        Yii::app()->db->createCommand("DELETE FROM {{twitter_tweeting}} WHERE " . ($t === 1 ? 'sbuorder_id' : 'order_id') . "=:id")->execute([':id' => $id]);
    }
}