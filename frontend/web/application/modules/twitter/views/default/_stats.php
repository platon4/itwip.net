<a href="javascript:void(0);" onclick="Tweets.resetList();">
    Все
</a> /
<b>Аккаунтов всего найдено: <?php echo $model->getCount(); ?></b> |
У вас в чёрном списке: <?php if($model->bwList('black')) { ?><a href="javascript:;" onclick="Tweets.getFromLis('black');" class="here"><?= $model->bwList('black'); ?></a><?php } else { ?>0<?php } ?>
 / в белом списке: <?php if($model->bwList('white')) { ?><a href="javascript:;" onclick="Tweets.getFromLis('white');" class="here"><?= $model->bwList('white'); ?></a><?php } else { ?>0<?php } ?>