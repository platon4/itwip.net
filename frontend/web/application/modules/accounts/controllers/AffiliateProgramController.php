<?php

class AffiliateProgramController extends Controller {

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
                'actions'=>array('referral','index','loyalty','banners'),
                'roles'=>array('user'),
            ),
            array('deny',// deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionIndex()
    {
        $this->render('index',array());
    }

    public function actionBanners()
    {
        $affilate=Loyalty::model()->findByPk(Yii::app()->user->id);
        echo json_encode(array('html'=>$this->renderPartial('_banners',array('affilate'=>$affilate),true)));

        Yii::app()->end();
    }

    public function actionReferral($act=null,$_q=null,$_o=null,$_a=null)
    {
        $affilate=Loyalty::model()->findByPk(Yii::app()->user->id);
        $count   =$count   =Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{loyalty}} l INNER JOIN {{accounts}} a ON l.owner_id=a.id WHERE l.parent_referral=:id AND a.status=1")->queryScalar(array(
            ':id'=>Yii::app()->user->id));
        ;

        $where =array('t.parent_referral=:id','a.status=1');
        $params=array(':id'=>Yii::app()->user->id);

        if(trim($_q) != '')
        {
            $where[]        ='a.name LIKE :name';
            $params[':name']='%'.$_q.'%';
        }

        $scount=Yii::app()->db->createCommand("SELECT COUNT(*) as count FROM {{loyalty}} t INNER JOIN {{accounts}} a ON t.owner_id=a.id WHERE ".implode(' AND ',$where))->queryScalar($params);

        $pages          =new CPagination($scount);
        $pages->pageSize=50;

        $orderArr =array('date'=>'a._date_create','last'=>'a._date_last_visit','income'=>'t.brought_user');
        $orderType=($_a == "ASC")?' ASC':' DESC';

        if(isset($orderArr[$_o]))
            $order=$orderArr[$_o];
        else
            $order=$orderArr['date'];

        $order=$order.$orderType;

        $referrals=Yii::app()->db->createCommand("SELECT * FROM {{loyalty}} t INNER JOIN {{accounts}} a ON t.owner_id=a.id WHERE ".implode(' AND ',$where)." ORDER BY ".$order." LIMIT ".$pages->getOffset().",".$pages->getLimit())->queryAll(true,$params);

        if($act == 'list')
            echo json_encode(array('pages'=>$this->renderPartial("_pages",array(
                    'pages'=>$pages),true),'html'=>$this->renderPartial('_list',array(
                    'referrals'=>$referrals),true)));
        else
            echo json_encode(array('html'=>$this->renderPartial('_referral',array(
                    'referrals'=>$referrals,'pages'=>$pages,'count'=>$count,'affilate'=>$affilate),true)));

        Yii::app()->end();
    }

    /**
     * Программа лояльности
     */
    public function actionLoyalty()
    {
        $affilate=Loyalty::model()->findByPk(Yii::app()->user->id);
        $count   =Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{loyalty}} l INNER JOIN {{accounts}} a ON l.owner_id=a.id WHERE l.parent_referral=:id AND a.status=1")->queryScalar(array(
            ':id'=>Yii::app()->user->id));

        $ref_steps            =LoyaltyHelper::_getData('referral');
        $ref_next_step        =explode('-',$ref_steps[$affilate->loyalty_referral + 1][1]);
        $left_to_ref_next_step=$ref_next_step[0] - $count;

        $f_steps            =LoyaltyHelper::_getData('finance');
        $left_to_f_next_step=$f_steps[$affilate->loyalty_finance + 1][1] - $affilate->in_balance;

        echo json_encode(array('html'=>$this->renderPartial('_loyalty',array('left_to_ref_next_step'=>$left_to_ref_next_step,
                'left_to_f_next_step'=>$left_to_f_next_step,'count'=>$count,'affilate'=>$affilate),true)));
        Yii::app()->end();
    }

}
