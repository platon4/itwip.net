<?php
$this->pageTitle=Yii::app()->name.' - '.Yii::t('accountsModule.accounts','_newPageTitle');
Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl.'/css/elements.css');
?>
<style>
	.captcha-image
	{
		margin-left: 15px; 
	}
	.captcha-image img {
		border: 1px solid #ccc;
	}
</style>
<div id="info">
    <div id="info_inset">
        <div id="modal_info">
            <div class="title_modal_info"><?php echo Yii::t('accountsModule.accounts','_newPageTitle'); ?></div>
            <div class="content_modal_info">
                <div id="newContainer">
                    <?php $this->renderPartial('_newAccount',array('model'=>$model,'captcha'=>$captcha)); ?>
                </div>
            </div>
        </div>
    </div>
</div>