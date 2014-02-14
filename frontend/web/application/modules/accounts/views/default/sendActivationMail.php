<?php 
	$this->pageTitle=Yii::app()->name . ' - ' . Html::encode($title);
	$this->layout = '//layouts/info';
 ?>
<div id="info">
    <div id="info_inset">
    	<div id="modal_info">
			<div class="title_modal_info"><?php echo Html::encode($title); ?></div>
			<div class="content_modal_info">
			  <?php echo $message; ?>
			</div>
			<div style="text-align: center;"><button class="button" id="regButton" onclick="window.location.href='<?php echo $link; ?>'"><?php echo Yii::t('accountsModule.accounts','_resend_mail'); ?></button>&nbsp;&nbsp;&nbsp;<a href="/"><?php echo Yii::t('main','_go_main'); ?></a></div>
    	</div>
    </div>
</div>