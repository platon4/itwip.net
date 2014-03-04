<table style="width: 100%;" class="table_style_1">
    <?php
    if(count($logs))
    {
        ?>
        <?php
        foreach($logs as $log)
        {
            $date=strtotime($log['_date']);
            $time=strtotime($log['_time']);

            $blocked_bonus='';
            $bonus        ='';
            $blocked_money='';
            $money        ='';

            if($log['amount_type'] == 1)
            {
                if($log['_transfer'])
                {
                    if($log['_transfer'] == 3)
                    {
                        $blocked_bonus='-'.Finance::money($log['_amount'],1,true);
                    } else
                    {
                        $blocked_bonus=($log['_transfer'] == 2)?'-'.Finance::money($log['_amount'],1,true):'+'.Finance::money($log['_amount'],1,true);
                        $bonus        =($log['_transfer'] == 1)?'-'.Finance::money($log['_amount'],1,true):'+'.Finance::money($log['_amount'],1,true);
                    }
                } else
                {
                    if($log['_type'] == 1)
                    {
                        if($log['is_blocked'])
                            $blocked_bonus='-'.Finance::money($log['_amount'],1,true);
                        else
                            $bonus        ='-'.Finance::money($log['_amount'],1,true);
                    } else
                    {
                        if($log['is_blocked'])
                            $blocked_bonus='+'.Finance::money($log['_amount'],1,true);
                        else
                            $bonus        ='+'.Finance::money($log['_amount'],1,true);
                    }
                }
            }
            else
            {
                if($log['_transfer'])
                {
                    if($log['_transfer'] == 3)
                    {
                        $blocked_money='-'.Finance::money($log['_amount'],0,true);
                    } else
                    {
                        $blocked_money=($log['_transfer'] == 2)?'-'.Finance::money($log['_amount'],0,true):'+'.Finance::money($log['_amount'],0,true);
                        $money        =($log['_transfer'] == 1)?'-'.Finance::money($log['_amount'],0,true):'+'.Finance::money($log['_amount'],0,true);
                    }
                } else
                {
                    if($log['_type'] == 1)
                    {
                        if($log['is_blocked'])
                            $blocked_money='-'.Finance::money($log['_amount'],0,true);
                        else
                            $money        ='-'.Finance::money($log['_amount'],0,true);
                    } else
                    {
                        if($log['is_blocked'])
                            $blocked_money='+'.Finance::money($log['_amount'],0,true);
                        else
                            $money        ='+'.Finance::money($log['_amount'],0,true);
                    }
                }
            }
            ?>
            <tr>
                <td class="date"><?php echo date("d.m.Y",$date); ?> <?php echo date("H:i",$time); ?></td>
                <td class="note"><?php echo Finance::_getLogNote($log['_type'],$log['_system'],$log['order_id'],$log['_notice']); ?></td>
                <td class="bonus_money_unlock"><?php echo $bonus; ?></td>
                <td class="bonus_money_lock"><?php echo $blocked_bonus; ?></td>
                <td class="personal_money_unlock"><?php echo $money; ?></td>
                <td class="personal_money_lock"><?php echo $blocked_money; ?></td>
            </tr>
        <?php } ?>

        <tr class="title">
            <td colspan="2"><div style="float: left;"><?php
                    $this->renderPartial('application.views.main._pages',array(
                        'ajax_query'=>'Finance._getPage','pages'=>$pages));
                    ?></div><div style="float: right;"><?php echo Yii::t('financeModule.index','_total'); ?></div></td>
            <td style="text-align:left;"><?php echo Finance::money($all['bonus'],1,true); ?></td>
            <td style="text-align:left;"><?php echo Finance::money($all['blocked_bonus'],1,true); ?></td>
            <td style="text-align:left;"><?php echo Finance::money($all['money'],0,true); ?></td>
            <td class="no_border"  style="text-align:left;"><?php echo Finance::money($all['blocked_money'],0,true); ?></td>
        </tr> 
        <?php
    } else
    {
        ?>
        <tr><td colspan="5" style="text-align:center;"><?php echo Yii::t('financeModule.index','_no_operation'); ?></td></tr>
        <?php } ?>
</table>