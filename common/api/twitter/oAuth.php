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

    public function auth_request($callBack, $owner_id)
    {
        $code = $this->request('POST', $this->url('oauth/request_token', ''), array(
            'oauth_callback' => $callBack
        ));

        if($code === 200) {
            $params = $this->extract_params($this->response['response']);
            Yii::$app->redis->set('twitter:accounts:auth:token:' . $owner_id, json_encode($params));
            Yii::$app->redis->expire('twitter:accounts:auth:token:' . $owner_id, 24 * 60 * 60);
            Yii::$app->getResponse()->redirect($this->url("oauth/authorize", '') . "?oauth_token=" . $params['oauth_token']);
        } else {
            return $this;
        }
    }
} 