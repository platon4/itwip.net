<?php if($model->m->getCount()) { ?>
    <table>
        <?php foreach($model->m->getRows() as $row) { ?>
			<?php $money_prefix = $row['payment_type'] == 1 ? ' руб.Б.' : ' руб.'; ?>
            <tr id="order_<?php echo $row['id']; ?>" data-amount="<?php echo $row['return_amount']; ?>" data-atype="<?php echo $row['payment_type']; ?>">
                <td class="id"><?php echo $row['id']; ?></td>		  
                <td class="view_orders">
					<?php
						if($row['type_order'] == 'indexes')
						{
							echo str_replace('{time}', isset($row['params']['time']) ? $model->m->getTime($row['params']['time']) : 'Не определено', $model->getOrderType($row['type_order']));
						} else
							echo $model->getOrderType($row['type_order']);
					?>
				</td>
                <td class="date"><?php echo date("d.m.Y H:i",strtotime($row['create_date'])); ?></td>
                <td class="status">
                    <?php
                    switch($row['status'])
                    {
                        case 0:
                            echo '<span class="no_money">Не оплачен</span>';
                            break;

                        case 1:
                            echo '<span class="ok">Выполняется</span>';
                            break;

						case 2:
                        case 3:
                            echo $row['type_order'] == 'indexes' && $row['status'] != 3 ? '<span class="ok">Ожидает проверки</span>' : '<span class="completed">Выполнено</span>';
                            break;

                        default:
                            echo '<span class="no_money">Ошибка заказа</span>';
                    }
                    ?>
                </td>                                              
                <td class="rate_balance">
                    <?php
                    switch($row['status'])
                    {
                        case 0:
							$money = $row['payment_type'] == 1 ? '<span style="float: right" title="Бонусный счёт">' . $row['return_amount'] . $money_prefix . '</span>' : '<span style="float: right">' . $row['return_amount'] . $money_prefix . '</span>';
                            echo '<span class="no_money"><span class="text_balance">К оплате:</span>' . $money . '</span>';
                            break;

                        case 2:
                            echo '<span class="text_balance">Потрачено:</span> <span style="float: right">'.round($row['amount_use'],2).$money_prefix.'</span>';
                            break;

                        default:
                            echo '<span class="text_balance">Потрачено:</span> ' . Finance::money($row['amount_use'], $row['payment_type'], true, ['float: right']) . '<br><span class="text_balance">Остаток:</span> ' . Finance::money(($row['return_amount'] - $row['amount_use']), $row['payment_type'], true, ['float: right']);
                    }
                    ?>
                </td>
                <td class="progress_order">                 
                    <div data-percent="<?php echo round(($row['completed_taks'] / $row['all_taks']) * 100,2); ?>%" class="progress progress-success progress-striped">
                        <div style="width: <?php echo round(($row['completed_taks'] / $row['all_taks']) * 100,2); ?>%;" class="bar"></div>
                    </div>
                    выполнено: <?php echo $row['completed_taks']; ?> из <?php echo $row['all_taks']; ?>
                </td>                         
                <td class="no_border icons">
                    <?php if($row['status'] == 0) { ?>
                        <a class="button icon_small" href="javascript:void(0);" title="Оплатить заказ" onclick="Twitter.o.confirmPay('<?php echo $row['id']; ?>', this);"><i class="fa fa-rub"></i></a>
                    <?php } ?>
                    <a class="button icon_small" href="/twitter/orders/status?h=<?php echo $row['order_hash']; ?>&t=<?php echo $row['type_order']; ?>" title="Посмотреть детали"><i class="fa fa-eye"></i></a>
                    <a class="button icon_small delete" href="javascript:;" title="Удалить заказ и вернуть не потраченные средства" onclick="Twitter.o.remove('<?php echo $row['id']; ?>');"><i class="fa fa-trash-o"></i></a>
                </td>
            </tr>
    <?php } ?>
    </table>
		<div id="pagesNavigation" style="float: left; padding: 7px 7px;">
			 <?php $this->renderPartial('application.views.main._pages', array('ajax_query' => 'Twitter.o.getPage', 'pages' => $model->m->getPages())); ?>
		</div>
		<div style="float: right; margin-right: 6px;">
			 <?php echo Yii::t('twitterModule.accounts', '_pageNavHow'); ?>
				<select name="shoOnPage" onchange="Twitter.o.setLimit(this.value); return false;">
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
<?php } else { ?>
	<div style="text-align: center; padding: 10px;">У вас не одного заказа, хотите <a href="/twitter/tweets/collection">создать</a> ?</div>
<?php } ?>