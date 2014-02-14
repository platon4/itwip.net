<div id="block_1_1" style="width: auto;">
    <div id="block_1_1_1"><img alt="" src="<?php echo Html::encode($row['avatar']); ?>"></div>
    <div id="block_1_1_2">
    	<span class="block name shadow"><?php echo Html::encode($row['name']); ?></span>
    	<span class="block login shadow">Логин: <a href="https://twitter.com/<?php echo Html::encode($row['screen_name']); ?>" target="_blank">@<?php echo Html::encode($row['screen_name']); ?></a></span>
    	<span class="block langue shadow">Язык: <?php echo Yii::t('main', Html::_getLang($row['_lang'])); ?></span>
    	<span class="block date shadow">Регистрация в twitter: <?php echo date("d.m.Y", $row['created_at']); ?> (<?php echo Html::_dateTransform($row['created_at'], 'unix', 'days'); ?> д.)</span>
    </div>
    <div id="block_1_2"> </div>
    <div id="block_1_2_2">
      <span class="block shadow">Яндекс авторитет: <?php echo Html::encode($row['yandex_rank']); ?></span>
      <span class="block shadow">Наличие "быстроробота Я": <?php if($row['in_yandex']) { echo Yii::t('main','_yes'); } else { echo Yii::t('main','_no'); } ?></span>
      <span class="block shadow">Google PR: <?php echo Html::encode($row['google_pr']); ?></span>
      <!--<span class="block shadow">Индексируется в Google: <?php if($row['in_google']) { echo Yii::t('main','_yes'); } else { echo Yii::t('main','_no'); } ?></span>-->
      <br>
      <span class="block shadow">Тематика аккаунта: <?php echo $subjects; ?></span>
      <span class="block shadow">Пол блогера: <?php if($row['_gender']) { if($row['_gender']==2) { echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_men'); } else { echo Yii::t('twitterModule.accounts', '_twitterAccountSetting_woman'); } } else { echo  Yii::t('twitterModule.accounts','_unknow_gender'); } ?></span>
      <span class="block shadow">Возраст блогера: <?php if($row['_age']) { echo $_age[$row['_age']] . ' лет'; } else { echo Yii::t('twitterModule.accounts','_age_unknow'); } ?> </span>
      <span class="block shadow">Работа в режиме: <?php if(!$row['working_in']) { echo Yii::t('twitterModule.accounts','_t_manual'); } else { echo Yii::t('twitterModule.accounts','_t_auto'); } ?></span>
    </div>
    <div id="block_1_1_3">
    	<span id="tweets"><h3><?php echo Html::encode($row['tweets']); ?></h3>твитов</span>
    	<span id="no"></span>
    	<span id="following"><h3><?php echo Html::encode($row['following']); ?></h3>читаемых</span>
    	<span id="no"></span>
    	<span id="followers"><h3><?php echo Html::encode($row['followers']); ?></h3>читателей</span>
    </div>
</div>
