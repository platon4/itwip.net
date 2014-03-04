<div class="block_1"><span id="wCountMessages"><?php echo $count; ?></span> последних сообщений <?php if($count) { ?><div class="wMremove block_1_check" title="Инвертировать выделение"><?php echo Html::checkBox('_wall','',array('id'=>'_wall','onchange'=>'_all_ckbox(\'_messagesWidget\',this);')); ?></div><?php } ?></div>
<div class="block_2">
<div class="block_2_list">
	<form id="_messagesWidget">
		<?php $this->renderPartial('_messages_list',array('count'=>$count,'messages'=>$messages)); ?>
	</form>	
</div>
</div>
<div class="block_3">
<button class="button btn_blue" onclick="window.location.href='/accounts/messages';">Полная версия сообщений</button>
<span class="right">
<?php if($count) { ?>
	<button class="wMremove button icon" title="Удалить выбранные" onclick="wMessages._removeMessages(this);"><i class="fa fa-trash-o"></i></button>
<?php } ?>
</span>
</div>