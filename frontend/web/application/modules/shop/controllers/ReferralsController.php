<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Referrals
 *
 * @author Александр
 */
class ReferralsController extends Controller
{
    public $activeMenu = 'shop';

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions'=>array('index','buy'),
                'roles'=>array('user'),
            ),
            array('deny',// deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $model=new Referrals;

        $model->attributes=isset($_POST['R'])?$_POST['R']:array();

        if($model->validate())
        {
            $rows=$model->_getRows();

            if(Yii::app()->request->isAjaxRequest)
            {
                echo json_encode(array('code'=>200,'html'=>$this->renderPartial('_rows',array(
                        'rows'=>$rows,'pages'=>$model->_getPages()),true)));
            } else
                $this->render('index',array('pages'=>$model->_getPages(),'count'=>$model->_getCount(),
                    'rows'=>$rows));
        } else
            throw new CHttpException(502,Yii::t('main','Invalid params.'));
    }

    public function actionBuy()
    {
        $buy=new Buy;

        $buy->attributes=isset($_POST['S'])?$_POST['S']:array();

        if($buy->validate())
        {
            if($buy->process())
            {
                $json=array('code'=>200,'count'=>$buy->_getCount(),'message'=>Yii::t('shopModule.index','_buy_referral_success'));
            } else
            {
                $json=array('code'=>3,'message'=>$buy->getError());
            }
        } else
            $json=array('code'=>502,'message'=>$buy->getError());

        echo json_encode($json);
        Yii::app()->end();
    }

}
