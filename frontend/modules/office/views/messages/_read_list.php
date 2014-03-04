<?php foreach($messages as $message) { ?>
	<div class="message_here<?php echo ($message['ot_id'])?' admin':'';?>">
		<div class="who_date"><?php echo ($message['ot_id'])?'Поддержка':'Пользователь';?>: <?php echo CHtml::encode($message['name']); ?> <span style="float:right"><?php echo date('d.m.Y H:i',strtotime($message['_date'])); ?></span></div>
		<p><?php echo Html::bbCode($message['_text']); ?></p>
	</div>
<?php } ?>