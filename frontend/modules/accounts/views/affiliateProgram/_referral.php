<div class="no_border_bottom" id="info_page">
	<div class="icon"><i class="fa fa-info"></i></div>
	<div class="text"><?php echo Yii::t('accountsModule.affiliateProgram', '_info_page'); ?></div>
</div>
<div class="block_1">
	<?php echo Yii::t('accountsModule.affiliateProgram', '_personal_code'); ?>
	<input type="text" id="_ref_link" name="ref_link" style="width: 250px;" value="<?php echo Yii::app()->homeUrl.'?_r='.$affilate->_code; ?>" onclick="$(this).select(); return false;"/>
</div>
<div class="line_title no_border_bottom" style="cursor: default">
	<?php echo Yii::t('accountsModule.affiliateProgram', '_you_are_invited'); ?> <span id="_accounts_count"><?php echo $count; ?></span>, <?php echo Yii::t('accountsModule.affiliateProgram', '_reparation'); ?> <a href="javascript:;" onclick="_switchTab('loyalty_Program');"><?php echo LoyaltyHelper::_referral($affilate->loyalty_referral); ?>%</a>
	<span class="group_input search float_right" style="margin: -5px 4px"><input type="text" onkeyup="Affiliate._getFromQuery(this.value);" placeholder="<?php echo Yii::t('accountsModule.affiliateProgram', '_search_name'); ?>" id="setQuery"></span>
</div>
<div class="table_head">
	<div class="table_head_inside">
	<table>
		<tr>
			<td class="date"><a href="javascript:;" onclick="Affiliate._setOrder('date',this);"><?php echo Yii::t('accountsModule.affiliateProgram', '_date'); ?> <i class="fa fa-caret-down"></i></a></td>
			<td class="date"><a href="javascript:;" onclick="Affiliate._setOrder('last',this);"><?php echo Yii::t('accountsModule.affiliateProgram', '_last_visit'); ?> <i class="fa fa-caret-down"></i></a></td>
			<td class="name"><?php echo Yii::t('accountsModule.affiliateProgram', '_name'); ?></td>
			<td class="balance"><?php echo Yii::t('accountsModule.affiliateProgram', '_up_balance'); ?></td>
			<td class="balance"><?php echo Yii::t('accountsModule.affiliateProgram', '_down_balance'); ?></td>
			<td class="income"><a href="javascript:;" onclick="Affiliate._setOrder('income',this);"><?php echo Yii::t('accountsModule.affiliateProgram', '_brought_you'); ?> <i class="fa fa-caret-down"></i></a></td>
		</tr>
	</table>
	</div>
</div>
<div class="acconts_list">
	<table id="_referraList">
		<?php $this->renderPartial('_list',array('referrals'=>$referrals)); ?>
	</table>
</div>
<div class="table_bottom">
	<div class="table_bottom_inside">
		<div class="page_nav_page">
			<div id="pagesListpagesList">
				<?php $this->renderPartial("_pages", array('pages' => $pages)); ?>
			</div>
		</div>
	</div>
</div>