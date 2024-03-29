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
        $model->load($_GET, '');

        if (!$model->validate())
            Error::e($model->getErrorName(), $model->getError(), $model->getReturnUrl());
    }

    public function actionUpdate()
    {
        $model = new oAuth();
        $model->setScenario('update');
        $model->load($_GET, '');

        if (!$model->validate())
            Error::e($model->getErrorName(), $model->getError(), $model->getReturnUrl());
    }

    public function actionAuthProcess()
    {
        $model = new oAuth();
        $model->setScenario('auth-process');
        $model->load($_GET, '');

        if (!$model->validate())
            Error::e($model->getErrorName(), $model->getError(), $model->getReturnUrl());
    }

    public function actionUpdateProcess()
    {
        $model = new oAuth();
        $model->setScenario('update-process');
        $model->load($_GET, '');

        if (!$model->validate())
            Error::e($model->getErrorName(), $model->getError(), $model->getReturnUrl());
    }
} 