<?php
$this->pageTitle      =Yii::app()->name.' - '.Yii::t('main','_shop_referral');
$this->metaDescription=Yii::t('main','_show_referral_Description');
$this->breadcrumbs[]  =array(
    0=>array(Yii::t('breadcrumbs','_shop'),''),
    1=>array(Yii::t('breadcrumbs','_referrals'),'')
);
?>
<div id="shop_referrals" class="block">
    <div class="block_title"><div class="block_title_inset"><i class="fa fa-shopping-cart"></i> <h5>Магазин покупки рефералов</h5></div></div>
    <div class="block_content">
        <div class="no_border_bottom" id="info_page">
            <div class="icon"><i class="fa fa-info"></i></div>
            <div class="text">
                Данная страница предназначена в первую очередь для блогеров, которые вложились в развитие проекта - открыв приём твитов за бонусные средства. Сервис не гарантирует обязательный доход, и не возвращает средств за купленного реферала. Но администрация сервиса, предоставляет к продаже только активных пользователей системы, которые не являются уже рефералами. Цены формируются, от кол-ва пополнений и кол-ва twitter аккаунтов, минимальная цена 100 руб.Б. 
            </div>
        </div>
        <div style="cursor: default" class="line_title no_border_bottom">
            Всего система предлагает к продаже рефералов: <span id="_refCounts"><?php echo $count; ?></span>
        </div>    
        <div class="table_head">
            <div class="table_head_inside">
                <table>
                    <tr>
                        <td class="date"><a onclick="Shop.setOrder('date',this);" href="javascript:;">Регистрация <i class="fa fa-caret-down"></i></a></td>
                        <td class="date_last"><a onclick="Shop.setOrder('last_visit',this);" href="javascript:;">Последний визит <i class="fa fa-caret-down"></i></a></td>
                        <td class="balance_added"><a onclick="Shop.setOrder('in',this);" href="javascript:;">Пополнил <i class="fa fa-caret-down"></i></a></td>
                        <td class="balance_output"><a onclick="Shop.setOrder('out',this);" href="javascript:;">Вывел <i class="fa fa-caret-down"></i></a></td>
                        <td class="balance_my"><a onclick="Shop.setOrder('balance',this);" href="javascript:;">Л. счёт <i class="fa fa-caret-down"></i></a></td>
                        <td class="balance_bonus"><a onclick="Shop.setOrder('bonus',this);" href="javascript:;">Б. счёт <i class="fa fa-caret-down"></i></a></td>
                        <td class="add_accounts"><a onclick="Shop.setOrder('tw_count',this);" href="javascript:;">Tw.аккаунтов <i class="fa fa-caret-down"></i></a></td>		
                        <td class="price">Цена</td>		
                        <td class="icon"></td>		
                    </tr>
                </table>
            </div>
        </div>
        <div id="_referraList" class="acconts_list">
            <?php $this->renderPartial('_rows',array('rows'=>$rows,'pages'=>$pages)); ?>
        </div>
    </div>
</div>