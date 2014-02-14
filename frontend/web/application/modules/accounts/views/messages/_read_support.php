<div class="support_massage_read_inset">
    <div class="message_read_list">
        <div class="message_read_title"><i class="fa <?php
            switch($_m->_status)
            {
                case 1:
                    echo 'fa-coffee';
                    break;

                case 2:
                    echo 'fa-comment-o';
                    break;

                case 3:
                    echo 'fa-smile-o';
                    break;

                default:
                    echo 'fa-clock-o';
            }
            ?>"></i> <?php echo Html::encode($_m->_subject); ?></div>
        <div id="support_message_list" class="message_read_correspondence">
            <?php foreach($_ms as $message)
            { ?>
                <div class="message_here<?php echo ($message['ot_id'])?' admin':''; ?>">
                    <div class="who_date"><?php echo ($message['ot_id'])?Yii::t('accountsModule.message','_administration'):Yii::t('accountsModule.message','_you'); ?> <span style="float:right"><?php echo date("d.m.Y H:i",strtotime($message['_date'])); ?></span></div>
                    <p><?php echo Html::bbCode($message['_text']); ?></p>
                </div>
<?php } ?>
        </div>
    </div>
</div>
    <?php if(!$_add)
    { ?> 
    <div class="message_read_reply">
    <?php if($_m->_status != 3)
    { ?> 
            <div class="new_area">
                <textarea placeholder="Напишите здесь своё сообщение" id="_newMessage"></textarea>
            </div>
            <?php } ?>
        <div class="buttons_reply">
            <button id="_remove_button" class="button btn_red" onclick="Support._remove('<?php echo $_m->id; ?>', this);"><?php echo Yii::t('accountsModule.message','_remove_application'); ?></button>
            <?php if($_m->_status != 3)
            { ?>  
                <button id="_close_button"  class="button btn_green" onclick="Support._close('<?php echo $_m->id; ?>', this);"> <i class="icon-smile"></i> <?php echo Yii::t('accountsModule.message','_close_application'); ?></button>
                <button id="_new_button" class="button"  onclick="Support._new('<?php echo $_m->id; ?>', this);"><?php echo Yii::t('accountsModule.message','_send_message'); ?></button>
    <?php } ?>
        </div>
    </div>
<?php } ?>