<?php if($count) { ?>
	<?php foreach($messages as $message) { ?>
		<div class="block_2_list_views">
			<div class="block_2_list_views_date shadow"><span><?php echo date('d.m.Y',strtotime($message['_date'])); ?>г.</span></div>
			<?php 
				if($message['type']=='support')
					 $link='/accounts/messages?_s=support';
				else
					$link='/accounts/messages';
			?>
			<div class="block_2_list_views_text shadow"><a href="<?php echo $link; ?>"><?php echo Html::encode($message['_title']); ?></a></div>
			<div class="block_2_list_views_check"><?php echo Html::checkBox('_message['.$message['type'].'][]','',array('value'=>$message['id'])); ?></div>
		</div>
	<?php } ?>	
<?php } else { ?>	
	<div class="block_2_list_views">
		<div class="block_2_list_views_text shadow" style="text-align: center; display: block; padding-top: 5px;">У вас нет сообщений</div>
	</div>				
<?php } ?>