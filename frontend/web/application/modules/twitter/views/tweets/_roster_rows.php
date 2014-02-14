<?php
if ($model->getTweets() !== array())
{

    $i = $model->getPages()->getOffset();

    foreach ($model->getTweets() as $row)
    {
        $i++;
        ?>
        <form id="_twForm">
            <div class="post no_border_top" id="post_<?php echo $row['id']; ?>">
                <div class="number">
                    <div style="vertical-align: middle; margin-left: 5px; float: left;"><?php echo $i; ?></div>		
                </div>
                <div class="text_edit">
                    <div class="text" id="text_<?php echo $row['id']; ?>"><?php echo Html::tweet($row['tweet']); ?></div>
                </div>
                <div style="display:none;" id="errorList_<?php echo $row['id']; ?>"></div>
                <div class="check">
                    <?php
                    $infoRows = json_decode($row['_info']);
                    $msgInfo='false';
                    if (!CHelper::isEmpty($infoRows))
                    {
                        echo sprintf('<span class="alert"><i class="fa fa-exclamation-triangle" data-tooltip="true" data-tid="msg_%s"></i></span><div id="msg_%s" style="display: none;">', $row['id'],$row['id']);
                        
                        foreach ($infoRows as $info)
                        {
                            foreach ($info as $msg)
                            {
                                $replace = array();
                                $msgInfo='true';
                                if (isset($msg->replace))
                                    $replace = array($msg->replace->key => $msg->replace->value);

                                echo '<div>' . Yii::t('twitterModule.tweets', $msg->text, $replace) . '</div>';
                            }
                        }

                        echo '</div>';
                    }
                    ?>
                    <a class="edit" onclick="Tweets.edit({tid: '<?php echo $row['id']; ?>', title: '<?php echo Yii::t('twitterModule.tweets', '_remove_post_confirm_title', array('{n}' => $i)); ?>', tweet: '<?php echo Html::encode($row['tweet'], 'javascript'); ?>', msg: <?php echo $msgInfo; ?>});
                            return false;" href="javascript:;"><i class="fa fa-pencil" title=""></i></a>
                    <a class="delete" onclick="Tweets.removeTweets('<?php echo Yii::t('twitterModule.tweets', '_remove_post_confirm_text'); ?>', $row['id']);
                            return false;" href="javascript:;"><i class="fa fa-remove" title="Удалить твит"></i></a>
                       <?php echo Html::checkBox('tweets[]', '', array('value' => $row['id'], 'id' => 'tweets')); ?>	
                </div>
            </div>
        <?php } ?>
    </form>
    <div id="block_2_bottom">
        <div id="block_2_bottom_inset">
            <div id="pagesNavigation" style="float: left; margin-left: 7px; margin-top: 3px;">
                <?php $this->renderPartial('application.views.main._pages', array('ajax_query' => 'Tweets._getContent', 'pages' => $model->getPages())); ?>
            </div>
            <select id="tw_action" class="aHidden">
                <option value="" class="disabled"><?php echo Yii::t('main', '_actio_from_select'); ?></option>
                <option value="remove"><?php echo Yii::t('main', '_remove'); ?></option>
            </select>
            <a class="button aHidden" href="javascript:;" onclick="Tweets.action(this, $('#tw_action').val()); return false;">ок</a>
            <a class="button icon aHidden" href="#block_2_top" title="<?php echo Yii::t('twitterModule.tweets', '_list_up_top'); ?>"><i class="fa fa-arrow-up"></i></a>
            <a class="button icon aHidden" href="javascript:;" onclick="Tweets.selectAll(); return false;"><i class="fa fa-check "></i></a>
        </div>
    </div>
    <?php } else { ?>
    <div class="post no_border_top no_border_bottom">
        <div style="padding: 7px 0; text-align: center;"><?php echo Yii::t('twitterModule.tweets', '_no_tweets_count'); ?></div>
    </div>
<?php } ?>
