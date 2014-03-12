<?php

namespace console\modules\twitter\controllers;

use Yii;
use yii\db\Command;
use yii\db\Query;
use console\components\Daemon;
use console\modules\twitter\components\Tweeting;

/*
 * php cmd twitter/tweeting
 */

class TweetingController extends \console\components\Controller
{
    protected $apps;
    protected $daemon;
    protected $_dname;

    public function actionIndex($daemon)
    {
        $this->daemon = $daemon;

        /* проверяем если домен не запущен уже */
        if(!Daemon::isRunning($this->getDaemoName())) {
            $redis = Yii::$app->redis;

            Daemon::setProcess($this->getDaemoName());

            $tweeting = new Tweeting();

            /* Запускаем демона */
            while(true) {
                $this->message('Demonion start of the cycle');

                /* проверяем если твиттинг не остановлен */
                if($redis->exists('console:twitter:tweeting') === false) {
                    if(Daemon::isSetProcess($this->getDaemoName())) {
                        $where = ['and', 'daemon=:daemon', 'process_time<=:time', 'owner_id=1']; // Изменить <
                        $rids = Yii::$app->redis->mGet(Yii::$app->redis->keys('console:twitter:tweeting:tasks:id:*'));

                        if($rids !== false) {
                            $ids = [];
                            foreach($rids as $id) {
                                if($id !== false)
                                    $ids[] = $id;
                            }

                            if(!empty($ids))
                                $where[] = ['not in', 'id', $ids];
                        }

                        $tasks = (new Query())->from('{{%twitter_tweeting}}')->where($where, [':daemon' => $this->daemon, ':time' => date('H:i:s')])->limit(10)->all();

                        if(!empty($tasks)) {
                            foreach($tasks as $task) {
                                Yii::$app->redis->set('orders:in_process:0:' . $task['order_id'], $task['order_id']);
                                Yii::$app->redis->set('orders:in_process:1:' . $task['sbuorder_id'], $task['order_id']);

                                $tweeting->processTask($task);

                                Yii::$app->redis->delete(['orders:in_process:0:' . $task['order_id'], 'orders:in_process:1:' . $task['sbuorder_id']]);
                            }
                        } else {
                            $this->message('Daemon timeout 5 sec.');
                            sleep(5);
                        }
                    } else {
                        Daemon::stopDaemon($this->daemon, 0, 'Daemon won\'t start, error set process');
                    }
                } else {
                    Daemon::stopDaemon($this->daemon, 0, 'Deaemon is stopped');
                }

                $timeout = rand(5, 10);
                $this->message('Demonion end of the cycle, timeout: ' . $timeout);

                /* делаем перерыв на 1 секунду */
                sleep($timeout);
                exit(0); // УБРАТЬ НА ПРОДАКШЕНЕ
            }
        } else {
            Daemon::stopDaemon($this->daemon, 0, 'Daemon is already running');
        }
    }

    protected function message($message)
    {
        echo $message . PHP_EOL;
    }

    protected function getDaemoName()
    {
        if($this->_dname === null)
            $this->_dname = md5(get_called_class()) . '_' . $this->daemon;

        return $this->_dname;
    }
}