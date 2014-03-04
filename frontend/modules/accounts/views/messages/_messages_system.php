<?php foreach($messages as $message) { ?>
	<?php $time=strtotime($message->_date); ?>
      <div id="message_<?php echo $message->id; ?>"<?php echo ($message->_is_read==0)?' class="messag new"':''; ?> class="messag">
        <table>
          <tr>
			<td class="input">
                 <?php echo Html::CheckBox('message[]',false,array('value'=>$message->id)); ?>
            </td>
			<td class="status" onclick="Message._getMessage('<?php echo $message->id; ?>');"><i class="<?php echo ($message->_is_read==0)?'fa fa-envelope':'fa fa-envelope-o'; ?>"></i></td>
			<td class="date" onclick="Message._getMessage('<?php echo $message->id; ?>');"><?php echo date("d.m.Y H:i",$time); ?></td>
			<td class="text" onclick="Message._getMessage('<?php echo $message->id; ?>');">
		    	<span class="title"><?php echo Html::encode($message->_title); ?> &ndash;</span>
			    <span class="more"><?php echo Html::encode(Html::_substr($message->_text,90)); ?></span>
			</td>
			<td class="delete">
                 <a href="javascript:;" onclick="Message._remove('<?php echo $message->id; ?>',this); return false;"><i class="fa fa-trash-o" title="<?php echo Yii::t('main', '_remove'); ?>"></i></a>
            </td>
          </tr>
        </table>
      </div>
<?php } ?>
