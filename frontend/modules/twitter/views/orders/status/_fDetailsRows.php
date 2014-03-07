<table>
    <?php foreach($model->m->getRows() as $row) { ?>
        <tr>
            <td class="id"><?php echo $row['id']; ?></td>
            <td class="link">
                <a class="links" href="<?php echo Html::encode($row['url']); ?>" target="_blank"><?php echo Html::encode($row['url']); ?></a>
                <a class="test" href="<?php echo $row['yandex_url']; ?>" target="_blank"><span title="Проверить наличие ссылки в индексе Яндекса">[проверить]</span></a>
            </td>
            <td class="date"><?php echo $row['posted_date']; ?></td>
            <td class="date"><?php echo $row['check_date']; ?></td>
            <td class="status">
                <?php
                switch($row['status']) {
                    case 0:
                        echo '<span class="wait" title="Ожидается размещение твита с ссылкой для индексации.">Ожидает размещения</span>';
                        break;
                    case 1:
                        echo '<span class="work" title="Твит с ссылкой размещён, ожидаем время для проверки на индексацию.">На индексации</span>';
                        break;
                    case 2:
                        echo '<span class="ok">Проиндексирована</span>';
                        break;
                    case 3:
                        echo '<span class="fail" title="К сожалению Ваша ссылка не была проиндексированна Яндексом, деньги будут возвращены на Ваш счёт.">Не проиндексирована</span>';
                        break;
                }
                ?>
            </td>
        </tr>
    <?php } ?>
</table>
<div class="table_bottom_inside">
    <div class="page_nav_page">
        <?php $this->renderPartial('application.views.main._pages', array('ajax_query' => 'Twitter.o.g.getPage', 'pages' => $model->m->getPages())); ?>
    </div>
    <div class="page_nav_how">
        <?php echo Yii::t('twitterModule.accounts', '_pageNavHow'); ?>
        <select name="shoOnPage" onchange="Twitter.o.g.setLimit(this.value); return false;">
            <?php foreach($model->m->getLimits() as $option) {
                ?>
                <?php
                if($model->m->getLimit() == $option['value']) {
                    $htmlOption = array('value'    => $option['value'],
                                        'selected' => 'selected');
                } else {
                    $htmlOption = array('value' => $option['value']);
                }

                echo Html::tag('option', $htmlOption, $option['title']);
                ?>
            <?php } ?>
        </select>
    </div>
</div>

