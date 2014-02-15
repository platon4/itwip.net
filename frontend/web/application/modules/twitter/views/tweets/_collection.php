<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_twitterPostingQuick_Title');
$this->metaDescription = Yii::t('main', '_twitterPostingQuick_Description');
$this->breadcrumbs[] = array(
    0 => array(Yii::t('breadcrumbs', '_twitter'), ''),
    1 => array(Yii::t('breadcrumbs', '_tw_advertiser'), ''),
    2 => array(Yii::t('breadcrumbs', '_tw_quickPosts_collection'), '')
);
?>
<?php if(Yii::app()->user->hasFlash('TWEETS_MESSAGE')) { ?>
<div id="_messageBox" class="line_info alert" style="margin-bottom: 13px;">
    <?php echo Yii::app()->user->getFlash('TWEETS_MESSAGE'); ?>
</div>
<script>
    setTimeout(function(){ 
        $('#_messageBox').fadeOut();
    },3000);
</script>
<?php } ?>
<div>
	Виталя
</div>
<div class="block postingQuick">
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-keyboard-o"></i> <h5><?php echo Yii::t('twitterModule.tweets', '_twitterPostingQuick_title'); ?></h5></div></div>
    <div class="block_content">
        <div id="info_page" class="no_border_bottom">
            <div class="icon"><i class="fa fa-info"></i></div>
            <div class="text"><?php echo Yii::t('twitterModule.tweets', '_twitterPostingQuick_infoPage'); ?></div>
        </div>

        <div class="line_title">
            <?php echo Yii::t('twitterModule.tweets', '_twitterPostingQuick_manualInput'); ?>
            <div class="open_icon"><i class="fa fa-caret-up"></i></div>
        </div>
        <div id="block_1">
            <div id="block_1_1">
                <form id="tweetsData">
                    <?php echo Html::hiddenField('_tid', CHelper::generateID()); ?>
                    <div id="block_1_1_1"><textarea id="PostingList" name="Tweets[]"></textarea></div>
                    <div id="_data" style="height: 0; visibility: hidden;"></div>
                </form>				
                <script>
                    $('#PostingList').bind('change click keyup', function() {
                        Tweets.check(this);
                    });
                </script>
                <div id="block_1_1_2"><?php echo Yii::t('twitterModule.tweets', '_twitterPostingQuick_manualInputTotal'); ?> <span id="postCount">0</span></div>
            </div>
            <div id="block_1_2">
                <h5><?php echo Yii::t('twitterModule.tweets', '_twitterPostingQuick_manualInputTooltip'); ?></h5>
                <ul>
                    <li>- <?php echo Yii::t('twitterModule.tweets', '_twitterPostingQuick_manualInputTooltip_1'); ?></li>
                    <li>- <?php echo Yii::t('twitterModule.tweets', '_twitterPostingQuick_manualInputTooltip_2'); ?></li>
                    <li>- <?php echo Yii::t('twitterModule.tweets', '_twitterPostingQuick_manualInputTooltip_3'); ?></li>
                    <li>- <?php echo Yii::t('twitterModule.tweets', '_twitterPostingQuick_manualInputTooltip_4'); ?></li>
                </ul>
            </div>
        </div>
        <div class="line_title">
            <?php echo Yii::t('twitterModule.tweets', '_upload_file'); ?>
            <div class="open_icon"><i class="fa fa-caret-down"></i></div>
        </div>
        <div id="block_2" style="display: none;">
            <div id="block_2_1">
                <div id="block_2_1_1">
                    <?php echo Html::dropDownList('file_type', '', array('txt' => 'txt'), array(
                        'id' => '_filesType', 'class' => 'styler'));
                    ?>	
                    <span class="group_input search">
                        <?php
                        echo Html::textField('_file', '', array('id' => '_files', 'class' => 'fClick',
                            'placeholder' => Yii::t('twitterModule.tweets', '_pl_select_file'),
                            'readonly' => 'readonly', 'style' => 'width: 226px;'));
                        echo Html::htmlButton(Yii::t('twitterModule.tweets', '_select_file'), array(
                            'class' => 'fClick button icon'));
                        ?>							
                    </span> 
                    <?php echo Html::fileField('file', '', array('data-type' => '_filesType', 'data-html' => 'file_load_list',
                        'class' => 'fileUpload', 'id' => '_filesInput', 'data-input' => '_files', 'style' => 'visibility: hidden; position: absolute;'));
                    ?>
                </div>
                <div id="block_3_1_2">
                    <ol id="file_load_list"></ol>
                </div>
                <div id="_fileData"></div>
                <div style="display: none;">
                    <form id="_parseTemplate">
                        <textarea id="parseUrl" name="parseTemplate[url]">{url} {title}</textarea>
                        <textarea id="parseExclide" name="parseTemplate[exclude]"></textarea>
                        <textarea id="parseWords" name="parseTemplate[words]"></textarea>
                    </form>
                </div>
            </div>
            <div id="block_2_2">
                <h5><?php echo Yii::t('twitterModule.tweets', '_help_upload_file'); ?></h5>
                <ul>
                    <li><?php echo Yii::t('twitterModule.tweets', '_help_upload_file_1'); ?></li>
                    <li><?php echo Yii::t('twitterModule.tweets', '_help_upload_file_2'); ?></li>
                    <li><?php echo Yii::t('twitterModule.tweets', '_help_upload_file_3'); ?></li>
                </ul>
            </div>
        </div>

        <div class="line_title">
<?php echo Yii::t('twitterModule.tweets', '_upload_file_sitemap'); ?>
            <div class="open_icon"><i class="fa fa-caret-down"></i></div>
        </div>
        <div id="block_3"  style="display: none;">
            <div id="block_3_1">
                <div id="block_3_1_1">
                    <div style="margin-bottom: 30px; padding-left: 2px;">
                        <a href="javascript:void(0);" class="here" onclick="Tweets.parseTemplate(this);
                                return false;"><i class="fa fa-cog"></i> Настройки шаблона для генерации твитов</a>
                    </div>
                    <?php
                    echo Html::textField('_surl', '', array('placeholder' => Yii::t('twitterModule.tweets', '_pl_load_tweets_form_sitemap_url'),
                        'style' => 'width: 425px;')) . " ";
                    echo Html::htmlButton(Yii::t('twitterModule.tweets', '_load_file'), array(
                        'data-only' => '_only', 'data-url' => '_surl', 'data-type' => 'xml',
                        'onclick' => 'Tweets.floadUrl(this); return false;', 'class' => 'button icon'));
                    ?>
                    <div style="margin-top: 4px;">
<?php echo Html::checkBox('_only_new', '', array('id' => '_only',
    'class' => 'styler'));
?> <?php echo Yii::t('twitterModule.tweets', '_sitemap_load_only_new'); ?>
                        <i title="<?php echo Yii::t('twitterModule.tweets', '_sitemap_load_only_new_help'); ?>" class="tooltip">?</i>
                    </div>
                </div>
                <div id="block_3_1_3">
                        <?php echo Html::hiddenField('_type', 'xml', array('id' => '_sitemapType')); ?>
                    <span class="group_input search">
                        <?php
                        echo Html::textField('_sitemap', '', array('placeholder' => Yii::t('twitterModule.tweets', '_pl_load_tweets_form_sitemap'),
                            'class' => 'fClick', 'style' => 'width: 350px;'));
                        echo Html::htmlButton(Yii::t('twitterModule.tweets', '_select_file'), array('class' => 'fClick button icon'));
                        ?>
                    </span>
<?php echo Html::fileField('file', '', array('data-type' => '_sitemapType', 'class' => 'fileUpload',
    'id' => '_sitemapInput', 'data-input' => '_sitemap', 'style' => 'visibility: hidden; position: absolute;'));
?>
                </div>
                <div id="sitemap_progress" style="margin-top: 14px;"></div>
                <div id="block_3_1_4">
                    <ol id="_sitemaplist"></ol>
                </div>
            </div>
            <div id="block_3_2">
                <h5><?php echo Yii::t('twitterModule.tweets', '_help_sitemap_url'); ?></h5>
                <ul>
                    <li><?php echo Yii::t('twitterModule.tweets', '_help_sitemap_url_0'); ?></li>
                    <li><?php echo Yii::t('twitterModule.tweets', '_help_sitemap_url_1'); ?></li>
                    <li><?php echo Yii::t('twitterModule.tweets', '_help_sitemap_url_2'); ?></li>
                </ul>			
            </div>
        </div>
        <div id="block_bottom">
            <span style="float: left; padding-top: 7px; font-weight: bold"><?php echo Yii::t('twitterModule.tweets', '_all_collection_tweets'); ?> <span id="all_tweets_add">0</span></span>
            <span id="_loadingMsg" style="display:none;">Пожалуйста подаждите, идет обработка твитов</span>  <a class="button btn_blue" href="javascript:;" onclick="Tweets.send(this, 'tweetsData');
                    return false;"><?php echo Yii::t('twitterModule.tweets', '_tweets_button_send'); ?> <i class="fa fa-arrow-right"></i></a>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function() {
        $(".fClick").click(function() {
            $(this).parent().next().click();
        });

        $(".line_title").click(function() {
            Tweets.accordion(this);
        });

        var uploadButton = $('<button/>')
                .addClass('button icon')
                .text('<?php echo Yii::t('twitterModule.tweets', '_load_file'); ?>')
                .on('click', function() {
                    var $this = $(this),
                            data = $this.data();
                    $this
                            .off('click')
                            .html('<img src="/i/loads.gif">')
                            .prop('disabled', true);

                    data.submit().always(function() {
                        $this.remove();
                    });
                });

        $('.fileUpload').fileupload({
            url: '/twitter/ajaxTweets/_upload',
            dataType: 'json',
            autoUpload: false,
            acceptFileTypes: /(\.|\/)(txt|xml)$/i,
            maxFileSize: 5000000, // 5 MB
        }).on('fileuploadadd', function(e, data) {

            if (Tweets.s.urlWait == true)
            {
                Dialog.open(_error, {content: '<?php echo Yii::t('twitterModule.tweets', '_wat_loading_antherior_fail'); ?>', buttons: [{text: _close, class: "button", click: function() {
                                $(this).dialog("close");
                            }}]});
                return false;
            }

            $("#" + $(this).attr('data-input')).val(data.files[0].name);

            $(this).parent().append(uploadButton.clone(true).data(data));
            Tweets.s.urlWait = true;
        }).on('fileuploaddone', function(e, data) {
            $("#" + $(this).attr('data-input')).val('');
            Tweets.s.urlWait = false;

            switch (data.result.code)
            {
                case 200:
                    $("#" + $(this).attr('data-html')).append(data.result.html);
                    $('#_data').append('<textarea id="tweet_' + data.result.areaID + '" name="Tweets[]" style="visibility: hidden; height: 0px;">' + data.result.tweets + '</textarea>');
                    $('#all_tweets_add').html(parseInt($('#all_tweets_add').html()) + data.result.count);
                    break;

                case 201:
                    Tweets.progress(data.result.uid, $('#sitemap_progress'), data.result.html);
                    break;

                default:
                    Dialog.open(_error, {content: data.result.html, buttons: [{text: _close, class: "button", click: function() {
                                    $(this).dialog("close");
                                }}]});
            }
        }).on('fileuploadfail', function(e, data) {
            Tweets.s.urlWait = false;
            Dialog.open(_error, {content: unknow_response, buttons: [{text: _close, class: "button", click: function() {
                            $(this).dialog("close");
                        }}]});
        }).on('fileuploadsubmit', function(e, data) {
            data.formData = {
                '_token': it._token,
                '_type': $("#" + $(this).attr('data-type')).val(),
                'parseTemplate[words]': $('#parseWords').val(),
                'parseTemplate[exclude]': $('#parseExclide').val(),
                'parseTemplate[url]': $('#parseUrl').val()
            };
        });

<?php if (isset(Yii::app()->session['_psitemap']))
{
    ?>Tweets.progress('<?php echo htmlspecialchars(Yii::app()->session['_psitemap']['identifier']); ?>', $('#sitemap_progress'), '<?php echo Yii::t('twitterModule.tweets', '_file_add_collection', array(
        '{id}' => Yii::app()->session['_psitemap']['identifier'], '{count}' => Yii::app()->session['_psitemap']['count']));
    ?>');<?php } ?>
    });
</script>