<div class="line_title no_border_bottom">
    <?php echo Yii::t('twitterModule.tweets','_matched_accounts'); ?> <span id="_accounts_count"><?php echo $model->getCount(); ?></span>
</div>
<div class="table_head">
    <div class="table_head_inside">
        <table>
            <tbody><tr>
                    <td class="account"><?php echo Yii::t('main','_account'); ?></td>
                    <td class="followers"><a href="javascript:;" onclick="_T.setOrder('followers', this);"> Читателей <i class="fa fa-caret-down"></i></a></td>
                    <td class="level"><a href="javascript:void(0);" onclick="_T.setOrder('itr', this);"><?php echo Yii::t('main','_itr'); ?> <i class="fa fa-caret-down"></i></a></td>
                    <td class="rang"><a href="javascript:void(0);" onclick="_T.setOrder('yrk', this);"><?php echo Yii::t('main','_authority'); ?> <i class="fa fa-caret-down"></i></a></td>
                    <td class="pr"><a href="javascript:void(0);" onclick="_T.setOrder('gpr', this);"><?php echo Yii::t('main','_pr'); ?> <i class="fa fa-caret-down"></i></a></td>
                    <td class="tape"><a href="javascript:void(0);" onclick="_T.setOrder('tape', this);" href="javascript:;"><i class="fa fa-comments-o"></i> <i class="fa fa-caret-down"></i></a></td>
                    <td class="index"><a href="javascript:void(0);" onclick="_T.setOrder('yin', this);"><?php echo Yii::t('main','_indexation'); ?> <i class="fa fa-caret-down"></i></a></td>
                    <td class="select"><span title="<?php echo Yii::t('twitterModule.tweets','_confirmation_applications_info'); ?>"><?php echo Yii::t('twitterModule.tweets','_confirmation'); ?></span></td>
                    <td class="price"><a href="javascript:void(0);" onclick="_T.setOrder('price', this);"><?php echo Yii::t('main','_price_post'); ?> <i class="fa fa-caret-down"></i></a></td>
                    <td class="text"><span title="<?php echo Yii::t('twitterModule.tweets','_post_info'); ?>"><?php echo Yii::t('twitterModule.tweets','_post'); ?></span></td>
                    <td class="no_border check" title="<?php echo Yii::t('main','_invert_selection'); ?>"><?php echo Html::checkBox('_all_select','',array(
        'id'=>'_all_select','onchange'=>'_T._selectAll(this)')); ?></td>
                </tr>
            </tbody></table>
    </div>
</div> 
<div id="_pages" class="acconts_list">
    <form id="_tweetsFormPlace">
<?php $this->renderPartial('orderTweets/_ptype_accounts_list',array('_data'=>$_data,
    'model'=>$model)); ?>
    </form>
</div>
<h3 class="top_title" onclick="Tweets.accordion(this);" style="cursor: pointer;"><?php echo Yii::t('twitterModule.tweets','_additional_network_settings'); ?></h3>
<div id="more_options" style="padding-bottom: 15px;">
    <div class="options">
<?php echo Html::checkBox('_ping','',array('onchange'=>'_T.embedButtonUpdate();',
    'id'=>'_ping')); ?> <?php echo Yii::t('twitterModule.tweets','_network_settings_2'); ?>
    </div>
</div>
<div class="end_posting">
    <span style="float: left; padding-top: 7px; font-weight: bold">Разместится подготовленных твитов: <span id="_all_tweets">0</span> из <span id="_tweetsCount">0</span>, выбрано аккаунтов: <span id="_all_accounts">0</span>, на сумму: <span id="_all_amount">0</span> руб.</span>
    <button id="embedButton" class="button btn_blue" disabled="disabled" onclick="_T.embedTweets(this);"><?php echo Yii::t('twitterModule.tweets','_place_posts'); ?> <i class="icon-double-angle-right"></i></button>
</div>