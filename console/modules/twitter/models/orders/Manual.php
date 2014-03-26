<?php

namespace console\modules\twitter\models\orders;

use common\helpers\twitter\Tweets;
use common\helpers\Url;
use Yii;
use yii\db\Query;
use console\modules\twitter\models\OrdersInterface;

class Manual implements OrdersInterface
{
    use \console\modules\twitter\models\OrdersTrait;

    protected $hCount = 0;
    protected $hours = [];
    protected $hTask = false;
    protected $_taskParams = [];

    public function make()
    {
        $this->init();

        if($this->hasTask()) {
            $this->setTask();
        }

        $this->setProcessDate($this->getProcessDate());
        $this->isFinish();
    }

    public function init()
    {
        if($this->processOrder() > 0)
            $this->hTask = true;
    }

    public function isFinish()
    {
        $count = (new Query())
            ->from('{{%twitter_ordersPerform}}')
            ->where(['order_hash' => $this->get('order_hash'), 'status' => 1])
            ->count();

        if($count == 0)
            $this->_update['order'][$this->get('id')]['is_process'] = 1;
    }

    public function processOrder()
    {
        if($this->_processDate === null) {
            $params = $this->getParam('targeting');

            if(isset($params['t']) && is_array($params['t'])) {

                $found = false;
                $i = $c = $w = $skipDay = 0;
                $start = $stop = false;
                $day = (int) date('w');

                do {
                    if($day === $i && $skipDay === 0)
                        $start = true;

                    if(isset($params['t'][$i])) {
                        if($day === $i) {
                            if($skipDay === 0) {
                                $found = true;
                                foreach($params['t'][$i] as $hour) {
                                    $this->hours[] = $hour;
                                    $this->hCount++;
                                }
                            }
                        }

                        if($skipDay)
                            $stop = true;
                    } elseif($start === true)
                        $skipDay++;

                    if($i < 6) {
                        $i++;
                    } else {
                        $i = 0;
                        $w++;
                    }
                } while($w < 2 && $stop === false);

                $this->_processDate = date('Y-m-d', time() + ($skipDay * 86400));

                return $found === true ? 2 : 0;
            } else {
                $this->_processDate = date('Y-m-d', time() + 86400);
                return 1;
            }
        }
    }

    public function setTask()
    {
        $tasks = $this->getTasks();

        if(!empty($tasks)) {
            foreach($tasks as $row) {
                $this->_setTaskParams($row);
                $this->processTask($row);
            }
        }
    }

    public function processTask($task)
    {
        $this->_task[] = [
            'order_id'     => $this->get('id'),
            'order_hash'   => $this->get('order_hash'),
            'sbuorder_id'  => $task['id'],
            'orderType'    => 'manual',
            'tweet_hash'   => $task['hash'],
            'domen'        => $this->getDomen($task['tweet']),
            'tw_account'   => $task['tw_account'],
            'process_time' => $this->getTaskProcessTime($task),
            'payment_type' => $this->get('payment_type'),
            'params'       => $this->getTaskParams($task),
            'daemon'       => $this->getDaemon()
        ];

        $this->_update['task'][$task['id']]['is_process'] = 1;
    }

    public function getDomen($tweet)
    {
        $domen = Url::getDomen(Tweets::getUrl($tweet));
        return $domen !== null ? $domen : '';
    }

    public function getTaskProcessTime()
    {
        if(!empty($this->hours) && is_array($this->hours)) {
            $h = '';
            foreach($this->hours as $k => $hour) {
                $hour = $hour - 1;

                if($hour >= date('H')) {
                    if($this->getInterval() > 35) {
                        unset($this->hours[$k]);
                    }

                    $h = $hour . ':00:00';
                    break;
                }
            }

            return $h;
        } else {
            return date('H:i:s');
        }
    }

    public function getTaskParams($data)
    {
        return json_encode([
            'tweet'         => $data['tweet'],
            'account'       => $data['tw_account'],
            'order_owner'   => $this->get('owner_id'),
            'amount'        => $data['cost'],
            'return_amount' => $data['return_amount'],
            'interval'      => $this->getInterval()
        ]);
    }

    public function hasTask()
    {
        return $this->hTask;
    }

    public function getTasks()
    {
        return (new Query())
            ->select('*')
            ->from('{{%twitter_ordersPerform}}')
            ->where(['order_hash' => $this->get('order_hash'), 'is_process' => 0, 'status' => 1])
            ->limit($this->getLimit())
            ->all();
    }

    public function getProcessDate()
    {
        return $this->_processDate;
    }

    public function getLimit()
    {
        return (int) ceil(($this->hCount > 0 ? $this->hCount * 60 : 1440) / $this->getInterval());
    }

    public function getInterval()
    {
        if($this->_interval === null) {
            $params = $this->getParam('targeting');

            if(isset($params['interval']) && $params['interval'] >= 30) {
                $this->_interval = rand($params['interval'] - rand(5, 15), $params['interval'] + rand(5, 15));
            } else {
                $this->_interval = rand(20, 40);
            }
        }

        return $this->_interval;
    }

    public function setProcessDate($date)
    {
        $this->_update['order'][$this->get('id')]['process_date'] = $date;
    }
}