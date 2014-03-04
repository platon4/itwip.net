<?php echo Html::beginForm(); ?>
<?php if(!$captcha)
{ ?>
    <?php echo Html::hiddenField('_step',1); ?>
    <?php
    echo ($model->getError('_all'))?Html::error($model,'_all'):'';

    echo Html::activeTextField($model,'name',array('autocomplete'=>'off',
        'placeholder'=>Yii::t('index','_name_reg_place'),'class'=>( $model->getError('name')?'error':'')));

    echo ($model->getErrors('name'))?Html::error($model,'name'):'';

    echo Html::activeTextField($model,'email',array(
        'autocomplete'=>'off',
        'placeholder'=>Yii::t('index','_email_reg_place'),'class'=>($model->getError('email')?'error':'')
    ));

    echo ($model->getError('email'))?Html::error($model,'email'):'';

    echo Html::activePasswordField($model,'password',array('autocomplete'=>'off',
        'placeholder'=>Yii::t('index','_password_reg_place')));
    ?>
    <?php echo ($model->getError('password'))?Html::error($model,'password'):''; ?>
    <div class="checkbox_p">
        <label><?php
            echo Html::activeCheckBox($model,'agreed',array(
                'uncheckValue'=>null));
            ?></label>
    <?php echo Yii::t('index','_agreed_reg_place',array(
        '{link}'=>Yii::app()->createUrl('/regulations'))); ?>
            <?php echo Html::error($model,'agreed'); ?>
    </div>
<?php } else
{ ?>
    <div class="captcha-image">
    <?php $this->widget('CCaptcha'); ?>
    </div>	
    <?php echo CHtml::activeTextField($model,'code',array(
        'value'=>'')); ?>
    <?php echo Html::error($model,'code'); ?>
    <?php echo Html::hiddenField('_step',10); ?>
<?php } ?>
    <button class="mAccounts button btn_blue" data-action="newContainer" data-send="/accounts/new" onclick="_iAction(this);
            return false;"><?php echo Yii::t('index','_newButton'); ?></button>
<?php echo Html::endForm(); ?>