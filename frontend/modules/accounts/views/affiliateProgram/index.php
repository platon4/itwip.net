<?php
    $this->pageTitle      =Yii::app()->name.' - '.Yii::t('main','_affiliate_program_Title');
    $this->metaDescription=Yii::t('main','_affiliate_program_Description');
?>
<div id="affiliate_program" class="section">
    <ul class="tabs">
        <li onclick="_affiliateLoad('referral_container', 'referral');" class="current"><span class="inset"><?php echo Yii::t('accountsModule.affiliateProgram','_referrals'); ?></span></li>
        <li id="loyalty" onclick="_affiliateLoad('loyalty_container', 'loyalty');"><span class="inset"><?php echo Yii::t('accountsModule.affiliateProgram','_loyalty_program'); ?></span></li>
        <li id="loyalty" onclick="_affiliateLoad('loyalty_banners', 'banners');"><span class="inset">Промо материалы</span></li>
    </ul>
    <div id="referral_Program" class="box actived">
        <div id="referral_container" class="box_inset"><div style="text-align: center; padding: 20px 0;"><img alt="Loading..." src="/i/loading_11.gif"></div></div>
    </div>
    <div id="loyalty_Program" class="box">
        <div id="loyalty_container" class="box_inset"><div style="text-align: center; padding: 20px 0;"><img alt="Loading..." src="/i/loading_11.gif"></div></div>
    </div>    <div id="loyalty_Program" class="box">
        <div id="loyalty_banners" class="box_inset"><div style="text-align: center; padding: 20px 0;"><img alt="Loading..." src="/i/loading_11.gif"></div></div>
    </div>
</div>
<script>
    var _loading = '<div style="text-align: center; padding: 20px 0;"><img alt="Loading..." src="/i/loading_11.gif"></div>';

    function _affiliateLoad(e, _t)
    {
        $.ajax({
            type: "GET",
            url: "/accounts/affiliateProgram/" + _t,
            dataType: "json",
            success: function(obj, textStatus)
            {
                $('#' + e).html(obj.html);
            },
            beforeSend: function()
            {
                $('#' + e).html(_loading);
            }
        });
    }

    function _switchTab(e)
    {
        $('.tabs').find('li').removeClass('current');
        $('#loyalty').addClass('current');
        $('#referral_Program').removeClass('actived');
        $('#loyalty_Program').addClass('actived');
        _affiliateLoad('loyalty_container', 'loyalty');
    }
    _affiliateLoad('referral_container', 'referral');
</script>