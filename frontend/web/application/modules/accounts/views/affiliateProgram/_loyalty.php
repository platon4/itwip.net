<div class="table">
	<div class="td">
		<div style="cursor: default" class="line_title"><?php echo Yii::t('accountsModule.affiliateProgram', '_referral_loyalty_program'); ?></div>
		<table class="table_style_1" style="width:100%;">
		  <tr class="title"><td><?php echo Yii::t('accountsModule.affiliateProgram', '_number_referrals'); ?></td><td> <?php echo Yii::t('accountsModule.affiliateProgram', '_reparation_referrals'); ?></td></tr>
			<?php foreach(LoyaltyHelper::_getData('referral') as $key=>$ref) { ?>
					<?php 
						$amountArr=explode('-',$ref[1]);
						
						if(count($amountArr)>1)
							$amount=CMoney::_c($amountArr[0]).'-'.CMoney::_c($amountArr[1]);
						else
							$amount=CMoney::_c($amountArr[0]);
					?>
					<tr><td><?php echo $amount; ?></td><td<?php echo $affilate->loyalty_referral==$key? ' class="select_affiliate"':''; ?>><?php echo $affilate->loyalty_referral==$key? '<i class="fa fa-hand-o-right"></i>':''; ?> <?php echo $ref[0]; ?>%</td></tr>
			<?php } ?>
		  <tr><td class="add_affiliate" colspan="2"><?php if($affilate->loyalty_referral==11) { echo Yii::t('accountsModule.affiliateProgram','_loyalty_max_step'); } else {  echo Yii::t('accountsModule.affiliateProgram', '_next_step_invite').' '.$left_to_ref_next_step; } ?></td></tr>
		</table>
	</div>
	<div class="center_td"></div>
	<div class="td">
		<div style="cursor: default" class="line_title"><?php echo Yii::t('accountsModule.affiliateProgram', '_financial_loyalty_program'); ?></div>
		<table class="table_style_1" style="width:100%;">
			<tr class="title"><td><?php echo Yii::t('accountsModule.affiliateProgram', '_joined_worth'); ?></td><td><?php echo Yii::t('accountsModule.affiliateProgram', '_reduction_commission'); ?></td></tr>
			<?php foreach(LoyaltyHelper::_getData('finance') as $k=>$f) { ?>
					<tr><td><?php echo ($k==11)?'Партнёрам':CMoney::_c($f[1]); ?></td><td<?php echo $affilate->loyalty_finance==$k? ' class="select_affiliate"':''; ?>><?php echo $affilate->loyalty_finance==$k? '<i class="fa fa-hand-o-right"></i>':''; ?> <?php echo $f[0]; ?>%</td></tr>
			<?php } ?>		
			<tr><td class="add_affiliate" colspan="2"><?php if($affilate->loyalty_referral==11) { echo Yii::t('accountsModule.affiliateProgram','_loyalty_max_step'); } else {  echo Yii::t('accountsModule.affiliateProgram', '_next_stag_fill').' '.CMoney::_c($left_to_f_next_step,true); } ?></td></tr>
		</table>
	  </div>
</div>