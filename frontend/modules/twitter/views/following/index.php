<?php
  $this->pageTitle      =Yii::app()->name.' - '.Yii::t('main','_twitterFollowing_Title');
  $this->metaDescription=Yii::t('main','_twitterFollowing_Description');
  $this->breadcrumbs[]  =array(
      0=>array(Yii::t('breadcrumbs','_twitter'),'/twitter'),
      1=>array(Yii::t('breadcrumbs','_tw_following'),'')
  );
?>

<div class="block" id="following">
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-group"></i> <h5>Покупка фолловеров</h5></div></div>
    <div class="block_content">
        <div id="info_page">
            <div class="icon"><i class="fa fa-info"></i></div>
            <div class="text"><?php echo Yii::t('twitterModule.following', '_infoPage'); ?></div>
        </div>

        <div id="block_1">
            <h3 class="top_title">Укажите логины аккаунтов для которых необходимы читатели</h3>
            <div id="block_1_1">
            <textarea></textarea>
            <div id="block_1_1_2">В текстовом поле аккаунтов:
            <span id="postCount">0</span>
            </div>
            </div>
            <div id="block_1_2">
                <h5>Подсказки:</h5>
                <ul>
                    <li>- Логин аккаунта должен начинатся с "@" <i>(пример: @login)</i></li>
                    <li>- Каждый аккаунт пишите с новой строки</li>
                    <li>- Убедитесь, что аккаунты не заблокированны твитером</li>
                    <li>- Для получения фолловеров, не обязательно добавлять аккаунт в систему</li>
                </ul>
            </div>
        </div>

        <div>
            <h3 class="top_title">Выберите подходящий для Вас способ отбора фолловеров</h3>
            <div id="select_activity">
                <div style="padding-bottom: 10px;" class="activity">
                    <div class="select"><input type="radio" name="PlacementMethod" value="1" style="position: absolute; left: -9999px;" onchange="Tweets.PlacementMethod('manual', 'YP9f7e6ac','',this);" id="PlacementMethod_manual"><span style="display: inline-block" class="radio styler" onclick="_radioBox(this); return false;"><span></span></span></div>
                    <div class="text"><strong>Самостоятельный выбор аккаунтов и твитов для размещения</strong> -  По заданным Вами параметрам, будет выведен список подходящих аккаунтов, к каждому аккаунту Вы сможете прикрепить твит или несколько из подготовленного ранее списка, или он добавится автоматически при выборе аккаунта. После создания заказа, твиты разместятся автоматически.</div>
                </div>
                <div class="activity">
                    <div class="select"><input type="radio" name="PlacementMethod" value="1" style="position: absolute; left: -9999px;" onchange="Tweets.PlacementMethod('fast', 'YP9f7e6ac','',this);" id="PlacementMethod_fast"><span style="display: inline-block" class="radio styler" onclick="_radioBox(this); return false;"><span></span></span></div>
                    <div class="text"><b>Быстрая индексация страниц сайта в Яндекс, с гарантией</b> - Оптимальный выбор если Вы хотите проиндексировать свой сайт быстро и с гарантией, затратив на это минимум сил и времени. По окончании заданного времени происходит проверка всех ссылок на индексацию, если страницы не попадают в индекс яндекса, потраченные средства за них вернутся на Ваш личный счёт.</div>
                </div>
            </div>
        </div>
        <div>
            Выводим способ
        </div>


    </div>
</div>