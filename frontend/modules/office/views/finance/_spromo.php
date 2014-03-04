<tbody>
	<tr class="title"><td>Промо-код</td><td>Сумма</td><td class="no_border">Кем использован ID</td></tr>
<?php if(count($promoCodes)) { ?>
	<?php foreach($promoCodes as $spromo) { ?>
		<tr><td><?php echo Html::encode($spromo['_code']); ?></td><td style="text-align: center;"><?php echo $spromo['_amount']; ?></td><td style="width: 130px; text-align: center;"><?php echo (isset($spromo['_owner_use']))?$spromo['_owner_use']:'Не использован'; ?></td></tr>
	<?php } ?>
<?php } else { ?>
	<tr><td colspan="3" style="text-align: center;">Промо коды отсутствуют</td></tr>
<?php } ?>
</tbody>