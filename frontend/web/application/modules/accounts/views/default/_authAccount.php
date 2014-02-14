<?php echo Html::beginForm(); ?>
<?php echo Html::activeTextField($model,'email',array('placeholder'=>Yii::t('index','_email_reg_place'),
    'class'=>( $model->getError('email')?'error':''))); ?>
<?php echo Html::error($model,'email'); ?>
<?php echo Html::activePasswordField($model,'password',array('placeholder'=>Yii::t('index','_password_reg_place'))); ?>
<?php echo Html::error($model,'password'); ?>
<div class="checkbox_p">
    <label>
<?php echo Html::activeCheckBox($model,'rememberMe',array('uncheckValue'=>null)); ?> Запомнить меня
    </label>
    <div style="margin-top: 10px;">
        <a href="/accounts/lost">Восстановить пароль</a>
    </div>
</div>
<button type="submit" id="authButton" class="mAccounts button btn_blue" return false;"><?php echo Yii::t('index','_authButton'); ?></button>
<?php echo Html::endForm(); ?>