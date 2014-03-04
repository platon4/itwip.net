<table>
    <?php if($model->getList() !== array()) { ?>
        <?php foreach($model->getList() as $row) { ?>
            <tr>
                <td class="date"><?php echo date("d.m.Y H:i", strtotime($row['date_create'])); ?></td>
                <td class="name"><?php echo Html::encode($row['title']); ?></td>
                <td class="tweet"><?php echo $row['_count']; ?></td>
                <td class="edit">
                    <a class="button icon_small" href="javascript:;" onclick="Twitter.p.roster('<?= $row['_hash']; ?>',this);" title="<?php echo Yii::t('twitterModule.prepared', '_edit_tweets'); ?>"><i class="fa fa-twitter"></i></a>
                    <a target="_blank" class="button icon_small" href="/twitter/tweets/prepared?action=download&Prepared[_tid]=<?php echo $row['_hash']; ?>" title="<?php echo Yii::t('twitterModule.prepared', '_download_tweets'); ?>"><i class="fa fa-download"></i></a>
                    <a class="button icon_small" href="javascript:void(0);" onclick="Twitter.p.remvoe('<?php echo $row['_hash']; ?>',this);" title="<?php echo Yii::t('twitterModule.prepared', '_deletet_list'); ?>"><i class="fa fa-trash-o"></i></a>
                </td>
            </tr>
        <?php } ?>
    <?php } else { ?>
        <div style="text-align:center; padding:6px;">Сохраненные списки постов отсутствуют</div>
    <?php } ?>
</table>
<?php echo $this->renderPartial('application.views.main._pages', array('ajax_query' => 'Twitter.p.setPage', 'pages' => $model->getPages())); ?>
