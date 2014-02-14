<?php if(count($list))
{ ?>
    <table>
    <?php foreach($list as $row): ?>
            <tr>
                <td class="account">
                    <div class="account_img"><img src="<?php echo ($row['avatar'])?Html::encode($row['avatar']):'/i/_default.png'; ?>"></div>
                    <div class="account_NameLogin">
                        <span class="account_Name block"><?php echo Html::encode($row['name']); ?></span>
                        <span class="account_Login block"><a href="https://twitter.com/<?php echo Html::encode($row['screen_name']); ?>" target="_blank">@<?php echo Html::encode($row['screen_name']); ?></a></span>
                    </div>
                </td>
                <td class="status"><?php echo Html::twStatus($row['_status']); ?></td>
                <td class="level"><?php echo $row['itr']; ?></td>
                <td class="index"><img src="/i/elements/yandex<?php echo ($row['in_yandex'])?'':'_no'; ?>.png" alt=""/>
                </td>                  
                <td class="application"><?php echo $row['orders']?'<a href="/twitter/tweets/request?id='.$row['id'].'">'.$row['orders'].'</a>':0; ?></td>
                <td class="posted"><?php if($row['fulfilled']>0) { ?><a href="/twitter/tweets/fulfilled?tid=<?php echo $row['id']; ?>"><?php echo $row['fulfilled']; ?></a><?php } else { ?>0<?php } ?></td>
                <td class="today"><?php echo Finance::money(round($row['amount_today'],2),0,true); ?></td>
                <td class="last"><?php echo Finance::money(round($row['amount_yeasterday'],2),0,true); ?></td>
                <td class="no_border only"><?php echo Finance::money(round($row['amount_all'],2),0,true); ?></td>
                <td class="no_border icons"><a href="/twitter/accounts/settings?tid=<?php echo $row['id']; ?>" class="button icon_small" target="_blank"><i class="fa fa-cog"></i></a></td>
            </tr>
    <?php endforeach; ?>
    </table>
<?php } else
{ ?>
    <div class="_noAccount">У вас нету добавленных аккаунтов</div>
<?php } ?>