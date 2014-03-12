<?php
    $this->breadcrumbs[] = array(
        0 => array(Yii::t('breadcrumbs', '_twitter'), '/twitter'),
        1 => array(Yii::t('breadcrumbs', '_tw_accounts'), '/twitter/accounts'),
        2 => array(Yii::t('breadcrumbs', '_tw_accounts_add'), '/twitter/accounts/add')
    );
?>
<div class="block twitterAccountAdd">
<?php echo Html::beginForm(); ?>
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-twitter"></i> <h5><?php echo Yii::t('twitterModule.accounts', '_twitterAccountAdd_title'); ?></h5></div></div>
    <div class="block_content">
        <h3 class="shadow"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountAdd_h1'); ?></h3>
        <p class="shadow"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountAdd_text1'); ?></p>
        <h3><?php echo Yii::t('twitterModule.accounts', '_twitterAccountAdd_h2'); ?></h3>
        <p><?php echo Yii::t('twitterModule.accounts', '_twitterAccountAdd_text2'); ?></p>
        <h3><?php echo Yii::t('twitterModule.accounts', '_twitterAccountAdd_h3'); ?></h3>
        <p><?php echo Yii::t('twitterModule.accounts', '_twitterAccountAdd_text3'); ?></p>
		<div style="margin-top: 20px; margin-bottom: 15px;">
		<?php echo Html::activeCheckBox($model, 'agreed', array('class' => 'styler')); ?> <?php echo Yii::t('twitterModule.accounts', '_twitterAccountAdd_text4'); ?>
		<?php if(count($model->getErrors())) { ?>
			<div style="margin-top: 10px;">
			     <?php echo Html::error($model,'agreed'); ?>
			</div>
		<?php } ?>
		</div>
         <button class="button btn_blue" type="submit"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountAdd_button'); ?></button>
    </div>
<?php echo Html::endForm(); ?>
</div>