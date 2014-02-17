<?php

namespace console\modules\twitter\models\orders;

use Yii;

class Manual implements OrdersInterface
{
	use OrdersTrait;

	protected function process($data)
	{
		$redis = Yii::$app->redis;

		if(is_array($data) && $data !== array()) {

			$this->_row = ['account_id' => $this->getParams('account')];

			$this->_indexes = [
				['key' => 'account', 'value' => $this->getParams('account')]
			];

			$this->_update = [''];

		}
	}
}