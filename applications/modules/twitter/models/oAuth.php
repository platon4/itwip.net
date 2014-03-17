<?php

namespace app\modules\twitter\models;

use Yii;
use common\helpers\Url;

class oAuth extends \app\components\Model
{
    public $token;
    protected $_returnUrl;
    public $oauth_verifier;
    protected $pattern = "/^[a-zA-Z0-9_-]{5,20}+$/";
    protected $_data;

    public function rules()
    {
        return [
            ['token', 'required', 'on' => ['update', 'auth', 'add']],
            ['token', 'dataValidate', 'on' => ['update', 'auth', 'add']],
            ['token', 'update', 'on' => ['update']],
            ['token', 'auth', 'on' => ['auth']],
            ['oauth_verifier', 'required', 'on' => ['add']],
            ['token', 'addAccount', 'on' => 'add']
        ];
    }

    public function dataValidate()
    {
        if (preg_match($this->pattern, $this->token) && Yii::$app->redis->exists('twitter:accounts:auth:' . $this->token) === true) {
            $data = json_decode(Yii::$app->redis->get('twitter:accounts:auth:' . $this->token), true);

            if ($data['hash'] = md5(Yii::$app->request->getUserIP() . $data['owner_id'] . $data['account_id'] . $data['app'] . $this->token)) {
                $this->_data = $data;
            } else {
                $this->addError('token', Yii::t('yii', 'Некорректный запрос, попробуйте повторить процедуру заново.'));
                $this->_returnUrl = rtrim(Yii::$app->homeUrl, '/') . '/twitter/accounts';
            }
        } else {
            $this->addError('token', Yii::t('yii', 'Некорректный запрос, попробуйте повторить процедуру заново.'));
            $this->_returnUrl = rtrim(Yii::$app->homeUrl, '/') . '/twitter/accounts';
        }
    }

    public function addAccount()
    {
        $request = (new \common\api\twitter\oAuth([
            'consumer_key' => $this->_data['app_key'],
            'consumer_secret' => $this->_data['app_secret'],
            'ip' => $this->_data['app_ip']
        ]))->auth_credentials($this->oauth_verifier, $this->_data['owner_id']);

        if ($request instanceof \common\api\twitter\oAuth) {
            $this->addError('twitter', 'Не удалось получить данные с твиттера, пожалуйста, попробуйте еще раз.');
        } else {
            $model = new Account();
            $model->load($request, '');

            if ($model->validate()) {
                Yii::$app->getResponse()->redirect(Url::homeUrl() . '/twitter/accounts?act=new');
            } else {
                $this->addError('token', $model->getError());
            }
        }
    }

    public function update()
    {
        //$this->authProcess($data);
    }

    public function auth()
    {
        $request = (new \common\api\twitter\oAuth([
            'consumer_key' => $this->_data['app_key'],
            'consumer_secret' => $this->_data['app_secret'],
            'ip' => $this->_data['app_ip']
        ]))->auth_request(Url::getHostUrl() . '/twitter/oauth/process?token=' . $this->token, $this->_data['owner_id']);

        if ($request instanceof \common\api\twitter\oAuth) {
            $this->addError('twitter', 'Не удалось подключится к твиттеру, пожалуйста, попробуйте позже.');
        }
    }

    public function getErrorName()
    {
        return 'Ошибка';
    }

    public function getError()
    {
        if (parent::hasErrors()) {
            return current(current(parent::getErrors()));
        } else {
            return '';
        }
    }

    public function getReturnUrl()
    {
        if ($this->_returnUrl !== null)
            return $this->_returnUrl;
        else
            return Yii::$app->homeUrl;
    }
}