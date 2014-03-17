<?php
if(!isset($options))
	$options = [];

if(!isset($remove))
	$remove = false;

if(!isset($selected))
    $selected = 0;
?>
<div style="margin-bottom: 5px;">
	<?php echo Html::GroupDropDownList('subject[]', $selected, $subjects, array('options' => $options, 'classes' => array('h_list', 'list'), 'empty' => array(0 => Yii::t('twitterModule.accounts', '_topicAny')), 'id' => $bid, 'class' => 'styler')); ?>
	<?php if(!$remove) { ?>
		<button type="button" class="button icon" title="<?php echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_themeAccountAdd'); ?>" onclick="Subjects._addSubject($('#_subjects_0'), this); return false;"><i class="fa fa-plus"></i></button>
	<?php } else { ?>
		<button type="button" class="button icon" onclick="Subjects._removeSubject(this); return false;"><i class="fa fa-minus"></i></button>
	<?php } ?>
</div>