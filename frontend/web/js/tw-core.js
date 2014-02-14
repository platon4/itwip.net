var cCount = 0, timer;

Accounts = {

	params: { _order: '', _oType: 'DESC', _query: '', _search: false, _token: false, _ajax: 1, _page: 0, _limit: 0,},
	
	_getAccounts: function()
	{
		this.params._token = it._token;

		$.ajax({
			type: "POST",
			url: "/twitter/accounts/index?page=" + this.params._page,
			data: this.params,
			dataType: "json",
			success: function (obj, textStatus) 
			{
				$("#_listTwAccounts").html(obj['list']);
				$("#pagesList").html(obj['pages']);
			},
			beforeSend:function()
			{
				Accounts._loading('show');
			},
			complete: function(obj, textStatus)
			{
				Accounts._loading('hide');
				Accounts.params._search = false;
			}			
		});
	},
	_getFromQuery: function(element)
	{
		this.params._search = true;
		this.params._query = $("#setQuery").val();
		
		clearTimeout(timer);
		
		timer=setTimeout(function() 
		{
			Accounts._getAccounts();
		}, 300)
	},
	_getPage: function(page)
	{
		this.params._page = page;
		this._getAccounts();		
	},
	_setLimit: function(element)
	{
		this.params._limit = $(element).val();
		this._getAccounts();
	},
	_setOrder: function(element)
	{
		$(".table_head_inside").find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
		
		if(this.params._order == $(element).attr('data-order') && cCount == 0)
		{
			this.params._oType = "DESC";
			cCount = 1;
			
			$(element).find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
		}
		else {
			this.params._oType = "ASC";
			this.params._order = $(element).attr('data-order');
			cCount = 0;
			
			
			$(element).find('i').removeClass('fa-caret-down').addClass('fa-caret-up');
		}

		this._getAccounts();
	},
	_loading: function(t)
	{
		if(this.params._search == true) 
		{
			if(t == "show")
			{
				$("#_searchButton").addClass("fa-spinner fa-spin");
				$("#_searchButton").removeClass("fa-search");
			}
			else {
				$("#_searchButton").removeClass("fa-spinner fa-spin");
				$("#_searchButton").addClass("fa-search");
			}
		}

		if(t == "show")
		{
			$("._cHide").css('display', 'none');
			$("._loading").css('display', 'block');
		}
		else {
			$("._cHide").css('display', 'block');
			$("._loading").css('display', 'none');
		}
	}
}