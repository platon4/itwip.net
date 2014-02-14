<?php

/**
 * Description of autoEject
 *
 * @author Александр
 */
class autoWithdraw extends FormModel 
{
    public $autoEject;
    
    public function rules()
    {
        return array(
            array('autoEject','numerical','integerOnly'=>true,'allowEmpty'=>false,'min'=>Yii::app()->params['minAutoEjectAmount'],'max'=>Yii::app()->params['maxAutoEjectAmount'],'tooSmall'=>Yii::t('financeModule.index','_autoeject_min_amount',array('{min}'=>Yii::app()->params['minAutoEjectAmount'])),'tooBig'=>Yii::t('financeModule.index','_autoeject_max_amount',array('{max}'=>Yii::app()->params['maxAutoEjectAmount']))),
        );
    }
}
