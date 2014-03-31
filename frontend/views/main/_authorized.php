<?php
$this->pageTitle      =Yii::app()->name.' - '.Yii::t('main','_indexUser_Title');
$this->metaDescription=Yii::t('main','_indexUser_Description');
?>
<div class="content_right_v">
    <div class="content_right_l">
        <div style="width: 100%;" class="block big_news">
	        <div class="block_title"><div class="block_title_inset"><i style="color:#CC6300" class="fa fa-fire"></i> <h5 style="color:#CC6300">Важные новости сервиса</h5></div></div>
            <div class="block_content">
                <ul class="list_big_news">
                    <li class="tops"><span class="line"></span>
                      <div class="more_news">
                          <div><span class="date">30.03.2014</span><span class="title">Время зарабатывать! 51% дохода по партнёрской программе навсегда!</span></div>
                          <div class="text">&laquo;Не слышали о парнёрской программе нашего сервиса? Теперь самое время узнать об этом, ведь теперь можно быть в первывх рядах и зарабатывать не плохие деньги! Более подробно мы написали об этом в статье - <a href="http://community.itwip.net/novosti-servisa/hotite-nachat-zarabatyvat-yeto-legche-ch.html" target="_blank">читать</a>&raquo;</div>
                      </div>
                    </li>
                    <li><span class="line"></span>
                      <div class="more_news">
                          <div><span class="date">25.03.2014</span><span class="title">В версии сервиса 1.1.0 появились новые возможности для рекламодателя.</span></div>
                          <div class="text">&laquo;В разделе размещения твитов Вы можете встретить новые функции:<br><b>Временной таргетинг</b> - теперь Вы сами выбираете когда будут размещены твиты, и с каким интервалом. Больше контроля, больше возможностей.<br><b>Быстрая индексация с гарантией</b> - хотите проиндексировать страницы? Но надоело платить за воздух, нет проблем платите только за проиндексированные страницы. <a href="http://community.itwip.net/novosti-servisa/bystraja-indeksacija-stranic-saita-s-gar.html">Описание</a> &raquo;</div>
                      </div>
                    </li>
                    <li style="padding-bottom: 10px;"><span class="line"></span>
                      <div class="more_news">
                          <div><span class="date">25.03.2014</span><span class="title">Наш сервис готов принять в команду Администрации новых людей</span></div>
                          <div class="text">&laquo;Если Вам нравится наша идея и её воплощение, и Вы давно хотели поучаствовать в больших проектах, мы готовы дать Вам такую возможность - <a href="http://community.itwip.net/novosti-servisa/nabor-moderatorov-v-sostav-administracii.html">подробнее</a>&raquo;</div>
                      </div>
                    </li>
                </ul>
            </div>
        </div>

        <div class="block news_block"  style="width: 100%; margin-top: 20px;">
            <div class="block_title"><div class="block_title_inset"><i class="fa fa-bullhorn"></i> <h5>Последние новости из сообщества</h5></div></div>
            <div class="block_content"  style="padding: 10px 0px;">
              <?php if($this->beginCache('widget.lastTopicsMain', array('duration'=>3600))) { ?>
                 <?php $this->widget('application.widgets.lastAdminTopics'); ?>
              <?php $this->endCache(); } ?>
            </div>
        </div>
    </div>
    <div class="content_right_r">
        <div class="block online_support_block" style="width: 100%; margin: 0px 0px 20px;">
            <div class="block_title"><div class="block_title_inset"><i class="fa fa-thumbs-up"></i> <h5>Делитесь партнёрской ссылкой в социальных сетях</h5></div></div>
            <div class="block_content">
              <div style="padding: 15px 15px 0px;font-size: 11px;">В кнопки уже встроена Ваша партнёрская ссылка, поделитесь ей с друзьми ведь это дополнительный пассивный доход по партнёрской программе, не упускайте шанс забрать у сервиса 50% денег !</div>
              <div class="share42init" data-url="http://itwip.ru/{ref}" data-title="Сервис рекламы в социальных сетях, и их монетизации." data-image="/i/index/logo.png" data-zero-counter="0" style="padding: 15px 0px 15px 15px;"></div>
              <script type="text/javascript" src="/js/share42.js"></script>
            </div>
        </div>

        <?php $this->widget('application.widgets.Messages'); ?>

        <div class="block online_support_block" style="width: 100%; margin: 20px 0px 20px;">
            <div class="block_title"><div class="block_title_inset"><i class="fa fa-lightbulb-o"></i> <h5>Поддержка пользователей</h5></div></div>
            <div class="block_content">
                            <div class="block_2">
                    <div class="online_support_img"></div>
                </div>
                <div class="block_1">
                    <button class="button" onclick="window.location.href = 'http://community.itwip.net/faq/sendquest.html';" style="width: 280px;">Спросить в сообществе у пользователей</button><br>
                    <button class="button btn_blue" onclick="window.location.href = '/support';" style="margin-top: 10px;width: 280px;">Написать запрос в поддержку сервиса</button>
                </div>

            </div>
        </div>
    </div>
</div>
