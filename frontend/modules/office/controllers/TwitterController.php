<?php

class TwitterController extends Controller {

    public function init()
    {
        parent::init();
        Yii::app()->clientScript->registerScriptFile('/js/_a/tw-oZ5q.js');
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
                'actions'=>array('orders','accounts'),
                'roles'=>array('admin'),
            ),
            array('allow',
                'actions'=>array('settings','_m','_save','accountsmoderation'),
                'roles'=>array('moderator'),
            ),
            array('deny',// deny all users
                'users'=>array('*'),
            ),
        );
    }

    public function actionAccountsModeration($_s=0,$_q='',$_o='',$_a='DESC')
    {
        if(in_array($_s,array(0,1,2)))
        {
            $w=array();
            $p=array();
            
            switch($_s)
            {
                case 1:
                    $w[]='_status=1';
                    break;

                case 2:
                    $w[]='_status=2';
                    break;

                default:
                    $w[]='_status=0';
            }
            
            if(trim($_q)!='')
            {
               $w[]='(screen_name LIKE :search OR name LIKE :search)';
               $p[':search']='%'.$_q.'%';
            }
            
            $where=implode(" AND ",$w);

            $pages          =new CPagination(Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{tw_accounts}} WHERE {$where}")->queryScalar($p));
            $pages->pageSize=30;

            $countsAll=Yii::app()->db->createCommand("SELECT COUNT(*) as count,_status FROM {{tw_accounts}} GROUP BY _status")->queryAll();

            $counts=array();

            foreach($countsAll as $countRow)
            {
                $counts[$countRow['_status']]=$countRow['count'];
            }

            $ad=($_a=='ASC' OR $_a=='DESC')?$_a:'DESC';
            
            switch($_o)
            {
                case "itr":
                     $order='itr '.$ad;
                    break;
                
                case "mdr":
                     $order='_mdr '.$ad;
                    break;

                 case "tape":
                     $order='tape '.$ad;
                    break;               
                
                default:
                  $order='date_add '.$ad;
        }

        $accounts=Yii::app()->db->createCommand("SELECT * FROM {{tw_accounts}} WHERE {$where} ORDER BY {$order} LIMIT ".$pages->getOffset().", ".$pages->getLimit())->queryAll(true,$p);

        if(!Yii::app()->request->isAjaxRequest)
        {
            $this->render('moderation',array('counts'=>$counts,'_s'=>$_s,'accounts'=>$accounts,
                'pages'=>$pages));
        } else
        {
            echo json_encode(array('counts'=>array('_m'=>(int)$counts[0],
            '_w'=>(int)$counts[1],'_b'=>(int)$counts[2]),'list'=>$this->renderPartial('_mlist',array(
            'accounts'=>$accounts),true),'pages'=>$this->renderPartial("_mpages",array(
            'pages'=>$pages),true)));
            
             Yii::app()->end();
        }
        } else
        throw new CHttpException('403',Yii::t('yii','Your request is invalid.'));
    }

    public function actionAccounts()
    {
        $pages          =new CPagination(Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{tw_accounts}}")->queryScalar());
        $pages->pageSize=10;

        $accounts=Yii::app()->db->createCommand("SELECT * FROM {{tw_accounts}} ORDER BY date_add DESC LIMIT ".$pages->getOffset().", ".$pages->getLimit())->queryAll();

        if(!Yii::app()->request->isAjaxRequest)
        {
            $this->render('accounts',array('accounts'=>$accounts,'pages'=>$pages));
        } else
        {
            echo json_encode(array('list'=>$this->renderPartial('_list',array('accounts'=>$accounts),true),
                'pages'=>$this->renderPartial("_pages",array('pages'=>$pages),true)));
            Yii::app()->end();
        }
    }

    public function action_m($id)
    {
        $m=(isset($_POST['_m']) AND intval($_POST['_m']))?intval($_POST['_m']):0;

        if($m)
        {
            $sth     =Yii::app()->db->createCommand("UPDATE {{tw_accounts}} SET _mdr=:m WHERE id=:id");
            $rowCount=$sth->execute(array(':id'=>$id,':m'=>$m));
        }
        echo json_encode(array('code'=>200));
        Yii::app()->end();
    }

    public function actionSave($id)
    {
        $s=(isset($_POST['_status']) AND intval($_POST['_status']))?intval($_POST['_status']):0;

        $sth     =Yii::app()->db->createCommand("UPDATE {{tw_accounts}} SET _status=:s WHERE id=:id");
        $rowCount=$sth->execute(array(':id'=>$id,':s'=>$s));

        echo json_encode(array('code'=>200));
        Yii::app()->end();
    }

    public function actionSettings($id)
    {
        $account=Yii::app()->db->createCommand("SELECT * FROM {{tw_accounts}} WHERE id=:id")->queryRow(true,array(
            ':id'=>$id));

        if(count($account))
        {
            if(!Yii::app()->request->isAjaxRequest)
            {
                
            } else
            {
                $action=isset($_POST['_t'])?$_POST['_t']:false;

                switch($action)
                {
                    case "status":
                        $html   =$this->renderPartial('_status',array('account'=>$account),true);
                        $buttons='<button type="button" onclick="Tw.status(\''.$account['id'].'\', this);" class="button btn_blue ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">Сохранить</span></button>';
                        break;

                    case "settings":
                        $html   =$this->renderPartial('_msettings',array('account'=>$account),true);
                        $buttons='<button type="button" onclick="_M.save(\''.$account['id'].'\', this);" class="button btn_blue ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" role="button" aria-disabled="false"><span class="ui-button-text">Сохранить</span></button>';
                        break;


                    default:
                        $html=Yii::t('main','no_action');
                }

                echo json_encode(array('title'=>Yii::t('officeModule.twitter_accounts','_accounts_title',array(
                        '{login}'=>$account['screen_name'])),'html'=>$html,'buttons'=>$buttons));
                Yii::app()->end();
            }
        } else
        {
            
        }
    }

    public function action_save($id)
    {
        if(intval($id))
        {
            $account=Yii::app()->db->createCommand("SELECT id,created_at,itr,tweets,following,followers,_mdr,yandex_rank,google_pr FROM {{tw_accounts}} WHERE id=:id")->queryRow(true,array(
                ':id'=>$id));

            if(count($account))
            {
                $m=new M;

                if(isset($_POST['M']['_status']) AND $_POST['M']['_status'] == 2)
                    $m->scenario=2;

                $m->attributes=$_POST['M'];

                if($m->validate())
                {
                    $updates=array('_status=:status','_mdr=:mdr','_reason=:reason','tape=:tape');
                    $params =array(':reason'=>$m->_message,':id'=>$id,':status'=>$m->_status,
                        ':mdr'=>$m->_m,':tape'=>$m->tape);

                    if($m->_m != $account['_mdr'])
                    {
                        $updates[]     ='itr=:itr';
                        $params[':itr']=THelper::itr($account['tweets'],$account['followers'],date('d.m.Y H:i:s',$account['created_at']),$account['listed_count'],$account['yandex_rank'],$account['google_pr'],$m->_m);
                    }

                    if(count($updates))
                        Yii::app()->db->createCommand("UPDATE {{tw_accounts}} SET ".implode(', ',$updates)." WHERE id=:id")->execute($params);

                    $message='<div class="line_info ok">'.Yii::t('officeModule.twitter_accounts','_save_m_success').'</div>';
                    $code   =200;
                }else
                {
                    $message='<div class="line_info alert">'.Html::errorSummary($m).'</div>';
                    $code   =502;
                }


                echo json_encode(array('code'=>$code,'message'=>$message));
                Yii::app()->end();
            } else
                throw new CHttpException('404',Yii::t('officeModule.twitter_accounts','_account_not_found'));
        } else
            throw new CHttpException('403',Yii::t('yii','Your request is invalid.'));
    }

    public function actionOrders()
    {
        $this->render('orders');
    }

}
