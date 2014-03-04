<?php
    $this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_support_Title');
    $this->metaDescription =  Yii::t('main', '_support_Description');
	
	$this->layout = '//layouts/info';
?>
<div id="info">
    <div id="info_inset">
    	<div id="modal_info">
    		<div class="title_modal_info">Запрос в поддержку</div>
    		<div class="content_modal_info">
				<?php if(!$_send) { ?>
					<div id="support_modal">
					<?php echo CHtml::beginForm(); ?>
						<div style="margin-top: 15px;">
						<?php echo Html::activeDropDownList($form,'_to',array(
						    ''=>'Кому адресовать вопрос',
							'0'=>'Общие вопросы ( модераторы сайта )',
							'1'=>'Ошибки, баги, проблемы в работе ( отдел программирования )',
						    '2'=>'Финансовые вопросы ( служба финансов )',
						    '3'=>'Предложения партнёрства или новых идей ( владелец сервиса ) ',
						)); ?>
						<?php echo ($form->getError('_to')) ? Html::error($form, '_to') : ''; ?> 
						</div>
						<?php if(Yii::app()->user->isGuest) { ?>
							<div  style="margin-top: 15px;">
								<?php echo Html::activeTextField($form,'_email',array('style'=>'width:483px;','placeholder'=>'Оставьте свой e-mail для связи.')); ?>
								<?php echo ($form->getError('_email')) ? Html::error($form, '_email') : ''; ?>
							</div>
						<?php } ?>
						<div style="margin-top: 15px; margin-bottom: 15px;">
							<?php echo Html::activeTextField($form,'_subject',array('style'=>'width:483px;','placeholder'=>'Коротко опишите тему вопроса')); ?>
							<?php echo ($form->getError('_subject')) ? Html::error($form, '_subject') : ''; ?>
						</div>
						<div>
							<?php echo Html::activeTextArea($form,'_text',array('style'=>'width:483px; height: 170px; resize: vertical;','placeholder'=>'Опишите подробнее свой вопрос. Использование тегов запрещено.')); ?>
							<?php echo ($form->getError('_text')) ? Html::error($form, '_text') : ''; ?>
						</div>
						<?php if(Yii::app()->user->isGuest) { ?>
							<?php if(CCaptcha::checkRequirements()): ?>
                            <style>
                            .captcha span img { position: absolute; margin-top: -8px; }
                            .captcha span a { position: absolute; margin-left: 120px; margin-top: 5px;}
                            </style>
							<div class="captcha" style="margin-top: 15px; margin-bottom: 30px">								
								<span><?php echo CHtml::activeTextField($form, 'verifyCode', array('value' => '')); ?></span>
								<?php echo Html::error($form, 'verifyCode'); ?>
                                <span><?php $this->widget('CCaptcha',array('buttonOptions'=>array('style'=>'display: inline-block;'))); ?></span>								
							</div>	
							<?php endif; ?>
						<?php } ?>
						<div style="text-align: right;margin-top: 20px;clear: both;">
							<a href="javascript:history.go(-1);" class="button">Вернутся назад</a>
							<button type="submit" class="button btn_blue">Отправить запрос</button>
						</div>
					<?php echo CHtml::endForm(); ?>
					</div>
				<?php } else { ?>
					<?php $this->renderPartial('_support_send',array('form'=>$form)); ?>
				<?php } ?>
    		</div>
    	</div>
    </div>
</div>