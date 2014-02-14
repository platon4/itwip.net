<h3 class="top_title" onclick="Tweets.accordion(this);" style="cursor: pointer;"><?php echo Yii::t('twitterModule.tweets', '_title_settings_time_targeting'); ?></h3>
<div id="block_1_4" style="padding-bottom: 15px; display:none;">
	<div id="time_targeting">
		<table id="quick_selection">
			<tr>
				<td style="text-align: left;"><?php echo Yii::t('twitterModule.tweets', '_сhoose'); ?></td>
				<td class="days"><a href="javascript:;" onclick="Twitter.o.m.t.range('day', 'week', this);" class="selection here"><?php echo Yii::t('twitterModule.tweets', '_week'); ?></a></td>
				<td class="days"><a href="javascript:;" onclick="Twitter.o.m.t.range('day', 'workday', this);" class="here"><?php echo Yii::t('twitterModule.tweets', '_workday'); ?></a></td>
				<td class="days"><a href="javascript:;" onclick="Twitter.o.m.t.range('day', 'weekend', this);" class="here"><?php echo Yii::t('twitterModule.tweets', '_weekend'); ?></a></td>
				<td>|</td>
				<td class="hours"><a href="javascript:;" onclick="Twitter.o.m.t.range('hours', '24', this);" class="selection here"><?php echo Yii::t('twitterModule.tweets', '_24-hour'); ?></a></td>
				<td class="hours"><a href="javascript:;" onclick="Twitter.o.m.t.range('hours', 'morning', this);" class="here"><?php echo Yii::t('twitterModule.tweets', '_ morning'); ?></a></td>
				<td class="hours"><a href="javascript:;" onclick="Twitter.o.m.t.range('hours', 'daytime', this);" class="here"><?php echo Yii::t('twitterModule.tweets', '_daytime'); ?></a></td>
				<td class="hours"><a href="javascript:;" onclick="Twitter.o.m.t.range('hours', 'evenings', this);" class="here"><?php echo Yii::t('twitterModule.tweets', '_evenings'); ?></a></td>
				<td class="hours"><a href="javascript:;" onclick="Twitter.o.m.t.range('hours', 'night', this);" class="here"><?php echo Yii::t('twitterModule.tweets', '_night'); ?></a></td>
			</tr>
		</table>
		<table class="time_targeting">
			<tr class="count">
				<td style="font-size: 10px; color: #7E7E7E">
					<i><?php echo Yii::t('twitterModule.tweets', '_day_time'); ?></i>
				</td>
				<td>0</td>
				<td>1</td>
				<td>2</td>
				<td>3</td>
				<td>4</td>
				<td>5</td>
				<td>6</td>
				<td>7</td>
				<td>8</td>
				<td>9</td>
				<td>10</td>
				<td>11</td>
				<td>12</td>
				<td>13</td>
				<td>14</td>
				<td>15</td>
				<td>16</td>
				<td>17</td>
				<td>18</td>
				<td>19</td>
				<td>20</td>
				<td>21</td>
				<td>22</td>
				<td>23</td>
			</tr>
			<?php
			$daysTarget = array(1 => array('class' => 'day', 'text' => Yii::t('twitterModule.tweets', '_Monday')), 2 => array('class' => 'day', 'text' => Yii::t('twitterModule.tweets', '_Tuesday')), 3 => array('class' => 'day', 'text' => Yii::t('twitterModule.tweets', '_Wednesday')), 4 => array('class' => 'day', 'text' => Yii::t('twitterModule.tweets', '_Thursday')), 5 => array('class' => 'day', 'text' => Yii::t('twitterModule.tweets', '_Friday')), 6 => array('class' => 'day', 'text' => Yii::t('twitterModule.tweets', '_Saturday')), 0 => array('class' => 'day', 'text' => Yii::t('twitterModule.tweets', '_Sunday')));

			foreach($daysTarget as $k => $v) {
				echo '<tr>';
				for($td = 0; $td <= 24; $td++) {
					$javascript = '';
					if($td == 0) {
						$str   = $v['text'];
						$class = ' class="' . $v['class'] . '"';
					}
					else {
						$str        = '<input type="checkbox" name="Order[data][t][' . $k . '][]" value="' . $td . '" style="display: none; opacity:0; position:absolute; left:9999px; visibility: hidden;" checked="checked" />';
						$class      = ' class="select hasInterval"';
						$javascript = 'onclick="Twitter.o.m.t.toggle(this);"';
					}

					echo '<td id="days_' . $k . '_' . $td . '" ' . $class . $javascript . '>' . $str . '</td>';
				}
				echo '</tr>';
			}
			?>
		</table>
	</div>
	<div id="day">
		<table>
			<tr>
				<td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_place_with'); ?> </td>
				<td class="param">
					<span class="block group_input search">
						<input id="targetingDatepicker" class="hasInterval" type="text" name="Order[data][sDate]" style="width: 135px;" placeholder="<?php echo date("d.m.Y"); ?>">
						<button class="button icon" onclick="$('#targetingDatepicker').focus();">
							<i class="fa fa-calendar"></i>
						</button>
					</span>
				</td>
			</tr>
			<tr>
				<td class="info_param"><?php echo Yii::t('twitterModule.tweets', '_interval'); ?>
					<i class="tooltip" title="<?php echo Yii::t('twitterModule.tweets', '_interval_info'); ?>">?</i>
				</td>
				<td class="param input">
					<select name="Order[data][interval]" class="hasInterval">
						<option value="30">30 <?php echo Yii::t('twitterModule.tweets', '_interval_min'); ?></option>
						<option value="40">40 <?php echo Yii::t('twitterModule.tweets', '_interval_min'); ?></option>
						<option value="50">50 <?php echo Yii::t('twitterModule.tweets', '_interval_min'); ?></option>
						<option value="60">1 <?php echo Yii::t('twitterModule.tweets', '_interval_hour'); ?></option>
						<option value="120">2 <?php echo Yii::t('twitterModule.tweets', '_interval_hours'); ?></option>
						<option value="180">3 <?php echo Yii::t('twitterModule.tweets', '_interval_hours'); ?></option>
						<option value="240">4 <?php echo Yii::t('twitterModule.tweets', '_interval_hours'); ?></option>
						<option value="300">5 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option>
						<option value="360">6 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option>
						<option value="420">7 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option>
						<option value="480">8 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option>
						<option value="540">9 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option>
						<option value="600">10 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option>
						<option value="660">11 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option>
						<option value="720">12 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option>
						<option value="1440">24 <?php echo Yii::t('twitterModule.tweets', '_interval_hourss'); ?></option>
					</select>
					<div style="margin-top: 3px; color: #666666; font-size: 9px;">Выбранный интервал +- 10 мин.</div>
				</td>
			</tr>
		</table>
	</div>
</div>
<script>
	$(function () {
		$("#targetingDatepicker").datepicker({minDate: 0, dateFormat: "dd.mm.yy"});

		$('.hasInterval').on('change click', function () {
			Twitter.o.m.t.update();
		});
	});
</script>