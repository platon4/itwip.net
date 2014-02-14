<?php

class FinanceController extends Controller
{

    public function init()
    {
        parent::init();
        Yii::app()->clientScript->registerScriptFile('/js/_a/_f-a65Q.js');
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
                'actions' => array('promo', 'index', 'replenishment', 'withdrawals', 'withdrawalrequests', 'expenses'),
                'roles' => array('admin'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionExpenses()
    {
        $this->render('expenses');
    }

    public function actionWithdrawalRequests()
    {
        $this->render('withdrawalrequests');
    }

    public function actionWithdrawals()
    {
        $this->render('withdrawals');
    }

    public function actionReplenishment()
    {
        $this->render('replenishment');
    }

    public function actionPromo($act = '', $_u = '')
    {
        $promo = new Promo();

        if (isset($_POST['Promo']))
        {
            $types = array('simple', 'adavance');
            $type = (isset($_POST['_type']) AND in_array($_POST['_type'], $types)) ? $_POST['_type'] : false;

            $promo->scenario = $type;
            $promo->attributes = $_POST['Promo'];

            if ($promo->validate() && $type)
            {
                $codeGenerate = str_shuffle('0aQ9qweA18rtyZXuPio2WpCasEM3dfgVh_jkNlzR4UxBcO5vYbn6T7m');
                $params = array();
                $inserts = array();

                if ($type == 'simple')
                {
                    for ($l = 0; $l <= $promo->_count; $l++)
                    {
                        $rcode = array();

                        for ($c = 0; $c <= 3; $c++)
                        {
                            $xcode = '';

                            for ($x = 0; $x <= 3; $x++)
                            {
                                $xcode.=$codeGenerate{mt_rand(0, 53)};
                            }

                            $rcode[] = $xcode;
                        }

                        $pcode = implode('-', $rcode);

                        $inserts[] = '(:_hash_code_' . $l . ',:_code_' . $l . ',:_amount,:_create_date,:_tie,:_count,:_type)';
                        $params[':_hash_code_' . $l] = md5($pcode);
                        $params[':_code_' . $l] = $pcode;
                    }

                    $params[':_amount'] = $promo->amount;
                    $params[':_create_date'] = date('Y-m-d H:i:s');
                    $params[':_tie'] = 0;
                    $params[':_count'] = 1;
                    $params[':_type'] = 0;
                }
                else
                {

                    $rcode = array();
                    $rcode[] = $promo->mark;

                    for ($c = 0; $c <= 2; $c++)
                    {
                        $xcode = '';

                        for ($x = 0; $x <= 3; $x++)
                        {
                            $xcode.=$codeGenerate{mt_rand(0, 53)};
                        }

                        $rcode[] = $xcode;
                    }

                    $pcode = implode('-', $rcode);

                    $inserts[] = '(:_hash_code,:_code,:_amount,:_create_date,:_tie,:_count,:_type)';
                    $params[':_hash_code'] = md5($pcode);
                    $params[':_code'] = $pcode;
                    $params[':_amount'] = $promo->amount;
                    $params[':_create_date'] = date('Y-m-d H:i:s');
                    $params[':_tie'] = $promo->tie;
                    $params[':_count'] = (intval($promo->limit)) ? $promo->limit : 0;
                    $params[':_type'] = 1;
                }

                $code = 200;
            }
            else
                $code = 502;

            if (count($inserts))
                $sth = Yii::app()->db->createCommand("INSERT INTO {{promo_code}} (_hash_code,_code,_amount,_create_date,_tie,_count,_type) VALUES " . implode(", ", $inserts))->execute($params);

            if (!Yii::app()->request - isAjaxRequest)
            {
                $this->redirect('/office/finance/promo');
            }
            else
            {
                echo json_encode(array('code' => $code, '_type' => $type, 'html' => Html::errorSummary($promo)));
            }

            Yii::app()->end();
        }

        $poromo_use = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{promo_code_logs}} WHERE _type=0")->queryScalar();
        $promo_no_use = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{promo_code}} WHERE _type=0")->queryScalar();

        if ($act != '')
        {
            switch ($act)
            {
                case "simple":

                    if ($_u == 'use')
                        $count_sql = 'SELECT COUNT(*) FROM {{promo_code_logs}} WHERE _type=0';
                    else
                        $count_sql = 'SELECT COUNT(*) FROM {{promo_code}} WHERE _type=0';

                    $pages = new CPagination(Yii::app()->db->createCommand($count_sql)->queryScalar());
                    $pages->pageSize = 50;

                    if ($_u == 'use')
                    {
                        $table = '{{promo_code_logs}}';
                        $order = ' ';
                    }
                    else
                    {
                        $table = '{{promo_code}}';
                        $order = ' ORDER BY _create_date DESC ';
                    }

                    $promoCodes = Yii::app()->db->createCommand("SELECT * FROM " . $table . " WHERE _type=0" . $order . "LIMIT " . $pages->getOffset() . ", " . $pages->getLimit())->queryAll();
                    break;

                case "adavance":

                    $pages = new CPagination(Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{promo_code}} WHERE _type=1")->queryScalar());
                    $pages->pageSize = 50;

                    $adavancePromoCodes = Yii::app()->db->createCommand("SELECT * FROM {{promo_code}} WHERE _type=1 ORDER BY _create_date DESC LIMIT " . $pages->getOffset() . ", " . $pages->getLimit())->queryAll();
                    break;

                default:
                    echo json_encode(array('code' => 403));
                    Yii::app()->end();
            }
        }
        else
        {
            $spages = new CPagination($promo_no_use);
            $apages = new CPagination(Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{promo_code}} WHERE _type=1")->queryScalar());

            $spages->pageSize = 50;
            $apages->pageSize = 50;

            $promoCodes = Yii::app()->db->createCommand("SELECT * FROM {{promo_code}} WHERE _type=0 ORDER BY _create_date DESC LIMIT " . $spages->getOffset() . ", " . $spages->getLimit())->queryAll();
            $adavancePromoCodes = Yii::app()->db->createCommand("SELECT * FROM {{promo_code}} WHERE _type=1 ORDER BY _create_date DESC LIMIT " . $apages->getOffset() . ", " . $apages->getLimit())->queryAll();
        }

        if (Yii::app()->request->isAjaxRequest)
        {
            if ($act == 'simple')
                $html = $this->renderPartial('_spromo', array('promoCodes' => $promoCodes,
                    'pages' => $pages), true);
            else
                $html = $this->renderPartial('_apromo', array('adavancePromoCodes' => $adavancePromoCodes,
                    'pages' => $pages), true);

            echo json_encode(array('html' => $html, 'poromo_use' => $poromo_use, 'promo_no_use' => $promo_no_use));
            Yii::app()->end();
        }
        else
            $this->render('promo', array('promoCodes' => $promoCodes, 'adavancePromoCodes' => $adavancePromoCodes,
                'poromo_use' => $poromo_use, 'promo_no_use' => $promo_no_use));
    }

    public function actionIndex()
    {
        $data = Yii::app()->cache->get('officeFinanceStats');

        if ($data === false)
        {
            $finances = new Finances;
            $data = $finances->getFinances();
            Yii::app()->cache->set('officeFinanceStats', $data, 60);
        }

        $this->render('index', array('finance' => $data));
    }
}
