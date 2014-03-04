<?php if(count($rows))
{ ?>
    <table>	
        <?php foreach($rows as $row)
        {
            ?>
            <tr id="order_<?php echo $row['id']; ?>">
                <td class="account">
                    <div class="account_img">
                        <img src="<?php echo Html::encode($row['avatar']); ?>">
                    </div>
                    <div class="account_NameLogin">
                        <span class="account_Name block"><?php echo Html::encode($row['name']); ?></span>
                        <span class="account_Login block"><a target="_blank" href="https://twitter.com/<?php echo Html::encode($row['screen_name']); ?>">@<?php echo Html::encode($row['screen_name']); ?></a></span>
                    </div>
                </td>
                <td class="details"><span><i class="fa fa-twitter"></i></span> <?php echo Html::tweet($row['_tweet']); ?></td>
                <td class="date"><?php echo date("Y",strtotime($row['_date']))>=2013?date("d.m.Y H:i",strtotime($row['_date'])):'неопределено'; ?></td>
                <td class="price"<?php echo Finance::money($row['_tweet_price'],$row['money_type'],true); ?></td>
                <td class="icons"><a class="yes" href="javascript:void(0);" title="Подтвердить заказ" onclick="Request.approve('<?php echo $row['id']; ?>',this);"><i class="fa fa-check"></i></a> <a class="no" href="javascript:void(0);" title="Отклонить заказ" onclick="Request.refuse('<?php echo $row['id']; ?>',this);"><i class="fa fa-times"></i></a></td>
            </tr> 
    <?php } ?>
    </table>
<?php } else { ?>
    <div style="padding: 5px; text-align:center;">Нет заявок на выполнение</div>
<?php } ?>