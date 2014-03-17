<?php

namespace common\api\twitter;

use Yii;

class oAuth extends \common\api\twitter\libraries\tmhOAuth
{
    public function __construct($config = [])
    {
        if(!isset($config['ip']))
            $config['ip'] = null;

        parent::__construct(array_merge($config, [
            'curl_ssl_verifyhost' => 3
        ]));
    }

    public function auth_request($callBack)
    {
        $code = $this->request('POST', $this->url('oauth/request_token', ''), array(
            'oauth_callback' => $callBack
        ));

        echo $code;

        if($code === 200) {
            Yii::$app->session->set('oAtuh', $this->extract_params($this->response['response']));
            $this->redirect($this->url("oauth/authorize", '') . "?oauth_token=" . Yii::$app->session->get('oAuth')['oauth_token']);
        } else {
            return $this;
        }
    }
} 