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
        <div class="block" style="width: 100%; margin-bottom: 20px;">
            <div class="block_title"><div class="block_title_inset"><h5>Поздравляем с наступающим новым годом!</h5></div></div>
            <div class="block_content"  style="padding: 10px 0px;position: relative">
                <a href="/" style="display: block;width: 100%;height: 100%;position: absolute;top: 0;left: 0;"></a>
                <div class="table">
                    <div class="td" style="width: 180px">
                        <object type="application/x-shockwave-flash" data="http://xflash.ucoz.ru/slider/620.swf" width="170" height="200">
                            <param name="bgcolor" value="#ffffff" />
                            <param name="allowFullScreen" value="true" />
                            <param name="allowScriptAccess" value="always" />
                            <param name="wmode" value="transparent" />
                            <param name="movie" value="" />  
                            <param name="flashvars" value="st=;file=" />
                            <param name="link" value="false" />
                        </object>                     
                    </div>
                    <div class="td" style="padding-top: 25px;">
                        Для всех без исключения пользователей, мы подготовили небольшой но довольно приятный подарок!<br /><br /> До 21 января 2014г. Ваш доход будет 70% с приглашённого Вами реферала! <br /><br />От всей нашей дружной команды, желаем Вам успехов и больших заработков в новом году !!!
                    </div>
                </div>
               
            </div>
        </div> 
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
