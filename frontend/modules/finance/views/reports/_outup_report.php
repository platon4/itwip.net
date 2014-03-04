<table style="width: 100%;" class="table_style_1">
    <?php
    if(count($logs))
    {
        ?>
        <?php
        foreach($logs as $row)
        {
            ?>
            <tr>
                <td style="width:125px;"><?php echo date('d.m.Y',strtotime($row['_date'])).' '.$row['_time']; ?></td>
                <td style="width:125px;"><?php echo ($row['_date_execute'] > date('Y-m-d H:i:s',strtotime('2000-12-1 00:00:00')))?date('d.m.Y H:i:s',strtotime($row['_date_execute'])):''; ?></td>
                <td>
                    <?php 
                        switch($row['_status'])
                        {
                            case 2:
                                 echo '<span style="color: #316D00;">Выплачено</span>';
                                break;
                            
                            case 3:
                                echo '<span style="color: #7E1300;">Ошибка вывода</span>';
                                break;
                            
                            default:
                                echo '<span style="color: #FFA701;">Обрабатываеться</span>';
                        }
                    ?>
                </td>
                <td style="width: 77px;"><?php echo Finance::money($row['amount'],false,true); ?></td>
                <td style="width: 77px;"><?php echo ($row['_status']==3)?'-':Finance::money($row['_commission'],false,true); ?></td>
                <td style="width: 77px;"><?php echo ($row['_status']==3)?'-':Finance::money($row['_out'],false,true); ?></td>
            </tr>
        <?php } ?>
        <?php
    } else
    {
        ?>
        <tr><td colspan="7" style="text-align:center;"><?php echo Yii::t('financeModule.index','_no_operation'); ?></td></tr>
    <?php } ?>
    <tr class="title"><td class="no_border" colspan="4" style="text-align: left;"><?php
            $this->renderPartial('application.views.main._pages',array(
                'ajax_query'=>'Finance._getPage','pages'=>$pages));
            ?></td><td style="text-align:right;"><?php echo Yii::t('financeModule.index','_total'); ?></td><td style="text-align:left; width: 77px;" class="no_border"><?php echo Finance::money($all['money'],false,true); ?></td></tr>
</table>