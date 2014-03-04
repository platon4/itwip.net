<ul class="list_news">
    <?php
    $found=false;

    foreach($topics as $row)
    {
        $found=true;

        if(substr($row['seolink'],0,1)=='/')
        {
             $row['seolink']=substr($row['seolink'],1); 
        }
        if(CHelper::strlen($row['title']) > $length)
        {
            $row['title']=CHelper::substr($row['title'],0,$length).'...';
        }
        ?>
        <li><span class="date"><?php echo date("d.m.Y",strtotime($row['pubdate'])); ?></span><span class="theme"><a href="http://community.itwip.net/<?php echo Html::encode($row['seolink']); ?>.html" target="_blank"><?php echo Html::encode($row['title']); ?></a></span></li>
    <?php } ?>
        <?php if(!$found)
        { ?>
        <li><span style="color: #fff;"><?php echo Yii::t('t','_community_not_found'); ?></span></li>
<?php } ?>
</ul>