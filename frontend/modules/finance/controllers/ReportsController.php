<?php

class ReportsController extends Controller
{
    public $activeMenu = 'accounts';

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
                'actions'=>array('_graph','_get','_blocking'),
                'roles'=>array('user'),
            ),
            array('deny',// deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function action_blocking()
    {
        $pages          =new CPagination(Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{money_blocking}} WHERE owner_id=:owner")->queryScalar(array(
                    ':owner'=>Yii::app()->user->id)));
        $pages->pageSize=7;

        $logs=Yii::app()->db->createCommand("SELECT * FROM {{money_blocking}} WHERE owner_id=:owner ORDER BY _date DESC LIMIT ".$pages->getOffset().",".$pages->getLimit())->queryAll(true,array(
            ':owner'=>Yii::app()->user->id));

        echo json_encode(array('code'=>200,'html'=>$this->renderPartial('_blocking_amount',array(
                'logs'=>$logs,'pages'=>$pages),true)));
        Yii::app()->end();
    }

    public function action_get($act='')
    {
        $_o=(isset($_POST['_o']))?trim($_POST['_o']):'_date';
        $_a=(isset($_POST['_a']) AND ($_POST['_a'] == 'ASC' OR $_POST['_a'] == 'DESC'))?trim($_POST['_a']):'DESC';

        //Дата
        $_f=(isset($_POST['_f']) AND trim($_POST['_f']) != '')?trim($_POST['_f']):date("Y-m-d");
        $_t=(isset($_POST['_t']) AND trim($_POST['_t']) != '')?trim($_POST['_t']):date("Y-m-d");

        $condition=array('owner_id=:id');
        $params   =array(':id'=>Yii::app()->user->id);

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
                        $condition[]      ='_date>=:f_date';
                        $condition[]      ='_date<=:t_date';
                        $params[':f_date']=$dateValidate->_from;
                        $params[':t_date']=$dateValidate->_to;
                    }
                }
            }
        }

        $all=array(
            'blocked_money'=>'',
            'blocked_bonus'=>'',
            'bonus'=>'',
            'money'=>''
        );

        switch($act)
        {
            case "out":
                $table       ='{{money_withdrawal}}';
                $all['money']=Yii::app()->db->createCommand("SELECT SUM(_out) as inc_summ FROM {{money_withdrawal}} WHERE _status!=3 AND ".implode(" AND ",$condition))->queryScalar($params);

                $view_file='_outup_report';
                $oArr     =array('date'=>'_date '.$_a.',_time','amount'=>'amount');

                break;

            default:
                $table    ='{{money_logs}}';
                $view_file='_report';
                $oArr     =array('date'=>'_date '.$_a.',_time');

                $amounts=Yii::app()->db->createCommand("SELECT _type,_amount,amount_type,is_blocked,_transfer FROM {{money_logs}} WHERE ".implode(" AND ",$condition))->queryAll(true,$params);

                foreach($amounts as $m)
                {
                    if($m['amount_type'] == 1)
                    {
                        if($m['_transfer'])
                        {
                            if($m['_transfer'] == 3)
                            {
                                $all['blocked_bonus']-=$m['_amount'];
                            } else
                            {
                                $all['blocked_bonus']=($m['_transfer'] == 2)?$all['blocked_bonus'] - $m['_amount']:$all['blocked_bonus'] + $m['_amount'];
                                $all['bonus']        =($m['_transfer'] == 1)?$all['bonus'] - $m['_amount']:$all['bonus'] + $m['_amount'];
                            }
                        } else
                        {
                            if($m['_type'] == 1)
                            {
                                if($m['is_blocked'])
                                    $all['blocked_bonus']-=$m['_amount'];
                                else
                                    $all['bonus'] -=$m['_amount'];
                            }
                            else
                            {
                                if($m['is_blocked'])
                                    $all['blocked_bonus']+=$m['_amount'];
                                else
                                    $all['bonus'] +=$m['_amount'];
                            }
                        }
                    }
                    else
                    {
                        if($m['_transfer'])
                        {
                            if($m['_transfer'] == 3)
                            {
                                $all['blocked_money']-=$m['_amount'];
                            } else
                            {
                                $all['blocked_money']=($m['_transfer'] == 2)?$all['blocked_money'] - $m['_amount']:$all['blocked_money'] + $m['_amount'];
                                $all['money']        =($m['_transfer'] == 1)?$all['money'] - $m['_amount']:$all['money'] + $m['_amount'];
                            }
                        } else
                        {
                            if($m['_type'] == 1)
                            {
                                if($m['is_blocked'])
                                    $all['blocked_money']-=$m['_amount'];
                                else
                                    $all['money'] -=$m['_amount'];
                            } else
                            {
                                if($m['is_blocked'])
                                    $all['blocked_money']+=$m['_amount'];
                                else
                                    $all['money'] +=$m['_amount'];
                            }
                        }
                    }
                }
        }

        if(isset($oArr[$_o]))
        {
            $order=$oArr[$_o].' '.$_a;
        } else
            $order=$oArr['date'].' '.$_a;

        $pages          =new CPagination(Yii::app()->db->createCommand("SELECT COUNT(*) FROM {$table} WHERE ".implode(" AND ",$condition))->queryScalar($params));
        $pages->pageSize=25;

        //if(Yii::app()->user->checkAccess('admin'))
        // print_r($condition);

        $moneys=Yii::app()->db->createCommand("SELECT * FROM {$table} WHERE ".implode(" AND ",$condition)." ORDER BY {$order} LIMIT ".$pages->getOffset().",".$pages->getLimit())->queryAll(true,$params);

        echo json_encode(array('code'=>200,'html'=>$this->renderPartial($view_file,array(
                'all'=>$all,
                'logs'=>$moneys,'pages'=>$pages),true)));

        Yii::app()->end();
    }

    public function action_graph($act)
    {
        /*
          if(!Yii::app()->user->checkAccess('admin') AND Yii::app()->user->id != 3)
          {
          echo json_encode(array('code'=>200,'html'=>'<div style="padding: 10px; text-align: center;">Данный блок, находиться на реконструкции.</div>'));
          Yii::app()->end();
          }
         */

        $data=array();//данные на каждый день
        $ps  =array(
            'twitter'=>0,
            'referrals'=>0,
            'bonus'=>0,
            'balance'=>0,
        );

        $amount=0;//сумма

        $moneys=Yii::app()->db->createCommand("SELECT * FROM {{money_logs}} WHERE owner_id=:id AND _type=:type AND is_blocked=0 AND _date>=:start_date AND _date<=:end_date ORDER BY _date")->queryAll(true,array(
            ':type'=>($act == 'out')?1:0,':id'=>Yii::app()->user->id,':start_date'=>date("Y-m-01"),
            ':end_date'=>date("Y-m-t")));

        $_prev_date='';
        $_amount   =0;

        foreach($moneys as $money)
        {
            if($_prev_date != $money['_date'])
                $amount=0;

            $amount+=$money['_amount'];
            $_amount+=$money['_amount'];

            $data[date('j',strtotime($money['_date']))]=$amount;

            if($act == 'out')
            {
                switch($money['_system'])
                {
                    case 1:
                        $ps['balance']+=$money['_amount'];
                        break;
                    case 2:
                        $ps['twitter']+=$money['_amount'];
                        break;
                }
            } else
            {
                switch($money['_system'])
                {
                    case 1:
                    case 2:
                        $ps['balance']+=$money['_amount'];
                        break;
                    case 3:
                        $ps['referrals']+=$money['_amount'];
                        break;
                    case 0:
                        $ps['bonus']+=$money['_amount'];
                        break;
                    case 5:
                        $ps['twitter']+=$money['_amount'];
                        break;
                }
            }

            $_prev_date=$money['_date'];
        }

        if($act == 'out')
        {
            $lists=array(
                0=>array('lang_key'=>'_twitter','precent'=>((100 * $ps['twitter']) / $_amount),
                    'color'=>'00C3F8'),
                1=>array('lang_key'=>'_extract_balance','precent'=>((100 * $ps['balance']) / $_amount),
                    'color'=>'EC4853'),
            );//список елементов в графике                
        } else
        {
            $lists=array(
                0=>array('lang_key'=>'_twitter','precent'=>((100 * $ps['twitter']) / $_amount),
                    'color'=>'00C3F8'),
                1=>array('lang_key'=>'_referrals','precent'=>((100 * $ps['referrals']) / $_amount),
                    'color'=>'F86865'),
                2=>array('lang_key'=>'_bonus_stats','precent'=>((100 * $ps['bonus']) / $_amount),
                    'color'=>'FFA700'),
                3=>array('lang_key'=>'_add_balance','precent'=>((100 * $ps['balance']) / $_amount),
                    'color'=>'56CC41'),
            );//список елементов в графике           
        }

        $html=$this->renderPartial('_graph',array('data'=>$data,'lists'=>$lists,
            'amount'=>$_amount),true);

        echo json_encode(array('code'=>200,'html'=>$html));
        Yii::app()->end();
    }
}
