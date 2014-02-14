<?php
    $this->pageTitle=Yii::app()->name.' - '.Yii::t('accountsModule.accounts','_authPageTitle');
    Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/elements.css');
?>
<div id="info">
    <div id="info_inset">
        <div id="modal_info">
            <div class="title_modal_info"><?php echo Yii::t('accountsModule.accounts','_authPageTitle'); ?></div>
            <div class="content_modal_info">
                <div id="authContainer">
                    <?php $this->renderPartial('_authAccount',array('model'=>$model)); ?>
                </div>
            </div>
        </div>
    </div>
</div>
