<?php if(count($do_list)) { ?>
	<?php foreach($do_list as $row) { ?>
	<li<?php echo ($row['_is_finished'])?' class="finished"':''; ?>>
	  <?php echo Html::checkBox('tdo_'.$row['id'],$row['_is_finished'],array('onchange'=>'ToDo._change(\''.$row['id'].'\',this);','id'=>'tdo_'.$row['id'])); ?>
	  <label for="1">
		<span class="text"><?php echo Html::encode($row['_text']); ?></span>
		<span class="date">
		 <?php echo date("d.m.Y H:i",$row['_date']); ?>
		</span>
		<span class="delete">
			<a href="javascript:void(0);" onclick="ToDo._remove('<?php echo $row['id']; ?>',this);"><i class="fa fa-trash-o"></i></a>
		</span>
	  </label>
	</li>
	<?php } ?>
<?php } else { ?>
	<li><div style="padding:4px;">Нет заданий</div></li>
<?php } ?>