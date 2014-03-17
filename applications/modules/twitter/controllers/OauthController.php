<?php

namespace app\modules\twitter\controllers;

use app\components\Error;
use Yii;
use app\modules\twitter\models\oAuth;
use app\components\UserException;

class OauthController extends \app\components\Controller
{
    public function actionAuthorize()
    {
        $model = new oAuth();
        $model->setScenario('auth');

        if (!$model->load($_GET, '') || !$model->validate())
            Error::e($model->getErrorName(), $model->getError(), $model->getReturnUrl());
    }

    public function actionUpdate()
    {
        $model = new oAuth();
        $model->setScenario('update');

        if (!$model->load($_GET, '') || !$model->validate())
            Error::e($model->getErrorName(), $model->getError(), $model->getReturnUrl());
    }
} 