<div id="accountsModeration" class="section">
    <ul class="tabs">
        <li onclick="_M._get(0);" class="current"><span class="inset">На модерации <sup id="_count_status_0" style="font-size: 11px"><?php echo (int) $counts['0']; ?></sup></span></li>
        <li onclick="_M._get(2);"><span class="inset">Не допущены <sup id="_count_status_2" style="font-size: 11px"><?php echo (int) $counts['2']; ?></sup></span></li>
        <li onclick="_M._get(1);"><span class="inset">Мало читателей <sup id="_count_status_1" style="font-size: 11px"><?php echo (int) $counts['1']; ?></sup></span></li>
        <li onclick="_M._get(1);"><span class="inset">Отключены <sup id="_count_status_1" style="font-size: 11px"><?php echo (int) $counts['1']; ?></sup></span></li>
        <li onclick="_M._get(1);"><span class="inset">Работают <sup id="_count_status_1" style="font-size: 11px"><?php echo (int) $counts['1']; ?></sup></span></li>
    </ul>
    <div class="box actived">
        <div class="box_inset">
            <div class="line_title_noopen no_border_bottom"  style="height: 18px;">
                <span style="float: right; margin-top: -6px; margin-right: 5px;" class="block group_input search"><input type="text" id="setQuery" placeholder="Найти аккаунт" onkeyup="_M._getFromQuery();"><button class="button icon" onclick="_M._getFromQuery();"><i id="_searchButton" class="fa fa-search"></i></button></span>
            </div>
            <div class="table_head">
                <div class="table_head_inside">
                    <table>
                        <tr>
                            <td class="account">Аккаунт</td>
                            <td class="status">Статус</td>
                            <td class="details">Детали</td>
                            <td class="date"><a onclick="_M._setOrder('date', this);" href="javascript:;">Добавлен <i class="fa fa-caret-down"></i></a></td>
                            <td class="level"><a onclick="_M._setOrder('itr', this);" href="javascript:;">iTR <i class="fa fa-caret-down"></i></a></td>
                            <td class="kf"><a onclick="_M._setOrder('mdr', this);" href="javascript:;">КФ <i class="fa fa-caret-down"></i></a></td>
                            <td class="kf"><a onclick="_M._setOrder('tape', this);" href="javascript:;"><span title="Активность ленты"><i class="fa fa-bullhorn"></i></span> <i class="fa fa-caret-down"></i></a></td>
                            <td class="edit no_border"></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="acconts_list">
                <table id="_accountsList">
                    <?php $this->renderPartial('_mlist', array('accounts' => $accounts)); ?>
                </table>
            </div>
            <div class="table_bottom">
                <div class="table_bottom_inside">
                    <div class="page_nav_page">
                        <div class="_cHide" id="pagesList">
                            <?php $this->renderPartial("_mpages", array('pages' => $pages)); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>