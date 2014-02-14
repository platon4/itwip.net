var cCount=0,loading_img = '<img src="/i/loads.gif" alt="Loading...">';

Message = {
    s:{
      _i:0,
      _w:false,
      page:1,
      _o:'',
      _d:'DESC',
	  load: false,
    },
    _order: function(_t,_e)
    {
        if(this.s._w==true) return false;
        $('#_table_o_caret').find('.fa-caret-up').removeClass('fa-caret-up').addClass('fa-caret-down');

        if(this.s._o==_t && cCount==0)
        {
           this.s._d='DESC';
           cCount = 1;
           $('#'+_e).removeClass('fa-caret-up').addClass('fa-caret-down');
        }
        else {
           this.s._d='ASC';
           cCount = 0;
           $('#'+_e).removeClass('fa-caret-down').addClass('fa-caret-up');
        }

        this.s._o=_t;
        this.s.page=0;
        Message._getMessages(true);
    },
    _remove: function(id,element)
    {
        var _t;

        _ajax(
        {
        	data: {"id":id},
            url: "/accounts/messages/_remove?id="+id,
        	success: function(result)
        	{
        		if(result.code==200)
        		{
                    $('#message_'+id).fadeOut('fast',function(){
                      $(this).remove();
                        _t=$('#_messagesList').find('.messag').length;

                        if(!_t)
                        {
                           $('#_messagesList').hide();
                           $('#_noMessageList').show();
                        }
                    });

                    $('#_message').empty();
                    $('#no_message').show();
                    $('#_noMessageText').show();
                    $('#_loadingMessgae').hide();

                    if(result.is_read==0)
                    {
						$('#_new_system_messages').html(parseInt($('#_new_system_messages').html())-1);
						$('#_mail_unread').html(parseInt($('#_mail_unread').html())-1);
                    }

                    $('#_all_system_messages').html(parseInt($('#_all_system_messages').html())-1);
                    $('#_all_mail_read').html(parseInt($('#_all_mail_read').html())-1);					
        		}
        	},
        	beforeSend: function()
        	{
        		$(element).children().removeClass('fa-remove').addClass('fa-spinner fa-spin');
        	},
        	complete: function()
        	{
        		$(element).children().removeClass('fa-spinner fa-spin').addClass('fa-remove');
        	}
        });
    },
    _action: function(_e)
    {
        var _b=$(_e),_bt=_b.html(),_t;

        if(this.s._w==true) return false;

        _ajax(
        {
        	data: $('#_mform').serialize(),
            url: "/accounts/messages/_massSystemAction",
        	success: function(result)
        	{
        		if(result.code==200)
        		{
                    if(result.action=='remove')
                    {
                        $.each(result.ids, function(index, value) {
                           $('#message_'+value).remove();
                        });

                        _t=$('#_messagesList').find('.messag').length;

                        if(!_t)
                        {
                           $('#_messagesList').hide();
                           $('#_noMessageList').show();

                           if(result._all>=1)
                           {
                                Message.s.page=0;
                                Message._getMessages();
                           }
                        }

                        $('#_message').empty();
                        $('#no_message').show();
                        $('#_noMessageText').show();
                        $('#_loadingMessgae').hide();
                    }

                    $('#checkAll').attr('checked',false).next().removeClass('checked');
                    $('#_new_system_messages').html(parseInt($('#_new_system_messages').html())-result._all_unread);
                    $('#_all_system_messages').html(parseInt($('#_all_system_messages').html())-result._all);                    
					
					$('#_mail_unread').html(parseInt($('#_mail_unread').html())-result._all_unread);
                    $('#_all_mail_read').html(parseInt($('#_all_mail_read').html())-result._all);
        		}
                else
                    Dialog.open(_info,{'content':result.message,'buttons':[{text: _close, click: function() { $(this).dialog("close"); }, class: "button"}]});
        	},
        	beforeSend: function()
        	{
        		_b.html('<i class="fa fa-spinner fa-spin" style="font-size: 16px;"></i>');
                Message.s._w=true;
                $('#system_massage_list_inset').css('opacity','0.5');
        	},
        	complete: function()
        	{
        	    _b.html(_bt);
                Message.s._w=false;
                $('#system_massage_list_inset').css('opacity','1');
        	}
        });
    },
    _getMessages: function(_l)
    {
		if(this.s.load==true) return false;
		
        var _t=$('#_messagesList').find('.messag').length,_s;
        this.s.page++;

        _ajax(
        {
            url: "/accounts/messages/index?page="+this.s.page+"&_o="+this.s._o+"&_d="+this.s._d,
        	success: function(result)
        	{
        	    if(result.code==200)
                {
                    if(!_t || _l==true)
                    {
                       $('#_messagesList').html(result.messages);
                    }
                    else {
                       $('#_messagesList').append(result.messages);
					}

                    $('#_messagesList').show();
                    $('#_noMessageList').hide();
                }
				else {
					Message.s.load=true;
				}		
            },
        	beforeSend: function()
        	{
        	    Message.s._w=true;

                if(!_t || _l==true)
                {
                    $('#_messagesList').hide();
                    $('#_noMessageList').show();
                    $('#_noMessagesText').hide();
                    $('#_loadingMessgaes').show();
                }
                else
                   $('#_loadingMessgaesAppend').show();

                _s=$('#system_massage_list_inset').scrollTop();
        	},
        	complete: function()
        	{
        	    Message.s._w=false;

                if(!_t || _l==true)
                {
                     $('#_noMessagesText').show();
                     $('#_loadingMessgaes').hide();
                }
                else
                   $('#_loadingMessgaesAppend').hide();

               $('#system_massage_list_inset').scrollTop(_s-15);
        	}
        });
    },
    _getMessage: function(id,element)
    {
        if(this.s._i==id) return false;

        _ajax(
        {
            url: "/accounts/messages/getMessage?id="+id,
        	success: function(result)
        	{
                 $('#_message').html(result.message);
                 $('#no_message').hide();
                 $('#message_'+id).find('.fa-envelope').removeClass('fa-envelope').addClass('fa-envelope-alt');

                 if(result.is_read==0)
                 {
                    $('#_new_system_messages').html(parseInt($('#_new_system_messages').html())-1);
					$('#_mail_unread').html(parseInt($('#_mail_unread').html())-1);
                 }
            },
        	beforeSend: function()
        	{
        	   $('#_messagesList').find('.messag').removeClass('read');
               $('#message_'+id).removeClass('new').addClass('read');
               $('#_message').empty();
               $('#no_message').show();
               $('#_noMessageText').hide();
               $('#_loadingMessgae').show();
        	},
        });

        this.s._i=id;
    }
}


Support = {
    s:{
      _i:0,
      _w:false,
      page:0,
    },
    _new: function(id,element)
    {
		if(this.s._w==true) return false;
		
        var _t,_b=$(element),_text=_b.html();

        _ajax(
        {
        	data: {"text":$('#_newMessage').val()},
            url: "/accounts/messages/_new?id="+id+"&act=support",
        	success: function(result)
        	{
        		if(result.code==200)
        		{
					$('#support_message_list').append(result.html);
					$('#support_'+id).find('.status').children().removeClass('fa-clock-o fa-smile-o fa-coffee fa-comment-o').addClass('fa-clock-o');
					$('.message_read_title').children().removeClass('fa-clock-o fa-smile-o fa-coffee fa-comment-o').addClass('fa-clock-o');
					
					$('#_newMessage').attr('disabled',true).attr('placeholder','').val('');

					$('.support_massage_read_inset').scrollTop($('#support_message_list')[0].scrollHeight);			
        		}
				else
					Dialog.open(_info,{'content':result.message,'buttons':[{text: _close, click: function() { $(this).dialog("close"); }, class: "button"}]});
        	},
        	beforeSend: function()
        	{
        		_b.html(loading_img);
				Support.s._w=true;
        	},
        	complete: function()
        	{
        		_b.html(_text);
				Support.s._w=false;
        	}
        });
    }, 		
    _remove: function(id,element)
    {
		if(this.s._w==true) return false;
		
        var _t,_b=$(element),_text=_b.html();

        _ajax(
        {
            url: "/accounts/messages/_remove?id="+id+"&act=support",
        	success: function(result)
        	{
        		if(result.code==200)
        		{
                    $('#support_'+id).fadeOut('fast',function(){
                      $(this).remove();
                        _t=$('#_messagesSupportList').find('.messag').length;

                        if(!_t)
                        {
                           $('#_messagesSupportList').hide();
                           $('#_noSupportMessageList').show();
                        }
                    });

                    $('#_support').empty();
                    $('#no_support').show();
                    $('#_noSupportMessageText').show();
                    $('#_loadingSupportMessgae').hide();

                    $('#_all_support_messages').html(parseInt($('#_all_support_messages').html())-1);
                    $('#_all_mail_read').html(parseInt($('#_all_mail_read').html())-1);					
        		}
        	},
        	beforeSend: function()
        	{
        		_b.html(loading_img);
				Support.s._w=true;
        	},
        	complete: function()
        	{
        		_b.html(_text);
				Support.s._w=false;
        	}
        });
    },
    _close: function(id,element)
    {
		if(this.s._w==true) return false;
		
        var _t,_b=$(element),_text=_b.html();

        _ajax(
        {
        	data: {"id":id},
            url: "/accounts/messages/_close?id="+id+"&act=support",
        	success: function(result)
        	{
        		if(result.code==200)
        		{
					$('#support_'+id).find('.status').children().removeClass('fa-clock-o fa-smile-o fa-coffee fa-comment-o').addClass('fa-smile-o');
					$('.message_read_title').children().removeClass('fa-clock-o fa-smile-o fa-coffee fa-comment-o').addClass('fa-smile-o');
					$('#_new_button').remove();
					$('#_close_button').remove();
					$('#_new_area').remove();
        		}
        	},
        	beforeSend: function()
        	{
        		_b.html(loading_img);
				Support.s._w=true;
        	},
        	complete: function()
        	{
        		_b.html(_text);
				Support.s._w=false;
        	}
        });
    }, 	
    _getMessage: function(id,element)
    {
        if(this.s._i==id) return false;

        _ajax(
        {
            url: "/accounts/messages/getMessage?id="+id+'&act=support',
        	success: function(result)
        	{
                 $('#_support').html(result.message);
                 $('#no_support').hide();

				$('.support_massage_read_inset').scrollTop($('#support_message_list')[0].scrollHeight);	
				 
                if(result.is_read==0)
                {
					$('#_new_support_messages').html(parseInt($('#_new_support_messages').html())-1);
					$('#_mail_unread').html(parseInt($('#_mail_unread').html())-1);
                }
            },
        	beforeSend: function()
        	{
        	   $('#_messagesSupportList').find('.messag').removeClass('read');
               $('#support_'+id).removeClass('new').addClass('read');
               $('#_support').empty();
               $('#no_support').show();
               $('#_noSupportMessageText').hide();
               $('#_loadingSupportMessgae').show();
        	},
        });

        this.s._i=id;
    }
}