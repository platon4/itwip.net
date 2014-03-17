<?php

namespace app\modules\twitter\models;

use Yii;

class oAuth extends \app\components\Model
{
    public $token;
    protected $_returnUrl;
    protected $pattern = "/^[a-zA-Z0-9_-]{5,20}+$/";
    protected $_data;

    public function rules()
    {
        return [
            ['token', 'dataValidate', 'on' => ['update', 'auth']],
            [['token'], 'update', 'on' => ['update']],
            [['token'], 'auth', 'on' => ['auth']],
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

    public function update()
    {
        //$this->authProcess($data);
    }

    public function auth()
    {
        //$this->authProcess($data);
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

    protected function authProcess()
    {
        $request = (new \common\api\twitter\oAuth([
            'user_key' => $data['user_key'],
            'user_secret' => $data['user_key'],
            'consumer_key' => $data['app_key'],
            'consumer_secret' => $data['app_secret']
        ]))->auth_request();

        if ($request instanceof \common\api\twitter\oAuth) {

        }
    }
}