<table>
    <?php foreach($model->m->getRows() as $row) { ?>
		<tr>
			<td class="id"><?php echo $row['id']; ?></td>
			<td class="link">
            <a href="<?php echo Html::encode($row['url']); ?>" target="_blank"><?php echo Html::encode($row['url']); ?></a>
            <a href="<?php echo $row['yandex_url']; ?>">проверить <i class="fa fa-external-link"></i></a>
            </td>
			<td class="date"><?php echo $row['posted_date']; ?></td>
			<td class="date"><?php echo $row['check_date']; ?></td>
			<td class="status">
				<?php
					switch($row['status'])
					{
						case 0:
							echo 'Ожидает размещения';
							break;
						case 1:
							echo 'На индексации';
							break;
						case 2:
							echo 'Проиндексирована';
							break;
						case 3:
							echo 'Не проиндексирована';
							break;
					}
				?>
			</td>
		</tr>
    <?php } ?>
</table>
<div class="table_bottom_inside">
	<div class="page_nav_page">
	    <?php $this->renderPartial('application.views.main._pages', array('ajax_query' => 'Twitter.o.g.getPage', 'pages' => $model->m->getPages())); ?>
	</div>
	<div class="page_nav_how">
	 <?php echo Yii::t('twitterModule.accounts', '_pageNavHow'); ?>
	    <select name="shoOnPage" onchange="Twitter.o.g.setLimit(this.value); return false;">
			<?php foreach($model->m->getLimits() as $option) {
				?>
				<?php
				if($model->m->getLimit() == $option['value']) {
					$htmlOption = array('value' => $option['value'],
						'selected' => 'selected');
				}
				else {
					$htmlOption = array('value' => $option['value']);
				}

				echo Html::tag('option', $htmlOption, $option['title']);
				?>
			<?php } ?>
		</select>
	</div>
</div>

