<?php
	$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('main', '_error');
	
	if(!Yii::app()->user->isGuest)
	{
		$this->layout = '//layouts/info';
	}
?>

<div id="error">
	<div id="modal_info">
		<div class="title_modal_info">Мы столкнулись с ошибкой: <b><?php echo $code; ?></b></div>
		<div class="content_modal_info">
		  <?php echo CHtml::encode($message); ?>
		</div>
		<center><a href="/"><?php echo Yii::t('main', '_back'); ?></a></center>
	</div>
</div>