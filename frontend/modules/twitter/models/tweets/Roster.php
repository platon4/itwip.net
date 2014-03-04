<?php

namespace twitter\models\tweets;

use Yii;
use twitter\models\tweets\Edit;
use twitter\components\validators\TweetEdit;

class Roster extends \FormModel
{
    public $_tid;
    public $_group;
    public $_action = 'get';
    public $ids = [];
    public $_title;
    public $edit = [];
    protected $_pages;
    protected $_media = [];
    protected $_tweets = [];
    protected $_figures = [];
    protected $_allowPlace;
    protected $_isSave;
    protected $groups = [
        'all',
        'unique-tweet',
        'unique-url',
        'links',
        'character',
        'domain',
        'hash-tags',
        'references',
        'censor',
        'politics'
    ];

    public function rules()
    {
        return [
            ['_tid', 'ext.validators.hashValidator', 'min' => 10, 'max' => 15],
            ['_tid', 'existsRoster'],
            ['_group', 'in', 'range' => $this->groups, 'message' => Yii::t('yii', 'Your request is invalid.')],
            ['ids', 'processIds'],
            ['_title', 'required', 'message' => Yii::t('twitterModule.tweets', '_restore_title_is_empty'), 'on' => 'saveRoster'],
            ['_title', 'length', 'max' => 255, 'on' => 'saveRoster'],
            ['_action,edit', 'safe']
        ];
    }

    public function attributelabels()
    {
        return [
            '_title' => 'Название списка'
        ];
    }

    public function existsRoster()
    {
        if(!Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_tweetsRoster}} WHERE _key=:key AND owner_id=:owner")->queryScalar(array(':owner' => Yii::app()->user->id, ':key' => $this->_tid)))
            $this->addError('_tid', Yii::t('twitterModule.tweets', '_not_roster_exists'));
    }

    protected function afterValidate()
    {
        $actions = [
            'saveRoster' => '_saveRoster',
            'get'        => '_getTweets',
            'info'       => 'getFigures',
            'remove'     => 'removeTweets',
            'tweetEdit'  => 'tweetEdit'
        ];

        $method = $actions[$this->_action];

        if(method_exists($this, $method))
            $this->$method();
        else
            $this->addError('_action', Yii::t('yii', 'Your request is invalid.'));
    }

    public function processIds()
    {
        if(is_array($this->ids) && $this->ids !== array()) {
            $i = 0;
            foreach($this->ids as $id) {
                $i++;

                if(!\CHelper::int($id) || $i > 50) {
                    $this->addError('ids', Yii::t('yii', 'Your request is invalid.'));

                    return false;
                }
            }
        }
    }

    public function getGroups()
    {
        return $this->groups;
    }

    public function getPages()
    {
        return $this->_pages;
    }

    public function getTweets()
    {
        return $this->_tweets;
    }

    public function getMedia($id)
    {
        return isset($this->_media[$id]) ? $this->_media[$id] : array();
    }

    public function getAction()
    {
        return $this->_action;
    }

    public function getFigures($key = null)
    {
        if($this->_figures === array()) {
            if(Yii::app()->redis->exists('twitter:tweets:' . $this->_tid . ':counts')) {
                $this->_figures = json_decode(Yii::app()->redis->get('twitter:tweets:' . $this->_tid . ':counts'), true);
            } else {
                $rows = Yii::app()->db->createCommand("SELECT _indexes as i,tweet FROM {{twitter_tweetsRoster}} WHERE _key=:tid AND owner_id=:owner")->queryAll(true, array(':tid' => $this->_tid, ':owner' => Yii::app()->user->id));

                $counts = [];

                foreach($this->groups as $v)
                    $counts[$v] = 0;

                foreach($rows as $row) {
                    if($row['i'] !== '' && $row['i'] !== null) {
                        $arr = explode(",", $row['i']);
                        if(count($arr)) {
                            foreach($arr as $m)
                                $counts[$m] += 1;
                        }
                    }

                    $counts['all'] += 1;
                }

                $this->_figures = $counts;

                Yii::app()->redis->set('twitter:tweets:' . $this->_tid . ':counts', json_encode($this->_figures));
                Yii::app()->redis->expire('twitter:tweets:' . $this->_tid . ':counts', 10 * 60);
            }
        }

        if($key !== null)
            return (isset($this->_figures[$key])) ? $this->_figures[$key] : 0;
        else
            return $this->_figures;
    }

    protected function _getTweets($all = false)
    {
        $fields = array('_key=:tid', 'owner_id=:owner_id');
        $values = array(':tid' => $this->_tid, ':owner_id' => Yii::app()->user->id);

        if($this->_group !== null && $this->_group !== '' && $all === false && $this->_group != 'all') {
            $fields[] = 'FIND_IN_SET (:index, _indexes)';
            $values[':index'] = $this->_group;
        }

        $where = "WHERE " . implode(" AND ", $fields);

        $this->_pages = new \CPagination(Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_tweetsRoster}} {$where}")->queryScalar($values));
        $this->_pages->pageSize = 50;

        $rows = Yii::app()->db->createCommand("SELECT * FROM {{twitter_tweetsRoster}} {$where} LIMIT " . $this->_pages->getOffset() . ", " . $this->_pages->getLimit())->queryAll(true, $values);

        $this->_tweets = $rows;
    }

    public function allowPlace()
    {
        if($this->_allowPlace === null && Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_tweetsRoster}} WHERE _key=:tid AND owner_id=:owner_id AND _placement=1")->queryScalar(array(':tid' => $this->_tid, ':owner_id' => Yii::app()->user->id)) > 0)
            $this->_allowPlace = true;
        else
            $this->_allowPlace = false;

        return $this->_allowPlace;
    }

    public function isSave()
    {
        if($this->_isSave === null && !Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{twitter_tweetsLists}} WHERE _hash=:lid AND owner_id=:owner_id")->queryScalar(array(':lid' => $this->_tid, ':owner_id' => Yii::app()->user->id)))
            $this->_isSave = false;
        else
            $this->_isSave = true;

        return $this->_isSave;
    }

    /*
     * Доделовать
     */
    protected function _saveRoster()
    {
        if(Yii::app()->db->createCommand("INSERT INTO {{twitter_tweetsLists}} (owner_id,_hash,date_create,title) VALUES (:owner_id,:_hash,:_date,:title) ON DUPLICATE KEY UPDATE date_create=:_date, title=:title")->execute([':owner_id' => Yii::app()->user->id, ':_hash' => $this->_tid, ':_date' => date("Y-m-d H:i:s"), ':title' => $this->_title])) {

            $tweets = Yii::app()->db->createCommand("SELECT id,tweet FROM {{twitter_tweetsRoster}} tr WHERE _key=:key AND NOT EXISTS (SELECT id FROM {{twitter_tweetsListsRows}} WHERE id=tr.parent)")->queryAll(true, [':key' => $this->_tid]);

            if(!empty($tweets)) {
                $inserts = [];

                foreach($tweets as $tweet) {
                    $inserts[] = [$this->_tid, $tweet['tweet'], $tweet['id']];
                }

                \CHelper::batchInsert('twitter_tweetsListsRows', ['_hash', 'tweet', 'parent'], $inserts);
            }
        } else {
            $this->setCode(404)->addError('_tid', Yii::t('twitterModule.tweets', '_not_roster_exists_for_save'));
        }
    }

    protected function tweetEdit()
    {
        $model = new Edit;

        if($model->load(['id' => $this->edit['id'], 'tweet' => $this->edit['tweet'], '_key' => $this->_tid], true) && $model->validate())
            $this->setCode(200)->_getTweets(true);
        else
            $this->setCode(202)->addError('edit', $model->getError());
    }

    protected function removeTweets()
    {
        if($this->_group != 'all') {
            $db = Yii::app()->db;

            if($this->_group != '') {
                $db->createCommand("DELETE FROM {{twitter_tweetsRoster}} WHERE _key=:tid AND owner_id=:owner_id AND FIND_IN_SET (:index, _indexes)")->execute(array(':index' => $this->_group, ':tid' => $this->_tid, ':owner_id' => Yii::app()->user->id));
            } else {
                $db->createCommand("DELETE FROM {{twitter_tweetsRoster}} WHERE _key=:tid AND owner_id=:owner_id AND id IN('" . implode("', '", $this->ids) . "')")->execute(array(':tid' => $this->_tid, ':owner_id' => Yii::app()->user->id));
            }

            Yii::app()->redis->delete('twitter:tweets:' . $this->_tid . ':counts');

            if($this->getFigures('all') === 0) {
                $db->createCommand("DELETE FROM {{twitter_tweetsLists}} WHERE _hash=:tid AND owner_id=:owner_id");
                $this->setCode(301)->addError('_tid', Yii::t('twitterModule.tweets', '_not_roster_exists'));
            } else
                $this->setCode(200)->_getTweets(true);
        } else {
            $this->addError('_action', Yii::t('yii', 'Your request is invalid.'));
        }
    }
}
