<?php
    $this->pageTitle      =Yii::app()->name.' - '.Yii::t('main','_finance_Title');
    $this->metaDescription=Yii::t('main','_finance_Description');
    $this->breadcrumbs[]=array(
        0=>array(Yii::t('breadcrumbs','_accounts'),''),
        1=>array(Yii::t('breadcrumbs','_finance'),'')
    );
?>
<script src="/js/chart.js"></script>
<script>
    $(function() {
        $("#from").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            onClose: function(selectedDate) {
                $("#to").datepicker("option", "minDate", selectedDate);
            }
        });
        $("#to").datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            onClose: function(selectedDate) {
                $("#from").datepicker("option", "maxDate", selectedDate);
            }
        });
    });
</script>
<div class="line_info" style="font-style: normal;">
    <span>
        <b><?php echo Yii::t('financeModule.index','_available'); ?></b> 
        <i class="fa fa-unlock"></i> <?php echo Finance::money(Yii::app()->user->_get('money_amount'),0,true); ?> /
        <span title="<?php echo Yii::t('financeModule.index','_blocked_info'); ?>"><i class="fa fa-lock"></i></span>&nbsp;&nbsp;<?php echo Finance::money($balance['money'],0,true); ?> 
    </span>
    
    <span style="margin-left: 20px;">
        <b><span title="<?php echo Yii::t('financeModule.index','_bonus_info'); ?>"><?php echo Yii::t('financeModule.index','_bonus'); ?></span></b>  
        <i class="fa fa-unlock"></i> <?php echo Finance::money(Yii::app()->user->_get('bonus_money'),1,true); ?> / 
        <span title="<?php echo Yii::t('financeModule.index','_blocked_info'); ?>"><i class="fa fa-lock"></i></span>&nbsp;&nbsp;<?php echo Finance::money($balance['bonus'],1,true); ?> 
    </span>

   <span style="float: right"><a href="/finance/replenishment"><?php echo Yii::t('financeModule.index','_top-up'); ?></a> <a href="/finance/output"><?php echo Yii::t('financeModule.index','_withdraw'); ?></a></span>
</div>
<div id="finance">
    <div class="table">
        <div id="finance_left">
            <div class="block balance_block">
                <div class="block_title"><div class="block_title_inset"><i class="fa fa-lock"></i> <h5><?php echo Yii::t('financeModule.index','_blocked_money'); ?></h5></div></div>
                <div id="_blockingAmount" class="block_content">
                    <div style="text-align: center; padding: 5px 0;"><img src="/i/loading_11.gif" alt="Loading..."></div>
                </div>
            </div>
        </div>
        <div id="finance_right">
            <div class="section stats">
                <ul class="tabs">
                    <li onclick="Finance._getGraph('income');" class="current"><span class="inset"><?php echo Yii::t('financeModule.index','_earned'); ?></span></li>
                    <li onclick="Finance._getGraph('out');" ><span class="inset"><?php echo Yii::t('financeModule.index','_spent'); ?></span></li>
                </ul>
                <div class="box actived">
                    <div id="_graph" class="box_inset"><div style="text-align: center; padding: 12px;"><img src="/i/loading_11.gif" alt="Loading..."></div></div>
                </div>
            </div>
        </div>
    </div>
    <div id="finance_bottom">
        <div class="block report">
            <div class="block_title"><div class="block_title_inset"><i class="fa fa-th-list"></i> <h5><?php echo Yii::t('financeModule.index','_detailed_statistics'); ?></h5></div></div>
            <div class="block_content">
            <div class="no_border_bottom" id="info_page">
            	<div class="icon"><i class="fa fa-info"></i></div>
            	<div class="text"><?php echo Yii::t('financeModule.index','_detailed_statistics_info'); ?></div>
            </div>
                <div class="period">
                    <ul id="_period">
                        <li><?php echo Yii::t('financeModule.index','_show_for'); ?></li>
                        <li><a class="here select" href="javascript:;" onclick="Finance._setParams('<?php echo date('d.m.Y'); ?>','<?php echo date('d.m.Y'); ?>',this);"><?php echo Yii::t('financeModule.index','_today'); ?></a></li>
                        <li><a class="here" href="javascript:;" onclick="Finance._setParams('<?php echo date('d.m.Y',time() - (7 * 86400)); ?>','<?php echo date('d.m.Y'); ?>',this);"><?php echo Yii::t('financeModule.index','_week'); ?></a></li>
                        <li><a class="here" href="javascript:;" onclick="Finance._setParams('<?php echo date('d.m.Y',time() - (30 * 86400)); ?>','<?php echo date('d.m.Y'); ?>',this);"><?php echo Yii::t('financeModule.index','_month'); ?></a></li>
                        <li><a class="here" href="javascript:;" onclick="Finance._setParams('<?php echo date('d.m.Y',time() - (365 * 86400)); ?>','<?php echo date('d.m.Y'); ?>',this);"><?php echo Yii::t('financeModule.index','_year'); ?></a></li>
                        <li><a class="here" href="javascript:;" onclick="Finance._setParams('all','all',this);"><?php echo Yii::t('financeModule.index','_all_time'); ?></a></li>
                        <li></li>
                        <li><?php echo Yii::t('financeModule.index','_period'); ?> <?php echo Yii::t('financeModule.index','_in'); ?> <input type="text" placeholder="<?php echo date("d.m.Y"); ?>" id="from" class="period_date" onchange="Finance._from();" value=""> <?php echo Yii::t('financeModule.index','_by'); ?> <input type="text" class="period_date"  placeholder="<?php echo date("d.m.Y"); ?>" id="to" onchange="Finance._from();" value=""> </li>
                    </ul>
                </div>
                <div class="list">
                    <table style="width: 100%;" class="table_style_1 ">
                        <tbody>
                            <tr class="title">
                            <td class="date"><a href="javascript:;" onclick="Finance._setOrder('date',this);"><?php echo Yii::t('financeModule.index','_date'); ?> <i class="fa fa-caret-down"></i></a></td>
                            <td><?php echo Yii::t('financeModule.index','_note'); ?></td>
                            <td class="balance no_border">                            
                                <table id="t1">
                                    <tr><td colspan="2">Бонусный</td><td colspan="2" class="no_border">Личный</td></tr>
                                    <tr>
                                        <td class="bonus_money_unlock"><i class="fa fa-unlock" title="Бонусные средства"></i></td>
                                        <td class="bonus_money_lock"><i class="fa fa-lock" title="Заблокированные бонусные средства"></i> </td>
                                        <td class="personal_money_unlock"><i class="fa fa-unlock" title="Личные средства"></i></td>
                                        <td class="personal_money_lock no_border"><i class="fa fa-lock" title="Заблокированные личные средства"></i></td>
                                    </tr>                           
                                </table>                                
                            </td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="_reportList"><div style="text-align: center; padding: 10px;"><img src="/i/loading_11.gif" alt="Loading..."></div></div>                   
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function($) {
        Finance._getBlockingMoney(0);
        Finance._getGraph('income');
        Finance._getReport('/reports/_get?');
    })(jQuery);
</script>