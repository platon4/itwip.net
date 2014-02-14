<div id="block_fast">
    <h3 class="top_title">Данные и настройки</h3>
    <form id="_orderCreate" name="_orderCreate">
        <input type="hidden" name="Order[method]" value="fast">
        <input type="hidden" name="Order[data][_tid]" value="<?php echo $model->_tid; ?>">
        <div id="_orderCreateForm" style="display: none; height: 0px;"></div>
        <div id="block_1_1">
            <table style="vertical-align: top;">
                <tr>
                    <td class="info_param">
                        Найдено уникальных ссылок в твитах: <span id="_urlsCount"><?php echo $model->uCount(); ?></span> <a class="here" href="javascript:void(0);" onclick="Twitter.o.f.urls.get();">посмотреть список.</a><br />
                        <p class="small_text">Около ссылочный текст, останется введённый Вами при создании твитов.<br />
                            <strong>Совет:</strong> Для скорейшей индексации, используйте при создании твитов - #хештеги</p>
                    </td>
                </tr>
            </table>             
        </div>
        <div id="block_1_2"></div>
        <div id="block_1_3">
            <table>
                <tr>
                    <td class="info_param">Скорость индексации <i title="По окончании данного времени будет происходить проверка, если страница за указанное время не попадает в индекс яндекса средства вернутся на Ваш счёт. <br />Цена указана за одну ссылку." class="tooltip">?</i></td>
                    <td class="param">
                        <?php echo Html::dropDownList('Order[data][_time]', 12, $model->getTimeList(), ['id' => 'dropdownPrices', 'onchange' => 'Twitter.o.f.update();']); ?>
                    </td>
                </tr>
            </table>
        </div>
    </form>
    <div class="end_posting">
        <span style="float: left; padding-top: 7px; font-weight: bold">Страниц к индексации: <span id="urlsCount"><?php echo $model->uCount(); ?></span>, цена: <span id="urlsPrices"><?php echo $model->getPrices()[12]; ?></span> руб., на сумму: <span id="urlsPricesAll"><?php echo $model->uCount() * $model->getPrices()[12]; ?></span> руб.</span>
        <button onclick="Twitter.o.f.confirm(this);" class="button btn_blue" id="embedButton">Создать заказ на индексацию страниц<i class="icon-double-angle-right"></i></button>
    </div>
</div>
