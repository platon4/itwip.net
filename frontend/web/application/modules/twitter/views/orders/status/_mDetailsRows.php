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
            <td class="tweet"><?php echo Html::encode($row['_tweet']); ?></td>
            <td class="status">
                <?php
					if($order['_status']==0)
					{
						echo '<span class="poorly">Не оплачен</span>';
					}
					elseif(!$row['approved'])
					{
						echo '<span class="wait" title="Блогер должен принять этот заказ в ручную"><i class="fa fa-user"></i> проверяется блогером</span>';
					}
					else
					{
						switch($row['status'])
						{
							case 0:
								echo '<span class="wait" title="Ожидает автоматического размещения, длительность ожидания зависит - от очереди твитов в аккаунт."><i class="fa fa-coffee"></i> ожидает размещения</span>';
								break;
							case 1:
								echo '<span class="poorly" title="Блогер не принял заказ"><i class="fa fa-times-circle-o"></i> отклонён блогером</span>';
								break;
							case 2:
								echo '<span class="ok" title="Твит успешно размещён"><i class="fa fa-check"></i> '.date("d.m.Y H:i",strtotime($row['_placed_date'])).'</span>';
								break;
							case 3:
								echo '<span class="poorly" title="Не удалось разметить твит, возможные причины: 1- Твитер отклонил твит 2- Проблемы  с доступом в twitter аккаунт"><i class="fa fa-times-circle-o"></i> не удалось разместить</span>';
								break;
						}
					}
                ?>
            </td>
            <td class="price"><?php echo Finance::money($row['_tweet_price'],$order['_type_payment'],true); ?></td>
            <td class="icon">
                <?php
                if($row['status'] == 2)
                {
                    echo '<a target="_blank" title="Перейти к просмотру твита" href="https://twitter.com/'.Html::encode($row['screen_name']).'/statuses/'.$row['str_id'].'" class="button icon_small"><i class="fa fa-eye"></i></a>';
                } else
                    echo '<a title="Удалить заказ и вернуть не потраченные средства" href="javascript:void(0);" onclick="Order.removeTweet(\''.$row['id'].'\',this);" class="button icon_small delete"><i class="fa fa-trash-o"></i></a>';
                ?>               
            </td>
        </tr>
    <?php } ?>
</table>
<div style="padding: 8px;">
    <?php $this->renderPartial('application.views.main._pages',array('ajax_query'=>'Order.getPage','pages'=>$model->m->getPages())); ?>
</div>