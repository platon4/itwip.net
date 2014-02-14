<h3 class="top_title" onclick="Tweets.accordion(this);" style="cursor: pointer;"><?php echo Yii::t('twitterModule.tweets', '_title_settings_time_targeting'); ?></h3>
<div id="block_1_4" style="padding-bottom: 15px; display:none;">
	<div id="time_targeting">
	  <table id="quick_selection">
		  <tr><td style="text-align: left;"><?php echo Yii::t('twitterModule.tweets', '_сhoose'); ?></td><td><a href="" class="selection here"><?php echo Yii::t('twitterModule.tweets', '_week'); ?></a></td><td><a href="" class="here"><?php echo Yii::t('twitterModule.tweets', '_workday'); ?></a></td><td><a href="" class="here"><?php echo Yii::t('twitterModule.tweets', '_weekend'); ?></a></td><td>|</td><td><a href="" class="selection here"><?php echo Yii::t('twitterModule.tweets', '_24-hour'); ?></a></td><td><a href="" class="here"><?php echo Yii::t('twitterModule.tweets', '_ morning'); ?></a></td><td><a href="" class="here"><?php echo Yii::t('twitterModule.tweets', '_daytime'); ?></a></td><td><a href="" class="here"><?php echo Yii::t('twitterModule.tweets', '_evenings'); ?></a></td><td><a href="" class="here"><?php echo Yii::t('twitterModule.tweets', '_night'); ?></a></td></tr>
	  </table>
		<table class="time_targeting">
		  <tr class="count"><td style="font-size: 10px; color: #7E7E7E"><i><?php echo Yii::t('twitterModule.tweets', '_day_time'); ?></i></td><td>0</td><td>1</td><td>2</td><td>3</td><td>4</td><td>5</td><td>6</td><td>7</td><td>8</td><td>9</td><td>10</td><td>11</td><td>12</td><td>13</td><td>14</td><td>15</td><td>16</td><td>17</td><td>18</td><td>19</td><td>20</td><td>21</td><td>22</td><td>23</td></tr>
			<?php
				$daysTarget=array(
					0=>array('class'=>'day','text'=>Yii::t('twitterModule.tweets', '_Monday')),
					1=>array('class'=>'day','text'=>Yii::t('twitterModule.tweets', '_Tuesday')),
					2=>array('class'=>'day','text'=>Yii::t('twitterModule.tweets', '_Wednesday')),
					3=>array('class'=>'day','text'=> Yii::t('twitterModule.tweets', '_Thursday')),
					4=>array('class'=>'day','text'=>Yii::t('twitterModule.tweets', '_Friday')),
					5=>array('class'=>'day','text'=>Yii::t('twitterModule.tweets', '_Saturday')),
					6=>array('class'=>'day','text'=>Yii::t('twitterModule.tweets', '_Sunday')),
				);
				
				for($tr=0; $tr<=6;$tr++)
				{
					echo '<tr>';
						for($td=0; $td<=24;$td++)
						{
							$text='';
							$javascript='';
							if($td==0) {
								$text=$daysTarget[$tr]['text'];
								$class=' class="'.$daysTarget[$tr]['class'].'"';
							}
							else {
								$class=' class="no_select"';
								$javascript='onclick="Tweets.targetToggle(this,\''.$tr.'\',\''.$td.'\');"';		
							}

							echo '<td id="days_'.$tr.'_'.$td.'" '.$class.$javascript.'><input type="hidden" name="days['.$tr.'][]" value="'.$td.'">'.$text.'</td>';
						}
					echo '</tr>';
				}
			?>
		</table>
	</div>
	<div id="day">
		<table>
			<tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_place_with'); ?> </td><td class="param"><span class="block group_input search"><input type="text" style="width: 135px;" placeholder="<?php echo date("d.m.Y"); ?>" id="setQuery"><button id="datepicker" class="button icon"><i class="icon-calendar"></i></button></span></td></tr>
			<tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_interval'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets', '_interval_info'); ?>">?</i></td><td class="param input"><select class="styler"><option><?php echo Yii::t('twitterModule.tweets', '_interval_no'); ?></option><option>10 <?php echo Yii::t('twitterModule.tweets', '_interval_min'); ?></option><option>20 <?php echo Yii::t('twitterModule.tweets', '_interval_min'); ?></option><option>30 <?php echo Yii::t('twitterModule.tweets', '_interval_min'); ?></option><option>40 <?php echo Yii::t('twitterModule.tweets', '_interval_min'); ?></option><option>50 <?php echo Yii::t('twitterModule.tweets', '_interval_min'); ?></option><option>60 <?php echo Yii::t('twitterModule.tweets', '_interval_min'); ?></option><option>1 <?php echo Yii::t('twitterModule.tweets', '_interval_hour'); ?></option><option>2 <?php echo Yii::t('twitterModule.tweets', '_interval_hours'); ?></option><option>3 <?php echo Yii::t('twitterModule.tweets', '_interval_hours'); ?></option><option>4 <?php echo Yii::t('twitterModule.tweets', '_interval_hours'); ?></option><option>5 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option><option>6 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option><option>7 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option><option>8 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option><option>9 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option><option>10 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option></select></td></tr>
			<?php if($_post_to) { ?>
				<tr><td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_post_to_1'); ?> <i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets', '_post_to_1_info'); ?>">?</i></td><td class="param"><input type="text" style="width: 165px;" placeholder="к-во аккаунтов: 1"></td></tr>
			<?php } ?>
		</table>
		<div id="calculate">
			  <h3><?php echo Yii::t('twitterModule.tweets', '_calculate_accommodation'); ?></h3>
				<ul>
				  <li><b><?php echo Yii::t('twitterModule.tweets', '_just_place_the_posts'); ?></b> 359</li>
				  <li><?php echo Yii::t('twitterModule.tweets', '_will_be_placed'); ?> 1 день 6 часов</li>
				  <li><?php echo Yii::t('twitterModule.tweets', '_end_of_posting'); ?> 12.07.13г.</li>
				</ul>
		</div>
	</div>
</div>
<script>
	$(function() {
		$( "#datepicker" ).datepicker("option", "dateFormat", "d.m.Y");
	});
</script>