<?php

namespace console\modules\twitter\models\orders;

use Yii;
use yii\base\Model;
use yii\db\Query;

class Manual implements OrdersInterface
{
	use OrdersTrait;

	/*
	 * Обработка заказа
	 */
	protected function process($data)
	{
		$this->_row = [
			'order_id' => $data['order']['id'],
			'order_owner' => $data['order']['owner_id'],
			'order_type' => $data['order']['type_order'],
			'account_id' => $this->getAccount()
		];

		$this->_indexes = [
			['key' => 'account', 'value' => $this->getTaskParams('account')]
		];

		$this->_update = [
			'fields' => ['process_date=:process_date'],
			'values' => [':process_date' => date('Y-m-d H:i:s')]
		];
	}

	public function getAccount()
	{
		return $this->getTaskParams('account');
	}
}