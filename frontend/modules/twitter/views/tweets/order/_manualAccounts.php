<div class="line_title no_border_bottom">
	<?php echo Yii::t('twitterModule.tweets', '_matched_accounts'); ?>
	<span id="_accounts_count"><?php echo $model->getAccountsCount(); ?></span>
</div>
<div class="table_head">
	<div class="table_head_inside">
		<table>
			<tbody>
			<tr>
				<td class="account"><?php echo Yii::t('main', '_account'); ?></td>
				<td class="followers"><a href="javascript:;" onclick="Twitter.o.m.d.setOrder('followers', this);"> Читателей
						<i class="fa fa-caret-down"></i>
					</a>
				</td>
				<td class="level">
					<a href="javascript:void(0);" onclick="Twitter.o.m.d.setOrder('itr', this);"><?php echo Yii::t('main', '_itr'); ?>
						<i class="fa fa-caret-down"></i>
					</a>
				</td>
				<td class="rang">
					<a href="javascript:void(0);" onclick="Twitter.o.m.d.setOrder('yrk', this);"><?php echo Yii::t('main', '_authority'); ?>
						<i class="fa fa-caret-down"></i>
					</a>
				</td>
				<td class="pr">
					<a href="javascript:void(0);" onclick="Twitter.o.m.d.setOrder('gpr', this);"><?php echo Yii::t('main', '_pr'); ?>
						<i class="fa fa-caret-down"></i>
					</a>
				</td>
				<td class="tape"><a href="javascript:void(0);" onclick="Twitter.o.m.d.setOrder('tape', this);" href="javascript:;"><i class="fa fa-comments-o"></i>
						<i class="fa fa-caret-down"></i>
					</a>
				</td>
				<td class="index"><a href="javascript:void(0);" onclick="Twitter.o.m.d.setOrder('yin', this);"><?php echo Yii::t('main', '_indexation'); ?>
						<i class="fa fa-caret-down"></i>
					</a>
				</td>
				<td class="select">
					<span title="<?php echo Yii::t('twitterModule.tweets', '_confirmation_applications_info'); ?>"><?php echo Yii::t('twitterModule.tweets', '_confirmation'); ?></span>
				</td>
				<td class="price">
					<a href="javascript:void(0);" onclick="Twitter.o.m.d.setOrder('price', this);"><?php echo Yii::t('main', '_price_post'); ?>
						<i class="fa fa-caret-down"></i>
					</a>
				</td>
				<td class="text">
					<span title="<?php echo Yii::t('twitterModule.tweets', '_post_info'); ?>"><?php echo Yii::t('twitterModule.tweets', '_post'); ?></span>
				</td>
				<td class="no_border check" title="<?php echo Yii::t('main', '_invert_selection'); ?>"><?php echo Html::checkBox('_all_select', '', array('id' => '_all_select', 'onchange' => 'Twitter.o.m.d.toggleAll(this)')); ?></td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
<div id="_pages" class="acconts_list">
	<div id="_tweetsFormPlace">
		<?php $this->renderPartial('/tweets/order/_manualAccountsRows', ['model' => $model]); ?>
	</div>
</div>
<?php $this->renderPartial('/tweets/order/_timeTarget', ['model' => $model]); ?>
<h3 class="top_title" onclick="Tweets.accordion(this);" style="cursor: pointer;"><?php echo Yii::t('twitterModule.tweets', '_additional_network_settings'); ?></h3>
<div id="more_options" style="padding-bottom: 15px;">
	<div class="options">
		<?php echo Html::checkBox('Order[data][_ping]', '', array('onchange' => 'Twitter.o.m.update();', 'id' => '_ping')); ?> <?php echo Yii::t('twitterModule.tweets', '_network_settings_2'); ?>
	</div>
</div>
<div class="end_posting">
	<span style="float: left; padding-top: 7px; font-weight: bold">Разместится подготовленных твитов: <span id="_all_tweets" class="allTweetsPlacement">0</span> из <span id="_tweetsCount">0</span>, выбрано аккаунтов: <span id="_all_accounts">0</span>, на сумму: <span id="_all_amount">0</span> руб.</span>
	<button id="embedButton" class="button btn_blue" disabled="disabled" onclick="Twitter.o.m.confirm(this); return false;"><?php echo Yii::t('twitterModule.tweets', '_place_posts'); ?> <i class="icon-double-angle-right"></i></button>
</div>