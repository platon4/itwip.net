<?php

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

    public function actionIndex()
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

        $all_accounts = Yii::app()->db->createCommand("SELECT COUNT(*) as count,_status FROM {{tw_accounts}} WHERE owner_id=:owner_id GROUP BY _status")->queryAll(TRUE, array(
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

        if($all_acc_count) {
            $orderArr = array('posted' => 'fulfilled', 'order' => 'orders', 'itr' => 'itr',
                              'status' => '_status', 'last' => 'date_add');
            $order = NULL;

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

            $orderData = Yii::app()->db->createCommand("SELECT COUNT(tt.id) as count,tt._tw_account FROM {{tweets_to_twitter}} tt INNER JOIN {{tw_accounts}} a ON tt._tw_account=a.id WHERE tt.approved=0 AND tt.status=0 AND a.owner_id=:owner GROUP BY tt._tw_account")->queryAll(TRUE, array(
                ':owner' => Yii::app()->user->id));

            $requests = array();
            foreach($orderData as $ord) {
                $requests[$ord['_tw_account']] = $ord['count'];
            }

            $list = Yii::app()->db->createCommand("SELECT id,screen_name,name,avatar,itr,_status,in_yandex,(SELECT COUNT(*) FROM {{tweets_to_twitter}} WHERE approved=1 AND _tw_account=a.id AND status=0) as orders, (SELECT COUNT(*) FROM {{tw_tweets}} WHERE tid=a.id) as fulfilled, (SELECT SUM(amount) FROM {{tw_accounts_income}} WHERE tid=a.id AND _date='" . date('Y-m-d') . "') as amount_today, (SELECT SUM(amount) FROM {{tw_accounts_income}} WHERE tid=a.id AND _date='" . date('Y-m-d', time() - 86400) . "') as amount_yeasterday, (SELECT SUM(amount) FROM {{tw_accounts_income}} WHERE tid=a.id) as amount_all FROM {{tw_accounts}} a WHERE " . implode(" AND ", $crt) . " ORDER BY {$order} LIMIT " . $pages->getOffset() . ", " . $pages->getLimit())->queryAll(TRUE, $params);

            $dataList = array();

            foreach($list as $data) {
                $data['order_count'] = isset($requests[$data['id']]) ? $requests[$data['id']] : 0;
                $dataList[] = $data;
            }
        }

        if(Yii::app()->request->isAjaxRequest) {
            JSON::encode([
                'list'  => $this->renderPartial('_indexList', ['list' => $dataList, '_count' => $all_acc_count], TRUE),
                'pages' => $this->renderPartial('_pages', ['pages' => $pages], TRUE)
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
    public function actionAdd($id = '', $_e = '', $_k = '')
    {
        $_code = (CHelper::int($_e)) ? CHelper::int($_e) : FALSE;

        if($_code) {
            if($_code == 200 AND CHelper::int($id) AND trim($_k) == md5(CHelper::_getIP() . $id . Yii::app()->params['twitter']['salt'])) {
                $this->render("_succes_add", array('id' => $id));
            } else {
                $errorData = require Yii::app()->getModulePath() . '/twitter/data/error.db.php';

                if(isset($errorData[$_code])) {
                    $info_data = array($errorData[$_code]['title'], $errorData[$_code]['message'],
                        $errorData[$_code]['link']);
                } else {
                    $info_data = array($errorData[0]['title'], $errorData[0]['message'],
                        $errorData[0]['link']);
                }

                $this->render("application.views.main.info", array('is_html' => TRUE,
                                                                   'title'   => $info_data[0], 'message' => $info_data[1], 'link' => $info_data[2]));
            }
        } else {
            $this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_twitterAccountAdd_Title');
            $this->metaDescription = Yii::t('main', '_twitterAccountAdd_Description');

            $form = new addAccounts;

            if(isset($_POST['addAccounts'])) {
                /*
                 * Форма получена
                 */
                $form->attributes = $_POST['addAccounts'];

                /*
                 * Валидация данных
                 */
                if($form->validate()) {
                    $twApp = Yii::app()->db->createCommand("SELECT * FROM {{tw_application}} WHERE tw_accounts < 1000 AND _is_active=1 ORDER BY RAND()")->queryRow();

                    if($twApp['id']) {
                        $this->redirect($twApp['_url'] . "/twitter/oAuth/authorize?id=" . Yii::app()->user->id . "&_k=" . md5($_SERVER['REMOTE_ADDR'] . $twApp['id'] . Yii::app()->user->id . Yii::app()->params['twitter']['salt']));
                        Yii::app()->end();
                    } else {
                        $form->addError('_error', Yii::t('twitterModule.accounts', '_app_not_found'));
                    }
                }
            }

            $this->render('add', array('model' => $form));
        }
    }

    /**
     * Настройки аккаунта
     */
    public function actionSettings($tid = 0, $remove = 0, $_e = 0, $_u = 0)
    {
        if(CHelper::int($tid) AND !$_e) {
            $settings = Settings::model()->with('accounts')->findByPk($tid);
            $model = $settings->accounts;
            if($model->id) {
                if($model->owner_id == Yii::app()->user->id) {
                    if($_u == 1)
                        Yii::app()->user->setFlash("_settings_save_success", Yii::t('twitterModule.accounts', '_flash_key_update'));

                    if(!$settings->_timeout OR $settings->_timeout < Yii::app()->params['twitter']['posting_timeout']) {
                        $settings->_timeout = Yii::app()->params['twitter']['posting_timeout'];
                    }

                    if($remove == 1) {
                        $this->removeAccounts($model, TRUE);
                    } else {
                        if(isset($_POST['Settings'])) {
                            $settings->attributes = $_POST['Settings'];
                            $filterParams = array();
                            $sbj = (isset($_POST['subject']) AND is_array($_POST['subject'])) ? $_POST['subject'] : array();

                            if(isset($_POST['Filter'])) {
                                foreach(Yii::app()->params['twitter']['filters'] as $k => $v) {
                                    foreach($_POST['Filter'] as $key => $value) {
                                        if($key == $v AND $value == 1) {
                                            $filterParams[] = $k;
                                        }
                                    }
                                }
                            }

                            if(in_array(3, $filterParams)) {
                                $settings->scenario = 'filter';
                            }

                            $settings->_stop = implode(",", $filterParams);

                            $ids = array();

                            foreach($sbj as $_k) {
                                if(in_array($_k, $ids)) {
                                    $settings->_addError('_subject', Yii::t('twitterModule.accounts', '_subject_add_dublicat'));
                                }

                                $ids[] = $_k;
                            }

                            if(count($ids) > 1 AND in_array(0, $ids)) {
                                $settings->_addError('_subject', Yii::t('twitterModule.accounts', '_subject_add_need_select'));
                            }

                            $settings->_subjects = implode(",", $ids);

                            if($settings->validate()) {
                                if(Yii::app()->user->_setting('_preferred_currency')) {
                                    $settings->_price = CMoney::convert($settings->_price);
                                }

                                if($settings->save())
                                    Yii::app()->user->setFlash("_settings_save_success", Yii::t('main', '_flash_settings_update'));
                            }

                            if($settings->_price <= 0) {
                                $settings->_price = ($model->in_yandex) ? CMoney::_c(CMoney::itrCost($model->itr) + 2) : CMoney::_c(CMoney::itrCost($model->itr));
                            }
                        }

                        if(Yii::app()->user->_setting('_preferred_currency')) {
                            $settings->_price = CMoney::_c($settings->_price);
                        }

                        $last_update = array('yandex_rank' => 0, 'in_yandex' => 0, 'in_google' => 0,
                                             'google_pr'   => 0);

                        $last_row = Yii::app()->redis->hGetAll('twitter:accounts:statsUpdate:' . $model->id);

                        if(count($last_row)) {
                            foreach($last_row as $_row) {
                                if($_row['last_update'] > (time() - (Yii::app()->params['twitter']['update_interval'][$_row['_type']] * 60))) {
                                    if(isset($last_update[$_row['_type']])) {
                                        $last_update[$_row['_type']] = 1;
                                    }
                                }
                            }
                        }

                        $ageData = require Yii::app()->getModulePath() . '/twitter/data/_age.php';
                        $subjects = Html::groupByKey(Subjects::model()->_getAll(array(
                            'order' => 'sort')), 'id', '_key', 'parrent');

                        $_subjects = explode(",", $settings->_subjects);
                        $_subject_html = "";

                        if(count($_subjects)) {
                            $i = 0;

                            foreach($_subjects as $_id) {
                                $_subject_html .= $this->renderPartial('application.modules.twitter.views.default._subjectsDropDownList', array(
                                    'remove'   => (!$i) ? 0 : 1, 'selected' => $_id, 'bid' => '_subjects_0',
                                    'subjects' => $subjects), TRUE);

                                foreach($subjects as $zs3q => $a3q2z) {
                                    if($zs3q == $_id) {
                                        unset($subjects[$zs3q]);
                                    } else {
                                        if(is_array($a3q2z)) {
                                            foreach($a3q2z as $u3n6 => $bb3q) {
                                                foreach($bb3q as $ak => $av) {
                                                    if($ak == $_id) {
                                                        unset($subjects[$zs3q][$u3n6][$ak]);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                $i++;
                            }
                        } else {
                            $_subject_html = $this->renderPartial('application.modules.twitter.views.default._subjectsDropDownList', array(
                                'selected' => 0, 'bid' => '_subjects_0', 'subjects' => $subjects), TRUE);
                        }

                        $settings->_price = round($settings->_price, 2);

                        Yii::app()->clientScript->registerScriptFile(Yii::app()->request->baseUrl . '/js/tw-settings-core.js');
                        $this->render('settings', array('settings' => $settings, '_subject_html' => $_subject_html,
                                                        'filter'   => $this->_filters($settings->_stop), 'ageData' => $ageData,
                                                        'model'    => $model, 'last_update' => $last_update));
                    }
                } else {
                    throw new CHttpException(403, Yii::t('twitterModule.accounts', '_error_accounts_owner'));
                }
            } else {
                throw new CHttpException(404, Yii::t('twitterModule.accounts', '_error_no_accounts'));
            }
        } else if($_e) {
            $errorData = require Yii::app()->getModulePath() . '/twitter/data/error.db.php';

            if(isset($errorData[$_e])) {
                $info_data = array($errorData[$_e]['title'], $errorData[$_e]['message'],
                    '/twitter/accounts');
            } else {
                $info_data = array($errorData[0]['title'], $errorData[0]['message'],
                    '/twitter/accounts');
            }

            $this->render("application.views.main.info", array('is_html' => TRUE,
                                                               'title'   => $info_data[0], 'message' => $info_data[1], 'link' => '/twitter/accounts'));
        } else
            throw new CHttpException(403, Yii::t('twitterModule.accounts', '_error_accounts_query'));
    }

    public function actionReAuth($tid)
    {
        $this->reAuthorize($tid);
    }

    public function actionReCheck($tid)
    {
        $c = new reCheck;

        $c->attributes = array('id' => $tid);

        if($c->validate())
            Yii::app()->user->setFlash('tw_settings_message', array('text' => Yii::t('twitterModule.accounts', '_accounts_succes_recheck'), 'type' => 'success'));
        else
            Yii::app()->user->setFlash('tw_settings_message', array('text' => $c->getError(), 'type' => 'error'));

        $this->redirect('/twitter/accounts/settings?tid=' . $c->id);
        Yii::app()->end();
    }

    protected function reAuthorize($tid)
    {
        if(CHelper::int($tid)) {
            $row = Yii::app()->db->createCommand("SELECT a.id,a._url,t._status FROM {{tw_accounts}} t INNER JOIN {{tw_application}} a ON t.app=a.id WHERE t.id=:id")->queryRow(TRUE, array(
                ':id' => $tid));

            if($row !== FALSE) {
                if(($row['_status'] == 4 OR $row['_status'] == 6) OR Yii::app()->user->checkAccess('admin')) {
                    $this->redirect($row['_url'] . '/twitter/oAuth/reAuthorize?id=' . $tid . '&_k=' . md5(CHelper::_getIP() . $row['id'] . $tid . Yii::app()->params['twitter']['salt']));
                    Yii::app()->end();
                } else {
                    throw new CHttpException(501, Yii::t('twitterModule.accounts', '_error_reauth_not_condition'));
                }
            } else
                throw new CHttpException(500, Yii::t('twitterModule.accounts', '_error_reauth_not_found'));
        }
    }

    protected function _filters($data)
    {
        $return = array();
        $dArr = explode(",", $data);

        foreach(Yii::app()->params['twitter']['filters'] as $k => $v) {
            if(in_array($k, $dArr)) {
                $return[$v] = 1;
            } else {
                $return[$v] = 0;
            }
        }

        return CHelper::tObject($return, 'Filter');
    }

    //Functions
    protected function removeAccounts($obj, $show = FALSE)
    {
        TwitterApp::model()->updateCounters(array('tw_accounts' => -1), "id=:id", array(
            ':id' => $obj->app));
        Accounts::model()->deleteByPk($obj->id);
        Settings::model()->deleteByPk($obj->id);
        Yii::app()->db->createCommand("DELETE FROM {{tw_update}} WHERE tw_id=" . $obj->id)->execute();
        Yii::app()->db->createCommand("DELETE FROM {{tw_accounts_stats}} WHERE tw_id=" . $obj->id)->execute();

        Logs::save("tw-list", "Date: " . date('d.m.Y H:i:s') . "; ID:" . $obj->id . "; Login:" . $obj->screen_name . "; Owner:" . $obj->owner_id . "\n", 'remove_tw', 'a+');

        if($show) {
            $this->render("application.views.main.info", array('title'   => Yii::t('twitterModule.accounts', '_account_is_delete_title'),
                                                               'message' => Yii::t('twitterModule.accounts', '_account_is_delete_text'),
                                                               'link'    => '/twitter/accounts/', 'link_screen' => Yii::t('twitterModule.accounts', '_to_accounts_list')));
        }
    }
}
