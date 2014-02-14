<?php
	$this->pageTitle=Yii::app()->name . ' - ' . Yii::t('twitterModule.tweets', '_tweets_in_processing');

	$this->layout = '//layouts/info';
?>
<div id="info">
    <div id="info_inset">
    	<div id="modal_info">
    		<div class="title_modal_info"><?php echo Html::encode(Yii::t('twitterModule.tweets', '_tweets_in_processing')); ?></div>
    		<div class="content_modal_info">
    			<div>
    				<div id="_loading">
    					<div style="margin: 10px 0; text-align: center;"><img src="/i/_tw_l.gif" alt=""></div>
    					<?php echo Html::encode(Yii::t('twitterModule.tweets', '_tweets_in_processing_help')); ?>
    				</div>
    			</div>
    		</div>
    	</div>
	</div>
</div>
<script type="text/javascript">
/*<![CDATA[*/
jQuery(function($) {
	
	function getProgress()
	{
		_ajax({
			url: "/twitter/ajaxTweets/processing?_k=<?php echo $id; ?>",
			success: function(obj) 
			{ 
				if(obj.code == 200)
				{
					window.location.replace('/twitter/tweets/add?_m=edit&tid=<?php echo $id; ?>');
				}

				if(obj.code != 0) 
				{
					$("#_loading").html(obj.html);
					clearInterval(timer);
				}
			}
		});
	}
	
	timer = setInterval(function() { getProgress(); }, 7000);
	getProgress();
});
/*]]>*/
</script>