<?php

return array(
    0=>array('title'=>Yii::t('main','_error'),'message'=>Yii::t('twitterModule.accounts','_unknown_error'),
        'link'=>Yii::app()->homeUrl.'twitter/accounts/add'),
    201=>array('title'=>Yii::t('main','_error'),'message'=>Yii::t('twitterModule.accounts','_process_acc_exists'),
        'link'=>Yii::app()->homeUrl.'twitter/accounts/add'),
    202=>array('title'=>Yii::t('main','_error'),'message'=>Yii::t('twitterModule.accounts','_process_acc_exists_other_owner'),
        'link'=>Yii::app()->homeUrl.'twitter/accounts/add'),
    203=>array('title'=>Yii::t('main','_error'),'message'=>Yii::t('twitterModule.accounts','_process_acc_no_allowed'),
        'link'=>Yii::app()->homeUrl.'twitter/accounts/add'),
    401=>array('title'=>Yii::t('main','_error'),'message'=>Yii::t('twitterModule.accounts','_process_token_failed'),
        'link'=>Yii::app()->homeUrl.'twitter/accounts/add'),
    501=>array('title'=>Yii::t('main','_error'),'message'=>Yii::t('twitterModule.accounts','_process_not_user_id'),
        'link'=>Yii::app()->homeUrl.'twitter/accounts/add'),
);
