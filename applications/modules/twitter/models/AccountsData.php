<?php

namespace app\modules\twitter\models;

use Yii;
use app\components\Model;

class AccountsData extends Model
{
    public $token;
    public $_c;
    private $_response = [];
    private $_d = [
        'yandexRank' => ['applications\modules\twitter\components\information\Yandex', 'getRank'],
        'yandexRobot' => ['applications\modules\twitter\components\information\Yandex', 'getRobot'],
        'googlePR' => ['applications\modules\twitter\components\information\Google', 'twitterGetPR'],
        'twitter' => ['applications\modules\twitter\components\information\Twitter', 'get']
    ];

    private $_data;

    public function rules()
    {
        return [
            ['token', 'required'],
            ['_c', 'safe'],
            ['token', 'paramsValidate'],
            ['token', 'collectingData']
        ];
    }

    public function scenarios()
    {
        return [
            'data' => ['token', '_c'],
        ];
    }

    public function setScenario($value)
    {
        if (array_key_exists($value, $this->scenarios())) {
            parent::setScenario($value);
        }
    }

    public function paramsValidate()
    {
        $token = Yii::$app->redis->get('twitter:accounts:data:' . $this->token);

        if ($token !== false) {
            $this->_data = json_decode($token, true);

            if (is_array($this->_c)) {
                foreach ($this->_c as $k => $c) {
                    if (!array_key_exists($c, $this->_d)) {
                        $this->addError('token', 'Некорректный запрос, пожалуйста попробуйте еще раз.');
                        break;
                    }
                }
            } else {
                if (!array_key_exists($this->_c, $this->_d) || $this->_c != 'all') {
                    $this->addError('token', 'Некорректный запрос, пожалуйста попробуйте еще раз.');
                }
            }
        } else {
            $this->addError('token', 'Некорректный запрос, пожалуйста попробуйте еще раз.');
        }
    }

    public function collectingData()
    {
        if (is_array($this->_c)) {
            foreach ($this->_c as $c) {
                $this->_response[$c] = call_user_func([$this->_d[$c][0], $this->_d[$c][1]], $this->_data['screen_name']);
            }
        } else {
            if ($this->_c == 'all') {
                foreach ($this->_d as $d => $f) {
                    $this->_response[$d] = call_user_func($f[0], $f[1], $this->_data['screen_name']);
                }
            } else {
                $this->_response[$this->_c] = call_user_func($this->_d[$this->_c][0] . $this->_d[$this->_c][1], $this->_data['screen_name']);
            }
        }

        $this->_response['code'] = 200;
    }

    public function getResponse()
    {
        if ($this->hasErrors())
            return ['message' => $this->getError(), 'code' => 0];
        else
            return $this->_response;
    }
} 