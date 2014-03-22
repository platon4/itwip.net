<?php

namespace console\modules\twitter\components;

use Yii;
use common\api\twitter\oAuth;
use console\components\Logger;
use yii\base\Exception;

class Tweeting
{
    protected $_code = 0;
    protected $tmh;
    protected $_result;
    protected $_methods = [
        'indexes' => 'console\modules\twitter\components\tweeting\Indexes',
        'manual'  => 'console\modules\twitter\components\tweeting\Manual'
    ];

    /**
     * Обрабатавает задание
     *
     * @param $task
     */
    public function processTask($task)
    {
        $this->initTask($task);
    }

    protected function initTask($task)
    {
        if(array_key_exists($task['orderType'], $this->_methods)) {
            $tw = new $this->_methods[$task['orderType']];

            $tw->process($task);
        } else {
            Logger::log('Error tweeting: invalid order type "' . $task['orderType'] . '"', 3);
        }
    }

    /**
     * Устанавливаем настройки для api твиттера
     *
     * @param $data
     * @return $this
     * @throws \yii\base\Exception
     */
    public function set($data)
    {
        if(!is_array($data) || empty($data))
            throw new Exception('Invalid tweeting set params.');

        $this->tmh = new oAuth([
            'consumer_key'    => $data['app_key'],
            'consumer_secret' => $data['app_secret'],
            'user_token'      => $data['user_key'],
            'user_secret'     => $data['user_secret'],
            'ip'              => $data['ip']
        ]);

        return $this;
    }

    /**
     * Отправляем твит в твиттер
     *
     * @param $tweet
     */
    public function send($tweet)
    {
        $this->_code = $this->tmh->request('POST', $this->tmh->url('1.1/statuses/update'), [
            'status' => $tweet
        ]);

        $this->_result = json_decode($this->tmh->response['response'], true);

        if($this->getCode() != 200) {
            Logger::error($this->_result, [], 'daemons/api', 'sendError');
        }
    }

    public function destroy($str_id)
    {
        $this->_code = $this->tmh->request('POST', $this->tmh->url('1.1/statuses/destroy'), [
            'id' => $str_id
        ]);

        if($this->getCode() != 200) {
            Logger::error(json_decode($this->tmh->response['response'], true), [], 'daemons/api', 'destroyError');
        }
    }

    /**
     * Получаем код ответа твиттера
     *
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * Получаем значение по ключу, с твиттера
     *
     * @param $key
     * @return bool
     */
    public function get($key)
    {
        return isset($this->_result[$key]) ? $this->_result[$key] : false;
    }

    /**
     * Получаем ответ твиттера
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->_result;
    }

    /**
     * Получаем ID твита
     *
     * @return bool
     */
    public function getStrId()
    {
        return $this->get('id_str');
    }
}