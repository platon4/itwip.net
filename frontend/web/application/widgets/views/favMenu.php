<?php $this->getFavoritsMenu(); ?>
<div class="start_menu_star">
	<p class="shadow" style="margin-bottom: 10px;"><?php echo Yii::t('menu', '_main_create_menu'); ?></p>
	<select id="favSTList" name="favList" class="styler" size="1">
		<option value="0"> <?php echo Yii::t('main', '_menu_select'); ?> </option>
		<?php 
			foreach($userMenu as $fmenu) 
			{    
				if(!$fmenu['parrent'])
				{
					if(!in_array($fmenu['id'], $this->favMenu))
					{
						echo '<option value="' . $fmenu['id'] . '" disabled="disabled">' . Yii::t('menu', $fmenu['_key']) . '</option>';
						$this->getParentMenuSelect($fmenu['smenu']);
					}
				}
			} 
		?>
	</select>
	<button id="favLoading" class="button" onclick="_addFav($('#favSTList').val(), $('#fav_' + $('#favSTList').val())); return false;"><?php echo Yii::t('main', '_add_btn'); ?></button>
</div>