<?php

class DefaultController extends Controller
{
    public $activeMenu = 'accounts';

    public function init()
    {
        parent::init();

        if(!Yii::app()->request->isAjaxRequest)
            Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl.'/js/it-core-finance.js');
    }

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
                'actions'=>array('index','output','replenishment'),
                'roles'=>array('user'),
            ),
            array('deny',// deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $balances=Yii::app()->db->createCommand("SELECT amount,_money_type FROM {{money_blocking}} WHERE owner_id=:owner")->queryAll(true,array(
            ':owner'=>Yii::app()->user->id));

        $balance=array('money'=>0,'bonus'=>0);
        foreach($balances as $b)
        {
            if($b['_money_type'] == 1)
            {
                $balance['bonus']+=$b['amount'];
            } else
                $balance['money']+=$b['amount'];
        }

        $this->render('index',array('balance'=>$balance));
    }

    public function actionOutput($act='')
    {
        //if(!Yii::app()->user->checkAccess('admin'))
            //$this->_message('В данный момент на этой странице ведутся работы, пожалуйста зайдите позже. Спасибо.');
        
        $this->render('output');
    }

    public function actionReplenishment($_id=0)
    {
        if(!$_id)
        {
            $_o=(isset($_POST['_o']))?trim($_POST['_o']):'_date';
            $_a=(isset($_POST['_a']) AND ($_POST['_a'] == 'ASC' OR $_POST['_a'] == 'DESC'))?trim($_POST['_a']):'DESC';

            //Дата
            $_f=(isset($_POST['_f']) AND trim($_POST['_f']) != '')?trim($_POST['_f']):date("Y-m-d");
            $_t=(isset($_POST['_t']) AND trim($_POST['_t']) != '')?trim($_POST['_t']):date("Y-m-d");

            $form   =new Replenishment;
            $confirm=false;

            if(isset($_POST['Replenishment']))
            {
                $form->attributes=$_POST['Replenishment'];

                if($form->validate())
                {
                    //if(in_array($form->_system,array(1)))
                    //{
                        //$form->addError('tesdt',Yii::t('financeModule.index','_error_pay_system_is_disabled'));
                    //} else
                    //{
                        $form->owner_id=Yii::app()->user->id;
                        $form->_date   =date("Y-m-d");
                        $form->_time   =date("H:i:s");

                        $precent=CMoney::_extractPrecent($form->amount,'finance');

                        $form->_add_to_balance  =$precent['amount'];
                        $form->_procente_extract=$precent['precent'];

                        if($form->save())
                        {
                            Yii::app()->request->redirect('?_id='.$form->getPrimaryKey());
                            Yii::app()->end();
                        }
                    //}
                }
            }

            $condition=array('owner_id=:owner_id','is_pay=1');
            $params   =array(':owner_id'=>Yii::app()->user->id);

            if($_f AND $_t)
            {
                if($_f != 'all' AND $_t != 'all')
                {
                    $dateValidate=new DateValidator;

                    $_f=date("Y-m-d",strtotime($_f));
                    $_t=date("Y-m-d",strtotime($_t));

                    $dateValidate->attributes=array('_from'=>$_f,'_to'=>$_t);

                    if($dateValidate->validate())
                    {
                        if($_f == $_t)
                        {
                            $condition[]     ='_date=:_date';
                            $params[':_date']=$dateValidate->_from;
                        } else
                        {
                            $condition[]      ='_date>=:f_date AND _date<=:t_date';
                            $params[':f_date']=$dateValidate->_from;
                            $params[':t_date']=$dateValidate->_to;
                        }
                    }
                }
            }

            $criteria=new CDbCriteria;

            $criteria->condition=implode(" AND ",$condition);
            $criteria->params   =$params;

            $oArr=array('date'=>'_date '.$_a.',_time','amount'=>'amount');

            if(isset($oArr[$_o]))
            {
                $criteria->order=$oArr[$_o].' '.$_a;
            } else
                $criteria->order=$oArr['date'].' '.$_a;

            $pages=new CPagination(Replenishment::model()->count($criteria));

            $pages->pageSize=25;
            $pages->applyLimit($criteria);

            $logs=Replenishment::model()->findAll($criteria);

            $all_amount=Yii::app()->db->createCommand()
                    ->select('SUM(_add_to_balance) as money')
                    ->from('{{money_replenishmentit}}')
                    ->where(implode(" AND ",$condition),$params)
                    ->queryScalar();

            if(Yii::app()->request->isAjaxRequest)
            {
                echo json_encode(array(
                    'amount'=>CMoney::_c($all_amount,true),
                    'pages'=>$this->renderPartial('application.views.main._pages',array(
                        'ajax_query'=>'Finance._getPage','pages'=>$pages),true),
                    'html'=>$this->renderPartial('_report_list',array('logs'=>$logs),true)
                        )
                );
                Yii::app()->end();
            } else
                $this->render('replenishment',array('total_amount'=>$all_amount,
                    'form'=>$form,'logs'=>$logs,'pages'=>$pages));
        }
        else
        {
            $form=Replenishment::model()->findByPk($_id);

            if(count($form))
            {
                if(!$form->is_pay)
                {
                    $this->render('replenishment_confirm',array('form'=>$form,'_id'=>$_id));
                } else
                    throw new CHttpException('403',Yii::t('financeModule.index','_error_id_is_pay.'));
            } else
                throw new CHttpException('404',Yii::t('financeModule.index','Unable to resolve the request.'));
        }
    }
}