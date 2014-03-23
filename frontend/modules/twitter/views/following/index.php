<?php
  $this->pageTitle      =Yii::app()->name.' - '.Yii::t('main','_twitterFollowing_Title');
  $this->metaDescription=Yii::t('main','_twitterFollowing_Description');
  $this->breadcrumbs[]  =array(
      0=>array(Yii::t('breadcrumbs','_twitter'),'/twitter'),
      1=>array(Yii::t('breadcrumbs','_tw_following'),'')
  );
?>

<div class="block" id="following">
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-group"></i> <h5>Покупка читателей</h5></div></div>
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

        <div id="block_2">
            <h3 class="top_title">Выберите подходящий для Вас способ отбора читателей</h3>
            <div id="select_activity">
                <div style="padding-bottom: 10px;" class="activity">
                    <div class="select"><?php echo Html::radioButton('test','1'); ?></div>
                    <div class="text"><b>Самостоятельный выбор аккаунтов</b> -  По заданным Вами параметрам, будет выведен список подходящих аккаунтов и Вы сможете определить подписчиков отметив их галочками. После создания заказа, фолловеры подпишутся автоматически.</div>
                </div>
                <div class="activity">
                    <div class="select"><?php echo Html::radioButton('test','2'); ?></div>
                    <div class="text"><b>Автоматический подбор аккаунтов по параметрам</b> - Укажите необходимые параметры и аккаунты будут выбраны и подписаны автоматически системой.</div>
                </div>
            </div>
        </div>

        <div id="block_3" >
            <div id="block_auto">
                <div id="block_auto_1">
                    <h3 class="top_title">Параметры подбора читателей</h3>
                        <table>
                            <tr>
                            <td class="info_param"><span title="Цена за один подписанный аккаунт">Цена за подписку</span></td>
                            <td class="param input">
                              <select id="Twitter_bw" name="Twitter[bw]">
                                <option value="0">0,10 коп.</option>
                                <option value="1">0,20 коп.</option>
                                <option value="1">0,30 коп.</option>
                                <option value="1">0,40 коп.</option>
                                <option value="1">0,50 коп.</option>
                              </select>
                            </td>
                            </tr>
                            <tr>
                              <td class="info" colspan="2">
                                  <div>
                                      <span><b>Примерные параметры аккаунтов для этой цены:</b></span>
                                      <span>Читателей: от 500 до 1000</span>
                                      <span>iTR: от 1 до 3</span>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <td class="info_param">В ленте пишут </td>
                              <td class="param input">
                                <select id="Twitter_bw" name="Twitter[bw]">
                                  <option value="0">Бот</option>
                                  <option value="1">Человек и бот (+1р.)</option>
                                  <option value="2">Человек (+3р.)</option>
                                </select>
                              </td>
                            </tr>
                            <tr><td class="info_param">Тематика блога</td> <td class="param input">значение</td></tr>
                            <tr><td class="info_param">Возраст блогера </td> <td class="param input">значение</td></tr>
                            <tr><td class="info_param">Пол блогера </td> <td class="param input">значение</td></tr>
                        </table>
                    <h3 class="top_title">Личная фильтрация аккаунтов</h3>
                    <table>
                      <tr>
                          <td class="info_param">Черно-белый список</td>
                          <td class="param">
                              <select id="Twitter_bw" name="Twitter[bw]">
                                <option value="0">Не важно</option>
                                <option value="1">Показать только из белого списка</option>
                                <option value="2">Не показывать из чёрного списка</option>
                              </select>
                          </td>
                      </tr>
                    </table>
                </div>
                <div id="block_auto_line"></div>
                <div id="block_auto_2">
                    <h3 class="top_title">Параметры подписки читателей</h3>
                        <table>
                            <tr><td class="info_param">Необходимо читателей на аккаунт</td><td class="param input">значение</td></tr>
                            <tr><td class="info_param">Подписовать с интервалом в</td><td class="param input">значение</td></tr>
                            <tr><td class="info_param">Но не больше N в день</td><td class="param input">значение</td></tr>
                        </table>
                </div>
            </div>
        </div>


    </div>
</div>