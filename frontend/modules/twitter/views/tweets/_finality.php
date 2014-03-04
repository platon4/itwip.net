<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_twitterPostingPosts_Title');
$this->metaDescription = Yii::t('main', '_twitterPostingPosts_Description');
$this->breadcrumbs[] = array(
    0 => array(Yii::t('breadcrumbs', '_twitter'), '/twitter'),
    1 => array(Yii::t('breadcrumbs', '_tw_advertiser'), ''),
    2 => array(Yii::t('breadcrumbs', '_tw_quickPosts_collection'), '/twitter/tweets/collection'),
    3 => array(Yii::t('breadcrumbs', '_tw_quickPosts_edit'), '/twitter/tweets/roster?_tid=' . $model->_tid),
    4 => array(Yii::t('breadcrumbs', '_tw_quickPosts_save'), '')
);
?>
<script>
    var tweetsCount = '<?php echo $model->getCount(); ?>', _tid = '<?php echo $model->_tid; ?>';
</script>
<div id="_twList" style="display:none;" class="select_posts">
    <div style="position: relative;" class="panel_posts">
        <input type="text" placeholder="<?php echo Yii::t('twitterModule.tweets', '_search_post'); ?>" name="_q" value="" onkeyup="Twitter.o.m.d.setListQuery(this.value);" onkeydown="Twitter.o.m.d.setListQuery(this.value);"/> <span class="closer"><a href="javascript:void(0);" title="<?php echo Yii::t('twitterModule.tweets', '_save_and_close'); ?>" onclick="Twitter.o.m.d.save(this);"><i class="fa fa-save"></i></a> <a href="javascript:void(0);" onclick="Twitter.o.m.d.closeList();" title="<?php echo Yii::t('twitterModule.tweets', '_сlose_without_saving'); ?>" class="no_save"><i class="fa fa-times"></i></a></span>
        <div class="select_view_tweet">Отображать: <a href="javascript:void(0);" onclick="Twitter.o.m.d.getBySelect('no_use', this);" class="here select">не использовались</a> / <a onclick="Twitter.o.m.d.getBySelect('all', this);" class="here">все</a></div>
    </div>
    <div id="tweetsList" class="view_posts"><div style="text-align: center; padding: 5px;"><img src="/i/loads.gif"></div></div>
</div>
<div class="block postingPosts">
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-twitter"></i> <h5><?php echo Yii::t('twitterModule.tweets', '_twitterPostingPosts_title'); ?></h5></div></div>
    <div class="block_content">
        <div id="info_page">
            <div class="icon"><i class="fa fa-info"></i></div>
            <div class="text"><?php echo Yii::t('twitterModule.tweets', '_twitterPostingPosts_infoPage'); ?></div>
        </div>
        <div class="line_title no_border_top no_border_bottom">
            <?php echo Yii::t('twitterModule.tweets', '_twitterPostingPosts_filter_title'); ?>
            <i title="<?php echo Yii::t('twitterModule.tweets', '_twitterPostingPosts_filter_title_info'); ?>" class="tooltip">?</i>
            <div class="open_icon"><i class="fa fa-caret-down"></i></div>
        </div>
        <div id="block_filter" style="display: none;">
            <?php echo $this->renderPartial('_filterList', array('filters' => $model->getFilters(), 'tid' => $model->_tid)); ?>
        </div>
        <div id="_filterRun" class="line_title no_border_bottom">
            <?php echo Yii::t('twitterModule.tweets', '_twitterPostingPosts_manual_settings_title'); ?>
            <div class="open_icon"><i class="fa fa-caret-up"></i></div>
        </div>
        <div id="block_1" >
            <h3 class="top_title"><?php echo Yii::t('twitterModule.tweets', '_twitterPostingPosts_manual_settings_activity'); ?></h3>
            <div id="select_activity">
                <div class="activity" style="padding-bottom: 10px;">
                    <div class="select"><?php echo Html::radiobutton('PlacementMethod', '', array('id' => 'PlacementMethod_manual', 'onchange' => 'Tweets.PlacementMethod(\'manual\', \'' . $model->_tid . '\',\'\',this);')); ?></div> 
                    <div class="text"><?php echo Yii::t('twitterModule.tweets', '_twitterPostingPosts_manual_settings_manual_posting'); ?></div> 
                </div>
                <div class="activity">
                    <div class="select"><?php echo Html::radiobutton('PlacementMethod', '', array('id' => 'PlacementMethod_fast', 'onchange' => 'Tweets.PlacementMethod(\'fast\', \'' . $model->_tid . '\',\'\',this);')); ?></div> 
                    <div class="text"><?php echo Yii::t('twitterModule.tweets', '_twitterPostingPosts_manual_settings_fast_posting'); ?></div> 
                </div>
            </div>
            <div id="pTweets" class="pType"></div>		
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $(".line_title").click(function() {
            Tweets.accordion(this);
        });
    })
</script>