<?php $count=count($messages); ?>
<script>
        $(function() {
            $( "#support_massage_list" ).resizable({
                alsoResize: "#support_massage_list_inset",
                maxHeight: 700,
                minHeight: 228,
        		handles: "s",
            });
        });

    	$(function() {
    		$( "#support_massage_read" ).resizable({
    			alsoResize: ".support_massage_read_inset",
    			maxHeight: 800,
    			minHeight: 370,
    			handles: "s",
    		});
    	});
</script>
<div id="support" class="box<?php echo ($show=='support')?' actived':''; ?>">
    <div class="box_inset">
        <div class="message_sort">
          <div class="period">
            <ul>
              <li><?php echo Yii::t('accountsModule.message', '_statuses'); ?>: </li>
              <li><i class="fa fa-clock-o"></i> - Ожидание ответа</li>
              <li><i class="fa fa-coffee"></i> - Выполняется</li>
              <li><i class="fa fa-comment-o"></i> - Ответил администратор</li>
              <li><i class="fa fa-smile-o"></i> - Закрыт</li>
              <li></li>
              <li style="float: right"><a href="/support" class="button btn_blue" style="margin-top: -4px;"><?php echo Yii::t('accountsModule.message', '_create_new_support'); ?></a></li>
            </ul>
          </div>
        </div>
      <form id="_mform">
  		<div id="support_massage_list">
            <div class="table_head">
              <div class="table_head_inside">
              	<table>
              	 <tr>
                    <td class="status"></td>
              	  <td class="id"><?php echo Yii::t('accountsModule.message', '_id'); ?></td>
              	  <td class="date_ot"><?php echo Yii::t('accountsModule.message', '_date_request'); ?></td>
              	  <td class="date_do"><?php echo Yii::t('accountsModule.message', '_last_reply'); ?></td>
              	  <td class="text no_border"><?php echo Yii::t('accountsModule.message', '_message_title_support'); ?></td>
              	 </tr>
              	</table>
              </div>
            </div>
            <div id="support_massage_list_inset">
                <div id="_messagesSupportList" style="<?php echo (!$count)?'display:none;':''; ?>">
                      <?php $this->renderPartial('_messages_support',array('messages'=>$messages)); ?>
                      <div id="_loadingSupportMessgaesAppend" style="text-align: center; margin: 10px 0; display: none;"><img alt="Loading..." src="/i/loading_11.gif"></div>
                </div>
  			  <div id="_noSupportMessageList" class="message_no" style="<?php echo ($count)?'display:none;':''; ?>">
  				<div id="_noSupportMessagesText" class="td">
  					<?php echo Yii::t('accountsModule.message', '_no_message_system'); ?>
  			     </div>
                   <div style="display:none;" class="td" id="_loadingSupportMessgaes"><img alt="Loading..." src="/i/loading_11.gif"></div>
  			  </div>
            </div>
        </div>
      </form>
     <div id="support_massage_read" style="height: 370px;">
	  	<div id="_support"></div>
		  <div id="no_support" class="message_no">
		        <div id="_noSupportMessageText" class="td"><?php echo Yii::t('accountsModule.message', '_select_message_read'); ?></div>
                <div id="_loadingSupportMessgae" class="td" style="display:none;"><img src="/i/loading_11.gif" alt="Loading..."></div>
		  </div>
      </div>
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    var $t=jQuery('#support_massage_list_inset');

    $t.scrollLeft(0);
    $t.scrollTop(0);

    $('#support_massage_list_inset').scroll(function () {
        if(this.scrollTop>=this.scrollHeight-this.clientHeight)
        {
            if(Support.s._w==false)
            {
                Support._getMessages();
            }
        }
    });
  });
</script>