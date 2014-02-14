<tr class="title"><td>Промо-код</td><td>id</td><td>Сумма</td><td>Лимит</td><td class="no_border">Исп.</td></tr>
<?php if(count($adavancePromoCodes)) { ?>
	<?php foreach($adavancePromoCodes as $apromo) { ?>
		<tr><td><?php echo Html::encode($apromo['_code']); ?></td><td><?php echo Html::encode($apromo['_tie']); ?></td><td style="text-align: center;"><?php echo $apromo['_amount']; ?></td><td style="text-align: center;"><?php echo ($apromo['_count']==0)?'&#8734;':$apromo['_count']; ?></td><td style="text-align: center;"><?php echo $apromo['_use_count']; ?></td></tr>
	<?php } ?>
<?php } else { ?>
	<tr><td colspan="5" style="text-align: center;">Промо коды отсутствуют</td></tr>
<?php } ?>		