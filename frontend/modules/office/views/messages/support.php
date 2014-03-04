<?php
	$this->pageTitle=Yii::app()->name . ' - ' . 'Поддержка';
?>
<script>
	function _resize()
	{
		$(function() {
			$( ".message_list" ).resizable({
				alsoResize: ".message_list_fix_height",
				maxHeight: 700,
				minHeight: 298,
				handles: "s",
			});
		});
		$(function() {
			$( ".message_read" ).resizable({
				alsoResize: ".message_read_inset",
				maxHeight: 800,
				minHeight: 370,
				handles: "s",
			});
		});	
	}
	_resize();
</script>
<div id="messages_support" class="section">
	<ul class="tabs">
			<li data-type="_all" class="tclick current"><span class="inset">Общие <sup style="font-size: 11px"><b id="_new_system_messages"><?php echo $count[0]['unread']; ?></b> / <span id="_all_system_messages"><?php echo $count[0]['all']; ?></span></sup></span></li>
		<?php if(Yii::app()->user->checkAccess('admin')) { ?>
			<li data-type="_bugs" class="tclick"><span class="inset">Ошибки, баги <sup style="font-size: 11px"><b id="_new_system_messages"><?php echo $count[1]['unread']; ?></b> / <span id="_all_system_messages"><?php echo $count[1]['all']; ?></span></sup></span></li>
			<li data-type="_finance" class="tclick"><span class="inset">Финансы <sup style="font-size: 11px"><b id="_new_system_messages"><?php echo $count[2]['unread']; ?></b> / <span id="_all_system_messages"><?php echo $count[2]['all']; ?></span></sup></span></li>
			<li data-type="_offers" class="tclick"><span class="inset">Предложения <sup style="font-size: 11px"><b id="_new_system_messages"><?php echo $count[3]['unread']; ?></b> / <span id="_all_system_messages"><?php echo $count[3]['all']; ?></span></sup></span></li>
		<?php } ?>
	</ul>
	<div class="box actived">
		<div class="box_inset">
			<div id="mContent"><?php echo $default; ?></div>
		</div>
	</div>
</div>
<script>
	var _loading='<img alt="Loading..." src="/i/loading_11.gif">',c='_all';
	
	(function($) {
		$('.tclick').on('click',function() {
			
			if($(this).attr('data-type') == c) return false;
			
			_ajax({
				type: "GET",
				url: "/office/messages/support?act="+$(this).attr('data-type'),
				dataType:"json",
				success: function (obj, textStatus) 
				{
					$('#mContent').html(obj.html);
					Support.s._g='_all';
					Support.s._s='urgent';
					_resize();
				},
				beforeSend:function()
				{
					$('#mContent').html('<div style="text-align: center; padding: 20px 0;">'+_loading+'</div>');
				}
			});
			
			c=$(this).attr('data-type');
		});
	})(jQuery)	
</script>