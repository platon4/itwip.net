<?php foreach($messages as $message) { ?>
	<?php $time=strtotime($message->_date); ?>
        <div onclick="Support._getMessage('<?php echo $message->id; ?>');" id="support_<?php echo $message->id; ?>"<?php echo ($message->user_read==0)?' class="messag new"':''; ?> class="messag">
          <table>
            <tr>
    			<td class="status"><i class="fa <?php 
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
																?>"></i></td>
    			<td class="id"><?php echo $message->id; ?></td>
    			<td class="date_ot"><?php echo date("d.m.Y H:i",$time); ?></td>
    			<td class="date_do"><?php echo strtotime($message->_date_last_answer)>=$time?date("d.m.Y H:i",strtotime($message->_date_last_answer)):''; ?></td>
    			<td class="text"><?php echo Html::encode($message->_subject); ?></td>
            </tr>
          </table>
        </div>
<?php } ?>

