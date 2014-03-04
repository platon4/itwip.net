<div>
<?php if(Yii::app()->user->isGuest) { ?>
	Ваш запрос успешно отправлен. Уведомление придёт Вам на E-mail "<b><?php echo Html::encode($form->_email); ?></b>".
<?php } else { ?>
	Спасибо за обращение, Ваш запрос отправлен в поддержку. <br><br>
	Отслеживать ответ, вы можете в <a href="/accounts/messages?_s=support">сообщениях</a>, на вкладке - "Поддержка".
<?php } ?>
</div>
<div style="text-align: center; margin-top: 20px;"><a href="/" class="button btn_blue">Перейти на главную</a></div>
<div class="line"></div>
<div id="contact">
  Наше сообщество -  <a href="http://community.itwip.net">http://community.itwip.net</a> ,
  наша эл.почта - <a href="mailto:support@itwip.net">support@itwip.net</a>
</div>
