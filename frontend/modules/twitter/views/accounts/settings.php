<?php

$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_twitterSettings_Title');
$this->metaDescription = Yii::t('main', '_twitterSettings_Description');

$this->breadcrumbs[] = [
    0 => [Yii::t('breadcrumbs', '_twitter'), '/twitter'],
    1 => [Yii::t('breadcrumbs', '_tw_accounts'), '/twitter/accounts'],
    2 => [Yii::t('breadcrumbs', '_account_setting', array('{account}' => '@' . Html::encode($model->get('screen_name')))), '']
];
?>
<?php if($model->get('_status') == 0) { ?>
    <div style="margin-bottom: 15px;">
        <div class="line_info alert">
            <div class="errorMessage"><?php echo Yii::t('twitterModule.accounts', '_accounts_no_moderation'); ?></div>
        </div>
    </div>
<?php } ?>
<?php
if(Yii::app()->user->hasFlash('tw_settings_message')) {
    $dialog = Yii::app()->user->getFlash('tw_settings_message');
    ?>
    <div id="_flashDialog" style="margin-bottom: 11px;" class="line_info <?php echo ($dialog['type'] == 'success') ? 'ok' : 'alert'; ?>">
        <?php echo Html::encode($dialog['text']); ?>
    </div>
    <script>
        setTimeout(function ction() {
                $('#_flashDialog').fadeOut();
            }
            , 4000);

    </script>
<?php } ?>
<?php if(Yii::app()->user->hasFlash('_settings_save_success')) { ?>
    <div id="_flash" style="margin-bottom: 11px;" class="line_info ok">
        <?php echo Yii::app()->user->getFlash('_settings_save_success'); ?>
    </div>
    <script>
        setTimeout(function ction() {
                $('#_flash').fadeOut();
            }
            , 3000);

    </script>
<?php } ?>
<div class="block twitterAccountSetting">
<div class="block_title">
    <div class="block_title_inset"><i class="fa fa-wrench"></i>
        <h5><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_title'); ?></h5></div>
</div>
<div id="dataContent" class="block_content">
    <?php
    $this->renderPartial('settingsContent', ['model' => $model]);
    ?>
</div>
<div id="dialog-message" title="<?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_deleteModalTitle'); ?>" style="display: none;">
    <div class="ui-dialog-content-text">
        <?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_deleteModalText'); ?>
    </div>
    <div class="ui-dialog-content-button">
        <button class="button btn_red"><?php echo Yii::t('main', '_yes'); ?></button>
        <button class="button"><?php echo Yii::t('main', '_no'); ?></button>
    </div>
</div>