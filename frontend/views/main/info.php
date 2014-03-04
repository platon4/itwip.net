<?php
	$this->pageTitle=Yii::app()->name . ' - ' . Html::encode($title);
	
	if(!Yii::app()->user->isGuest)
	{
		$this->layout = '//layouts/info';
	}	
?>
<div id="info">
    <div id="info_inset">
    	<div id="modal_info">
    		<div class="title_modal_info"><?php echo Html::encode($title); ?></div>
    		<div class="content_modal_info">
    		<?php if($is_html) { ?>
    			<?php echo $message; ?>
    		<?php } else { ?>
    			<?php echo Html::bbCode($message); ?>
    		<?php } ?>
    		<?php if($link) { ?>
    			<div style="text-align: center; margin-top: 10px;"><a href="<?php echo $link; ?>"><?php echo (isset($link_screen) && $link_screen) ? $link_screen : Yii::t('main', '_back'); ?></a></div>
    		<?php } ?>
    		</div>
    	</div>
    </div>
</div>