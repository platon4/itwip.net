<?php
$this->pageTitle      =Yii::app()->name.' - '.Yii::t('main','_twitterFulfilled_Title');
$this->metaDescription=Yii::t('main','_twitterFulfilled_Description');
$this->breadcrumbs[]  =array(
    0=>array(Yii::t('breadcrumbs','_twitter'),'/twitter'),
    1=>array(Yii::t('breadcrumbs','_tw_users'),''),
    2=>array(Yii::t('breadcrumbs','_tw_fulfilled'),'')
);

if($tid)
{
    ?>
<script type="text/javascript">
    Request.setID('<?php echo $tid; ?>');
</script>
    <?php
}
?>
<div id="fulfilled" class="block">
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-twitter"></i> <h5>Выполненые заказы</h5></div></div>
    <div class="block_content">
        <div class="table_head">
            <div class="table_head_inside">
                <table>
                    <tr>
                        <td class="account">Для аккаунта</td>
                        <td class="details">Детали заказа</td>						  
                        <td class="date"><a href="javascript:void(0);" onclick="Request.setOrder('date', this);">Дата выполнения <i class="fa fa-caret-down"></i></a></td>                         
                        <td class="price"><a href="javascript:void(0);" onclick="Request.setOrder('price', this);">Цена <i class="fa fa-caret-down"></i></a></td>
                        <td class="no_border icons"></td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="_listTwAccounts" class="acconts_list _cHide">	
            <?php
            $this->renderPartial('_fulfilled_rows',array(
                'rows'=>$rows,'pages'=>$pages));
            ?>
        </div>	
    </div>
</div>