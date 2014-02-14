Support = {
	s: {
		_g:'_all',
		_s:'urgent',
		_w: false,
		last: false,
	},
	_new: function(id,e)
	{
		if(this.s._w==true) return false;
		
		var _txt=$(e).html();
		
		_ajax({
			type: "POST",
			data: {"_text":$('#_newMessage').val()},
			url: "/office/messages/_new?id="+id+"&act="+c,
			dataType:"json",
			success: function (obj, textStatus) 
			{
				if(obj.code==200)
				{
					$('#_pListInsert').append(obj.html);
					$('.message_read_inset').scrollTop($('.message_read_correspondence')[0].scrollHeight);
					$('#_newMessage').val('');
					$('#message_'+id).find('.status').children().removeClass('fa-clock-o fa-smile-o fa-coffee fa-comment-o fa-trash-o').addClass('fa-comment-o');
					$('.message_read_title').children().removeClass('fa-clock-o fa-smile-o fa-comment-o fa-trash-o fa-coffee').addClass('fa-comment-o');					
				}
				else {
					Dialog.open(_error,{'content':obj.html});				
				}
			},
			beforeSend:function()
			{
				$(e).html('<i class="fa fa-spinner fa-spin"></i>');
				Support.s._w=true;
			},
			complete:function()
			{
				$(e).html(_txt);
				Support.s._w=false;
			}
		});		
	},
	_gTo: function(g,e,o)
	{
		if(this.s._g==g || this.s._s==g) return false;
		
		if(o=='order')
		{
			$('#gTo').find('._order').removeClass('select');
			$(e).addClass('select');
			this.s._s=g;
		}
		else {
			$('#gTo').find('._show').removeClass('select');
			$(e).addClass('select');	
			this.s._g=g;
		}
		
		this.getMessages();
	},
	getMessage: function(id,e)
	{
		if(this.s.last===e) return false;
		
		_ajax({
			type: "GET",
			url: "/office/messages/_getMessage?id="+id+"&act="+c,
			dataType:"json",
			success: function (obj, textStatus) 
			{
				$('#_message').html(obj.html);
				$('.message_read_inset').scrollTop($('.message_read_correspondence')[0].scrollHeight); 
				$('select').styler();
			},
			beforeSend:function()
			{
				$("#_messagesList").find('.read').removeClass('read');
				$(e).addClass('read');
				$('#_message').html('<div class="td" style="text-align: center;">'+_loading+'</div>');
			}
		});
		
		this.s.last=e;
	},
	getMessages: function()
	{
		_ajax({
			type: "GET",
			url: "/office/messages/_get?act="+c+"&_g="+this.s._g+"&_s="+this.s._s,
			dataType:"json",
			success: function (obj, textStatus) 
			{
				$('#_messagesList').html(obj.html);
			},
			beforeSend:function()
			{
				$('#_messagesList').html('<div style="text-align: center; margin-top: 80px;">'+_loading+'</div>');
				$('#_message').html('<div class="td" style="text-align: center;">Не выбран запрос для чтения и ответа</div>');
			}
		});	
	},
	setImportance: function(id,e)
	{
		if(this.s._w==true) return false;
		
		var _txt=$(e).html();
		
		_ajax({
			type: "POST",
			data: {"e":$('#_importance').val()},
			url: "/office/messages/_setImportance?id="+id,
			dataType:"json",
			beforeSend:function()
			{
				$(e).html('<i class="fa fa-spinner fa-spin"></i>');
				Support.s._w=true;
			},
			complete:function()
			{
				Support.s._w=false;
				$(e).html(_txt);
			}
		});	
	},
	remove: function(id,e)
	{
		if(this.s._w==true) return false;
		
		var _txt=$(e).html();
		
		_ajax({
			type: "POST",
			data: {"e":$('#_importance').val()},
			url: "/office/messages/remove?id="+id,
			dataType:"json",
			success: function(obj)
			{
				if(obj.code==200)
				{
					$('#message_'+id).find('.status').children().removeClass('fa-clock-o fa-smile-o fa-coffee fa-comment-o').addClass('fa-trash-o');
					$('.message_read_title').children().removeClass('fa-clock-o fa-smile-o fa-coffee fa-comment-o').addClass('fa-trash-o');
					
					$('.buttons_reply').remove();
					$('#_newMessage').prop('disabled',true);
					$('#_newMessage').prop('placeholder','Тикет удален или закрыт.');
				} 
				else {
					Dialog.open(_info,{});
				}
			},
			beforeSend:function()
			{
				$(e).html('<i class="fa fa-spinner fa-spin"></i>');
				Support.s._w=true;
			},
			complete:function()
			{
				$(e).html(_txt);
				Support.s._w=false;
			}
		});	
	},
	inProcess: function(id,e)
	{
		if(this.s._w==true) return false;
		
		var _txt=$(e).html();
		
		_ajax({
			type: "POST",
			url: "/office/messages/_process?id="+id,
			dataType:"json",
			success: function(obj)
			{
				if(obj.code==200)
				{
					$('#message_'+id).find('.status').children().removeClass('fa-clock-o fa-smile-o fa-coffee fa-comment-o fa-trash-o').addClass('fa-coffee');
					$('.message_read_title').children().removeClass('fa-clock-o fa-smile-o fa-comment-o fa-trash-o').addClass('fa-coffee');
					
					$(e).html('Решено');
					$(e).removeClass('btn_blue').addClass('btn_green');
				}
				else {
					$('#message_'+id).find('.status').children().removeClass('fa-clock-o fa-smile-o fa-coffee fa-comment-o fa-trash-o').addClass('fa-comment-o');
					$('.message_read_title').children().removeClass('fa-clock-o fa-smile-o fa-comment-o fa-trash-o fa-coffee').addClass('fa-comment-o');
					
					$(e).remove();
				}
			},
			beforeSend:function()
			{
				$(e).html('<i class="fa fa-spinner fa-spin"></i>');
				Support.s._w=true;
			},
			complete:function()
			{
				Support.s._w=false;
			}
		});	
	}
}