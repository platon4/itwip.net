<div class="message_read_inset">
    <div class="message_read_list">
        <div class="message_read_title"><i class="fa <?php
            if($ticket->_is_remove == 0)
            {
                switch($ticket->_status)
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
            } else
            {
                echo 'fa-trash-o';
            }
            ?>"></i> <?php echo CHtml::encode($ticket->_subject); ?> </div>
        <div id="_pListInsert" class="message_read_correspondence">
            <?php $this->renderPartial('_read_list',array('messages'=>$messages)); ?>
        </div>
    </div>
</div>
<div class="message_read_reply">
    <div id="message_area" class="new_area">
        <textarea id="_newMessage"<?php echo ($ticket->_is_remove == 1 OR $ticket->_status == 3)?' disabled="disabled" placeholder="Тикет удален или закрыт."':' placeholder="Написать ответ пользователю. НЕ ЗАБЫВАЙ ПРО ГРАМОТНОСТЬ И РЕЧЕВЫЕ ОБОРОТЫ, ЭТО ЛИЦО iTwip !"'; ?>></textarea>
    </div>
    <?php if($ticket->_is_remove == 0 AND $ticket->_status < 3)
    { ?>		
        <div class="buttons_reply">
            <span style="float: left;">
                <?php
                echo Html::dropDownList('_importance',$ticket->importance,array(
                    0=>'Срочное',
                    1=>'Важно',
                    2=>'Средняя',
                    3=>'Низкая'
                        ),array('id'=>'_importance')
                );
                ?>
                <button class="button" onclick="Support.setImportance('<?php echo $ticket->id; ?>', this);">ок</button>
            </span>
            <?php if(in_array($ticket->_status,array(0,1)) AND $ticket->_is_remove == 0)
            { ?>
                <?php if($ticket->_status == 0)
                { ?><button class="button btn_blue" onclick="Support.inProcess('<?php echo $ticket->id; ?>', this);">Взял на выполнение</button><?php } else
                { ?><button class="button btn_green" onclick="Support.inProcess('<?php echo $ticket->id; ?>', this);">Решено</button><?php } ?>
    <?php } ?>
    <?php if(in_array($ticket->_status,array(0,1,2,3)) AND $ticket->_is_remove == 0)
    { ?><button class="button btn_red" onclick="Support.remove('<?php echo $ticket->id; ?>', this);">Удалить запрос</button><?php } ?>
    <?php if(in_array($ticket->_status,array(0,1,2)) AND $ticket->_is_remove == 0)
    { ?><button class="button btn_green" onclick="Support._new('<?php echo $ticket->id; ?>', this);">Отправить сообщение</button><?php } ?>
        </div>
<?php } ?>		
</div>