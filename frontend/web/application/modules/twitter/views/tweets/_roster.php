<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('twitterModule.tweets', '_twitterEditingPosts_title');
$this->metaDescription = Yii::t('twitterModule.tweets', '_twitterEditingPosts_Description');
$this->breadcrumbs[] = array(
    0 => array(Yii::t('breadcrumbs', '_twitter'), '/twitter'),
    1 => array(Yii::t('breadcrumbs', '_tw_advertiser'), ''),
    2 => array(Yii::t('breadcrumbs', '_tw_quickPosts_collection'), '/twitter/tweets/collection'),
    3 => array(Yii::t('breadcrumbs', '_tw_quickPosts_edit'), ''),
);
?>
<script type="text/javascript">
    Tweets.s._tid = '<?php echo $model->_tid; ?>';
</script>
<div id="_tweetsRoster" class="block editingPosts">
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-pencil"></i> <h5><?php echo Yii::t('twitterModule.tweets', '_twitterEditingPosts_title'); ?></h5></div></div>
    <div class="block_content">
        <div id="info_page">
            <div class="icon"><i class="fa fa-info"></i></div>
            <div class="text"><?php echo Yii::t('twitterModule.tweets', '_tweets_edit_help'); ?></div>
        </div>
        <div id="_stats">
            <?php $this->renderPartial('_roster_information', array('model' => $model)); ?>
        </div>
        <div id="block_2">
            <div id="block_2_top">
                <div id="block_2_top_inset">
                    <a class="button icon aHidden" href="#block_2_bottom" title="<?php echo Yii::t('twitterModule.tweets', '_list_down_bottom'); ?>"><i class="fa fa-arrow-down"></i></a>
                    <a class="button icon select_all aHidden" href="javascript:;"  onclick="Tweets.selectAll();
                            return false;" title="<?php echo Yii::t('twitterModule.tweets', '_select_all_checkbox'); ?>"><i class="fa fa-check"></i></a>
                </div>
            </div>
            <div id="block_2_list">
                <?php $this->renderPartial('_roster_rows', array('model' => $model)); ?>
            </div>
        </div>
        <div id="block_bottom" class="aHidden">
            <?php if ($model->isSave() === false) { ?>
                <button class="button" onclick="Tweets.saveRoster(this);"><i class="fa fa-floppy-o"></i> <?php echo Yii::t('twitterModule.tweets', '_save_tweets_to_list'); ?></button>
            <?php } ?>
            <button id="_nextButton" class="button btn_blue" data-href="/twitter/tweets/finality?_tid=<?php echo $model->_tid; ?>" onclick="window.location = this.getAttribute('data-href'); return false;" <?php echo $model->allowPlace() === false ? ' disabled' : ''; ?>><?php echo Yii::t('twitterModule.tweets', '_go_to_place_tweets'); ?> <i class="fa fa-arrow-right"></i></button>
        </div>
    </div>
</div>
<script>
    $(function() {
        $('#_tweetsRoster').tooltip({
            my: "left top+15",
            at: "left bottom",
            items: "[data-tooltip]",
            content: function() {
                var element = $(this);
                if (element.is("[data-tooltip]")) {
                    return $('#' + element.attr('data-tid')).html();
                }
            }
        });
    });
</script>