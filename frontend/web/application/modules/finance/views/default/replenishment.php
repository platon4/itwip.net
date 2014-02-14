<?php
    $this->pageTitle      =Yii::app()->name.' - '.Yii::t('main','_recharge_Title');
    $this->metaDescription=Yii::t('main','_recharge_Description');
    $this->breadcrumbs[]=array(
        0=>array(Yii::t('breadcrumbs','_accounts'),''),
        1=>array(Yii::t('breadcrumbs','_finance'),'/finance'),
        2=>array(Yii::t('breadcrumbs','_finance_replenishment'),'')
    );
?>
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
        $('#to').datepicker({
            defaultDate: "+1w",
            changeMonth: true,
            numberOfMonths: 1,
            onClose: function(selectedDate) {
                $("#from").datepicker("option", "maxDate", selectedDate);
            }
        });
    });
</script>
<div id="replenishment">
    <div class="table">
        <div class="td">
            <div class="block replenishment">
                <?php echo Html::beginForm(); ?>
                <div class="block_title"><div class="block_title_inset"><i class="fa fa-rub"></i> <h5><?php echo Yii::t('financeModule.index','_recharge'); ?></h5></div></div>
                <div class="block_content">
                    <div id="info_page">
                        <div class="icon"><i class="fa fa-info"></i></div>
                        <div class="text"><?php echo Yii::t('financeModule.index','_recharge_info',array('{precent}'=>LoyaltyHelper::_getPrecent('finance'))); ?></div>
                    </div>
                    <div id="block_1">
                        <ul>
                            <li class="webmoney">
                                <label>
                                    <img src="/i/elements/webmoney-blue.png"   style="margin-top: 0px; margin-bottom: -3px;" /> <?php echo Yii::t('financeModule.index','_webmoney'); ?> <?php echo Html::activeRadioButton($form,'_system',array(
                    'uncheckValue'=>null,'value'=>1));
                ?>
                                </label>
                            </li>
                            <li class="robokassa no_padding">
                                <label>
                                    <img src="/i/elements/robo.png"  style="margin-top: 0px; margin-bottom: -3px;" /> <?php echo Yii::t('financeModule.index','_robokassa'); ?> <?php echo Html::activeRadioButton($form,'_system',array(
                                        'uncheckValue'=>null,'value'=>2));
                ?>
                                </label>
                            </li>
                        </ul>
                    </div>
                    <div id="block_2">
                        <table>
                            <tr><td><?php echo Yii::t('financeModule.index','_top-up_amount'); ?> </td><td><input id="_amount" type="text" name="Replenishment[amount]" onkeyup="this.value = this.value.replace(/[^\d\.]/g, '')" value=""/> р.</td></tr>
                            <tr><td><?php echo Yii::t('financeModule.index','_credit_account'); ?> </td><td><input id="_pb" type="text" onkeyup="this.value = this.value.replace(/[^\d\.]/g, '')" name="Replenishment[_pb]" value="" /> р.</td></tr>
                            <tr><td  style="width: 280px;"><?php echo Yii::t('financeModule.index','_your_balance'); ?> </td><td><input id="_cb" type="text" onkeyup="this.value = this.value.replace(/[^\d\.]/g, '')" name="Replenishment[_cb]" value="<?php echo Yii::app()->user->_get('money_amount'); ?>" placeholder="<?php echo Yii::app()->user->_get('money_amount'); ?>" /> р.</td></tr>
                        </table>
                            <?php if(count($form->getErrors()))
                            {
                                ?>
                            <div class="line_info alert">
    <?php echo CHtml::errorSummary($form); ?>
                            </div>
<?php } ?>
                    </div>
                    <div class="block_bottom">
                        <button id="_submitPay" type="submit" class="button btn_green"><?php echo Yii::t('financeModule.index','_top-up_b'); ?></button>
                    </div>
                </div>
<?php echo Html::endForm(); ?>
            </div>
        </div>
        <div class="td">
            <div class="block promo">
                <div class="block_title"><div class="block_title_inset"><i class="fa fa-key"></i> <h5><?php echo Yii::t('financeModule.index','_promo_code_title'); ?></h5></div></div>
                <div class="block_content">
                    <div id="info_page">
                        <div class="icon"><i class="fa fa-info"></i></div>
                        <div class="text"><?php echo Yii::t('financeModule.index','_promo_code_info'); ?></div>
                    </div>
                    <div id="block_1">
<?php echo Yii::t('financeModule.index','_enter_the_code'); ?> <input id="_promoCode" type="text" name="_promoCode" value="" />
                        <div id="promoMessage"></div>
                    </div>
                    <div class="block_bottom">
                        <button  onclick="Finance.promoUse('_promoCode', this);" class="button"><?php echo Yii::t('financeModule.index','_use'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="block replenishment_stats">
        <div class="block_title"><div class="block_title_inset"><i class="fa fa-th-list"></i> <h5><?php echo Yii::t('financeModule.index','_list_of_top-up'); ?></h5></div></div>
        <div class="block_content">
            <div class="period">
                <ul id="_period">
                    <li><?php echo Yii::t('financeModule.index','_show_for'); ?></li>
                    <li><a class="here select" href="javascript:;" onclick="Finance._setParams('<?php echo date('d.m.Y'); ?>', '<?php echo date('d.m.Y'); ?>', this);"><?php echo Yii::t('financeModule.index','_today'); ?></a></li>
                    <li><a class="here" href="javascript:;" onclick="Finance._setParams('<?php echo date('d.m.Y',time() - (7 * 86400)); ?>', '<?php echo date('d.m.Y'); ?>', this);"><?php echo Yii::t('financeModule.index','_week'); ?></a></li>
                    <li><a class="here" href="javascript:;" onclick="Finance._setParams('<?php echo date('d.m.Y',time() - (30 * 86400)); ?>', '<?php echo date('d.m.Y'); ?>', this);"><?php echo Yii::t('financeModule.index','_month'); ?></a></li>
                    <li><a class="here" href="javascript:;" onclick="Finance._setParams('<?php echo date('d.m.Y',time() - (365 * 86400)); ?>', '<?php echo date('d.m.Y'); ?>', this);"><?php echo Yii::t('financeModule.index','_year'); ?></a></li>
                    <li><a class="here" href="javascript:;" onclick="Finance._setParams('all', 'all', this);"><?php echo Yii::t('financeModule.index','_all_time'); ?></a></li>
                    <li></li>
                    <li><?php echo Yii::t('financeModule.index','_period'); ?> <?php echo Yii::t('financeModule.index','_in'); ?> <input type="text" placeholder="<?php echo date("d.m.Y"); ?>" id="from" class="period_date" onchange="Finance._from();"> <?php echo Yii::t('financeModule.index','_by'); ?> <input type="text" class="period_date"  placeholder="<?php echo date("d.m.Y"); ?>" id="to" onchange="Finance._from();"> </li>
                </ul>
            </div>
            <table style="width: 100%;" class="table_style_1">
                <tbody>
                    <tr class="title"><td width="65px"><a href="javascript:;" onclick="Finance._setOrder('date', this);"><?php echo Yii::t('financeModule.index','_date'); ?> <i class="fa fa-caret-down"></i></a></td><td width="40px"><?php echo Yii::t('financeModule.index','_time'); ?></td><td><?php echo Yii::t('financeModule.index','_method_funding'); ?></td><td style="width: 77px;"><a href="javascript:;" onclick="Finance._setOrder('amount', this);"><?php echo Yii::t('financeModule.index','_amount'); ?> <i class="fa fa-caret-down"></i></a></td><td style="width: 77px;"><?php echo Yii::t('financeModule.index','_commission'); ?></td><td style="width: 77px;" class="no_border"><?php echo Yii::t('financeModule.index','_admitted'); ?></td></tr>
                </tbody>
            </table>
            <div id="_reportList">
<?php $this->renderPartial('_report_list',array('logs'=>$logs)); ?>
            </div>
            <table style="width: 100%;" class="table_style_1">
                <tbody>
                    <tr class="title">
                        <td class="_loading no_border" style="width:77%; text-align:left;" id="_pages">
<?php $this->renderPartial('application.views.main._pages',array(
    'ajax_query'=>'Finance._getPage','pages'=>$pages)); ?>	
                        </td>
                        <td style="text-align:right; width: 80px;"><?php echo Yii::t('financeModule.index','_total'); ?></td>
                        <td style="text-align:right; width: 75px;" class="no_border _loading" id="_allAmount"><?php echo CMoney::_c($total_amount,true); ?></td>
                    </tr>		
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    var _c = parseFloat('<?php echo Yii::app()->user->_get('money_amount'); ?>'), _p = parseFloat('<?php echo LoyaltyHelper::_getPrecent('finance'); ?>'), _r = 0, _pc = 0;

    $(function() {
        $('#_amount').on("keyup", function() {
            if(trim($(this).val())=='') return false;
            
            _r = parseFloat($(this).val());
            _pc = round((_r * _p) / 100, 2);

            $('#_pb').val(round(_r - _pc, 2));
            $('#_cb').val(round(_c + (_r - _pc), 2));
        });

        $('#_pb').on("keyup", function() {
            if(trim($(this).val())=='') return false;
            
            _r = parseFloat($(this).val());
            _pc = round((_r * _p) / 100, 2);

            $('#_amount').val(round(_r + _pc, 2));
            $('#_cb').val(round(_r + _c, 2));
        });

        $('#_cb').on("keyup", function() {
            if(trim($(this).val())=='') return false;
            
            _r = parseFloat($(this).val());
            _pr = _r - _c;

            if (_r > _c)
            {
                $('#_amount').val(round((_pr / 85) * 100, 2));
                $('#_pb').val(round(_r - _c, 2));

                $(this).removeClass('error_input');
                $('#_submitPay').prop('disabled', false);
            }
            else {
                $(this).addClass('error_input');
                $('#_submitPay').prop('disabled', true);
            }
        });
    });
</script>