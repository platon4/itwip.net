<?php
if(!Yii::app()->user->isGuest)
    $this->layout='//layouts/info';
?>
<div id="info">
    <div id="info_inset">
        <div id="modal_info">
            <div class="title_modal_info">
                <?php
                switch($form->_system)
                {
                    case 1:
                        $title=Yii::t('financeModule.index','_system_title_webmoney');
                        break;

                    case 2:
                        $title=Yii::t('financeModule.index','_system_title_robokassa');
                        break;
                }

                $this->pageTitle=Yii::app()->name.' - '.Html::encode($title);
                echo $title;
                ?>
            </div>
            <div class="content_modal_info">
                <div>Пожалуйста подаждите, сейчас вы будите перенаправлены на выбраной вами систему оплаты.<div style="margin-top: 10px; text-align: center;"><img src="/i/loads.gif" alt="Loading..."></div></div>		
                <?php
                switch($form->_system)
                {
                    case 1:
                        ?>
                        <form id="_formAction" accept-charset="cp1251" method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp">
                            <input type="hidden" name="LMI_PAYMENT_AMOUNT" value="<?php echo $form->amount; ?>"> 
                            <input type="hidden" name="LMI_PAYMENT_DESC" value="<?php echo Html::encode(Yii::t('financeModule.index','_balance_add_user',array(
                            '{login}'=>Yii::app()->user->_get('email'),'{user}'=>Yii::app()->user->_get('name')))); ?>"> 
                            <input type="hidden" name="LMI_PAYMENT_NO" value="<?= $_id; ?>">
                            <input type="hidden" name="LMI_PAYEE_PURSE" value="R398233796434"> 
                            <input type="hidden" name="TTS_PAY" value="<?php echo $_id; ?>"> </p> 
                        </form>			
                        <?php
                        break;

                    case 2:

                        $mrh_login="eolitich";
                        $mrh_pass1="mj63%$./adjhlAjf6545%Djhjdada";
                        $inv_id   =$_id;
                        $out_summ =$form->amount;
                        $shp_item =$form->owner_id;
                        $in_curr  ="";
                        $culture  ="ru";
                        $encoding ="utf-8";
                        
                       // if($cOutSumm=CMoney::CalcOutSumm($out_summ ))
                          // $out_summ=$cOutSumm;
                        
                        $crc=md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item");
                        
                        echo "<form id=\"_formAction\" action='https://merchant.roboxchange.com/Index.aspx' method=POST>".
                        "<input type=hidden name=MrchLogin value=$mrh_login>".
                        "<input type=hidden name=OutSum value=$out_summ>".
                        "<input type=hidden name=InvId value=$inv_id>".
                        "<input type=hidden name=Desc value=".Html::encode(Yii::t('financeModule.index','_balance_add_user',array(
                                    '{login}'=>Yii::app()->user->_get('email'),'{user}'=>Yii::app()->user->_get('name')))).">".
                        "<input type=hidden name=SignatureValue value=$crc>".
                        "<input type=hidden name=Shp_item value='$shp_item'>".
                        "<input type=hidden name=IncCurrLabel value=$in_curr>".
                        "<input type=hidden name=Culture value=$culture>".
                        "</form>";
                        break;
                }
                ?>
            </div>
        </div>
    </div>
</div>
<script>
    setTimeout(function() {
        $('#_formAction').submit();
    }, 1000);
</script>