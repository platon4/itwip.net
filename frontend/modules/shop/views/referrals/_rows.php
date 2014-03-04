<?php if(count($rows)) { ?>
<table>
    <?php foreach($rows as $row)
    {
    ?>
    <tr id="<?php echo $row['id']; ?>">
        <td class="date"><?php echo date('d.m.Y',strtotime($row['_date_create'])); ?></td>
        <td class="date_last"><?php echo date('d.m.Y H:i',strtotime($row['_date_last_visit'])); ?></td>
        <td class="balance_added">~ <?php echo Finance::money(round($row['in_balance']),0,true); ?></td>
        <td class="balance_output">~ <?php echo Finance::money(round($row['out_balance']),0,true); ?></td>
        <td class="balance_my">~ <?php echo Finance::money(round($row['money_amount']),0,true); ?></td>
        <td class="balance_bonus"><?php echo Finance::money(round($row['bonus_money']),1,true); ?></td>
        <td class="add_accounts"><?php echo $row['tw_accounts']; ?></td>
        <td class="price"><?php echo Finance::money($row['_price'],1,true); ?></td>
        <td class="icon"><a class="button icon_small" title="Купить реферала" onclick="Shop.buyConfirm('<?php echo Yii::t('shopModule.index','_buy_ref_confirm',array(
    '{price}'=>Finance::money($row['_price'],1,true,array(),0,false)));
    ?>', '<?php echo $row['id']; ?>', '<?php echo $row['_price']; ?>');"><i class="fa fa-shopping-cart"></i></a></td>	
    </tr>
    <?php } ?>
</table>
<div style="padding: 7px;">
<?php $this->renderPartial('application.views.main._pages',array('pages'=>$pages,
'ajax_query'=>'Shop._getPage'));
?>
</div>
<?php } else
{ ?>
    <div style="padding: 20px; text-align: center;"> Рефералы на продажу отсутствуют. Каждый день появляются новые, заходите почаще.</div>
<?php } ?>

