<?php

namespace twitter\models\orders;

use Yii;

class Order extends \FormModel
{
	public $id;

	protected $_order;

	public function rules()
	{
		return [
			['id', 'numerical', 'integerOnly' => true, 'allowEmpty' => false],
			['id', 'getOrder'],
			['id', 'payment', 'on' => 'paid']
		];
	}

	/*
	 * Оплата заказа
	 */
	public function payment()
	{
		$order = $this->getOrder();
		$db    = Yii::app()->db;

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
		}
		else {
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
		if($this->_order === NULL) {
			$this->_order = Yii::app()->db->createCommand("SELECT * FROM {{twitter_orders}} WHERE id=:id AND owner_id=:owner")->queryRow(true, [':id' => $this->id, ':owner' => Yii::app()->user->id]);
		}

		if($this->_order === false)
			$this->addError('id', 'Заказ не найден, возможно вы указали неправильный ID, или заказ был удален.');

		return $this->_order;
	}
}