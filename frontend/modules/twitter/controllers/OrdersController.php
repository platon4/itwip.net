<?php

use twitter\models\orders\Status;
use twitter\models\orders\Order;

class OrdersController extends Controller
{
    public $activeMenu = 'tw_adv';

    public function filters()
    {
        return [
            'accessControl',
        ];
    }

    public function accessRules()
    {
        return [
            ['allow',
                'actions' => ['following', 'retweets', 'status', 'remove', 'paid'],
                'roles'   => ['user'],
            ],
            ['deny',
                'users' => ['*'],
            ],
        ];
    }

    public function actionRetweets()
    {
        $this->render("retweets");
    }

    public function actionFollowing()
    {
        $this->render("following");
    }

    /*
     * Статус заказов
     */
    public function actionStatus()
    {
        $model = new Status;
        $model->load($_GET, true);

        if($model->validate()) {
            if(Yii::app()->request->isAjaxRequest)
                Html::json(['code' => 200, 'html' => $this->renderPartial($model->getViewFile(), ['model' => $model], true)]);
            else
                $this->render($model->getViewFile(), ['model' => $model]);
        } else
            $this->_message($model->getError(), Yii::t('main', '_error'), '/twitter/orders/status');
    }

    public function actionRemove()
    {
        $model = new Order;
        $model->setScenario('remove');

        if($model->load($_GET, true) && $model->validate()) {
            Html::json(['message' => $model->getMessage(), 'code' => $model->getCode()]);
        } else
            Html::json(['message' => $model->getError(), 'code' => $model->getCode()]);
    }

    public function actionPaid()
    {
        $model = new Order;
        $model->setScenario('paid');

        if($model->load($_GET, true) && $model->validate()) {
            Html::json(['message' => 'Ваш заказ успешно оплачен.', 'code' => 200]);
        } else
            Html::json(['message' => $model->getError(), 'code' => 203]);
    }
}
