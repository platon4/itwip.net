Settings = 
{
	_params: {
		subjectCount: 1,
		wait: false,
	},
	fReset: function(element)
	{
		$("#" + element).get(0).reset()
	},
	accountRemove: function(tid, title, messages)
	{
		Dialog.open(title, {content: messages, buttons:[{text:btn_yes, class: "button btn_red", click: function() { window.location.replace("/twitter/accounts/settings?tid=" + tid + "&remove=1"); }}, {text:btn_no, class: "button", click: function() { $(this).dialog("close"); }}]});
	},
	_status: function(el, tid)
	{
		_ajax(
		{
			data: {"tid":tid, "action":"s", "s": ($(el).is(':checked') == true) ? "yes" : "no"},
			url: "/twitter/ajax/_settings",
			success: function(obj)
			{
				switch(obj.code)
				{
					case 200:
							$("#_status").html(obj._status);
						break;

					default:
						Dialog.open(_error, {content: unknow_response, buttons:[{text:_close, class: "button", click: function() { $(this).dialog("close"); }}]});
				}			
			}
		});
	},
	_credentials: function(el)
	{
		var _el = $(el).parent();
		
		_ajax(
		{
			data: {"_check": _el.attr('data-check'), "tid":_el.attr('data-send')},
			url: "/twitter/ajax/_credentials?_return=1",
			beforeSend: function() { _el.html('<img src="/i/loads.gif" alt="Loading ...">'); },
			success: function(obj) 
			{
				_el.html(obj.html);
				
				switch(obj.code)
				{
					case 200:
							$("#" + _el.attr('data-check')).html(obj.result);
						break;

					default:
						Dialog.open(_error, {content: obj.messages, buttons:[{text:_close, class: "button", click: function() { $(this).dialog("close"); }}]});
				}
			},
			complete: function(obj)
			{
				if(!isObject(obj))
				{
					_el.html('Error, please reload this page.');
				}
			}
		});	
	}	
}