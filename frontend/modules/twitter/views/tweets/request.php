<?php
$this->pageTitle      =Yii::app()->name.' - '.Yii::t('main','_twitterRequest_Title');
$this->metaDescription=Yii::t('main','_twitterRequest_Description');
$this->breadcrumbs[]  =array(
    0=>array(Yii::t('breadcrumbs','_twitter'),'/twitter'),
    1=>array(Yii::t('breadcrumbs','_tw_users'),''),
    2=>array(Yii::t('breadcrumbs','_tw_request'),'')
);
?>
<div id="request" class="block">
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-twitter"></i> <h5>Заявки на рекламу</h5> <div style="float: right;"><span id="loadingIndicator" style="display: none;"><i class="fa fa-spin fa-spinner"></i></span></div></div></div>
    <div class="block_content">
        <div class="no_border_bottom" id="info_page">
            <div class="icon"><i class="fa fa-info"></i></div>
            <div class="text"><?php echo Yii::t('twitterModule.tweets','_info_page_request'); ?></div>
        </div>        
        <div class="table_head">
            <div class="table_head_inside">
                <table>
                    <tr>
                        <td class="account">Для аккаунта</td>
                        <td class="details">Детали заказа</td>						  
                        <td class="date">Дата запроса</td>                         
                        <td class="price">Цена</td>
                        <td class="no_border icons"></td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="_listTwRequest" class="acconts_list _cHide">	
            <?php $this->renderPartial('_request_rows',array('rows'=>$rows)); ?>
        </div>
    </div>
</div>
<script type="text/javascript">
    setInterval(function() {
        Request._get();
    }, 15000);
</script>