<div id="header">
    <div id="header_1">
        <div id="header_1_1">
            <div id="logo">
                <a href="<?php echo Yii::app()->homeUrl; ?>"></a>
                <sup class="service"><a href="http://community.itwip.net">iTwip.net сообщество</a></sup>
            </div>
            
        </div>
        <div id="header_1_2">
            <div id="header_right">
                <div id="header_l"></div>
                <div id="header_right_1_1"><i class="fa fa-male"></i> <?php echo Html::encode(Yii::app()->user->name); ?></div>
                <div id="header_l"></div>
                <div id="header_right_1_2">
                    <div class="table">
                        <div class="td" style="vertical-align: middle; padding-right: 5px;"><i class="fa fa-rub"></i> </div>
                        <div class="td">
                            <div><a href="/finance"><?php echo Finance::money(Yii::app()->user->_get('money_amount'),0,true); ?></a> / <a href="/finance"><?php echo Finance::money(Yii::app()->user->_get('bonus_money'),1,true); ?></a></div>
                            <div><a href="/finance/replenishment" style="font-size: 10px;">Пополнить личный счёт</a></div>                  
                        </div>
                    </div>
                </div>                  
                <div id="header_l"></div>
                <div id="header_right_1_4"><i class="fa fa-envelope-o"></i> <a href="/accounts/messages"><b id="_mail_unread"><?php echo Yii::app()->user->_get('mail_unread'); ?></b></a> / <span id="_all_mail_read"><?php echo Yii::app()->user->_get('mail_all'); ?></span></div>
                <div id="header_l"></div>
                    <?php $this->widget('application.widgets.Bell'); ?>            
                <div id="header_right_1_3"><div class="button_border"><a href="<?php echo $this->createUrl('/accounts/service/logout'); ?>" class="button btn_red"><i class="fa fa-power-off"></i> <?php echo Yii::t('main','_exit'); ?></a></div></div>
            </div>
        </div>
    </div>
</div>


