<?php
$this->pageTitle=Yii::app()->name.' - '.Yii::t('twitterModule.accounts','_account_is_added');

$this->layout='//layouts/info';
?>
<div id="info">
    <div id="info_inset">
        <div id="modal_info">
            <div class="title_modal_info"><?php echo Html::encode(Yii::t('twitterModule.accounts','_account_is_added')); ?></div>
            <div class="content_modal_info">
                <div>
                    <div id="_loading">
                        <div style="margin: 10px 0; text-align: center;"><img src="/i/_tw_l.gif" alt=""></div>
                            <?php echo Yii::t('twitterModule.accounts','_account_is_added_load_data'); ?>	
                    </div>			
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    /*<![CDATA[*/
    jQuery(function($) {
        _ajax({
            url: "/twitter/ajax/_credentials",
            data: {"_check": "all", "tid": "<?php echo $id; ?>"},
            success: function(obj)
            {
                if (obj.code == 200)
                {
                    window.location.replace('/twitter/accounts/settings?tid=<?php echo $id; ?>');
                }
                else {
                    Dialog.open(_error, {content:'В данный момент не возможно собрать все данные, они будут обновлены позже.', buttons: [{text: _close, class: "button", click: function() {
                                    $(this).dialog("close");
                                }}]})
                }
            },
            complete: function()
            {
                $("#_loading").html('<?php echo Yii::t('twitterModule.accounts','_account_is_added_load_complete',array(
                                '{link}'=>'/twitter/accounts/settings?tid='.$id)); ?>');
            }
        });
    });
    /*]]>*/
</script>