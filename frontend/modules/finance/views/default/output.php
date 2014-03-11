<?php
$this->pageTitle      =Yii::app()->name.' - '.Yii::t('main','_output_Title');
$this->metaDescription=Yii::t('main','_output_Description');
$this->breadcrumbs[]  =array(
    0=>array(Yii::t('breadcrumbs','_accounts'),''),
    1=>array(Yii::t('breadcrumbs','_finance'),'/finance'),
    2=>array(Yii::t('breadcrumbs','_finance_output'),'')
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
<div class="line_info alert" style="margin-bottom: 20px;">Выплаты временно не доступны. Приносим извенения за неудобства.</div>

<div id="output">
    <div class="table">
        <div class="td">
            <div class="block output">
                <div class="block_title"><div class="block_title_inset"><i class="fa fa-usd"></i> <h5><?php echo Yii::t('financeModule.index','_withdraw_funds'); ?></h5></div></div>
                <div class="block_content">
                    <div id="info_page">
                        <div class="icon"><i class="fa fa-info"></i></div>
                        <div class="text"><?php
                            echo Yii::t('financeModule.index','_withdraw_funds_info',array(
                                '{precent}'=>LoyaltyHelper::_getPrecent('finance')));
                            ?></div>
                    </div>
                    <div id="block_1">
                        <form id="_withdraw">
                            <table>
                                <tr>
                                    <td><?php echo Yii::t('financeModule.index','_purse_for_translation'); ?> </td><td><input style="width: 110px;" type="text" disabled="disabled" value="<?php echo Yii::app()->user->_setting('purse'); ?>"/> <a class="edit" href="/accounts/settings" return false;"><i class="fa fa-pencil" title="" ></i></a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo Yii::t('financeModule.index','_amount_of_write-offs'); ?> (<a class="here" style="font-size: 11px" href="javascript:void(0);" onclick="insertMoney();"><?php echo Yii::t('financeModule.index','_all'); ?> <?php echo CMoney::_c(Yii::app()->user->_get('money_amount'),true); ?></a>): </td><td><input id="_amount" type="text" name="_o[_amount]" onkeyup="this.value = this.value.replace(/[^\d\.]/g, '')" value="0"> руб.</td>
                                </tr>
                                <tr>
                                    <td><?php
                                        echo Yii::t('financeModule.index','_topup_amount',array(
                                            '{precent}'=>LoyaltyHelper::_getPrecent('finance')));
                                        ?> </td><td><input type="text" id="_pb" name="_o[_pb]" value="0" onkeyup="this.value = this.value.replace(/[^\d\.]/g, '')"/> руб.</td></tr>
                                <tr>
                                    <td  style="width: 270px;"><?php echo Yii::t('financeModule.index','_credit_account_out'); ?> </td><td><input type="text" id="_cb" name="_o[_cb]" value="<?php echo CMoney::_c(Yii::app()->user->_get('money_amount')); ?>" onkeyup="this.value = this.value.replace(/[^\d\.]/g, '')"/> руб.</td>
                                </tr>
                                <tr id="_msg" style="display: none;"><td colspan="2" style="padding-top: 20px;"><div class="line_info ok"><?php echo Yii::t('financeModule.index','_your_balance_ok'); ?></div></td></tr>
                            </table>
                        </form>
                    </div>
                    <div class="block_bottom">
                        <button id="_submitPay" class="button btn_green" onclick="Finance.withdraw(this);" disabled="disabled"><?php echo Yii::t('financeModule.index','_withdraw_funds_button'); ?></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="td">
            <div class="block auto_output">
                <div class="block_title"><div class="block_title_inset"><i class="icon-wrench"></i> <h5><?php echo Yii::t('financeModule.index','_settings_auto_eject'); ?></h5></div></div>
                <div class="block_content">
                    <div id="info_page">
                        <div class="icon"><i class="icon-info"></i></div>
                        <div class="text"><?php echo Yii::t('financeModule.index','_settings_auto_eject_info'); ?></div>
                    </div>
                    <div id="block_1">
                        <form id="_autoEjectForm">
                            <?php echo Yii::t('financeModule.index','_withdraw_accumulation'); ?> <?php
                            echo Html::textField('autoEject','',array(
                                'onkeyup'=>'this.value = this.value.replace(/\D+/, \'\');'));
                            ?> руб.
                        </form>
                        <div id="_autoEjectMessage"></div>
                    </div>
                    <div class="block_bottom">
                        <button class="button" onclick="Finance._ejectSave(this);"><?php echo Yii::t('financeModule.index','_save'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="block output_stats">
        <div class="block_title"><div class="block_title_inset"><i class="icon-reorder"></i> <h5><?php echo Yii::t('financeModule.index','_list_applications_withdrawal'); ?></h5></div></div>
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
                    <tr class="title"><td width="125px"><a href="javascript:;" onclick="Finance._setOrder('date', this);"><?php echo Yii::t('financeModule.index','_date_created'); ?> <i class="fa fa-caret-down"></i></a></td><td width="125px"><?php echo Yii::t('financeModule.index','_date_performance'); ?></td><td><?php echo Yii::t('financeModule.index','_status'); ?></td><td style="width: 77px;"><a href="javascript:;" onclick="Finance._setOrder('amount', this);"><?php echo Yii::t('financeModule.index','_amount'); ?> <i class="fa fa-caret-down"></i></a></td><td style="width: 77px;"><?php echo Yii::t('financeModule.index','_commission'); ?></td><td style="width: 77px;" class="no_border"> <?php echo Yii::t('financeModule.index','_derived'); ?></td></tr>
                </tbody>
            </table>
            <div id="_reportList"><div style="text-align: center; padding: 10px;"><img src="/i/loading_11.gif" alt="Loading..."></div></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var _c = parseFloat('<?php echo Yii::app()->user->_get('money_amount'); ?>'), _p = parseFloat('<?php echo LoyaltyHelper::_getPrecent('finance'); ?>'), _r = 0, _pc = 0;

    $(function() {
        $('#_amount').on("keyup", function() {
            if (trim($(this).val()) == '' || parseFloat($(this).val()) < 0)
                return false;

            _r = parseFloat($(this).val());
            _pc = round((_r * _p) / 100, 2);

            if (_r > _c || trim($(this).val()) == '.') {
                $(this).addClass('error_input');
                return false;
            } else
                $(this).removeClass('error_input');

            $('#_pb').val(round(_r - _pc, 2));
            $('#_cb').val(round(_c - _r, 2));

            $('#_pb').removeClass('error_input');
            $('#_cb').removeClass('error_input');

            $('#_amount').change();
        });

        $('#_pb').on("keyup", function() {
            if (trim($(this).val()) == '' || $(this).val() < 0)
                return false;

            _r = parseFloat($(this).val());
            _pc = round((_r * _p) / 100, 2);

            if ((_c - (_r + _pc)) >= 0)
            {
                $(this).removeClass('error_input');
                $('#_amount').val(round(_r + _pc, 2));
                $('#_cb').val(round(_c - (_r + _pc), 2));
            }
            else
                $(this).addClass('error_input');

            $('#_amount').change();
        });

        $('#_cb').on("keyup", function() {
            if (trim($(this).val()) == '' || $(this).val() < 0)
                return false;

            _r = parseFloat($(this).val());
            _pr = _r - _c;

            if (_r <= _c)
            {
                $('#_amount').val(round(_c - _r,2));
                $('#_amount').keyup();

                $(this).removeClass('error_input');
            }
            else
                $(this).addClass('error_input');

            $('#_amount').change();
        });

        $('#_amount').on('change', function()
        {
            if ($(this).val() > 0)
                $('#_submitPay').prop('disabled', false);
            else
                $('#_submitPay').prop('disabled', true);
        })
    });

    function insertMoney()
    {
        $('#_amount').val(parseFloat('<?php echo Yii::app()->user->_get('money_amount'); ?>'));
        $('#_amount').keyup();
    }

    Finance._getReport('/reports/_get?act=out&');
</script>