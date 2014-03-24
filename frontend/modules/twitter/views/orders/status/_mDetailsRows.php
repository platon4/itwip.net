<?php $is_pay = $model->m->getOrder()['status']; ?>
<table>
    <?php foreach($model->m->getRows() as $row) { ?>
        <tr>
            <td class="account">
                <div class="account_img">
                    <img src="<?php echo Html::encode($row['avatar']); ?>" />
                </div>
                <div class="account_NameLogin">
                    <span class="account_Name block"><?php echo Html::encode($row['name']); ?></span>
                    <span class="account_Login block"><a href="https://twitter.com/<?php echo Html::encode($row['screen_name']); ?>" target="_blank">@<?php echo Html::encode($row['screen_name']); ?></a></span>
                </div>
            </td>
            <td class="tweet"><?php echo Html::encode($row['tweet']); ?></td>
            <td class="status">
                <?php
					if(!$is_pay)
					{
						echo '<span class="poorly">Не оплачен</span>';
					}
					else
					{
						switch($row['status'])
						{
							case 0:
								echo '<span class="wait" title="Блогер должен принять этот заказ в ручную"><i class="fa fa-user"></i> проверяется блогером</span>';
								break;
							case 1:
								echo '<span class="wait" title="Ожидает автоматического размещения, длительность ожидания зависит - от очереди твитов в аккаунт."><i class="fa fa-coffee"></i> ожидает размещения</span>';
								break;
							case 2:
								echo '<span class="poorly" title="Блогер не принял заказ"><i class="fa fa-times-circle-o"></i> отклонён блогером</span>';
								break;
							case 3:
								echo '<span class="ok" title="Твит успешно размещён"><i class="fa fa-check"></i> '.$row['placed_date'].'</span>';
								break;
							case 4:
								echo '<span class="poorly" title="'.Html::encode($row['message']).'"><i class="fa fa-times-circle-o"></i> не удалось разместить</span>';
								break;
						}
					}
                ?>
            </td>
            <td class="price"><?php echo Finance::money($row['amount'],$row['payment_type'],true); ?></td>
            <td class="icon">
                <?php
                if($row['status'] == 3)
                {
                    echo '<a target="_blank" title="Перейти к просмотру твита" href="https://twitter.com/'.Html::encode($row['screen_name']).'/statuses/'.$row['params']['tweet_id'].'" class="button icon_small"><i class="fa fa-eye"></i></a>';
                }

                	echo '<a title="Удалить твит" href="javascript:void(0);" onclick="Twitter.o.m.remove(\''.$row['id'].'\',this);" class="button icon_small delete"><i class="fa fa-trash-o"></i></a>';
                ?>
            </td>
        </tr>
    <?php } ?>
</table>
<div id="pagesNavigation" style="float: left; padding: 7px 7px;">
	 <?php $this->renderPartial('application.views.main._pages', array('ajax_query' => 'Twitter.o.g.getPage', 'pages' => $model->m->getPages())); ?>
</div>
<div style="float: right; margin-right: 6px;">
	 <?php echo Yii::t('twitterModule.accounts', '_pageNavHow'); ?>
	<select name="shoOnPage" onchange="Twitter.o.g.setLimit(this.value); return false;">
			<?php foreach($model->m->getLimits() as $option) {
				?>
				<?php
				if($model->m->getLimit() == $option['value']) {
					$htmlOption = array('value' => $option['value'],
						'selected' => 'selected');
				}
				else {
					$htmlOption = array('value' => $option['value']);
				}

				echo Html::tag('option', $htmlOption, $option['title']);
				?>
			<?php } ?>
		</select>
</div>
<div style="clear: both;"></div>