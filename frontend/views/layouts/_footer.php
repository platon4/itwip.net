<div id="footer" style="position: relative;">
 <div class="center">
   <div id="footer_block_1">
	 <div id="footer_block_1_1" class="footer_menu">
	   <h5><?php echo Yii::t('main', '_footer_m_h_2'); ?></h5>
		 <ul>
		   <?php if(Yii::app()->user->isGuest) { ?><li><a href="/accounts/auth"><?php echo Yii::t('main', '_footer_m_auth'); ?></a></li><?php } ?>
		   <?php if(Yii::app()->user->isGuest) { ?><li><a href="/accounts/lost"><?php echo Yii::t('main', '_footer_m_lost'); ?></a></li><?php } ?>
           <li><a href="/support"><?php echo Yii::t('main', '_footer_m_support'); ?></a></li>
		   <!--<li><a href="/help"><?php echo Yii::t('main', '_footer_m_help'); ?></a></li>-->
		   <li><a href="/regulations"><?php echo Yii::t('main', '_footer_m_regulations'); ?></a></li>
		 </ul>
	 </div>
	 <div id="footer_block_1_2" class="footer_menu">
	   <h5><?php echo Yii::t('main', '_footer_m_h_1'); ?></h5>
		 <ul>
		   <li><a href="http://community.itwip.net" target="_blank"><?php echo Yii::t('main', '_footer_m_community'); ?></a></li>
           <li style="padding-left: 0px; padding-top: 40px;"><a style="color: rgb(255, 255, 255); text-decoration: none; font-size: 24px; font-family: Arial; text-shadow: 0px 0px 8px rgb(255, 255, 255); border-bottom: medium none; padding-left: 0px;" href="http://studev.ru/"><span style="color:#FF6C00">S</span>D</a> <sup style="top: -10px;"><a style="border-bottom: none; font-size: 10px; text-shadow: 0px 0px 8px rgb(255, 255, 255);" href="http://studev.ru/">создание сайта</a></sup></li>
        </ul>
	 </div>
	 <div id="footer_block_1_3" class="footer_menu">
	 <?php if($this->beginCache('widget.lastTopics', array('duration'=>3600))) { ?>
		<?php $this->widget('application.widgets.lastTopics', array('limit'=>4)); ?>        
	 <?php $this->endCache(); } ?>
	 </div>
	 <div id="footer_block_1_4">
	   <h5><?php echo Yii::t('main', '_footer_m_h_4'); ?></h5>
		 <div style="margin-top: 5px; margin-bottom: 4px;">
		   <img src="/i/elements/robo.png" alt="" /> 
		   <img src="/i/elements/webmoney-white.png" alt="" />
		   <img src="/i/elements/yandexmoney.png" alt="" />
		   <img src="/i/elements/qiwi.png" alt="" />
           <img src="/i/elements/sberbank.png" alt="" />
           <img src="/i/elements/visa.png" alt="" />
           <img src="/i/elements/paypal.png" alt="" />
           <img src="/i/elements/mastercard.png" alt="" />
           <img src="/i/elements/sms.png" alt="" />
           <h5 style="display: inline-block; position: relative; top: -7px; margin-bottom: 0px; margin-left: 25px;">и другие...</h5>
         </div>

	   <!--<h5><?php echo Yii::t('main', '_footer_m_h_1_4'); ?></h5>-->
    </div>
   </div>
  </div>
  <div class="footer_hr"></div>
  <div id="footer_block_2">
	<div class="center">
	<div id="footer_block_2_1"><a href=""><?php echo Yii::t('main', '_footer_slogan'); ?></a></div>
	<div id="footer_block_2_2">
    <div class="text_like">Поделится нами с друзьями:</div>
    <div class="like_all">
        <script type="text/javascript" src="https://yandex.st/share/share.js" charset="utf-8"></script>
        <div class="yashare-auto-init" data-yashareL10n="ru" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki,moimir,gplus" data-yashareTheme="counter" data-yashareLink="http://itwip.net"  data-yashareTitle="iTwip.net - Реклама в социальных сетях, монетизация аккаунтов"></div>
    </div>
    </div>
	<div id="footer_block_2_3">
		<span class="contact"><?php echo Yii::t('main', '_footer_contact'); ?>: <span><b class="e-mail"></b> support@itwip.net</span> <!--<span><i class="icq"></i> 532352352</span>--> <span><i class="skype"></i> <a href="skype:itwip.net?chat">itwip.net</a></span></span>
		<span class="ver">
            <span style="position:relative; top: 16px;padding-right: 5px;">Версия 1.1.0</span>
            <span style="float:right">
              <!-- begin WebMoney Transfer : attestation label -->
              <a  style=" margin-top: -10px;" href="https://passport.webmoney.ru/asp/certview.asp?wmid=412128443746" target="_blank"><img src="/i/elements/v_blue_on_white_ru.png" alt="Здесь находится аттестат нашего WM идентификатора 412128443746" border="0" /></a>
              <!-- end WebMoney Transfer : attestation label -->
              <!-- begin WebMoney Transfer : accept label -->
              <a  style="margin-left: 5px;margin-top: -10px;" href="http://www.megastock.ru/" target="_blank"><img src="/i/elements/acc_blue_on_white_ru.png" alt="www.megastock.ru" border="0"></a>
              <!-- end WebMoney Transfer : accept label -->
            </span>
        </span>
	</div>
	</div>
  </div>
</div>
<script>
(function($) {
	$(window).resize(function() {
		dinamicSize();
	});
	dinamicSize();
})(jQuery)
</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-45749223-1', 'itwip.net');
  ga('send', 'pageview');

</script>