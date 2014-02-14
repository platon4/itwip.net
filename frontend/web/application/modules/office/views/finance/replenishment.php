<div id="replenishment" class="block">
	<div class="block_title"><div class="block_title_inset"><i class=""></i> <h5>Пополнение баланса пользователей</h5></div></div>
	<div class="block_content">
<div class="period" style="padding-bottom: 0px;">
                    <ul id="_period">
                        <li>Период: c <input type="text" onchange="Finance._from();" class="period_date hasDatepicker" id="from" placeholder="20.11.2013"> по <input type="text" onchange="Finance._from();" id="to" placeholder="20.11.2013" class="period_date hasDatepicker"> </li>
                        <li></li>
                        <li><span class="block group_input search"><input style="width: 477px;" type="text" placeholder="Поиск платежей по id пользователя" id="setQuery" /><button class="button icon"><i class="fa fa-search"></i></button></span></li>
                    </ul>
</div>
<div class="period">
                    <ul id="_period">
                        <li>Показать за:</li>
                        <li><a onclick="Finance._setParams('20.11.2013','20.11.2013',this);" href="javascript:;" class="here select">сегодня</a></li>
                        <li><a onclick="Finance._setParams('13.11.2013','20.11.2013',this);" href="javascript:;" class="here">неделя</a></li>
                        <li><a onclick="Finance._setParams('21.10.2013','20.11.2013',this);" href="javascript:;" class="here">месяц</a></li>
                        <li><a onclick="Finance._setParams('20.11.2012','20.11.2013',this);" href="javascript:;" class="here">год</a></li>
                        <li><a onclick="Finance._setParams('all','all',this);" href="javascript:;" class="here">всё время</a></li>
                        <li></li>
                        <li>Сортировать платежи:</li>
                        <li><a class="here select">Все</a></li>
                        <li><a class="here">Robokassa</a></li>
                        <li><a class="here">WebMoney</a></li>
                        <li><a class="here">Промо-код</a></li>
                    </ul>
</div>
                       


<table class="table_style_1 " style="width: 100%;">
    <tbody>
        <tr class="title">
            <td>id</td>
            <td style="width: 105px;">Дата</td>
            <td>Пользователь</td>
            <td>Метод</td>
            <td class="balance no_border">Сумма</td>
        </tr>
        
        <tr>
            <td>3214</td>
            <td>18.11.2013 22:25</td>
            <td>Дмитрий Валерьевич [id:43]</td>
            <td>Robokassa</td>
            <td>304 руб.</td>
        </tr>
        
        <tr class="title">
            <td colspan="3"></td>
            <td class="balance no_border" style="text-align: right;">Итого:</td>
            <td class="balance no_border" style="text-align: left;">3252 руб.</td>
        </tr>
    </tbody>
</table>
	
	</div>
</div>