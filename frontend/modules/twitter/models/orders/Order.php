<?php

namespace twitter\models\orders;

use Yii;

class Order extends \FormModel
{
    public $id;

    protected $_order;
    protected $_message;
    protected $returnAmounrt = [
        'manual'  => [1],
        'indexes' => [0]
    ];

    public function rules()
    {
        return [
            ['id', 'numerical', 'integerOnly' => true, 'allowEmpty' => false],
            ['id', 'getOrder'],
            ['id', 'payment', 'on' => 'paid'],
            ['id', 'removeOrder', 'on' => 'remove']
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
        if($this->_order === null)
            $this->_order = Yii::app()->db->createCommand("SELECT o.*, (SELECT SUM(return_amount) FROM {{twitter_ordersPerform}} WHERE order_hash=o.order_hash) as return_amount FROM {{twitter_orders}} o WHERE id=:id AND owner_id=:owner")->queryRow(true, [':id' => $this->id, ':owner' => Yii::app()->user->id]);

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

    public function removeOrder()
    {
        $row = $this->getOrder();

        if(Yii::app()->redis->exists('orders:in_process:' . $row['id']) === false) {
            try {
                $t = Yii::app()->db->beginTransaction();

                if($this->returnAmounrt > 0 && isset($this->returnAmounrt[$row['type_order']]) && in_array($row['status'], $this->returnAmounrt[$row['type_order']])) {
                    $returnAmount = Yii::app()->db->createCommand("SELECT SUM(return_amount) FROM {{twitter_ordersPerform}} WHERE order_hash=:hash AND status IN('" . implode("', '", $this->returnAmounrt[$row['type_order']]) . "')")->queryScalar([':hash' => $row['order_hash']]);

                    if($returnAmount > 0)
                        \Finance::rePayment($returnAmount, Yii::app()->user->id, $row['payment_type'], 0, $row['id']);
                }

                Yii::app()->db->createCommand("DELETE FROM {{twitter_orders}} WHERE id=:id")->execute([':id' => $row['id']]);
                Yii::app()->db->createCommand("DELETE FROM {{twitter_ordersPerform}} WHERE order_hash=:hash")->execute([':hash' => $row['order_hash']]);

                $this->setMessage('Заказ № ' . $this->id . ' успешно удален.');
                $t->commit();

            } catch(\Exception $e) {
                $this->addError('id', 'Не удалось удалить выбранный вами заказ, пожалуйста попробуйте еще раз.');
                $t->rollBack();
            }
        } else {
            $this->addError('id', 'В данный момент ваш заказ обрабатывается, удаление невозможно.');
        }
    }
}