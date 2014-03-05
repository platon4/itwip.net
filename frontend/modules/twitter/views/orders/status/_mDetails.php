<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_tw_status_orders_details_Title');
$this->metaDescription = Yii::t('main', '_tw_status_orders_details_Description');
$this->breadcrumbs[] = array(
	0 => array(Yii::t('breadcrumbs', '_twitter'), '/twitter'),
	1 => array(Yii::t('breadcrumbs', '_tw_advertiser'), ''),
	2 => array(Yii::t('breadcrumbs', '_tw_status_orders'), '/twitter/orders/status'),
	3 => array(Yii::t('breadcrumbs', '_tw_status_orders_details'), '')
);
?>
<script>
	Twitter.o.g.set({"hash": "<?php echo Html::encode($model->m->h); ?>","t":"manual"});
</script>
<div id="details_orders" class="block">
    <div class="block_title"><div class="block_title_inset"><i class=""></i> <h5>Детали заказа (ID <?php echo $model->m->getOrder()['id']; ?>)</h5></div></div>
    <div class="block_content">
        <div class="table_head">
            <div class="table_head_inside">
                <table>
                    <tr>
                        <td class="account">Аккаунт</td>
                        <td class="tweet">Твит</td>
                        <td class="status"><a href="javascript: void(0);" onclick="Twitter.o.g.setOrder('status', this);">Статус <i class="fa fa-caret-down"></i></a></td>
                        <td class="price">Цена</td>
                        <td class="icon"></td>
                    </tr>
                </table>
            </div>
        </div>
        <div class="acconts_list" id="_orderList">
            <?php $this->renderPartial('status/_mDetailsRows', ['model' => $model]); ?>
        </div>
    </div>
</div>