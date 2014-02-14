<?php

namespace twitter\components;


use Yii;
use twitter\components\libs\Accounts;

Yii::import('application.modules.twitter.components.libs.*');

class Twitter
{
	/*
	 * @var object
	 */
	protected static $_accounts;

	/*
	 * Данные аккаунтов указанных ID
	 * @return array
	 */
	public static function accounts($ids)
	{
		if(self::$_accounts === NULL) {
			self::$_accounts = new Accounts($ids);
		}

		return self::$_accounts;
	}
}