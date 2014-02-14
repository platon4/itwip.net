<?php

/**
 * @author eolitich
 * 
 *  ICQ: 603432370
 * 
 * File: TwitterBot
 */
class pmhBot
{

    const VERSION = '0.0.1';

    protected $path;
    protected $response = array();
    protected $isAuth = false;
    protected $authKey;
    protected $error = array();
    protected $url;
    protected $params = array('browser' => null, 'referer' => null, 'ip' => null);
    protected $headers = array();
    protected $request_params = array();
    protected $hToParse;
    protected $user_agents = array(
        0 => 'Mozilla/5.0 (X11; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0',
    );

    public function __construct()
    {
        $this->path = dirname(__FILE__);
        $this->config = array(
            'timezone' => 'UTC',
            'use_ssl' => true,
            'force_nonce' => false,
            'nonce' => false,
            'force_timestamp' => false,
            'timestamp' => false,
            'curl_connecttimeout' => 30,
            'curl_timeout' => 10,
            'curl_ssl_verifyhost' => 2,
            'curl_ssl_verifypeer' => true,
            'curl_cainfo' => dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cacert.pem',
            'curl_capath' => dirname(__FILE__),
        );
    }

    public function run($login, $password)
    {
        if ($this->authCheck($login, $password))
        {
            
        }
        else
        {
            $this->authorization($login, $password);
        }

        return $this;
    }

    protected function authorization($login, $password)
    {
        $this->isAuth = true;
        $this->referer = 'https://twitter.com/';
        $this->request('http://itwip.net/test.php', 'POST', array('session[username_or_email]' => $login, 'session[password]' => $password, 'remember_me' => 1, 'return_to_ssl' => 'true', 'scribe_log' => null, 'authenticity_token' => $authenticity_token), array());
    }

    protected function authCheck($login, $password)
    {
        $account = Yii::app()->redis->get(md5('pmhBot:' . $login . $password));
        $account = $account === false ? false : json_decode($account);

        if (isset($account->isAuth))
        {
            $this->params['browser'] = isset($account->browser) ? $account->browser : $this->user_agents[0];
            $this->params['ip'] = isset($account->ip) ? $account->browser : $this->user_agents[0];
        }
        else
        {
            $this->user_agent = $this->user_agents[0];
        }

        return true;
    }

    public function sendTweet()
    {
        $this->setError(403, 'Authentication failed.');

        return false;
    }

    public function getCode()
    {
        return isset($this->errors['code']) ? $this->errors['code'] : 0;
    }

    public function getMessage()
    {
        return isset($this->errors['message']) ? $this->errors['message'] : '';
    }

    public function hasError()
    {
        return $this->error !== array() ? true : false;
    }

    public function getError()
    {
        return $this->error;
    }

    protected function setError($code, $msg = '')
    {
        $this->error = array('code' => $code, 'message' => $msg);
    }

    protected function request($url, $method = 'GET', $params = array(), $headers = array(), $multipart = false)
    {
        if (!empty($headers))
            $this->headers = array_merge((array) $this->headers, (array) $headers);

        $this->url = $url;

        switch ($method)
        {
            case 'POST':
                $this->request_params = $params;
                break;
            default:
                if ($params !== array())
                {
                    foreach ($params as $k => $v)
                        $params[] = $k . '=' . $v;

                    $qs = implode('&', $params);
                    $this->url = strlen($qs) > 0 ? $this->url . '?' . $qs : $this->url;
                    $this->request_params = array();
                }
                break;
        }

        return $this->curl($method);
    }

    private function curl($method)
    {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $this->url);
        curl_setopt($curl, CURLOPT_USERAGENT, $this->params['browser']);

        if ($this->referer !== null)
            curl_setopt($curl, CURLOPT_REFERER, $this->referer);

        if ($this->headers !== array())
        {
            $headers = array();

            foreach ($this->headers as $k => $v)
                $headers[] = trim($k . ': ' . $v);

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        if ($cookie)
        {
            curl_setopt($curl, CURLOPT_COOKIEJAR, $this->path . $this->cookieFile . '.bin');
            curl_setopt($curl, CURLOPT_COOKIEFILE, $this->path . $this->cookieFile . '.bin');
        }

        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_FAILONERROR, 1); //Установите эту опцию в ненулевое значение, если вы хотите, чтобы PHP завершал работу скрыто, если возвращаемый HTTP-код имеет значение выше 300. По умолчанию страница возвращается нормально с игнорированием кода.
        //Максимальное время в секундах, которое вы отводите для работы CURL-функций.
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 15);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);

        if ($method === 'POST')
        {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $this->request_params);
        }

        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $info = curl_getinfo($curl);
        $error = curl_error($curl);
        $errno = curl_errno($curl);
        curl_close($curl);

        // store the response
        $this->response['code'] = $code;
        $this->response['response'] = $response;
        $this->response['info'] = $info;
        $this->response['error'] = $error;
        $this->response['errno'] = $errno;

        return $code;
    }
}
