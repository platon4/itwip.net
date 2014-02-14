<div id="menu_left_l">
	<ul id="mainMenu">
		<?php $this->getMenu(); ?>
	</ul>
</div>
<div id="menu_left_r">
	<div id="menu_left_r_w">
		<?php 
			$i = 0;
			
			foreach($userMenu as $list) 
			{
				$i ++;
				
				if($list['nactive'] == "main") 
				{
		?>
				<div id="con_m_1" class="list <?php echo ($list['nactive'] == $this->activeBlock) ? "active" : "none"; ?>">
					<h5 class="menu_title"><?php echo Yii::t('menu', '_main'); ?></h5>
					<div id="favMenu">
						<?php $this->render('favMenu', array('userMenu' => $userMenu)); ?>
					</div>
				</div>				
				<?php } else { ?>
				<div id="con_m_<?php echo $i; ?>" class="list <?php echo ($list['nactive'] == $this->activeBlock) ? "active" : "none"; ?>">
					<?php if($list['nactive'] == "regulations") { ?>
						<?php $this->render('application.views.menu._regulations'); ?>
					<?php } else { ?>
						<h5 class="menu_title"><?php echo Yii::t('menu', $list['_key']); ?></h5>
						<?php $this->getParentMenu($list['smenu']); ?>
					<?php } ?>	
				</div>				
				<?php } ?>		
		<?php } ?>
	</div>
</div>
<script type="text/javascript">
	$(function() {
		$("#mainMenu li").on("mouseenter", function(){
		
			$("#mainMenu").find("li.active").removeClass("active").addClass("none");
			$("#menu_left_r_w").find("div.active").removeClass("active").addClass("none");
			
			$(this).addClass("active");
			$("#" + $(this).attr("data-active")).addClass("active").removeClass("none");
		});
		
 		$("#menu_left_r").on("mouseleave",function()
		{	
			var _el = $("#mainMenu").find(".no_remove");
			
			$("#mainMenu").find("li.active").removeClass("active").addClass("none");
			$("#menu_left_r_w").find("div.active").removeClass("active").addClass("none");
			
			_el.addClass("active").removeClass("none");
			$("#" + _el.attr("data-active")).addClass("active").removeClass("none");
		});		
	});
</script>