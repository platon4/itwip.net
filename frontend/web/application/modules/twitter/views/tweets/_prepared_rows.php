<table>
<?php if($model->getTweets() !== array()) { ?>
    <?php foreach($model->getTweets() as $row) { ?>
        <tr>
          <td class="id"><?php echo $row['id']; ?></td>
          <td class="date"><?php echo date("d.m.Y H:i",strtotime($row['_date'])); ?></td>
          <td class="name"><?php echo Html::encode($row['_title']); ?></td>
          <td class="tweet"><?php echo $row['_count']; ?></td>
          <td class="edit">
            <a target="_blank" class="button icon_small" href="/twitter/tweets/add?_m=save&tid=<?php echo $row['_uid']; ?>" title="<?php echo Yii::t('twitterModule.prepared', '_place_tweets'); ?>"><i class="fa fa-twitter"></i></a>
            <a target="_blank" class="button icon_small" href="/twitter/tweets/add?_m=edit&tid=<?php echo $row['_uid']; ?>" title="<?php echo Yii::t('twitterModule.prepared', '_edit_tweets'); ?>"><i class="fa fa-pencil"></i></a>
            <a target="_blank" class="button icon_small" href="/twitter/tweets/prepared?action=download&tid=<?php echo $row['_uid']; ?>" title="<?php echo Yii::t('twitterModule.prepared', '_download_tweets'); ?>"><i class="fa fa-download"></i></a>
            <a class="button icon_small" href="javascript:void(0);" onclick="Tweets.remvoeList('<?php echo $row['id']; ?>',this);" title="<?php echo Yii::t('twitterModule.prepared', '_deletet_list'); ?>"><i class="fa fa-trash-o"></i></a>
          </td>
        </tr>
    <?php } ?>
<?php } else { ?>
    <div style="text-align:center; padding:6px;">Сохраненные списки постов отсутствуют</div>
<?php } ?>
</table>
<?php echo $this->renderPartial('application.views.main._pages',array('ajax_query'=>'Tweets.getPreparedPage','pages'=>$pages)); ?>
