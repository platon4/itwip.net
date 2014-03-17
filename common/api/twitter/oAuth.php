<?php

namespace common\api\twitter;

use Yii;

class oAuth extends \common\api\twitter\libraries\tmhOAuth
{
    public $_data;

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

            $this->set('oauth', $owner_id, json_encode($params));

            Yii::$app->getResponse()->redirect($this->url("oauth/authorize", '') . "?oauth_token=" . $params['oauth_token']);
        } else {
            return $this;
        }
    }

    public function auth_credentials($oauth_verifier, $user_id)
    {
        $this->config['user_token'] = $this->get('oauth', $user_id, 'oauth_token');
        $this->config['user_secret'] = $this->get('oauth', $user_id, 'oauth_token_secret');

        $code = $this->request('POST', $this->url('oauth/access_token', ''), array(
            'oauth_verifier' => $oauth_verifier,
        ));

        if($code == 200) {
            $this->set('access_token', $user_id, $this->extract_params($this->response['response']));
            $this->remove('oauth', $user_id);

            $this->config['user_token'] = $this->get('access_token', $user_id, 'oauth_token');
            $this->config['user_secret'] = $this->get('access_token', $user_id, 'oauth_token_secret');

            $_code = $this->request('GET', $this->url('1.1/account/verify_credentials.json?skip_status=false'));

            if($_code == 200)
                return json_decode($this->response['response'], true);
        }

        return $this;
    }

    public function set($key, $user_id, $value)
    {
        if(is_array($value))
            $value = json_encode($value);

        Yii::$app->redis->set('twitter:accounts:auth:token:' . $key . ':' . $user_id, $value);
        Yii::$app->redis->expire('twitter:accounts:auth:token:' . $key . ':' . $user_id, 24 * 60 * 60);
    }

    public function get($key, $user_id, $field)
    {
        if(!isset($this->_data[$key]) || $this->_data[$key] === null) {
            $data = Yii::$app->redis->get('twitter:accounts:auth:token:' . $key . ':' . $user_id);

            if($data !== false)
                $this->_data[$key] = json_decode($data, true);
            else
                $this->_data[$key] = false;
        }

        return isset($this->_data[$key][$field]) ? $this->_data[$key][$field] : false;
    }

    public function remove($key, $user_id)
    {
        Yii::$app->redis->delete('twitter:accounts:auth:token:' . $key . ':' . $user_id);
    }
} 