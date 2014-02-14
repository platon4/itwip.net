<?php
$this->pageTitle      =Yii::app()->name.' - '.Yii::t('main','_indexUser_Title');
$this->metaDescription=Yii::t('main','_indexUser_Description');
?>
<!--
<div class="line_info alert" style="margin-bottom: 20px;">
    <b>Внимание.</b> Проходят работы на стороне сервера, размещение твитов временно приостановлено. Созданные заказы будут размещены позже. Возможны кратковременные отключения сайта. Стараемся стать лучше и быстрее для Вас. 
</div>
-->

<div class="content_right_v">
    <div class="content_right_l"> 
    <?php $this->widget('application.widgets.Messages'); ?>    
    </div>
    <div class="content_right_r">
        <div class="block online_support_block" style="width: 100%; margin: 0px 0px 20px;">
            <div class="block_title"><div class="block_title_inset"><i class="fa fa-lightbulb-o"></i> <h5>Поддержка пользователей</h5></div></div>
            <div class="block_content">
                <div class="block_1">
                    <p class="shadow" style="margin-bottom: 10px;">Задайте интересущий Вас вопрос!</p>
                    <button class="button btn_blue" onclick="window.location.href = '/support';">Создать запрос</button> <button class="button" onclick="window.location.href = 'http://community.itwip.net/faq';">Спросить в сообществе</button>
                </div>
                <div class="block_2">
                    <div class="online_support_img"></div>
                </div>
            </div>
        </div>
        <div class="block news_block" style="width: 100%;">
            <div class="block_title"><div class="block_title_inset"><i class="fa fa-bullhorn"></i> <h5>Последние новости сервиса</h5></div></div>
            <div class="block_content"  style="padding: 10px 0px;">
              <?php if($this->beginCache('widget.lastTopicsMain', array('duration'=>3600))) { ?>
                 <?php $this->widget('application.widgets.lastAdminTopics'); ?>
              <?php $this->endCache(); } ?>
            </div>
        </div>
    </div>
</div>
