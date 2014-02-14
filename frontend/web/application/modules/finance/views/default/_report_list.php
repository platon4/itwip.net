<table style="width: 100%;" class="table_style_1">
<?php if(count($logs)) { ?>
	<?php 
		foreach($logs as $log) {
			
			$date=strtotime($log->_date);
			$time=strtotime($log->_time);
	?>
		<tr><td  width="65px"><?php echo date("d.m.Y",$date); ?></td><td width="40px"><?php echo date("H:i",$time); ?></td><td><?php echo CMoney::_systemPay($log->_system); ?></td><td style="width: 77px;"><?php echo CMoney::_c($log->amount,true); ?></td><td style="width: 77px;"><?php echo CMoney::_c($log->amount-$log->_add_to_balance,true); ?></td><td style="width: 77px;"><?php echo CMoney::_c($log->_add_to_balance,true); ?></td></tr>
	<?php } ?>
<?php } else { ?>
		<tr><td colspan="6" style="text-align:center;"><?php echo Yii::t('financeModule.index', '_no_operation'); ?></td></tr>
<?php } ?>
</table>
