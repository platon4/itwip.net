<div id="accountsModerationModal">
    <div id="_message" style="margin-bottom: 10px;"></div>
    <form id="_form_<?php echo $account['id']; ?>">
    <div>
        <?php echo Html::dropDownList('M[_status]', $account['_status'], array(0 => Yii::t('main', '_status_0'), 1 => Yii::t('main', '_status_1'), 2 => Yii::t('main', '_status_2'), 3 => Yii::t('main', '_status_3')),array('onchange'=>'_M.change(this);')); ?>
        <?php echo Html::dropDownList('M[_m]', $account['_mdr'], array(1 => '0.1', 2 => '0.2', 3 => '0.3', 4 => '0.4', 5 => '0.5', 6 => '0.6', 7 => '0.7', 8 => '0.8', 9 => '0.9', 10 => '1',), array('id' => '_m_' . $account['id'])); ?>
        <?php echo Html::dropDownList('M[tape]', $account['tape'], array(0 => 'Не указано', 1 => 'Человек и Бот', 2 => 'Человек',3=>'Бот')); ?> 
    </div>
    <div id="_statusArea" style="<?php echo $account['_status']==2?'':'display: none;'; ?>">
        <?php echo Html::textArea('M[_message]',$account['_message'],array('placeholder'=>'Если не принят, пишем пользователю почему.')); ?>
    </div>
   </form>
</div>