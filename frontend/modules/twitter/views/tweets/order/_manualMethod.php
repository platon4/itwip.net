<div id="block_manual">
	<div id="block_manual_setting">
		<?php $this->renderPartial('/tweets/order/_manualFilter', ['filter' => $model]); ?>
		<div id="block_1_6">
			<button class="button" onclick="Twitter.o.filter.save('manual', '<?= $model->_tid ?>');"><i class="fa fa-floppy-o"></i> <?php echo Yii::t('twitterModule.tweets', '_save_filter'); ?></button>
			<button class="button btn_blue" onclick="Twitter.o.m.d.getWithFilter();"><?= Yii::t('twitterModule.tweets', '_to_pick_up_accounts'); ?></button>
		</div>
	</div>
	<form id="_orderCreate" name="_orderCreate">
		<input type="hidden" name="Order[method]" value="manual">
		<input type="hidden" name="Order[data][_tid]" value="<?php echo $model->_tid; ?>">
		<div id="block_accounts"></div>
	</form>
</div>
<script type="text/javascript">
$(function () {
	$("#_orderCreate").submit(function (event) {
		event.preventDefault();
	});
});
</script>