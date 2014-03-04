<?php if(count($messages)) { ?>
	<?php foreach($messages as $message) { ?>
		<div id="message_<?php echo $message->id; ?>" class="messag<?php if($message->admin_read==0) { echo ' new';} ?>" onclick="Support.getMessage('<?php echo $message->id; ?>',this);">
			<table>
				<tr>
					<td class="status"><i class="fa <?php
																	if($message->_is_remove==0)
																	{
																		switch($message->_status)
																		{
																			case 1:
																					echo 'fa-coffee';
																				break;	
																				
																				case 2:
																					echo 'fa-comment-o';
																				break;
																				
																				case 3:
																					echo 'fa-smile-o';
																				break;
																				
																			default:
																				echo 'fa-clock-o';
																		}
																	}
																	else {
																		echo 'fa-trash-o';
																	}
																?>"></i></td>
					<td class="id"><?php echo $message->id; ?></td>
					<td class="date_ot"><?php echo date('d.m.Y H:i',strtotime($message->_date)); ?></td>
					<td class="date_do"><?php echo strtotime($message->_date)<=strtotime($message->_date_last_answer)?date('d.m.Y H:i',strtotime($message->_date_last_answer)):''; ?></td>
					<td class="text"><?php echo CHtml::encode($message->_subject); ?> </td>
					<td class="answered">
						<?php $reply=unserialize($message->_reply); ?>
						<div>id: <?php echo $message->owner_id; ?></div>
						<div><i class="fa fa-angle-right"></i> <?php echo $reply['name']?$reply['name']:'нет ответа'; ?></div>
					</td>
				</tr>
			</table>
		</div>
	<?php } ?>
<?php } else { ?>
	<div style="text-align: center; color: #BDBDBD; font-size: 18px; margin-top: 80px;">Тикеты отсутствуют</div>
<?php } ?>