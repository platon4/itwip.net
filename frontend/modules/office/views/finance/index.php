<?php //print_r($finance); ?>

<div class="block" id="">
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-rub"></i> <h5>Цыфорки</h5></div></div>
    <div class="block_content">
        <style>
            .em {list-style: none; padding: 10px;}
            .em li {padding: 5px;}
        </style>	 
        <ul class="em">
            <li><strong>Пополнения на WebMoney со статусом "is_pay 1"</strong>: <?php echo round($finance['webmoney']['_all'],2); echo " / "; echo round($finance['webmoney']['to_balance'],2); ?></li>
            <li>Наш доход от пополнений: <?php echo $wmd = round($finance['webmoney']['_all'],2) - round($finance['webmoney']['to_balance'],2); ?></li>
		<li><i class="fa fa-code"></i></li>
            <li><strong>Пополнения на RoboKassa со статусом "is_pay 1"</strong>: <?php echo round($finance['robokassa']['_all'],2); echo " / "; echo round($finance['robokassa']['to_balance'],2); ?></li>
            <li>Наш доход от пополнений: <?php echo $rd = round($finance['robokassa']['_all'],2) - round($finance['robokassa']['to_balance'],2); ?></li>
		<li><i class="fa fa-code"></i></li>
            <li><strong>Выводы средств со статусом "status 2"</strong>: <?php echo round($finance['out']['_all']); echo " / "; echo round($finance['out']['_out'],2); ?></li>
            <li>Наш доход с выводов средств 14.2%: <?php echo $income_outputs  = round($finance['out']['_all'] - $finance['out']['_out'],2) - (round($finance['out']['_out'],2) / 100 * 0.8);; ?></li>
		<li><i class="fa fa-code"></i></li>
		<li><strong>Реферальные отчисления общим числом</strong>: <?php echo $finance['users']['referral']; ?></li>
            <li><i class="fa fa-code"></i></li>	
            <li><strong>Чистый доход за всё время</strong>: <?php echo $wmd + $rd + $income_outputs - $finance['users']['referral']; ?></li>		
		<li><i class="fa fa-code"></i></li>	
            <li><strong>На личном счёте у пользователей</strong>: <?php echo $finance['users']['money_amount']; ?></li>
            <li><strong>На заблокированном личном счёте у пользователей</strong>: <?php echo $finance['users']['money_blocked_money']; ?></li>
            <li><strong>На бонусном счёте у пользователей</strong>: <?php echo $finance['users']['money_bonus']; ?></li>
            <li><strong>На заблокированном бонусном счёте у пользователей</strong>: <?php echo $finance['users']['money_blocked_bonus']; ?></li>
		<li><i class="fa fa-code"></i></li>
            <li><strong>В наличии на нашем счёте</strong>: <?php echo round($finance['webmoney']['_all'],2) + round($finance['robokassa']['_all'],2) - (round($finance['out']['_out'],2)+(round($finance['out']['_out'],2) / 100 * 0.8)); ?></li>
        </ul>
    </div>
</div>