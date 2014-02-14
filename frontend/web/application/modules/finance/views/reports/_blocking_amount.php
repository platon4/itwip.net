<table style="width: 100%;" class="table_style_1">
    <tr class="title"><td><?php echo Yii::t('financeModule.index','_note'); ?></td><td style="width: 77px;" class="no_border"><?php echo Yii::t('financeModule.index','_amount'); ?></td></tr>
    <?php if(count($logs)) { ?>
          <?php foreach($logs as $row)
            { ?>
    <tr><td><?php echo Finance::_getLogNote($row['_type'],$row['_for'],$row['_id'],$row['_notice']); ?></td><td><?php echo Finance::money($row['amount'],$row['_money_type'],true); ?></td></tr>
        <?php } ?>
        <tr class="title"><td colspan="2" style="text-align: left;"><?php $this->renderPartial('application.views.main._pages',array('ajax_query'=>'Finance._getBlockingPage','pages'=>$pages)); ?></td></tr>
    <?php } else { ?> 
        <tr><td colspan="2" style="text-align:center;"><?php echo Yii::t('financeModule.index','_blocked_no'); ?></td></tr>
    <?php } ?>
</table>