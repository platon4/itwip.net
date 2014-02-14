<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yii::t('main', '_status_orders_Title');
$this->metaDescription = Yii::t('main', '_status_orders_Description');
$this->breadcrumbs[] = array(
	0 => array(Yii::t('breadcrumbs', '_twitter'), '/twitter'),
	1 => array(Yii::t('breadcrumbs', '_tw_advertiser'), ''),
	2 => array(Yii::t('breadcrumbs', '_tw_status_orders'), '')
);
?>
<!--
<div class="line_info alert" style="margin-bottom: 20px;">
    <b>Внимание.</b> Проходят работы на стороне сервера, размещение твитов временно приостановлено. Созданные заказы будут размещены позже. Стараемся стать лучше и быстрее для Вас.  
</div>
-->
<div id="status_orders" class="block">
	<div class="block_title"><div class="block_title_inset"><h5>Статусы и процесс выполнения заказов</h5></div></div>
	<div class="block_content">
    <div class="no_border_bottom" id="info_page">
    	<div class="icon"><i class="fa fa-info"></i></div>
    	<div class="text">Страница для управления вашими рекламными заказами - оплата, удаление. Для просмотра подробностей кликните на кнопку деталей. При удалении заказа, не потраченная сумма "Остаток" возвращается Вам на баланс.</div>
    </div>
    <div class="table_head">
        <div class="table_head_inside">
            <table>
                <tr>
                    <td class="id">ID</td>
                    <td class="view_orders">Вид рекламы</td>
                    <td class="date">Дата создания</td>
                    <td class="status">Статус</td>
                    <td class="rate_balance">Баланс</td>
                    <td class="progress_order">Процесс выполнения</td>
                    <td class="no_border icons"></td>
                </tr>
            </table>
        </div>
    </div>
    <div class="acconts_list" id="_orderList">
        <?php echo $this->renderPartial('status/_indexRows', array('model' => $model)); ?>
    </div>
    </div>
</div>