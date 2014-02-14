<?php $count=count($messages); ?>
<script>
	$(function() {
		$( "#system_massage_list" ).resizable({
			alsoResize: "#system_massage_list_inset",
			maxHeight: 700,
			minHeight: 285,
			handles: "s",
		});
	});
	$(function() {
		$( "#system_massage_read" ).resizable({
			alsoResize: "#system_massage_read_inset",
			maxHeight: 700,
			minHeight: 215,
			handles: "s",
		});
	});
</script>
<div id="system" class="box<?php echo ($show=='support' OR $show=='ref')?'':' actived'; ?>">
    <div class="box_inset">
    <form id="_mform">
      <div id="system_massage_list">
          <div class="table_head">
            <div class="table_head_inside">
            	<table id="_table_o_caret">
            	 <tr>
            	  <td class="input"><?php echo Html::CheckBox('checkAll',false,array('id'=>'checkAll','onchange'=>'_all_ckbox(\'_mform\',this);')); ?></td>
            	  <td class="status"><a onclick="Message._order('status','_o_caret_status');"><i class="fa fa-envelope-o"></i> <i id="_o_caret_status" class="fa fa-caret-down"></i></a></td>
            	  <td class="date"><a onclick="Message._order('date','_o_caret_date');" href="javascript:;"><?php echo Yii::t('main', '_date'); ?> <i id="_o_caret_date" class="fa fa-caret-down"></i></a></td>
            	  <td class="text no_border"><?php echo Yii::t('accountsModule.message', '_message_title_content'); ?></td>
            	  <td class="delete no_border"></td>
            	 </tr>
            	</table>
            </div>
          </div>

          <div id="system_massage_list_inset">
              <div id="_messagesList" style="<?php echo (!$count)?'display:none;':''; ?>">
                    <?php $this->renderPartial('_messages_system',array('messages'=>$messages)); ?>   
              </div>
			  <div id="_loadingMessgaesAppend" style="text-align: center; margin: 10px 0; display: none;"><img alt="Loading..." src="/i/loading_11.gif"></div>
			  <div id="_noMessageList" class="message_no" style="<?php echo ($count)?'display:none;':''; ?>">
				<div id="_noMessagesText" class="td">
					<?php echo Yii::t('accountsModule.message', '_no_message_system'); ?>
			     </div>
                 <div style="display:none;" class="td" id="_loadingMessgaes"><img alt="Loading..." src="/i/loading_11.gif"></div>
			  </div>
          </div>

          <div class="table_bottom">
          	<div class="table_bottom_inside">
                <?php echo Html::dropDownList('messageAction','',array(''=>Yii::t('accountsModule.message', '_action_of_selected'),'remove'=>Yii::t('main', '_remove'))); ?>
          		<a onclick="Message._action(this); return false;" href="javascript:;" class="button"><?php echo Yii::t('main', '_yes'); ?></a>
          	</div>
          </div>
      </div>
    </form>
      <div id="system_massage_read">
          <div id="system_massage_read_inset">
				<div id="_message"></div>
				<div id="no_message" class="message_no">
					<div id="_noMessageText" class="td"><?php echo Yii::t('accountsModule.message', '_select_message_read'); ?></div>
                    <div id="_loadingMessgae" class="td" style="display:none;"><img src="/i/loading_11.gif" alt="Loading..."></div>
				</div>
          </div>
      </div>
    </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    var $t=jQuery('#system_massage_list_inset');

    $t.scrollLeft(0);
    $t.scrollTop(0);

    $('#system_massage_list_inset').scroll(function () {
        if(this.scrollTop>=this.scrollHeight-this.clientHeight)
        {
            if(Message.s._w==false && Message.s.load==false)
            {
                Message._getMessages();
            }
        }
    });
  });
</script>