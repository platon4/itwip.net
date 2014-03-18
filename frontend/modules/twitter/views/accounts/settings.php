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
<div class="block_content">
<div id="block_1">
    <div id="block_1_1">
        <div id="block_1_1_1">
            <img src="<?php echo (trim($model->get('avatar')) == "") ? "/i/_default.png" : str_replace("_normal", "", $model->get('avatar')); ?>" alt=""/>
        </div>
        <div id="block_1_1_2">
            <span class="block name shadow"><?php echo Html::encode($model->get('name')); ?></span>
            <span class="block login shadow"><?php echo Yii::t('main', '_login'); ?>: <a href="https://twitter.com/<?php echo Html::encode($model->get('screen_name')); ?>">@<?php echo Html::encode($model->get('screen_name')); ?></a></span>
            <span class="block langue shadow"><?php echo Yii::t('main', '_langue'); ?>: <?php echo Yii::t('main', Html::_gTwLang($model->get('_lang'))); ?></span>
            <span class="block date shadow"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_regTwitter'); ?> <?php echo date("d.m.Y", $model->get('created_at')); ?> (<?php echo Html::_dateTransform($model->get('created_at'), 'unix', 'days') . " " . Yii::t('main', '_days'); ?>)</span>
        </div>
        <div id="block_1_1_3">
            <span id="tweets"><h3><?php echo $model->get('tweets'); ?></h3><?php echo Yii::t('main', '_tweets'); ?></span>
            <span id="no"></span>
            <span id="following"><h3><?php echo $model->get('following'); ?></h3><?php echo Yii::t('main', '_following'); ?></span>
            <span id="no"></span>
            <span id="followers"><h3><?php echo $model->get('followers'); ?></h3><?php echo Yii::t('main', '_followers'); ?></span>
        </div>
    </div>
    <div id="block_1_2"></div>
    <div id="block_1_3">
        <div id="block_1_3_1">
            <table>
                <tr>
                    <td class="information"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_yaAvtoritet'); ?>
                        <span id="yandex_rank"><?php echo $model->get('yandex_rank'); ?></span></td>
                    <td class="recheck"><?php if($model->lastUpdate('yandex_rank') === false) { ?>
                            <div class="updBtn" data-check="yandex_rank" data-send="<?php echo Html::encode($model->get('id')); ?>">
                                <span onclick="Settings._credentials(this); return false;"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_check'); ?></span>
                            </div>
                        <?php } else { ?>
                            <div class="updBtn"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_recentlyTested'); ?></div><?php } ?>
                    </td>
                </tr>
                <tr>
                    <td class="information"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_yaIndex'); ?>
                        <span id="in_yandex"><?php echo ($model->get('in_yandex')) ? Yii::t('main', '_yes') : Yii::t('main', '_no'); ?></span>
                    </td>
                    <td class="recheck"><?php if($model->get('in_yandex') == 0) { ?>
                            <?php if($model->lastUpdate('in_yandex') === false) { ?>
                                <div class="updBtn" data-check="in_yandex" data-send="<?php echo Html::encode($model->get('id')); ?>">
                                    <span onclick="Settings._credentials(this); return false;"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_check'); ?></span>
                                </div>
                            <?php } else { ?>
                                <div class="updBtn"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_recentlyTested'); ?></div><?php } ?><?php } ?>
                    </td>
                </tr>
                <tr>
                    <td class="information"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_googlePr'); ?>
                        <span id="google_pr"><?php echo $model->get('google_pr'); ?></span></td>
                    <td class="recheck"><?php
                        if($model->lastUpdate('google_pr') === false) {
                            ?>
                        <div class="updBtn" data-check="google_pr" data-send="<?php echo Html::encode($model->get('id')); ?>">
                            <span onclick="Settings._credentials(this); return false;"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_check'); ?></span>
                            </div><?php
                        } else {
                            ?>
                            <div class="updBtn"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_recentlyTested'); ?></div><?php } ?>
                    </td>
                </tr>
                <!-- <tr><td class="information"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_googleIndex'); ?> <span id="in_google"><?php echo ($model->in_google) ? Yii::t('main', '_yes') : Yii::t('main', '_no'); ?></span></td>
                            <td class="recheck"><?php
                if($model->get('in_google') == 0) {
                    ?><?php
                    if($model->lastUpdate('in_google') === false) {
                        ?><div class="updBtn" data-check="in_google" data-send="<?php echo Html::encode($model->get('id')); ?>"><span onclick="Settings._credentials(this); return false;"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_check'); ?></span></div><?php
                    } else {

                        ?><div class="updBtn"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_recentlyTested'); ?></div><?php } ?><?php } ?></td></tr> -->
            </table>
        </div>
        <div id="block_1_3_2">
            <span class="block">
                <div style="display: inline-block; padding-top: 4px; border-right-width: 0px; padding-right: 5px;">
                    <div class="onoffswitch">
                        <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="myonoffswitch"<?php echo ($model->get('_status') == 1) ? ' checked' : ''; ?><?= (in_array($model->get('_status'), ['1', '7'])) ? '' : ' disabled'; ?> onchange="Accounts.settings.status(this, '<?= $model->get('id'); ?>'); return false;">
                        <label class="onoffswitch-label" for="myonoffswitch">
                            <div class="onoffswitch-inner"></div>
                            <div class="onoffswitch-switch"></div>
                        </label>
                    </div>
                </div>
            </span>
        </div>
        <div id="block_1_3_3">
                    <span class="block"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_accountStatus'); ?>
                        <span id="_status"><?php echo $model->status(); ?></span></span>
            <span class="block itr"><i class="tooltip" title="<?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_iTRtooltip'); ?>">?</i> <?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_iTR'); ?> <?= $model->get('itr'); ?> </span>
        </div>
    </div>
</div>
<div id="block_2" class="shadow"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_settings'); ?></div>
<?php echo Html::beginForm('', 'POST', array('id' => 'settingsForm')); ?>
<div id="block_3">
    <div id="block_3_line">
        <div id="block_3_line_text"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_themeAccount'); ?></div>
        <div id="block_3_line_form">
            <div id="_subjectsBox">
                <?= $model->getSubjects(); ?>
            </div>
            <?php
            if($model->get('', 'settings')->getError('_subject')) {
                echo Html::error($model->get('', 'settings'), '_subject');
            }
            ?>
        </div>
    </div>
    <div id="block_3_line">
        <div id="block_3_line_text"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_FloorAccount'); ?> </div>
        <div id="block_3_line_form">
            <?php
            echo Html::activeRadioButtonList($model->get('', 'settings'), '_gender', array(
                '2' => Yii::t('twitterModule.accounts', '_twitterAccountSetting_men'),
                '1' => Yii::t('twitterModule.accounts', '_twitterAccountSetting_woman'),
                '0' => Yii::t('twitterModule.accounts', '_twitterAccountSetting_any')), array(
                'separator' => '&nbsp;'));
            ?>
        </div>
    </div>
    <div id="block_3_line">
        <div id="block_3_line_text"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_AgeAccount'); ?></div>
        <div id="block_3_line_form">
            <?php
            echo Html::activeDropDownList($model->get('', 'settings'), '_age', $model->getAges(), ['class' => 'styler']);
            ?>
        </div>
    </div>
    <div id="block_3_line">
        <div id="block_3_line_text"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_modeOperation'); ?>
            <i class="tooltip" title="<?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_modeOperationTooltip'); ?>">?</i>
        </div>
        <div id="block_3_line_form">
            <?php
            echo Html::activeRadioButtonList($model->get('', 'settings'), 'working_in', array(
                '0' => Yii::t('twitterModule.accounts', '_twitterAccountSetting_modeOperationManual'),
                '1' => Yii::t('twitterModule.accounts', '_twitterAccountSetting_modeOperationAutomatic')), array(
                'separator' => '&nbsp;'));
            ?>
        </div>
    </div>
    <div id="block_3_line">
        <div id="block_3_line_text"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_pricePost'); ?>
            <span class="text_price_fix"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_pricePostRecommend'); ?> <?php echo CMoney::_c(CMoney::itrCost($model->itr), true); ?> <?php
                if($model->get('in_yandex')) {
                    echo '(+ ' . CMoney::_c(2, true) . ' "быстроробот")';
                }
                ?></span>
        </div>
        <div id="block_3_line_form">
            <?php
            echo Html::activeTextField($model->get('', 'settings'), '_price', array(
                'placeholder' => Yii::t('twitterModule.accounts', '_twitterAccountSetting_pricePostInput')));
            ?>
            <?php
            if($model->get('', 'settings')->getError('_price')) {
                echo Html::error($model->get('', 'settings'), '_price');
            }
            ?>
        </div>
    </div>
    <div id="block_3_line">
        <div id="block_3_line_text"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_priceRuntime'); ?>
            <i class="tooltip" title="<?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_priceRuntimeTooltip'); ?>">?</i>
        </div>
        <div id="block_3_line_form">
            <?php
            echo Html::activeTextField($model->get('', 'settings'), '_timeout', array('placeholder' => Yii::t('twitterModule.accounts', '_twitterAccountSetting_priceRuntimeInput')));
            ?>
            <?php
            if($model->get('', 'settings')->getError('_timeout')) {
                echo Html::error($model->get('', 'settings'), '_timeout');
            }
            ?>
        </div>
    </div>
    <!-- <div id="block_3_line">

                    <div id="block_3_line_text">

            <?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_quickPosts'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_quickPostsTooltip'); ?>">?</i>

                            <span class="text_price_fix"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_PriceFixed'); ?> 7 руб.</span>

                    </div>

                    <div id="block_3_line_form">

            <?php
    echo Html::activeRadioButtonList($model->get('', 'settings'), 'fast_posting', array(
        '1' => Yii::t('main', '_yes'), '0' => Yii::t('main', '_no')), array('separator' => '&nbsp;'));
    ?>
                    </div>
            </div> -->
    <div id="block_3_line">
        <div id="block_3_line_text">
            <?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_retweet'); ?>
            <span class="text_price_fix"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_PriceFixed'); ?> N руб.</span>
        </div>
        <div id="block_3_line_form">
            <?php
            echo Html::activeRadioButtonList($model->get('', 'settings'), 'allow_retweet', array(
                '1' => Yii::t('main', '_yes'),
                '0' => Yii::t('main', '_no')), array('separator' => '&nbsp;'));
            ?>
        </div>
    </div>
    <div id="block_3_line">
        <div id="block_3_line_text">
            <?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_followers'); ?>
            <i class="tooltip" title="<?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_followersTooltip'); ?>">?</i>
            <span class="text_price_fix"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_PriceFixed'); ?> N руб.</span>
        </div>
        <div id="block_3_line_form">
            <?php
            echo Html::activeRadioButtonList($model->get('', 'settings'), 'allow_following', array(
                '1' => Yii::t('main', '_yes'),
                '0' => Yii::t('main', '_no')), array('separator' => '&nbsp;'));
            ?>
        </div>
    </div>
    <div id="block_3_line">
        <div id="block_3_line_text">
            <?= Yii::t('twitterModule.accounts', '_twitterAccountSetting_fast_index'); ?>
            <i class="tooltip" title="<?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_fast_indexTooltip'); ?>">?</i>
            <span class="text_price_fix"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_PriceFixed'); ?> от 3 до 100 руб.</span>
        </div>
        <div id="block_3_line_form">
            <?= Html::activeRadioButtonList($model->get('', 'settings'), 'in_indexses', array('1' => Yii::t('main', '_yes'), '0' => Yii::t('main', '_no')), array('separator' => '&nbsp;')); ?>
        </div>
    </div>
    <div id="block_3_line">
        <div id="block_3_line_text">
            <?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_bonus_pay'); ?>
            <i class="tooltip" title="<?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_bonus_payTooltip'); ?>">?</i>
        </div>
        <div id="block_3_line_form">
            <?php echo Html::activeRadioButtonList($model->get('', 'settings'), '_allow_bonus_pay', array('1' => Yii::t('main', '_yes'), '0' => Yii::t('main', '_no')), array('separator' => '&nbsp;')); ?>
        </div>
    </div>
    <div id="block_3_line">
        <div id="block_3_line_text"><?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_stopWords'); ?></div>
        <div id="block_3_line_form">
            <span class="block" style="margin-top: 7px; margin-bottom: 7px;"><?php echo Html::CheckBox('Settings[filter][policy]', $model->getFilter('policy')); ?> <?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_stopWordspolicy'); ?></span>
                    <span class="block" style="margin-bottom: 7px;">
                        <?= Html::CheckBox('Settings[filter][personal]', $model->getFilter('personal'), ['onchange' => 'inputActive(\'Settings_words\'); return false;']); ?> <?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_stopWordsFilters'); ?>
                    </span>
                    <span class="block">
                        <?= Html::textArea('Settings[words]', $model->get('filterWords', 'settings'), ['class' => 'no_resize', 'placeholder' => Yii::t('twitterModule.accounts', '_twitterAccountSetting_stopWordsTextarea'), 'disabled' => (!$model->getFilter('personal')) ? 'disabled' : '']); ?>
                    </span>
            <?php
            if($model->get('', 'settings')->getError('_filter')) {
                echo Html::error($model->get('', 'settings'), '_filter');
            }
            ?>
        </div>
    </div>
</div>
<div id="block_4">
    <a href="javascript:;" onclick="Accounts.settings.update('<?= $model->get('id'); ?>', 'record', this);" class="button"/>Обновить данные аккаунта</a>
    <button class="button btn_red" onclick="Accounts.settings.remove('<?= $model->get('id'); ?>', '<?= Yii::t('twitterModule.accounts', '_account_remove_title', ['{account}' => Html::encode($model->get('screen_name'))]); ?>', '<?= Yii::t('twitterModule.accounts', '_account_remove_text'); ?>'); return false;">
        <i class="icon-trash"></i> Удалить аккаунт
    </button>
    <button type="submit" class="button btn_green">Сохранить настройки</button>
</div>
<?php echo Html::endForm(); ?>

<?php if($model->updateProcess()) { ?>
    <div class="load_sec">
        <div style="padding-top: 100px;">Происходит сбор данных, это может занять некоторое время (до 2 минут).<br>По завершению операции Вы сможете настроить аккаунт для работы в системе.<br><br>
        </div>
        <div><img src="/i/loading_11.gif" alt=""/></div>
    </div>
<?php } ?>
</div>
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