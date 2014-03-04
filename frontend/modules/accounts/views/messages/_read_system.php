<div id="_rmessage_<?php echo $message->id; ?>" class="message_system">
    <div class="message_system_title">
        <i class="fa fa-envelope-o"></i> <?php echo Html::encode($message->_title); ?> <a href="javascript:;" onclick="Message._remove('<?php echo $message->id; ?>', this);"><i title="<?php echo Yii::t('main','_remove'); ?>" class="fa fa-trash-o"></i></a>
    </div>
    <div class="message_system_more">
        <div class="message_system_more_what"><?php echo Yii::t('accountsModule.message','_from'); ?>: Система   <span style="float:right"><?php echo date("d.m.Y H:s",strtotime($message->_date)); ?></span></div>
        <p><?php echo Html::bbCode($message->_text); ?></p>
    </div>
</div>
