<?php if(count($referrals)) { ?>
	<?php foreach($referrals as $ref): ?>
		<tr>
			<td class="date"><?php echo date('d.m.Y H:i',strtotime($ref['_date_create'])); ?></td>
			<td class="date"><?php echo ($ref['_date_last_visit']!='0000-00-00 00:00:00')?date('d.m.Y H:i',strtotime($ref['_date_last_visit'])):''; ?></td>
			<td class="name"><?php echo Html::encode($ref['name']); ?></td>
			<td class="balance"><?php echo CMoney::_c($ref['in_balance'],true); ?></td>
			<td class="balance"><?php echo CMoney::_c($ref['out_balance'],true); ?></td>
			<td class="income"><?php echo CMoney::_c($ref['brought_user'],true); ?></td>
		</tr>
	<?php endforeach; ?>
<?php } else { ?>
	<tr><td colspan="6" style="text-align: center;"><?php echo Yii::t('accountsModule.affiliateProgram','no_referrals'); ?></td></tr>
<?php } ?>