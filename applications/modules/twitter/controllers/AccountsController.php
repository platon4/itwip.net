<?php

namespace app\modules\twitter\controllers;

use Yii;
use app\modules\twitter\models\AccountsData;
use yii\web\Response;

class AccountsController extends \app\components\Controller
{
    public function actionGet($act)
    {
        $model = new AccountsData();
        $model->setScenario($act);

        $model->load($_GET, '');

        $model->validate();

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $model->getResponse();
    }
} 