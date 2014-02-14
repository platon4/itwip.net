<table>
	<?php foreach($accounts as $row) { ?>
		<tr>
		  <td class="account">
			<div class="account_img"><img src="<?php echo Html::encode($row['avatar']); ?>"></div>
			<div class="account_NameLogin">
			  <span class="account_Name block"><?php echo Html::encode($row['name']); ?></span>
			  <span class="account_Login block"><a target="_blank" href="https://twitter.com/<?php echo Html::encode($row['screen_name']); ?>">@<?php echo Html::encode($row['screen_name']); ?></a></span>
			</div>
		  </td>
		  <td class="date"><?php echo date("d.m.Y",$row['date_add']); ?></td>
		  <td class="status"><?php echo $row['_status']==0?'На модерации':'Работает';?> <a href="javascript:;" onclick="Tw.getSettings('<?php echo $row['id']; ?>','status',this);"><i class="fa fa-pencil"></i></a></td>
		  <td class="itr"><?php echo $row['itr']; ?></td>
		  <td class="kf">
			<?php echo Html::dropDownList('_m',$row['_mdr'],array(1=>'0.1',2=>'0.2',3=>'0.3',4=>'0.4',5=>'0.5',6=>'0.6',7=>'0.7',8=>'0.8',9=>'0.9',10=>'1',),array('id'=>'_m_'.$row['id'])); ?>
			<button class="button" onclick="Tw.setM('<?php echo $row['id']; ?>',this);">ок</button>
		  </td>
		  <td class="no_border icons"><a class="button icon" href="javascript:;" onclick="Tw.getSettings('<?php echo $row['id']; ?>','settings',this);"><i class="fa fa-cog"></i></a></td>
		</tr>
	<?php } ?>
</table>
