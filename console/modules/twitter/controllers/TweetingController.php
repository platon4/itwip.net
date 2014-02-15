<?php

namespace console\modules\twitter\controllers;

use Yii;

class TweetingController extends \console\components\Controller
{
	public function actionIndex($test = '')
	{
		print_r($test);
	}
} 