<?php

$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_indexGuest_Title');
$this->metaKeywords = Yii::t('main', '_indexGuest_Keywords');
$this->metaDescription =  Yii::t('main', '_indexGuest_Description');

?>
<div id="content">
	<?php $this->renderPartial('_bodyForm'); ?>
	<div id="info_index">
	  <div class="center">
		<div id="info_index_1">
			<div id="info_index_1_1">
				<p><?php echo Yii::t('index', '_t_i_slogan'); ?></p>
			</div>
			<div id="info_index_1_2"></div>
		</div>
	  </div>
	  <div id="info_index_2">
		 <div class="center">
			<div id="info_index_2_1">
			  <div id="info_index_2_1_1"><h4><?php echo Yii::t('index', '_t_i_user'); ?></h4></div>
			  <div id="info_index_2_1_3"></div>
			  <div id="info_index_2_1_2"><h4><?php echo Yii::t('index', '_t_i_advertiser'); ?></h4></div>
			</div>
		 </div>
	  </div>
	  <div class="center">
		<div id="info_index_3">
		  <div id="info_index_3_1">
		  <div class="info_index_3_n" style="position: relative; top: -45px;  margin-bottom: -45px; height: 222px;">
			<div class="info_index_3_n_title">
			  <div class="info_index_3_n_icon"><i class="fa fa-twitter"></i></div>
			</div>
			<h5><?php echo Yii::t('index', '_t_i_user_h_1'); ?></h5>
			<p><?php echo Yii::t('index', '_t_i_user_1'); ?></p>
		  </div>
		  <div class="info_index_3_n">
			<div class="info_index_3_n_title">
			  <div class="info_index_3_n_icon"><i class="fa fa-thumbs-up"></i></div>
			</div>
			<h5><?php echo Yii::t('index', '_t_i_user_h_2'); ?></h5>
			<p><?php echo Yii::t('index', '_t_i_user_2'); ?></p>
		  </div>

		  <div class="info_index_3_n">
			<div class="info_index_3_n_title">
			  <div class="info_index_3_n_icon"><i class="fa fa-money"></i></div>
			</div>
			<h5><?php echo Yii::t('index', '_t_i_user_h_3'); ?></h5>
			<p><?php echo Yii::t('index', '_t_i_user_3'); ?></p>
		  </div>
		  </div>
		  <div id="info_index_3_3"></div>
		  <div id="info_index_3_2">
		  <div class="info_index_3_n" style="position: relative; top: -45px;  margin-bottom: -45px; height: 222px;">
			<div class="info_index_3_n_title">
			  <div class="info_index_3_n_icon"><i class="fa fa-search"></i></div>
			</div>
			<h5><?php echo Yii::t('index', '_t_i_advertiser_h_1'); ?></h5>
			<p><?php echo Yii::t('index', '_t_i_advertiser_1'); ?></p>
		  </div>
		 <div class="info_index_3_n" style="height: 154px;">
			<div class="info_index_3_n_title">
			  <div class="info_index_3_n_icon"><i class="fa fa-bullhorn"></i></div>
			</div>
			<h5><?php echo Yii::t('index', '_t_i_advertiser_h_2'); ?></h5>
			<p><?php echo Yii::t('index', '_t_i_advertiser_2'); ?></p>
		  </div>
		  <div class="info_index_3_n">
			<div class="info_index_3_n_title">
			  <div class="info_index_3_n_icon"><i class="fa fa-wrench"></i></div>
			</div>
			<h5><?php echo Yii::t('index', '_t_i_advertiser_h_3'); ?></h5>
			<p><?php echo Yii::t('index', '_t_i_advertiser_3'); ?></p>
		  </div>
		  </div>
		</div>
	  </div>
	</div>
 </div>