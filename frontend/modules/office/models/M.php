<?php

/**
 * Description of M
 *
 * @author Александр
 */
class M extends CFormModel {
    
    public $_status;
    public $_m;
    public $_message;
    public $tape;
    
    public function rules()
    {
        return array(
            array('_message','required','on'=>2,'message'=>Yii::t('officeModule.twitter_accounts','_reason_empty')),
            array('_status','in','range'=>array(0,1,2,3),'allowEmpty'=>false,'message'=>Yii::t('officeModule.twitter_accounts','_invalid_status')),
            array('_m','in','range'=>array(1,2,3,4,5,6,7,8,9,10),'allowEmpty'=>false,'message'=>Yii::t('officeModule.twitter_accounts','_invalid_mdr')),
            array('tape','in','range'=>array(0,1,2,3),'allowEmpty'=>false,'message'=>Yii::t('officeModule.twitter_accounts','_invalid_tape')),
        );
    }
}
