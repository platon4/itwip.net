<?php

class Auth extends FormModel
{

    public $email;
    public $password;
    public $rememberMe;
    private $_identity;
    protected $settings;
    protected $user;
    protected $_attemps_count;
    public $hArr = array('community');

    public function rules()
    {
        return array(
            array('password', 'length', 'min' => 3),
            array('email', 'length', 'max' => 55),
            array('email', 'email'),
            array('rememberMe', 'boolean'),
            array('email', 'required'),
            array('password', 'required'),
            array('password', '_attemps'),
            array('password', 'authenticate'),
            array('email', '_isActive'),
        );
    }

    public function attributeLabels()
    {
        return array(
            'email'    => Yii::t('index', '_email_reg_place'),
            'password' => Yii::t('index', '_password_reg_place'),
        );
    }

    public function _isActive()
    {
        if(!$this->_getUser('status')) {
            Yii::app()->user->logout();
            $this->addError('email', Yii::t('accountsModule.accounts', '_account_is_not_activation'));
        }
    }

    public function _attemps()
    {
        $attemps_count = Yii::app()->db->createCommand("SELECT id,_count,date,block_date FROM {{attemps}} WHERE ip=:ip")->queryRow(true, array(
            ':ip' => CHelper::_getIP()));
        $block_time = ($attemps_count['_count'] >= 6) ? $attemps_count['_count'] * (($attemps_count['_count'] / 2) + 5) * 60 : ($attemps_count['_count'] * 5) * 60;

        if($attemps_count['_count'] >= 3 AND $attemps_count['block_date'] > time()) {
            $this->addError('password', Yii::t('accountsModule.accounts', '_user_auth_attemps', array(
                '{min}' => round($block_time / 60))));
        }

        $this->_attemps_count = $attemps_count['_count'];
    }

    public function _getUser($key)
    {
        return isset($this->user->$key) ? $this->user->$key : null;
    }

    public function _getSetting($key)
    {
        if($this->settings === null) {
            $this->settings = @unserialize($this->_getUser('_settings'));
        }

        return isset($this->settings[$key]) ? $this->settings[$key] : null;
    }

    /**
     * Authenticates the password.
     * This is the 'authenticate' validator as declared in rules().
     */
    public function authenticate($attribute, $params)
    {
        $this->_identity = new UserIdentity($this->email, $this->password);
        $this->user = $this->_identity->_getData();

        if(!$this->_identity->authenticate()) {
            $this->addError('password', Yii::t('index', '_error_email_or_password'));
            $this->authFailed();
        }
    }

    /**
     * Logs in the user using the given username and password in the model.
     * @return boolean whether login is successful
     */
    public function login()
    {
        if($this->_identity === null) {
            $this->_identity = new UserIdentity($this->email, $this->password);
            $this->_identity->authenticate();
        }
        if($this->_identity->errorCode === UserIdentity::ERROR_NONE) {
            Yii::app()->user->login($this->_identity, $this->rememberMe ? 3600 * 24 * 30 : 0);
            return true;
        } else {
            $this->authFailed();
            return false;
        }
    }

    public function authFailed()
    {
        $block_time = ($this->_attemps_count >= 6) ? $this->_attemps_count * (($this->_attemps_count / 2) + 5) * 60 : ($this->_attemps_count * 5) * 60;

        if($this->_getUser('id')) {
            Yii::app()->db->createCommand("INSERT INTO {{auth_logs}} (accounts_id,_ip,_date,_browse) VALUES (:accounts_id,:_ip,:_date,:_browse)")
                ->execute(array(
                    ':accounts_id' => $this->_getUser('id'),
                    ':_ip'         => CHelper::_getIP(),
                    ':_date'       => date("Y-m-d H:i:s"),
                    ':_browse'     => CHelper::_getBrowse(),
                ));

            if(trim($this->_getSetting('_icq')) != '' AND $this->_getSetting('icq_attemps_notification')) {
                CHelper::toICQ($this->_getSetting('_icq'), Yii::t('accountsModule.accounts', '_auth_attemps', array(
                    '{count}' => $this->_attemps_count, '{browse}' => CHelper::_getBrowse(),
                    '{ip}'    => CHelper::_getIP())));
            }

            if($this->_getSetting('email_attemps_notification')) {
                $email = Yii::app()->email;

                $email->to = $this->_getUser('email');
                $email->view = "_auth_attemps";
                $email->viewVars = array('browse' => CHelper::_getBrowse(), 'ip' => CHelper::_getIP());
                $email->from = Yii::app()->params['robot_email'];

                $email->subject = Yii::t('accountsModule.accounts', '_auth_attemps_title', array(
                    '{site_title}' => Yii::app()->name));

                $email->send();
            }
        }

        Yii::app()->db->createCommand("INSERT INTO {{attemps}} (user_id,ip,date,_count) VALUES (:user_id,:ip,:date,:_count) ON DUPLICATE KEY UPDATE date='" . time() . "',_count=_count+1, block_date=:block_date")
            ->execute(array(
                ':user_id'    => $this->_getUser('id'),
                ':ip'         => CHelper::_getIP(),
                ':date'       => time(),
                ':_count'     => 1,
                ':block_date' => time() + $block_time,
            ));
    }

    public function authSuccess()
    {
        Yii::app()->db->createCommand("UPDATE {{accounts}} SET _ip=:ip,_date_last_visit=:last WHERE id=:id")
            ->execute(array(
                ':ip'   => CHelper::_getIP(),
                ':last' => date('Y-m-d H:i:s'),
                ':id'   => $this->_getUser('id'),
            ));

        Yii::app()->db->createCommand("INSERT INTO {{auth_logs}} (accounts_id,_ip,_date,_browse,_success) VALUES (:accounts_id,:_ip,:_date,:_browse,:_success)")
            ->execute(array(
                ':accounts_id' => $this->_getUser('id'),
                ':_ip'         => CHelper::_getIP(),
                ':_date'       => date("Y-m-d H:i:s"),
                ':_browse'     => CHelper::_getBrowse(),
                ':_success'    => 1
            ));

        Yii::app()->db->createCommand("DELETE FROM {{attemps}} WHERE ip=:ip")->execute(array(
            ':ip' => CHelper::_getIP()));

        Yii::app()->fdb->createCommand("UPDATE {{users}} SET logdate=:time WHERE id=:id")->execute(array(
            ':id' => $this->_getUser('id'), ':time' => date('Y-m-d H:i:s')));
    }

    public function getReturnUrl()
    {
        $params = array('host' => '', 'next' => '');
        $params = Yii::app()->session->get('authReturn');
        Yii::app()->session->remove('authReturn');

        if(in_array($params['host'], $this->hArr)) {
            if(substr($params['next'], 0, 1) == '/')
                $params['next'] = substr($params['next'], 1);

            return 'http://' . $params['host'] . '.' . str_replace(['http://', 'https://'], '', Yii::app()->homeUrl) . $params['next'];
        } else {
            return Yii::app()->user->returnUrl;
        }
    }
}
