<?php

namespace twitter\models\tweets;

use Yii;
use twitter\components\validators\TweetEdit;

class Edit extends \FormModel
{
    protected $validator;
    protected $_roster;

    public $id;
    public $tweet;
    public $_key;

    public function rules()
    {
        return [
            ['id', 'numerical', 'integerOnly' => true, 'allowEmpty' => false],
            ['id', 'rosterExists'],
            ['_key', 'ext.validators.hashValidator', 'min' => 7, 'max' => 20],
            ['tweet', 'length', 'max' => 1000, 'tooLong' => 'Превышено допустимое коло-го символов в твите.'],
        ];
    }

    public function rosterExists()
    {
        if($this->getRoster() === false) {
            $this->addError('id', 'Не удалось найти редактируемый твит, пожалуйста попробуйте еще раз.');
        }
    }

    public function getRoster()
    {
        if($this->_roster === null) {
            $this->_roster = Yii::app()->db->createCommand("SELECT * FROM {{twitter_tweetsRoster}} WHERE id=:id")->queryRow(true, [':id' => $this->id]);
        }

        return $this->_roster;
    }

    public function afterValidate()
    {
        $tw = new TweetEdit($this->id, $this->_key);
        $tw->validate($this->tweet);

        if($tw->allowNext()) {
            try {
                $t = Yii::app()->db->beginTransaction();

                Yii::app()->db->createCommand("UPDATE {{twitter_tweetsRoster}} SET tweet=:tweet,tweet_hash=:tweet_hash,_url=:url,_url_hash=:url_hash,_indexes=:indexes,_info=:info,_placement=:next WHERE id=:id")
                    ->execute([
                        ':id'         => $this->id,
                        ':tweet'      => $this->tweet,
                        ':tweet_hash' => $tw->getHash(),
                        ':url'        => $tw->getUrl(),
                        ':url_hash'   => $tw->getUrlHash(),
                        ':indexes'    => $tw->getIndexes(),
                        ':info'       => $tw->getInfo(),
                        ':next'       => $tw->allowNext()
                    ]);

                $t->commit();
            } catch(Exception $e) {
                $t->rollBack();
            }
        } else {
            foreach($tw->getErrors() as $key => $error) {
                $replace = [];

                if(isset($error['replace']))
                    $replace = $error['replace'];

                $this->addError('tweet', Yii::t('twitterModule.tweets', '_error_groups_' . $key, $replace));
                break;
            }
        }
    }
}
