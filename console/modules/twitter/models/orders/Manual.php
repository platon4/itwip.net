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

    public function make()
    {
        if($this->getProcessDate() <= date('Y-m-d')) {
            $this->setTask();
        } else {
            $this->setProcessDate($this->getProcessDate());
        }
    }

    public function setTask()
    {
        $task = $this->getTasks();

        print_r($task);
    }

    public function getTasks()
    {
        return (new Query())
            ->select('*')
            ->from('{{%twitter_ordersPerform}}')
            ->where(['order_hash' => $this->get('order_hash')])
            ->limit($this->getLimit())
            ->all();
    }

    public function getProcessDate()
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
                            if(date('w') === $i && $c === 0) {
                                $found = true;
                                foreach($params['t'][$i] as $hour) {
                                    $this->hours[] = $hour;
                                    $this->hCount++;
                                }
                                break;
                            }
                            $d++;
                        }
                    }

                    if($i >= 6) $i = 0; else $i++;
                    $b++;
                } while($d < $count && $b <= 24);

                if($found === false)
                    $this->_processDate = date('Y-m-d', time() + ($c * 86400));
            } else {
                $this->_processDate = date('Y-m-d', time() + 86400);
            }
        }

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
}