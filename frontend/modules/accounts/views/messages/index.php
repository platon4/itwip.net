<?php
    $this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_messages_Title');
    $this->metaDescription =  Yii::t('main', '_messages_Description');

    $show=(isset($_GET['_s']))?$_GET['_s']:false;
?>
<div class="section" id="message">
	<ul class="tabs">
	  <li<?php echo ($show=='support' OR $show=='ref')?'':' class="current"'; ?>><span class="inset"><?php echo Yii::t('accountsModule.message', '_system'); ?> <sup style="font-size: 11px"><b id="_new_system_messages"><?php echo $new_system_message; ?></b> / <span id="_all_system_messages"><?php echo $all_system_messages; ?></span></sup></span></li>
	  <li<?php echo ($show=='support')?' class="current"':''; ?>><span class="inset"><?php echo Yii::t('accountsModule.message', '_support'); ?> <sup style="font-size: 11px"><b id="_new_support_messages"><?php echo $new_support_message; ?></b> / <span id="_all_support_messages"><?php echo $all_support_messages; ?></span></sup></span></li>
	</ul>
	<?php $this->renderPartial('_system_list',array('show'=>$show,'messages'=>$messages)); ?>
	<?php $this->renderPartial('_support_list',array('show'=>$show,'messages'=>$supports)); ?>
</div>