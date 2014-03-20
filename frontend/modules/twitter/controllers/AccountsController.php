<?php

use \twitter\models\accounts\Accounts;

class AccountsController extends Controller
{
    public $activeMenu = 'tw_exe';
    public $_tw;
    public $_account;
    public $limitList = [
        0 => ['title' => '10', 'value' => '10'],
        1 => ['title' => '20', 'value' => '20'],
        2 => ['title' => '30', 'value' => '30'],
        3 => ['title' => '40', 'value' => '40'],
        4 => ['title' => '50', 'value' => '50'],
        5 => ['title' => '100', 'value' => '100']
    ];

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
                'actions' => ['recheck', 'index', 'add', 'settings', 'processing', 'reauth'],
                'roles'   => ['user'],
            ],
            ['deny', // deny all users
                'users' => ['*'],
            ],
        ];
    }

    public function actionIndex($act = '')
    {
        $list = array();
        $crt = array();
        $params = array();

        $crt[] = "owner_id=:owner_id";
        $params[':owner_id'] = Yii::app()->user->id;

        if(isset($_POST['_query']) AND trim($_POST['_query'])) {
            $crt[] = "(screen_name LIKE :query OR name LIKE :query)";
            $params[':query'] = '%' . $_POST['_query'] . '%';
        }

        $all_accounts = Yii::app()->db->createCommand("SELECT COUNT(*) as count,_status FROM {{tw_accounts}} WHERE owner_id=:owner_id GROUP BY _status")->queryAll(true, array(
            ':owner_id' => Yii::app()->user->id));

        $all_acc_count = 0;
        $all_accounts_moderation = 0;
        $all_acc_in_work = 0;

        foreach($all_accounts as $all_count) {

            if($all_count['_status'] == 1) {
                $all_acc_in_work += $all_count['count'];
            } else if($all_count['_status'] == 0) {
                $all_accounts_moderation += $all_count['count'];
            }

            $all_acc_count += $all_count['count'];
        }

        $dataList = array();
        $pages = null;

        if($all_acc_count) {
            $orderArr = array('posted' => 'fulfilled', 'order' => 'orders', 'itr' => 'itr',
                              'status' => '_status', 'last' => 'date_add');
            $order = null;

            $orderType = (isset($_POST['_oType']) AND $_POST['_oType'] == "ASC") ? ' ASC' : ' DESC';

            if(isset($_POST['_order']) && isset($orderArr[$_POST['_order']])) {
                $orderBy = $orderArr[$_POST['_order']];
            } else
                $orderBy = $orderArr['last'];

            $order = $orderBy . $orderType;

            if(isset($_POST['_limit']) && (CHelper::int($_POST['_limit']) OR $_POST['_limit'] == "all")) {
                $limit = $_POST['_limit'];
                Yii::app()->session['_accountsLimit'] = $_POST['_limit'];
            } else if(isset(Yii::app()->session['_accountsLimit']) AND (CHelper::int(Yii::app()->session['_accountsLimit']) OR Yii::app()->session['_accountsLimit'] == "all")) {
                $limit = CHelper::int(Yii::app()->session['_accountsLimit']);
            } else {
                $limit = 10;
            }

            $pages = new CPagination(Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{tw_accounts}} WHERE " . implode(" AND ", $crt))->queryScalar($params));
            $pages->pageSize = $limit;

            $orderData = Yii::app()->db->createCommand("SELECT COUNT(tt.id) as count,tt._tw_account FROM {{tweets_to_twitter}} tt INNER JOIN {{tw_accounts}} a ON tt._tw_account=a.id WHERE tt.approved=0 AND tt.status=0 AND a.owner_id=:owner GROUP BY tt._tw_account")->queryAll(true, array(
                ':owner' => Yii::app()->user->id));

            $requests = array();
            foreach($orderData as $ord) {
                $requests[$ord['_tw_account']] = $ord['count'];
            }

            $list = Yii::app()->db->createCommand("SELECT id,screen_name,name,avatar,itr,_status,in_yandex,(SELECT COUNT(*) FROM {{tweets_to_twitter}} WHERE approved=1 AND _tw_account=a.id AND status=0) as orders, (SELECT COUNT(*) FROM {{tw_tweets}} WHERE tid=a.id) as fulfilled, (SELECT SUM(amount) FROM {{tw_accounts_income}} WHERE tid=a.id AND _date='" . date('Y-m-d') . "') as amount_today, (SELECT SUM(amount) FROM {{tw_accounts_income}} WHERE tid=a.id AND _date='" . date('Y-m-d', time() - 86400) . "') as amount_yeasterday, (SELECT SUM(amount) FROM {{tw_accounts_income}} WHERE tid=a.id) as amount_all FROM {{tw_accounts}} a WHERE " . implode(" AND ", $crt) . " ORDER BY {$order} LIMIT " . $pages->getOffset() . ", " . $pages->getLimit())->queryAll(true, $params);

            foreach($list as $data) {
                $data['order_count'] = isset($requests[$data['id']]) ? $requests[$data['id']] : 0;
                $dataList[] = $data;
            }
        }

        if(Yii::app()->request->isAjaxRequest) {
            JSON::encode([
                'list'  => $this->renderPartial('_indexList', ['list' => $dataList, '_count' => $all_acc_count], true),
                'pages' => $this->renderPartial('_pages', ['pages' => $pages], true)
            ]);
            Yii::app()->end();
        } else {
            $this->render('index', [
                'list'                    => $dataList,
                'all_accounts_count'      => $all_acc_count,
                'all_accounts_moderation' => $all_accounts_moderation,
                'all_accounts_in_work'    => $all_acc_in_work,
                'pages'                   => $pages,
                'limitList'               => $this->limitList
            ]);
        }
    }

    /**
     * Форма добавление твиттер аккаунта
     */
    public function actionAdd()
    {
        $model = new Accounts();
        $model->setScenario('add');

        if($model->load($_POST) && $model->validate()) {
            if($model->authorize())
                $this->redirect($model->getRedirectUrl());
            else
                $model->addError('agreed', 'В данный момент свободных приложений нету, попробуйте позже.');
        }

        $this->render('add', array('model' => $model));
    }

    /**
     * Настройки аккаунта
     */
    public function actionSettings($act = '')
    {
        $model = new Accounts();

        if(isset($_POST['Settings']) && $act == '')
            $act = 'save';

        if(($act != '' && strlen($act) < 30))
            $model->setScenario($act);

        if($model->load($_GET, true) && $model->validate()) {
            if(Yii::app()->request->isAjaxRequest) {
                Html::json(['code' => $model->getCode(), 'message' => $model->getMessage(), 'url' => $model->getRedirectUrl()]);
            } else {
                Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/tw-ac3Q2l-core.js');

                $message = Yii::app()->redis->get('userFlash:twitter:accounts:' . Yii::app()->user->id . ':' . $model->tid);

                if($message !== false) {
                    Yii::app()->user->setFlash('tw_settings_message', ['type' => 'success', 'text' => $message]);
                    Yii::app()->redis->delete('userFlash:twitter:accounts:' . Yii::app()->user->id . ':' . $model->tid);
                }

                $this->render('settings', ['model' => $model]);
            }
        } else {
            $this->_message($model->getError(), Yii::t('main', '_error'), '/twitter/accounts');
        }
    }
}
