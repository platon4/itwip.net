<?php

namespace console\modules\twitter\controllers;

use Yii;
use yii\db\Query;
use common\helpers\String;

/*
 * php /var/www/itwip/cmd twitter (Interval 10 min)
 */

class DefaultController extends \console\components\Controller
{

    public function actionIndex()
    {
        $this->launchTweetingDaemons();
    }

    /**
     * Берем список приложений, и запускаем демоног под них
     */
    protected function launchTweetingDaemons()
    {
        $rows = (new Query())->select('daemon')->from('{{%twitter_apps}}')->where(['is_active' => 1])->groupBy(['daemon'])->all();

        if(!empty($rows)) {
            $path = realpath(Yii::$app->getBasePath() . '/..');

            if(!is_dir(Yii::$app->getRuntimePath() . "/daemon")) {
                @mkdir(Yii::$app->getRuntimePath() . "/daemon", 0777);
                @chmod(Yii::$app->getRuntimePath() . "/daemon", 0777);
            }

            foreach($rows as $row) {
                $daemon = $row['daemon'];
                $log = Yii::$app->getRuntimePath() . "/daemon/daemon_log_" . $daemon . ".txt";

                exec("php $path/cmd twitter/tweeting " . $daemon . " > $log 2>&1 &");
            }
        }
    }
} 