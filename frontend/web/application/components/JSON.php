<?php

class JSON extends CJSON
{
	public static function encode($data)
	{
		echo parent::encode($data);
		Yii::app()->end();
	}
}