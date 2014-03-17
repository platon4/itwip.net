<?php

class DefaultController extends Controller
{
    public $activeMenu = 'accounts';

    public function actions()
    {
        return array(
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xC7C4C5,
                'transparent' => true,
                'minLength' => '6',
                'maxLength' => '8',
                'testLimit' => 3,
                'height' => '45',
            ),
        );
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
                'actions' => array('listen', 'lost', 'auth', 'confirm_change', 'support', 'captcha',
                    'created', 'new'),
                'roles' => array('guest'),
            ),
            array('deny',
                'users' => array('*'),
            ),
        );
    }

    protected function beforeAction($event)
    {
        $actions = array('new', 'lost', 'created');

        if (in_array(Yii::app()->controller->action->id, $actions) AND !Yii::app()->user->isGuest)
            $this->_message(Yii::t('accountsModule.accounts', 'you_alearted_auth'), Yii::t('main', '_error'), '/');
        else
            return true;
    }

    public function actionAuth($_h = '', $_next = '')
    {
        $auth = new Auth;

        if (!Yii::app()->session->offsetExists('authReturn'))
            Yii::app()->session->add('authReturn', array('host' => $_h, 'next' => $_next));

        if (!Yii::app()->user->isGuest)
        {
            if (in_array($_h, $auth->hArr))
                $this->redirect($auth->getReturnUrl());
            else
                $this->_message(Yii::t('accountsModule.accounts', 'you_alearted_auth'), Yii::t('main', '_error'), '/');

        }

        if (isset($_POST['Auth']))
        {
            $auth->attributes = $_POST['Auth'];

            if ($auth->validate() AND $auth->login())
            {
                $auth->authSuccess();

                if (Yii::app()->request->isAjaxRequest)
                {
                    echo json_encode(array('code' => 200, '_url' => $auth->getReturnUrl()));
                }
                else
                {
                    $this->redirect($auth->getReturnUrl());
                }

                Yii::app()->end();
            }
        }

        if (Yii::app()->request->isAjaxRequest)
        {
            echo json_encode(array('code' => 201, 'html' => $this->renderPartial('_authAccount', array(
                    'model' => $auth), true)));
            Yii::app()->end();
        }
        else
        {
            $this->render('auth', array('model' => $auth));
        }
    }
    /*
     * Создание новаго аккаунта
     */

    public function actionNew()
    {
        $new = new newAccount;
        $captcha = false;

        if (isset($_POST['newAccount']))
        {
            if (isset($_POST['_step']) AND $_POST['_step'] == 1)
            {
                $new->attributes = $_POST['newAccount'];

                if ($new->validate())
                {
                    $captcha = true;
                    Yii::app()->session['newAccount'] = $_POST['newAccount'];
                }
            }
            elseif (isset($_POST['_step']) AND $_POST['_step'] == 10)
            {
                $captcha = true;
                $new->scenario = 'captcha';
                $new->attributes = array_merge(Yii::app()->session['newAccount'], $_POST['newAccount']);

                if ($new->validate())
                {
                    if ($new->createAccount())
                    {
                        $captcha = false;
                        $new->sendActivationMail();

                        if (Yii::app()->request->isAjaxRequest)
                        {
                            echo json_encode(array('code' => 200, '_url' => Yii::app()->homeUrl . 'accounts/created?_k=' . $new->_getResendKey()));
                        }
                        else
                        {
                            $this->redirect(Yii::app()->homeUrl . 'accounts/created?_k=' . $new->_getResendKey());
                        }

                        Yii::app()->end();
                    }
                }
                else
                {
                    if (!$new->hasErrors('code') AND $new->hasErrors())
                    {
                        $captcha = false;
                    }
                }
            }
            else
            {
                $new->addError('_all', Yii::t('accoiuntsModule', '_new_unknown_step'));
            }
        }

        if (Yii::app()->request->isAjaxRequest)
        {
            echo json_encode(array('code' => 201, 'html' => $this->renderPartial('_newAccount', array(
                    'model' => $new, 'captcha' => $captcha), true)));
            Yii::app()->end();
        }
        else
            $this->render('new', array('model' => $new, 'captcha' => $captcha));
    }

    /**
     * Успешное создание аккаунта, повторное отправка письма
     */
    public function actionCreated($_k, $do = null)
    {
        $created = new Created;

        $created->attributes = array(
            'key' => $_k,
        );

        if ($created->validate())
        {
            if (!$created->isActivation())
            {
                if ($do == 'resend')
                {
                    if ($created->timeOut())
                    {
                        $created->reSendActivationMail();
                        $message = Yii::t('accountsModule.accounts', '_is_resend_text', array(
                                    '{email}' => $created->email));
                    }
                    else
                    {
                        $message = Yii::t('accountsModule.accounts', '_is_resend_limit', array(
                                    '{n}' => 15));
                    }
                }
                else
                {
                    $message = Yii::t('accountsModule.accounts', '_is_create_text', array(
                                '{email}' => $created->email));
                }

                $this->render('sendActivationMail', array(
                    'title' => Yii::t('accountsModule.accounts', '_is_create_title'),
                    'message' => $message,
                    'link' => $this->createUrl('/accounts/created?_k=' . $created->key . '&do=resend'),
                    'change_mail_link' => $this->createUrl('service/changeEmail'),
                    'is_html' => true,
                ));
            }
            else
            {
                $this->render('application.views.main.info', array('title' => Yii::t('main', '_info'),
                    'message' => Yii::t('accountsModule.accounts', '_error_is_activation'),
                    'link' => '/accounts/auth', 'link_screen' => Yii::t('accountsModule.accounts', '_auth_page')));
            }
        }
        else
        {
            $this->_message($created->getError(), Yii::t('main', '_error'));
        }
    }

    public function actionLost()
    {
        $lostform = new lostAccount;

        if (isset($_POST['lostAccount']))
        {
            $lostform->attributes = $_POST['lostAccount'];

            if ($lostform->validate())
            {
                $lost = lostAccount::model()->find('email=?', array($lostform->email));

                if ($lost->id)
                {
                    $lostPass = new lostPassword;

                    $key = CHelper::_md5(time() . $_SERVER['REMOTE_ADDR'] . $lost->email . (time() * (rand(50, 1000))));

                    $email = Yii::app()->email;

                    $email->to = $lost->email;
                    $email->view = "_lost_password";
                    $email->viewVars = array('type' => 'link', 'link' => Yii::app()->homeUrl . 'accounts/service/lostPassword?_k=' . $key,
                        'ip' => CHelper::_getIP());
                    $email->from = Yii::app()->params['robot_email'];

                    $email->subject = Yii::t('accountsModule.accounts', '_email_lost_subject', array(
                                '{site_title}' => Yii::app()->name));

                    $email->send();

                    $lostPass->acc_id = $lost->id;
                    $lostPass->lost_key = $key;
                    $lostPass->date = time();
                    $lostPass->ip = CHelper::_getIP();

                    lostPassword::model()->deleteAll("`acc_id` = :acc_id", array(
                        ':acc_id' => $lost->id));

                    $lostPass->save();

                    $this->redirect(Yii::app()->homeUrl . 'accounts/service/lostPassword/send');
                }
                else
                    $lostform->addError('email', Yii::t('index', '_email_lost_not_found'));
            }
        }

        $this->render('lost', array('model' => $lostform));
    }

    public function actionSupport()
    {
        $form = new Support;
        $_send = false;

        if (isset($_POST['Support']))
        {
            if (isset(Yii::app()->session['_support']) AND Yii::app()->session['_support'] == 'yes')
            {
                if (Yii::app()->user->isGuest)
                    $form->scenario = 'guest';

                $form->attributes = $_POST['Support'];

                if ($form->validate())
                {
                    $form->_ip = CHelper::_getIP();
                    $form->_date = date("Y-m-d H:i:s");

                    if (Yii::app()->user->isGuest)
                    {

                        if (!Support::model()->count('_ip=:_ip AND _date>="' . date('Y-m-d H:i:s', time() - 3 * 60) . '"', array(
                                    ':_ip' => $form->_ip)))
                        {
                            $email = Yii::app()->email;

                            $email->to = 'support@itwip.net';
                            $email->view = "support";
                            $email->viewVars = array('mail' => $form->_email, 'ip' => $form->_ip,
                                'browse' => CHelper::_getBrowse(), 'text' => $form->_text);
                            $email->from = $form->_email;

                            $email->subject = Yii::t('accountsModule.message', '_support_subject', array(
                                        '{site_title}' => Yii::app()->name));

                            $email->send();
                            $_send = true;
                        }
                        else
                            $this->_message(Yii::t('accountsModule.message', '_support_flood_protect', array(
                                        '{min}' => 3)), Yii::t('main', '_error'), '/');
                    }
                    else
                    {
                        $form->owner_id = Yii::app()->user->id;
                        $form->user_read = 1;

                        if ($form->save())
                        {
                            $command = Yii::app()->db->createCommand('INSERT INTO {{tickets_messages}} (ticket_id,_text,_date) VALUES (:t_id,:text,:date)');
                            $command->execute(array(
                                ':t_id' => Yii::app()->db->getLastInsertID(),
                                ':text' => $form->_text,
                                ':date' => date("Y-m-d H:i:s"),
                            ));

                            $command = Yii::app()->db->createCommand('UPDATE {{accounts}} SET mail_all = mail_all+1 WHERE id = :id');
                            $command->execute(array(':id' => Yii::app()->user->id));

                            $_send = true;

                            unset(Yii::app()->session['_support']);
                            Notifications::admins(false, Yii::t('accountsModule.message', '_new_request_to_support') . "\n----------------------------------\nТема:\n" . $form->_subject . "\n----------\nТекст:\n" . $form->_text);
                        }
                        else
                            $this->_message(Yii::t('accountsModule.message', '_no_support_query_create'), Yii::t('main', '_error'), '/support');
                    }
                }
            }
            else
                $this->redirect('/support');
        }
        else
            Yii::app()->session['_support'] = 'yes';

        $this->render('support', array('form' => $form, '_send' => $_send));
    }

    public function actionConfirm_change($_h)
    {
        if (strlen($_h) == 32)
        {
            $model = dataChange::model()->find('_hash=:hash', array(':hash' => $_h));

            if (count($model))
            {
                $data = unserialize($model->_data);
                $user = User::model()->findByPk($model->account_id);
                $settings = unserialize($user->_settings);

                if (count($data))
                {
                    $update = array();
                    $bound = array();

                    $params = array(
                        'email' => array('name' => 'email', 'type' => 'table'),
                        'new_password' => array('name' => 'password', 'type' => 'table'),
                        'purse' => array('name' => 'purse', 'type' => 'settings'),
                    );

                    foreach ($data as $key => $value)
                    {
                        if (isset($params[$key]))
                        {
                            if ($params[$key]['type'] == 'settings')
                            {
                                $settings[$params[$key]['name']] = $value;
                            }
                            else
                                $user->$params[$key]['name'] = $value;
                        }
                    }

                    $user->_settings = serialize($settings);
                    $user->save();
                    $model->delete();

                    $this->_message(Yii::t('accountsModule.settings', '_data_save_change_succes'), '', '/accounts/settings/');
                }
                else
                    throw new CHttpException('500', Yii::t('accountsModule.settings', '_error_change_data_params_invalid'));
            }
            else
                throw new CHttpException('403', Yii::t('accountsModule.settings', '_error_change_data_link_invalid'));
        }
        else
            throw new CHttpException('403', Yii::t('yii', 'Your request is invalid.'));
    }

    public function actionListen()
    {
        
    }
}
