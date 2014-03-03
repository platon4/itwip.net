<?php
$rows = [
    'links'        => array('remove_tolltip' => '', 'text' => Yii::t('twitterModule.tweets', '_stats_links_exceeded'), 'allow_delete' => true),
    'character'    => array('remove_tolltip' => '', 'text' => Yii::t('twitterModule.tweets', '_stats_symbols'), 'allow_delete' => true),
    'censor'       => array('remove_tolltip' => '', 'text' => Yii::t('twitterModule.tweets', '_stats_censor'), 'allow_delete' => true),
    'unique-tweet' => array('remove_tolltip' => '', 'text' => Yii::t('twitterModule.tweets', '_stats_duplikate'), 'allow_delete' => true),
    'unique-url'   => array('remove_tolltip' => '', 'text' => Yii::t('twitterModule.tweets', '_stats_domen_blocked'), 'allow_delete' => true),
    'links'        => array('remove_tolltip' => '', 'text' => Yii::t('twitterModule.tweets', '_stats_notUniqueUrl'), 'allow_delete' => true),
    'politics'     => array('remove_tolltip' => '', 'text' => Yii::t('twitterModule.tweets', '_stats_wordsFilter'), 'allow_delete' => true),
    'hash-tags'    => array('remove_tolltip' => '', 'text' => Yii::t('twitterModule.tweets', '_stats_exceededHash'), 'allow_delete' => true),
    'references'   => array('remove_tolltip' => '', 'text' => Yii::t('twitterModule.tweets', '_stats_references'), 'allow_delete' => true),
];
?>
<div id="block_1">
    <div id="block_1_1">
        <?php
        $i = 0;
        $b = 1;

        foreach($rows as $key => $row) {
            $i++;

            $count = $model->getFigures($key);
            ?>
            <div id="block_rindexes">
                <span id="_<?php echo $key; ?>"<?php if($count) { ?> class="link_here" onclick="Tweets.listToogle('<?php echo $key; ?>'); return false;"<?php } ?>><i class="<?php if($count) { ?>icon-eye-open<?php } else { ?>icon-eye-close<?php } ?>"></i> <?php echo $row['text']; ?></span>: <?php echo $count; ?>
                <?php if($count && $row['allow_delete']) { ?>
                    <a class="delete_post" href="javascript:;" onclick="Tweets.removeTweets('<?php echo Yii::t('twitterModule.tweets', '_remove_tweets_title'); ?>','<?php echo Yii::t('twitterModule.tweets', '_remove_tweets_group_conf'); ?>','0','<?php echo $key; ?>'); return false;" title="<?php echo $row['remove_tolltip']; ?>">
                        <i class="fa fa-trash-o"></i> </a>
                <?php } ?>
            </div>
            <?php
            if($i % 3 == 0) {
                $b++;
                echo '</div><div id="block_1_' . $b . '">';
            }
        }
        ?>
    </div>
</div>    
