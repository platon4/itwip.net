<?php

namespace twitter\models\tweets;

use Yii;

class Prepared extends \FormModel
{
    public $_action;
    public $_rosterCount;
    public $_tid;
    public $_file_type;
    protected $_actions = array(
        'download' => array('method' => 'tweetsDownalod'),
    );
    protected $tweets;

    public function rules()
    {
        return array(
            array('_action', 'ext.validators.actionsValidator', 'allowEmpty' => true, 'actions' => $this->_actions),
            array('_tid', 'ext.validators.hashValidator', 'min' => 10, 'max' => 15, 'on' => 'download'),
            array('_file_type', 'in', 'range' => array('txt'), 'on' => 'download', 'message' => Yii::t('twitterModule.tweets', '_not_file_type_to_download')),
        );
    }

    protected function afterValidate()
    {
        if ((is_array($this->_action) && isset($this->_action['method'])) || (!is_array($this->_action) && !CHelper::isEmpty($this->_action)))
            $this->_init();
    }

    protected function _init()
    {
        $method = is_array($this->_action) ? $this->_action['method'] : $this->_action;

        if (method_exists($this, $method))
            $this->$method();
        else
            $this->addError('_action', Yii::t('yii', 'Your request is invalid.'));
    }

    public function rosterCount()
    {
        if ($this->_rosterCount === null)
            $this->_rosterCount = Yii::app()->db->createCommand("SELECT COUNT(*) FROM {{tw_tweets_lists}} WHERE owner_id=:owner")->queryScalar(array(':owner' => Yii::app()->user->id));

        return $this->_rosterCount;
    }

    public function getTweets()
    {
        if ($this->tweets === null) {

            $this->tweets = Yii::app()->db->createCommand("SELECT * FROM {{tw_tweets_lists}} WHERE owner_id=:owner")->queryAll(true, array(':owner' => Yii::app()->user->id));
        }

        return $this->tweets;
    }

    public function getView()
    {
        if ((is_array($this->_action['method']) && isset($this->_action['render']) && $this->_action['render'] === true) || CHelper::isEmpty($this->_action))
            return true;
        else
            return false;
    }

    public function getViewFile($partial = false)
    {
        if (!CHelper::isEmpty($this->_action)) {
            if (isset($this->_action['renderFiles']))
                return $partial === true ? $this->_action['renderFiles']['partial'] : $this->_action['full'];
            else
                throw new \CException(Yii::t('yii', 'Render file not found.'));
        }
        else
            return $partial === true ? '_prepared_rows' : '_prepared';
    }

    public function getRosterName()
    {
        return;
    }

    protected function tweetsDownalod()
    {
        $download = new \DownloadTweets;

        $download->setTweets($this->getTweets());
        $download->setOutPutFile($this->_file_type);
        $download->setFileName($this->getRosterName());
        $download->outPutFile();
    }
}
