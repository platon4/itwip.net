<?php
if(count($rows))
{
    ?>  
    <table>	
        <?php
        foreach($rows as $row)
        {
            ?>
            <tr>
                <td class="account">
                    <div class="account_img">
                        <img src="<?php echo Html::encode($row['avatar']); ?>">
                    </div>
                    <div class="account_NameLogin">
                        <span class="account_Name block"><?php echo Html::encode($row['name']); ?></span>
                        <span class="account_Login block"><a target="_blank" href="https://twitter.com/<?php echo Html::encode($row['screen_name']); ?>">@<?php echo Html::encode($row['screen_name']); ?></a></span>
                    </div>
                </td>
                <td class="details"><span><i class="fa fa-twitter"></i></span> <?php echo Html::encode($row['_text']); ?></td>
                <td class="date"><?php echo date("d.m.Y H:i",strtotime($row['_date'])); ?></td>
                <td class="price">
                     <?php echo Finance::money($row['_cost'],$row['pay_type'],true); ?>
                </td>
                <td class="icons"><a class="details" target="_blank" href="https://twitter.com/<?php echo Html::encode($row['screen_name']); ?>/status/<?php echo $row['tw_id']; ?>" title="Посмотреть твит"><i class="fa fa-eye"></i></a></td>
            </tr>     
    <?php } ?>
    </table>
<div style="padding: 7px;">
    <?php $this->renderPartial('application.views.main._pages',array('pages'=>$pages,'ajax_query'=>'Request._getPage')); ?>
</div>
<?php } else
{
    ?>
    <div style="padding: 6px; text-align: center;">Заказы отсутствуют</div>
<?php } ?>
