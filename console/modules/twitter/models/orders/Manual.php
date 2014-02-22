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
	protected function process($data, $tasks)
	{
		print_r($data);
		print_r($tasks);
		$redis = Yii::$app->redis;

		if(is_array($data) && $data !== array()) {

			$this->_row = [
				'order_id' => $data['id'],
				'order_owner' => $data['owner_id'],
				'order_type' => $data['type_order'],
				'account_id' => $this->getAccount()
			];

			$this->_indexes = [
				['key' => 'account', 'value' => $this->getParams('account')]
			];

			$this->_update = [
				'fields' => ['process_date=:process_date'],
				'values' => [':process_date' => date('Y-m-d H:i:s')]
			];
		}
	}

	public function getAccount()
	{
		return $this->getParams('account');
	}
}