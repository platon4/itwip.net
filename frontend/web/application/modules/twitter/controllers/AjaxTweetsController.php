<?php

class ajaxTweetsController extends Controller
{

    public function init()
    {
        parent::init();

        if (!Yii::app()->request->isAjaxRequest)
            throw new CHttpException('403', 'Url should be requested via ajax only.');
    }

    public function filters()
    {
        return array(
            'accessControl',
        );
    }

    public function accessRules()
    {
        return array(
            array('allow',
                'actions' => array('getList', '_upload', 'tweetsCollection', 'getprogress', 'tweets', 'removetweets', '_removeupload', 'parsetemplate', 'savelist', 'order'),
                'roles' => array('user'),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionOrder($id, $act = '')
    {
        if ($act == 'confirm')
        {
            $row = Yii::app()->db->createCommand("SELECT * FROM {{tw_orders}} WHERE id=:id")->queryRow(true, array(
                ':id' => $id));
            if ($row !== null)
            {
                $prefix = ($row['_type_payment']) ? ' руб.Б.' : ' руб.';
                $json = array('code' => 200, 'content' => 'Вы действительно хотите оплатить заказ на сумму: ' . $row['_amount'] . $prefix);
            }
            else
            {
                $json = array('code' => 404, 'content' => 'Ошибка, не удалось найти выбранный вами заказ.');
            }
        }
        else if ($act == 'remove')
        {
            $row = Yii::app()->db->createCommand("SELECT * FROM {{tw_orders}} WHERE id=:id")->queryRow(true, array(
                ':id' => $id));

            if (isset($row['id']) AND intval($row['id']))
            {
                try
                {
                    $transaction = Yii::app()->db->beginTransaction();

                    if ($row['owner_id'] == Yii::app()->user->id)
                    {
                        if ($row['_status'] == 2)
                        {
                            $json = array('code' => 403, 'content' => 'Выполненный заказ невозможно удалить.');
                            $transaction->rollBack();
                        }
                        else
                        {
                            if ($row['_status'] > 0)
                            {
                                $order_amount = ($row['_amount'] - $row['_amount_use']);

                                Finance::rePayment($order_amount, Yii::app()->user->id, $row['_type_payment'], 0, $row['id']);
                            }

                            Yii::app()->db->createCommand("DELETE FROM {{tw_orders}} WHERE id=:id")->execute(array(
                                ':id' => $id));

                            Yii::app()->db->createCommand("DELETE FROM {{tweets_to_twitter}} WHERE _order=:id")->execute(array(
                                ':id' => $row['id']));

                            $json = array('code' => 200, 'content' => 'Заказ успешно удален.');
                            $transaction->commit();
                        }
                    }
                    else
                    {
                        $json = array('code' => 403, 'content' => 'Отказана в доступе.');
                        $transaction->rollBack();
                    }
                }
                catch (Exception $e)
                {
                    $json = array('code' => 502, 'content' => 'Не удалось удалить выбранный вами заказ, пожалуйста попробуйте еще раз.');
                    $transaction->rollBack();
                }
            }
            else
            {
                $json = array('code' => 403, 'content' => 'Не удалось удалить выбранный вами заказ, пожалуйста попробуйте еще раз.');
            }
        }
        /*
         *  ################################  Удаление твита из заказа ####################
         */
        elseif ($act == 'removeTweet')
        {
            $tweet = Yii::app()->db->createCommand("SELECT * FROM {{tweets_to_twitter}} WHERE id=:id")->queryRow(true, array(
                ':id' => $id));

            if (isset($tweet['id']) AND intval($tweet['id']))
            {
                try
                {
                    $transaction = Yii::app()->db->beginTransaction();

                    $row = Yii::app()->db->createCommand("SELECT * FROM {{tw_orders}} WHERE id=:id")->queryRow(true, array(
                        ':id' => $tweet['_order']));

                    if ($row['owner_id'] == Yii::app()->user->id)
                    {
                        if ($tweet['status'] != 2)
                        {
                            $tweet_price = ($row['_ping'] == 1) ? $tweet['_tweet_price'] + 0.50 : $tweet['_tweet_price'];
                            Yii::app()->db->createCommand("DELETE FROM {{tweets_to_twitter}} WHERE id=:id")->execute(array(
                                ':id' => $id));

                            if ($row['_status'] > 0)
                            {
                                $tw = Yii::app()->db->createCommand("SELECT screen_name FROM {{tw_accounts}} WHERE id=:id")->queryRow(true, array(
                                    ':id' => $tweet['_tw_account']));

                                Finance::rePayment($tweet_price, Yii::app()->user->id, $row['_type_payment'], 1, $tweet['_order'], $tw['screen_name']);
                            }

                            if (!Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{tweets_to_twitter}} WHERE _order=:id")->queryScalar(array(
                                        ':id' => $tweet['_order'])))
                            {
                                Yii::app()->db->createCommand("DELETE FROM {{tw_orders}} WHERE id=:id")->execute(array(
                                    ':id' => $tweet['_order']));

                                $code = 199;
                            }
                            else
                            {
                                $code = 200;
                                Yii::app()->db->createCommand("UPDATE {{tw_orders}} SET _amount=_amount-{$tweet_price},_remain=_remain-1 WHERE id=:id")->execute(array(
                                    ':id' => $tweet['_order']));
                            }

                            $json = array('code' => $code, 'content' => 'Заказ успешно удален.');
                            $transaction->commit();
                        }
                        else
                        {
                            $json = array('code' => 403, 'content' => 'Невозможно удалить уже выполненый заказ.');
                            $transaction->rollBack();
                        }
                    }
                    else
                    {
                        $json = array('code' => 403, 'content' => 'Отказана в доступе.');
                        $transaction->rollBack();
                    }
                }
                catch (Exception $e)
                {
                    $json = array('code' => 502, 'content' => 'Не удалось удалить выбранный вами заказ, пожалуйста попробуйте еще раз.');
                    $transaction->rollBack();
                }
            }
            else
            {
                $json = array('code' => 403, 'content' => 'Не удалось удалить выбранный вами заказ, пожалуйста попробуйте еще раз.');
            }
        }
        /*
         *  ################################  Удаление твита из заказа ####################
         */
        else
        {
            $row = Yii::app()->db->createCommand("SELECT * FROM {{tw_orders}} WHERE id=:id")->queryRow(true, array(
                ':id' => $id));

            $balance = ($row['_type_payment']) ? Yii::app()->user->_get('bonus_money') : Yii::app()->user->_get('money_amount');

            if ($row['_status'] == 0)
            {
                try
                {
                    $transaction = Yii::app()->db->beginTransaction();

                    if (!Finance::payment($row['_amount'], Yii::app()->user->id, $row['_type_payment'], 0, $row['id']))
                    {
                        $json = array('code' => 201, 'content' => 'На вашем счету недостаточно средств для оплаты заказа.');
                        $transaction->rollBack();
                    }
                    else
                    {
                        Yii::app()->db->createCommand("UPDATE {{tw_orders}} SET _status=1 WHERE id=:id")->execute(array(
                            ':id' => $id));
                        $json = array('code' => 200, 'content' => 'Ваш заказ успешно оплачен.');
                        $transaction->commit();
                    }
                }
                catch (Exception $e)
                {
                    $json = array('code' => 502, 'content' => 'Не удалось оплатить баланс, пожалуйста, попробуйте еще раз.');
                    $transaction->rollBack();
                }
            }
            else
            {
                $json = array('code' => 209, 'content' => 'Данный заказ уже оплачен.');
            }
        }

        echo json_encode($json);

        Yii::app()->end();
    }

    public function actionGetList($act = '', $id, $tid, $_q = '')
    {
        $w = array('_key=:key', 'owner_id=:owner');
        $p = array(':key' => $tid, ':owner' => Yii::app()->user->id);

        if ($act != 'all')
        {
            $w[] = '(FIND_IN_SET(:id,_acc_lists) OR _acc_lists=\'\')';
            $p[':id'] = $id;
        }

        if (trim($_q) != '')
        {
            $w[] = '_text LIKE :like';
            $p[':like'] = '%' . $_q . '%';
        }

        $id = intval($id);
        $tweets = Yii::app()->db->createCommand("SELECT id,_text,_acc_lists FROM {{tw_tweets_process}} WHERE " . implode(" AND ", $w) . "")->queryAll(true, $p);


        $_countSQL = Yii::app()->db->createCommand("SELECT COUNT(*) as count,_text,_acc_lists FROM {{tw_tweets_process}} WHERE _key=:key AND owner_id=:owner")->queryAll(true, array(
            ':key' => $tid, ':owner' => Yii::app()->user->id));

        $_data = array();

        foreach ($_countSQL as $row)
        {
            $_data[] = $row;
        }

        echo json_encode(array('code' => 200, 'html' => $this->renderPartial('_balonList', array(
                'act' => $act, '_data' => $_data, 'id' => $id, 'tweets' => $tweets), true)));
        Yii::app()->end();
    }

    public function actionSaveList($id, $tid)
    {
        if (intval($id) AND CHelper::validID($tid))
        {
            $messages = '';
            $code = 200;
            $tweets = (isset($_POST['Tweets']) AND is_array($_POST['Tweets'])) ? $_POST['Tweets'] : array();
            $_tcount = count($tweets);

            $row = Yii::app()->db->createCommand("SELECT s._stop,s._filter,a.screen_name FROM {{tw_accounts_settings}} s INNER JOIN {{tw_accounts}} a ON s.tid=a.id WHERE s.tid=:acc")->queryRow(true, array(
                ':acc' => $id));

            if ($_tcount)
            {
                $_stop = explode(',', $row['_stop']);
                $_filter = explode(',', $row['_filter']);

                $_tw = new TWvalidator(false);
                $errors = array();
                $ids = array();
                $_stop_next = false;
                $a3r = array(
                    4 => 1, //Порно и эротика
                    3 => 2, //Ненормативная лексика
                    6 => 4 //Политика   
                );

                foreach ($tweets as $k => $_id)
                {
                    if (intval($_id))
                        $ids[] = intval($_id);
                }

                $tw_unselect = Yii::app()->db->createCommand("SELECT id,_acc_lists,_errors FROM {{tw_tweets_process}} WHERE _key=:key AND owner_id=:owner AND FIND_IN_SET(:acc, _acc_lists)")
                        ->queryAll(true, array(':acc' => $id, ':key' => $tid, ':owner' => Yii::app()->user->id));

                foreach ($tw_unselect as $un_select)
                {
                    if (!in_array($un_select['id'], $ids))
                    {
                        $lists = explode(",", $_u['_acc_lists']);
                        $lst = array();

                        foreach ($lists as $v)
                        {
                            if (trim($v) != $id)
                                $lst[] = trim($v);
                        }

                        Yii::app()->db->createCommand("UPDATE {{tw_tweets_process}} SET _acc_lists=:list WHERE id=:id")
                                ->execute(array(
                                    ':id' => $un_select['id'],
                                    ':list' => implode(",", $lst)
                        ));
                    }
                }


                $twl = Yii::app()->db->createCommand("SELECT id,_acc_lists,_text,_errors FROM {{tw_tweets_process}} WHERE _key=:key AND owner_id=:owner AND id IN('" . implode("','", $ids) . "')")->queryAll(true, array(
                    ':owner' => Yii::app()->user->id, ':key' => $tid));

                foreach ($twl as $_u)
                {

                    $save_list = explode(",", $_u['_acc_lists']);

                    $stops = ($_u['_errors'] == 0) ? array() : explode(",", $_u['_errors']);

                    if (count($stops))
                    {
                        foreach ($stops as $k => $_s)
                        {
                            if (in_array($_s, array(1, 2, 5)))
                            {
                                $messages = Yii::t('twitterModule.tweets', '_error_tweets');
                                $_stop_next = true;
                                break;
                            }

                            if (isset($a3r[$_s]) AND in_array($a3r[$_s], $_stop))
                            {
                                if ($a3r[$_s] == 1)
                                {
                                    $messages = Yii::t('twitterModule.tweets', '_error_filter_tweet_adult', array(
                                                '{account}' => $row['screen_name']));
                                }
                                else if ($a3r[$_s] == 2)
                                {
                                    $messages = Yii::t('twitterModule.tweets', '_error_filter_tweet_censor', array(
                                                '{account}' => $row['screen_name']));
                                }
                                else if ($a3r[$_s] == 4)
                                {
                                    $messages = Yii::t('twitterModule.tweets', '_error_filter_tweet_politic', array(
                                                '{account}' => $row['screen_name']));
                                }

                                $_stop_next = true;
                                break;
                            }
                        }
                    }

                    if (!$_stop_next)
                    {
                        if (in_array(3, $_stop))
                        {
                            $errors = $_tw->validate(trim($_u['_text']), array('filter'), $_filter);
                        }

                        if (!count($errors['code_list']))
                        {
                            $lists = explode(",", $_u['_acc_lists']);
                            $lst = array();

                            foreach ($lists as $v)
                            {
                                if (trim($v))
                                    $lst[] = trim($v);
                            }

                            if (!in_array($id, $lst))
                                $lst[] = $id;

                            Yii::app()->db->createCommand("UPDATE {{tw_tweets_process}} SET _acc_lists=:list WHERE id=:id")
                                    ->execute(array(
                                        ':id' => $_u['id'],
                                        ':list' => implode(",", $lst)
                            ));
                        } else
                        {
                            $code = 403;
                            $messages = $errors['error_text']['filter'];
                            break;
                        }
                    }
                    else
                    {
                        $code = 403;
                        break;
                    }
                }
            }
            else
            {
                $twl = Yii::app()->db->createCommand("SELECT id,_acc_lists FROM {{tw_tweets_process}} WHERE _key=:key AND owner_id=:owner AND FIND_IN_SET(:acc, _acc_lists)")->queryAll(true, array(
                    ':owner' => Yii::app()->user->id, ':acc' => $id, ':key' => $tid));

                foreach ($twl as $_u)
                {
                    $list = explode(",", $_u['_acc_list']);

                    foreach ($list as $k => $remove)
                    {
                        if ($remove == $id)
                            unset($list[$k]);
                    }

                    Yii::app()->db->createCommand("UPDATE {{tw_tweets_process}} SET _acc_lists=:list WHERE id=:id AND owner_id=:owner")->execute(array(
                        ':owner' => Yii::app()->user->id, ':id' => $_u['id'], ':list' => implode(",", $list)));
                }
            }

            $count = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{tw_tweets_process}} WHERE _key=:key AND owner_id=:owner AND FIND_IN_SET(:acc, _acc_lists)")->queryScalar(array(
                ':owner' => Yii::app()->user->id, ':acc' => $id, ':key' => $tid));

            echo json_encode(array('code' => $code, 'messages' => $messages, 'count' => $count));
            Yii::app()->end();
        }
        else
            throw new CHttpException('403', Yii::t('yii', 'Your request is invalid.'));
    }

    public function action_removeUpload()
    {
        $uid = (CHelper::validID($_POST['uid'])) ? CHelper::validID($_POST['uid']) : 0;

        if ($uid)
        {
            Sitemap::model()->deleteAll('owner_id=:owner_id AND _uid=:uid', array(
                ':owner_id' => Yii::app()->user->id, ':uid' => $uid));
            unset(Yii::app()->session['_psitemap']);
            CHelper::removeFile('tw_' . $uid . '.txt', Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . '/tmp');

            JSON::encode(array('code' => 200));
        }
        else
            throw new CHttpException('403', Yii::t('yii', 'Your request is invalid.'));
    }

    public function action_upload()
    {
        $data = array('code' => 0, 'html' => '');

        if ((isset($_POST['_url']) AND trim($_POST['_url']) != ""))
        {
            $fleName = $_POST['_url'];
            $is_url = $_POST['only_new'];
        }
        else
        {
            $fleName = 'file';
            $is_url = false;
        }

        $file = TFile::file($fleName);
        $file->rules(array(
            array('size', 'max' => array(5, 'mb')),
            array('formats', array('txt', 'xml')),
            array('current', array($_POST['_type'])),
        ));

        $template = isset($_POST['parseTemplate']) ? $_POST['parseTemplate'] : false;

        $fileData = $file->getFile();

        if ($file->validate())
        {
            $tw = new CTweets;
            $tw->process($fileData, $file->getType(), $_POST['only_new'], $template);
            $tw->rules(array(
                array('url', array($_POST['_url'])),
            ));

            if ($tw->validate())
            {
                if (!$tw->collection())
                {
                    $tw_list = implode("\n", $tw->getTweets());
                    $aid = (time() + rand(0, 10000));

                    $data = array(
                        'code' => 200,
                        'html' => Yii::t('twitterModule.tweets', '_file_add', array(
                            '{file}' => Html::encode($file->getFullName(true)),
                            '{count}' => $tw->getCount(),
                            '{id}' => $aid)),
                        'tweets' => $tw_list,
                        'areaID' => $aid,
                        'count' => $tw->getCount(),
                    );
                }
                else
                {
                    $data = array(
                        'code' => 201,
                        'html' => Yii::t('twitterModule.tweets', '_file_add_collection', array(
                            '{id}' => $tw->getIdentifier(), '{count}' => $tw->getCount())),
                        'uid' => $tw->getIdentifier(),
                    );

                    Yii::app()->session['_psitemap'] = array('identifier' => $tw->getIdentifier(),
                        '_file' => Html::encode($file->getFullName(true)), 'count' => $tw->getCount());
                    $tw->collection('start');
                }
            }
            else
                $data = array('code' => $tw->getError('code'), 'html' => $tw->getError('error'));
        }
        else
            $data = array('code' => $file->getError('code'), 'html' => $file->getError('error'));

        JSON::encode($data);
        Yii::app()->end();
    }

    public function actiongetprogress($uid)
    {
        if (CHelper::validID($uid))
        {
            $oid = Yii::app()->user->id;

            $pdata = Sitemap::model()->findAll('owner_id=:owner_id AND _uid=:uid', array(
                ':owner_id' => $oid, ':uid' => $uid));
            $all_count = count($pdata);
            $ecount = 0;
            $rcount = 0;
            $wait = 0;

            $tweets = array();

            if ($all_count)
            {
                foreach ($pdata as $std)
                {
                    switch ($std->_status)
                    {
                        case "0":
                            $wait++;
                            break;
                        case "1":
                            $ecount++;
                            $tweets[] = $std->_text;
                            break;
                        case "2":
                            $rcount++;
                            break;
                    }
                }

                if ($wait)
                {
                    $procent = round((($ecount + $rcount) / $all_count) * 100, 2);

                    if (!file_exists(Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . '/tmp/tw_' . $uid . '.txt'))
                    {
                        CHelper::wget(Yii::app()->homeUrl . "twitter/tweets/tcollection?uid=" . $uid);
                    }

                    JSON::encode(array('code' => 200, 'procent' => $procent, 'ecount' => $ecount,
                        'rcount' => $rcount));
                }
                else
                {
                    if ($ecount)
                    {
                        if (isset(Yii::app()->session['_psitemap']['_file']))
                        {
                            $tw_list = implode("\n", $tweets);
                            $aid = (time() + rand(0, 10000));

                            $html = Yii::t('twitterModule.tweets', '_file_add', array(
                                        '{count}' => $ecount, '{id}' => $uid, '{file}' => Yii::app()->session['_psitemap']['_file']));

                            unset(Yii::app()->session['_psitemap']);
                            CHelper::removeFile('tw_' . $uid . '.txt', Yii::app()->getRuntimePath() . DIRECTORY_SEPARATOR . '/tmp');

                            JSON::encode(array('code' => 201, 'tweets' => $tw_list, 'html' => $html,
                                'areaID' => $uid, 'ecount' => $ecount));
                        }
                        else
                            JSON::encode(array('code' => 203, 'html' => Yii::t('twitterModule.tweets', '_error_parser_no_session')));
                    } else
                    {
                        unset(Yii::app()->session['_psitemap']);
                        JSON::encode(array('code' => 204, 'html' => Yii::t('twitterModule.prepared', '_error_parser_204')));
                    }
                }
            }
            else
            {
                unset(Yii::app()->session['_psitemap']);
                JSON::encode(array('code' => 202, 'ecount' => 0, 'mcount' => 0, 'rcount' => 0,
                    'html' => Yii::t('twitterModule.tweets', '_error_progress_no_sitemap')));
            }
        }
        else
            throw new CHttpException('403', Yii::t('yii', 'Your request is invalid.'));
        Yii::app()->end();
    }

    public function actiontweets()
    {
        $tweets_list = (isset($_POST['PostingList']) AND is_array($_POST['T']) AND count($_POST['PostingList'])) ? $_POST['PostingList'] : array();
        $tw_list = array();

        foreach ($tweets_list as $tweets)
        {
            $tweets_data = explode("\n", str_replace("<br>", "\n", $tweets));

            foreach ($tweets_data as $text)
                if (trim($text))
                    $tw_list[] = $text;
        }

        if (count($tw_list))
        {
            $uid = CHelper::generateID();
            $tw = new PTweets;

            $s = 0;
            $_t = array();
            $_p = array(':_key' => $uid, ':owner_id' => Yii::app()->user->id, ':_date' => date('Y-m-d'));

            foreach ($tw_list as $tweet)
            {
                $tw->attributes = array(
                    '_text' => $tweet,
                );

                if ($tw->validate())
                {
                    $_t[] = array(0 => "(:owner_id,:_key,:_hash_{$s},:text_{$s},:_date)",
                        1 => array(':text_' . $s => $tw->_text, ':_hash_' . $s => $tw->_hash($tw->_text)));
                    $s++;
                }
            }

            if ($s)
            {
                Yii::app()->db->createCommand("DELETE FROM {{tw_tweets_process}} WHERE owner_id=:owner AND _save=0")->execute(array(
                    ':owner' => Yii::app()->user->id));

                $vaues = array();
                $prm = array();

                for ($i = 0; $i <= $s - 1; $i++)
                {
                    $vaues[] = $_t[$i][0];
                    $prm[] = $_t[$i][1];

                    if (($i % 400 == 0 AND $i > 0) OR $i == $s - 1)
                    {
                        $_prms = array();

                        foreach ($prm as $k => $v)
                        {
                            foreach ($v as $_k => $_v)
                            {
                                $_prms[$_k] = $_v;
                            }
                        }

                        Yii::app()->db->createCommand("INSERT INTO {{tw_tweets_process}} (owner_id,_key,_hash,_text,_date) VALUES " . implode(', ', $vaues))->execute(array_merge($_p, $_prms));
                        $prm = array();
                        $vaues = array();
                    }
                }
            }

            if ($s)
            {
                JSON::encode(array('code' => 200, 'url' => Yii::app()->homeUrl . 'twitter/tweets/processing?_k=' . $uid,
                    'html' => Yii::t('internal', '_error_parser_204')));
            }
            else
                JSON::encode(array('code' => 2, 'html' => Yii::t('twitterModule.tweets', '_error_no_tweets_save')));
        }
        else
            JSON::encode(array('code' => 1, 'html' => Yii::t('twitterModule.tweets', '_error_no_tweets_add_edit')));
        Yii::app()->end();
    }

    public function actionParseTemplate()
    {
        $excludeWords = isset($_POST['parseTemplate']['words']) ? $_POST['parseTemplate']['words'] : '';
        $template = isset($_POST['parseTemplate']['url']) ? $_POST['parseTemplate']['url'] : '';
        $excludeUrl = isset($_POST['parseTemplate']['exclude']) ? $_POST['parseTemplate']['exclude'] : '';

        echo json_decode(array('template' => $this->renderPartial('_parseTemplate', array(
                'excludeWords' => $excludeWords, 'template' => $template, 'excludeUrl' => $excludeUrl))));

        Yii::app()->end();
    }
}
