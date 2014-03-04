<?php
    $this->pageTitle = Yii::app()->name . ' - ' . Yii::t('accountsModule.accounts', '_lostPageTitle');
    Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl . '/css/elements.css');
?>
<div id="info">
    <div id="info_inset">
    	<div id="modal_info">
    		<div class="title_modal_info"><?php echo Yii::t('accountsModule.accounts', '_lostPageTitle'); ?></div>
    		<div class="content_modal_info">
    			<div id="lostContainer">
					<?php echo Html::beginForm(); ?>
						<?php echo Html::activeTextField($model, 'email', array('placeholder' => Yii::t('index', '_email_reg_place'), 'class' => ( $model->getError('email')  ? 'error' : ''))); ?>			
						<?php echo Html::error($model, 'email'); ?>
						<div style="float: left; margin-top: 7px;">
							<?php echo CHtml::activeTextField($model, 'code', array('value' => '','style'=>'width:115px;')); ?>					
						</div>	
						<div style="float: right;">
							<?php $this->widget('CCaptcha',array('clickableImage'=>true,'showRefreshButton'=>false)); ?>
						</div>		
						<div style="clear: both;"></div>
						<?php echo Html::error($model, 'code'); ?>
						<div style="margin-top: 15px; text-align: center;">
							<button type="submit" id="lostButton" class="button btn_blue"><?php echo Yii::t('index', '_lostButton'); ?></button>
						</div>
					<?php echo Html::endForm(); ?>
    			</div>
    		</div>
    	</div>
	</div>
</div>