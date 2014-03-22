<?php

class Replenishment extends CActiveRecord
{
    public $_pb;
    public $_cb;
    public $_system = 1;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function rules()
    {
        return array(
            array('amount,_system,_procente_extract,_add_to_balance,is_pay,_date', 'safe'),
            array('_system', 'in', 'range' => array('1', '2'), 'message' => Yii::t('financeModule.index', '_error_not_pay_system')),
            array('amount', 'required', 'message' => Yii::t('financeModule.index', '_error_amount_empty')),
            array('amount', 'numerical', 'integerOnly' => false, 'min' => '1', 'max' => '100000', 'tooSmall' => Yii::t('financeModule.index', '_error_amount_is_small'), 'tooBig' => Yii::t('financeModule.index', '_error_amount_is_big'), 'message' => Yii::t('financeModule.index', '_error_incorect_amount')),
        );
    }

    public function beforeValidate()
    {
        $this->addError('error', 'Пополнение временно недоступно в связи проведением технических работ на стороне сервиса.');
    }

    public function tableName()
    {
        return "{{money_replenishmentit}}";
    }
}