<?php if($accounts)
{
    ?>
    <?php foreach($accounts as $row)
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
            <td class="status"><?php echo Html::twStatus($row['_status']); ?></td>
            <td class="details">
                Дней: <b><?php echo Html::_dateTransform($row['created_at'],'unix','days'); ?></b>, Авторитет: <b><?php echo $row['yandex_rank']; ?></b>, PR : <b><?php echo $row['google_pr']; ?></b> <br> Читает: <b><?php echo $row['following']; ?></b>, Читают: <b><?php echo $row['followers']; ?></b>, БР: <b><?php echo ($row['in_yandex'])?Yii::t('main','_yes'):Yii::t('main','_no'); ?></b>
            </td>
            <td class="date"><?php echo date("d.m.Y",$row['date_add']); ?></td>
            <td class="level"><?php echo $row['itr']; ?></td>
            <td class="kf"><?php echo ($row['_mdr'] * 0.1); ?></td>
            <td class="tape">
                <?php
                    switch($row['tape'])
                    {
                        case 1:
                            echo '<span title="Человек и Бот"><i class="fa fa-male"></i><i class="fa fa-android"></i></span>';
                            break;
                        case 2:
                            echo '<span title="Человек"><i class="fa fa-male"></i></span>';
                            break;
                        
                        case 3:
                            echo '<span title="Бот"><i class="fa fa-android"></i></span>';
                            break;
                        
                        default:
                            echo '-';
                    }
                ?>
                
            </td>
            <td class="edit"><button onclick="_M._getSettings('<?php echo $row['id']; ?>', this);" class="button icon_small" title="Редактировать статус и КФ"><i class="fa fa-pencil"></i></button></td>
        </tr>
    <?php } ?>
<?php } else
{ ?>
    <tr>
        <td style="text-align: center; padding: 7px;">Нет аккаунтов</td>
    </tr>
<?php } ?>
