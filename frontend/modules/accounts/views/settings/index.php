<?php
    $this->pageTitle      =Yii::app()->name.' - '.Yii::t('main','_accounts_settings_Title');
    $this->metaDescription=Yii::t('main','_accounts_settings_Description');
?>
<?php if(Yii::app()->user->hasFlash('_settings_save_success'))
{ ?>
    <div style="margin-bottom: 11px;" class="line_info ok">
        <?php echo Yii::app()->user->getFlash('_settings_save_success'); ?>
    </div>
<?php } ?>
<?php if(count($form['minor']->getErrors()) OR count($form['main']->getErrors()) OR count($form['confirm']->getErrors()))
{ ?>
    <div style="margin-bottom: 11px;" class="line_info alert">
        <?php echo Html::errorSummary($form['minor']); ?>
        <?php echo Html::errorSummary($form['main']); ?>
    <?php echo Html::errorSummary($form['confirm']); ?>
    </div>
<?php } ?>
<div id="settings" class="block">
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-wrench"></i> <h5><?php echo Yii::t('main','_accounts_settings_Title'); ?></h5></div></div>
    <div class="block_content">
<?php echo CHtml::beginForm(); ?>	
        <div id="block_1_1_block">
            <div id="block_1_1">
                <h3 class="top_title"><?php echo Yii::t('accountsModule.settings','_personal_data'); ?></h3>
                <table>
                    <tr><td><?php echo Yii::t('accountsModule.settings','_full_Name'); ?></td><td><?php echo Html::activeTextField($form['main'],'name',array(
    'value'=>'','placeholder'=>$form['main']->name)); ?></td></tr>
                    <tr><td><?php echo Yii::t('accountsModule.settings','_e-mail'); ?></td><td><?php echo Html::textField('cSettings[email]','',array(
    'placeholder'=>$form['main']->email)); ?></td></tr>
                    <tr><td><?php echo Yii::t('accountsModule.settings','_ICQ'); ?></td><td><?php echo Html::activeTextField($form['minor'],'_icq',array('autocomplete'=>'off')); ?></td></tr>
                </table>
                <h3 class="top_title"><?php echo Yii::t('accountsModule.settings','_change_password'); ?></h3>
                <table>
                    <tr><td><?php echo Yii::t('accountsModule.settings','_old_password'); ?></td><td><?php echo Html::passwordField('cSettings[password]','',array('autocomplete'=>'off','value'=>'')); ?></td></tr>
                    <tr><td valign="top"><?php echo Yii::t('accountsModule.settings','_new_password'); ?></td><td><?php echo Html::passwordField('cSettings[new_password]','',array('autocomplete'=>'off','value'=>'',
    'id'=>'_new_password')); ?><a href="javascript:;" onclick="_togglePassowrd(this, '_new_password');"><i class="fa fa-eye"></i></a></td></tr>
                </table>
                <h3 class="top_title"><?php echo Yii::t('accountsModule.settings','_payment_systems'); ?></h3>
                <table>
                    <tr><td><?php echo Yii::t('accountsModule.settings','_webMoney_r'); ?></td><td><?php echo Html::activeTextField($form['confirm'],'purse',array(
    'value'=>'','placeholder'=>($form['confirm']->purse)?$form['confirm']->purse:Yii::app()->user->_setting('purse'))); ?></td></tr>               
                </table>			
                <h3 class="top_title"><?php echo Yii::t('accountsModule.settings','_website'); ?></h3>
                <table>
                    <tr>
                        <td><span title="По курсу ЦБ РФ"><?php echo Yii::t('accountsModule.settings','_preferred_currency'); ?></span></td>
                        <td>
                            <?php echo Html::activeDropDownList($form['minor'],'_preferred_currency',array(
                                0=>Yii::t('accountsModule.settings','_ruble'))); ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo Yii::t('accountsModule.settings','_language'); ?></td>
                        <td>
<?php echo Html::activeDropDownList($form['minor'],'_language',array(0=>Yii::t('accountsModule.settings','_russian'))); ?>
                        </td>
                    </tr>
                </table>			
            </div>
            <div id="block_1_2"></div>
            <div id="block_1_3">
                <h3 class="top_title"><?php echo Yii::t('accountsModule.settings','_security'); ?></h3>
                <table>
                    <tr>
                        <td valign="top"><span title="<?php echo Yii::t('accountsModule.settings','_enable_ip_title'); ?>"><?php echo Yii::t('accountsModule.settings','_enable_ip'); ?></span></td>
                        <td>
<?php echo Html::activeTextField($form['main'],'_allow_ip',array('id'=>'_ip_user',
    'style'=>'width: calc(100% - 30px);')); ?>
                            <div style="margin-top: 5px;">Ваш текущий IP: <a href="javascript:;" onclick="insertValue(this, '_ip_user');"><? echo CHelper::_getIP(); ?></a></div></td>
                    </tr>
                </table>
                <h3 class="top_title"><?php echo Yii::t('accountsModule.settings','_system_alerts'); ?></h3>
                <table>
                    <tr><td><?php echo Html::activeCheckBox($form['minor'],'system_news_administration'); ?> <?php echo Yii::t('accountsModule.settings','_news_administration'); ?></td></tr>
                    <tr><td><?php echo Html::activeCheckBox($form['minor'],'system_expire_premium_subscription'); ?> <?php echo Yii::t('accountsModule.settings','_closing_premium_subscription'); ?></td></tr>
                    <tr><td><?php echo Html::activeCheckBox($form['minor'],'system_new_orders_fwebmaster'); ?> <?php echo Yii::t('accountsModule.settings','_new_applications_from_advertiser'); ?></td></tr>
                </table>
                <h3 class="top_title"><?php echo Yii::t('accountsModule.settings','_alert_e-mail'); ?></h3>
                <table>
                    <tr><td><?php echo Html::activeCheckBox($form['minor'],'email_new_private'); ?> <?php echo Yii::t('accountsModule.settings','_new_private-messages'); ?></td></tr>
                    <tr><td><?php echo Html::activeCheckBox($form['minor'],'email_attemps_notification'); ?> <?php echo Yii::t('accountsModule.settings','_failed_access_site'); ?></td></tr>
                    <tr><td><?php echo Html::activeCheckBox($form['minor'],'email_new_orders_fwebmaster'); ?> <?php echo Yii::t('accountsModule.settings','_new_applications_from_advertiser'); ?></td></tr>
                    <?php if(Yii::app()->user->checkAccess('moderator'))
                    { ?>
                        <tr><td><h5><?php echo Yii::t('accountsModule.settings','_addons_settings'); ?></h5></td></tr>
                        <tr><td>
                                <table style="margin: 0 0;">						
                                    <tr><td><?php echo Html::activeCheckBox($form['minor'],'email_new_snotification'); ?> <?php echo Yii::t('accountsModule.settings','_new_support_notification'); ?></td></tr>
                                </table>
                            </td></tr>	
<?php } ?>
                </table>
                <h3 class="top_title"><?php echo Yii::t('accountsModule.settings','_alert_ICQ'); ?></h3>
                <table>
                    <tr><td><?php echo Html::activeCheckBox($form['minor'],'icq_new_private'); ?> <?php echo Yii::t('accountsModule.settings','_new_private-messages'); ?></td></tr>
                    <tr><td><?php echo Html::activeCheckBox($form['minor'],'icq_attemps_notification'); ?> <?php echo Yii::t('accountsModule.settings','_failed_access_site'); ?></td></tr>
                    <tr><td><?php echo Html::activeCheckBox($form['minor'],'icq_new_orders_fwebmaster'); ?> <?php echo Yii::t('accountsModule.settings','_new_applications_from_advertiser'); ?></td></tr>
<?php if(Yii::app()->user->checkAccess('moderator'))
{ ?>
                        <tr><td><h5><?php echo Yii::t('accountsModule.settings','_addons_settings'); ?></h5></td></tr>
                        <tr><td>
                                <table style="margin: 0 0;">						
                                    <tr><td><?php echo Html::activeCheckBox($form['minor'],'icq_new_snotification'); ?> <?php echo Yii::t('accountsModule.settings','_new_support_notification'); ?></td></tr>
                                </table>
                            </td></tr>						
<?php } ?>
                </table>
            </div>
        </div>
        <div class="block_bottom">
            <button type="submit" class="button btn_green"> <?php echo Yii::t('accountsModule.settings','_save_data'); ?></button>
        </div>
<?php echo CHtml::endForm(); ?>	
    </div>
</div>
