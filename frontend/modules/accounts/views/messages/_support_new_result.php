<div class="message_here">
	<div class="who_date"><?php echo Yii::t('accountsModule.message','_you');?> <span style="float:right"><?php echo date("d.m.Y H:i",$_date); ?></span></div>
	<p><?php echo Html::bbCode($text); ?></p>
</div>