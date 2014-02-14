Promo={
	_add:function(_f,e)
	{
		_ajax({
			type: "POST",
			url: "/office/finance/promo",
			data:$('#'+_f).serialize(),
			dataType: "json",
			success: function (obj, textStatus)
			{
				if(obj.code==200)
				{
					Promo._get(obj._type);
				}
				else
					Dialog.open(_error,{content:obj.html});
			},
			beforeSend:function()
			{
				$(e).find('i').removeClass('fa-plus').addClass('fa-spinner fa-spin');
			},
			complete:function()
			{
				$(e).find('i').removeClass('fa-spinner fa-spin').addClass('fa-plus');
			}
		});
	},
	_get:function(_t,_u,e)
	{
		var _c;
		
		if(_t=='simple')
		{
			_c=$('#spromo');
		} 
		else {
			_c=$('#apromo');	
		}
		
		_ajax({
			type: "POST",
			url: "/office/finance/promo?act="+_t+"&_u="+_u,
			dataType: "json",
			success: function (obj, textStatus)
			{
				_c.html(obj.html);
				
				if(_t=='simple')
				{
					$('#_promo_no_use').html(obj.promo_no_use);
					$('#_promo_use').html(obj.poromo_use);
				}

				if(typeof e == 'object')
				{
					$('#gTo').find('.select').removeClass('select');
					$(e).addClass('select');
				}
			},
			beforeSend:function()
			{
				_c.html('<tr><td style="text-align: center; padding: 4px;"><img src="/i/loading_11.gif" alt="Loading..."></td></tr>');
			}
		});	
	}
}