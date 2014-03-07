<?php

namespace console\modules\twitter\models\orders;

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

        echo $this->getProcessDate()."\n";
        $this->setProcessDate($this->getProcessDate());
    }

    public function init()
    {
        if($this->processOrder() > 0)
            $this->hTask = true;

    }

    public function processOrder()
    {
        if($this->_processDate === null) {
            $params = $this->getParam('targeting');

            if(isset($params['t']) && is_array($params['t'])) {

                $found = false;
                $i = $c = $b = $d = 0;
                $count = count($params['t']);
                $start = false;

                do {
                    if((int) date('w') === $i) {
                        $start = true;
                    } elseif($start === true && $d === 0)
                        $c++;

                    if(isset($params['t'][$i])) {
                        if($start === true) {
                            if((int) date('w') === $i && $c === 0) {
                                $found = true;
                                foreach($params['t'][$i] as $hour) {
                                    $this->hours[] = $hour;
                                    $this->hCount++;
                                }
                            }
                            $d++;
                        }
                    }

                    if($i >= 6)
                        $i = 0;
                    else
                        $i++;

                    $b++;
                } while($d < $count && $b <= 14);

                if($found === true) {
                    $this->_processDate = date('Y-m-d');
                    return 2;
                } else {
                    $this->_processDate = date('Y-m-d', time() + ($c * 86400));
                    return 0;
                }
            } else {
                $this->_processDate = date('Y-m-d');
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
            'sbuorder_id'  => $task['id'],
            'orderType'    => 'manual',
            'tweet_hash'   => $task['hash'],
            'url_hash'     => $task['url_hash'],
            'process_time' => $this->getTaskProcessTime($task),
            'params'       => $this->getTaskParams($task)
        ];

        $this->_update['task'][$task['id']]['is_process'] = 1;
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
            'tweet'       => $this->_getTaskParams('tweet'),
            'account'     => $this->_getTaskParams('account'),
            'order_owner' => $this->get('owner_id'),
            'toowb'       => $data['cost'],
            'tooow'       => $data['return_amount'],
            'interval'    => $this->getInterval()
        ]);
    }

    public function _setTaskParams($data)
    {
        if(isset($data['_params']))
            $this->_taskParams = json_decode($data['_params'], true);
    }

    public function _getTaskParams($key)
    {
        return isset($this->_taskParams[$key]) ? $this->_taskParams[$key] : '';
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

            if(isset($params['targeting']['interval']) && $params['targeting']['interval'] >= 30) {
                $this->_interval = rand($params['targeting']['interval'] - 10, $params['targeting']['interval'] + 10);
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