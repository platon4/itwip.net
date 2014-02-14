<div id="block_filter_list">
<?php if(count($filters)) { ?>
	<?php foreach($filters as $filter) { ?>
			<div id="filter_<?php echo $filter['id']; ?>" class="filter">
			  <div class="selects">
				  <a href="javascript:;" onclick="Tweets.filterRun('<?php echo $filter['id']; ?>','<?php echo $filter['_ptype']; ?>','<?php echo $tid; ?>');" class="button icon_small" title="<?php echo Yii::t('twitterModule.tweets', '_select_filter'); ?>"><i class="fa fa-download"></i></a>
			  </div>
			  <div class="text">
				<?php 
					if($filter['is_system'])
					{
						echo '<b>'.CHtml::encode(Yii::t('twitterModule.tweets', $filter['title'])).'</b> - '.CHtml::encode(Yii::t('twitterModule.tweets', $filter['_description']));
					}
					else
						echo '<b>'.CHtml::encode($filter['title']).'</b> - '.CHtml::encode($filter['_description']);
				?>
			  </div>
			  <div class="delete">
				<?php if($filter['is_system']) { ?>
					<i class="fa fa-star-empty" title="<?php echo Yii::t('twitterModule.tweets', '_filter_no_delete'); ?>"></i>
				<?php } else { ?>
					<a href="javascript:;" onclick="Tweets.removeFilter('<?php echo $filter['id']; ?>', this);"><i class="fa fa-trash-o" title="<?php echo Yii::t('twitterModule.tweets', '_filter_delete'); ?>"></i></a>
				<?php } ?>  
			  </div>
			</div>			
	<?php } ?>
<?php } else { ?>	
	<div class="filter">
		<div class="text" style="text-align: center;">
			<?php echo Yii::t('twitterModule.tweets', '_filters_empty'); ?>
		</div>
	</div>
<?php } ?>
</div>