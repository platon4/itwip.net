<?php

use \twitter\models\accounts\Accounts;

class AjaxAccountsController extends Controller
{
    public function filters()
    {
        return [
            'accessControl',
            'ajaxOnly'
        ];
    }

    public function accessRules()
    {
        return [
            ['allow',
                'actions' => ['status'],
                'roles'   => ['user'],
            ],
            ['deny', // deny all users
                'users' => ['*'],
            ],
        ];
    }

    public function actionStatus()
    {
        $model = new Accounts();
        $model->setScenario('status');

        if($model->load($_POST, true) && $model->validate())
            Html::json(['code' => 200, 'html' => $model->status()]);
        else
            Html::json(['code' => 0, 'message' => $model->getError()]);
    }
} 