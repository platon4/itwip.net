<?php

use twitter\models\Twitter;

class DefaultController extends Controller
{
    public $activeMenu = 'tw_adv';

    public function filters()
    {
        return ['accessControl'];
    }

    public function accessRules()
    {
        return [
            ['allow',
                'actions' => ['index', 'resetparams', 'bwlist'],
                'roles'   => ['user'],
            ],
            ['deny',
                'users' => ['*']
            ]
        ];
    }

    public function actionResetParams()
    {
        Html::json(['html' => $this->renderPartial('_params', ['model' => new Twitter], TRUE)]);
    }

    public function actionIndex()
    {
        $model = new Twitter;
        $model->setScenario('get');

        $model->load($_POST);

        if($model->validate()) {
            if(Yii::app()->request->isAjaxRequest)
                Html::json(['code' => 200, 'html' => $this->renderPartial('_list', ['model' => $model], TRUE), 'stats' => $this->renderPartial('_stats', ['model' => $model], TRUE)]);
            else
                $this->render('index', ['model' => $model]);
        } else
            $this->_message($model->getError(), '', '/twitter');
    }

    public function actionBwList()
    {
        $model = new Twitter;
        $model->setScenario('bw');

        $model->load($_POST, TRUE);

        if($model->validate() && $model->bwToggle())
            Html::json(['code' => 200, 'stats' => $this->renderPartial('_stats', ['model' => $model], TRUE), 'white_count' => $model->getStat()['whitelisted'], 'black_count' => $model->getStat()['blacklisted']]);
        else
            Html::json(['code' => 201, 'message' => $model->getError()]);
    }
}
