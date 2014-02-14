<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_tw_status_orders_details_Title');
$this->metaDescription = Yii::t('main', '_tw_status_orders_details_Description');
$this->breadcrumbs[] = array(
	0 => array(Yii::t('breadcrumbs', '_twitter'), '/twitter'),
	1 => array(Yii::t('breadcrumbs', '_tw_advertiser'), ''),
	2 => array(Yii::t('breadcrumbs', '_tw_status_orders'), '/twitter/tweets/status'),
	3 => array(Yii::t('breadcrumbs', '_tw_status_orders_details'), '')
);
?>
<script>
	Twitter.o.f.d.hash = '<?php echo Html::encode($model->m->h); ?>';
</script>
<div id="status_orders" class="block">
	<div class="block_title"><div class="block_title_inset"><h5>Детали заказа быстрой индексации (ID <?php echo $model->m->getOrder()['id']; ?>)</h5></div></div>
	<div class="block_content">
		<div class="table_head">
			<div class="table_head_inside">
				<table>
					<tr>
						<td class="id">ID</td>
						<td class="">Ссылка</td>
						<td class="date"><span title="Время размещения ссылки для индексации в твиттер"><i class="fa fa-clock-o"></i> Размещения</span></td>
						<td class="date"><span title="Время проверки ссылки на индексацию"><i class="fa fa-clock-o"></i> Проверки</span></td>
						<td class="">Статус</td>
						<td class="">Проверить</td>
					</tr>
				</table>
			</div>
		</div>
		<div class="acconts_list" id="_orderList">
			<?php $this->renderPartial('status/_fDetailsRows', ['model' => $model]); ?>
		</div>
	</div>
</div>