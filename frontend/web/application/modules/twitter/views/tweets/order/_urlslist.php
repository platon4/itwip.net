<ul id="urlList">
    <?php foreach($model->getUrls() as $url) { ?>
        <li><a href="<?= Html::encode($url['_url']); ?>" target="_blank"><?=Html::encode($url['_url']);?></a> <a class="delete" href="javascript:;" onclick="Twitter.o.f.urls.remove('<?=$url['id'];?>',this);"><i class="fa fa-times"></i></a></li>
    <?php } ?>               
</ul>