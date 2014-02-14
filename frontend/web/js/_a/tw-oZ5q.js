Tw = {
    s: {
        _page: 0,
    },
    getSettings: function(id, type, e)
    {
        var _c = $(e).find('i').attr('class');

        _ajax({
            url: "/office/twitter/settings?id=" + id,
            type: "POST",
            data: {"_t": type},
            success: function(obj)
            {
                Dialog.open(obj.title, {content: obj.html});
                $('.ui-dialog-buttonset').prepend(obj.buttons);
                $('select').styler();
            },
            beforeSend: function()
            {
                $(e).find('i').removeClass('').addClass('fa fa-spinner fa-spin');
            },
            complete: function()
            {
                $(e).find('i').removeClass('fa fa-spinner fa-spin').addClass(_c);
            }
        });
    },
    setM: function(id, e)
    {
        var b = $(e), bt = b.html();

        _ajax({
            type: "POST",
            url: "/office/twitter/_m?id=" + id,
            data: {"_m": $('#_m_' + id).val()},
            dataType: "json",
            success: function(obj, textStatus)
            {
                if (obj.code != 200)
                {
                    Dialog.open(_error, {content: obj.error});
                }
            },
            beforeSend: function()
            {
                b.html('<i class="fa fa-spinner fa-spin"></i>');
            },
            complete: function()
            {
                b.html(bt);
            }
        });
    },
    status: function(id, e)
    {
        var b = $(e), bt = b.html();

        _ajax({
            type: "POST",
            url: "/office/twitter/save?id=" + id,
            data: $('#formSave').serialize(),
            dataType: "json",
            success: function(obj, textStatus)
            {
                if (obj.code == 200)
                {

                }
                else
                    Dialog.open(_error, {content: obj.error});
            },
            beforeSend: function()
            {
                b.html('<i class="fa fa-spinner fa-spin"></i>');
            },
            complete: function()
            {
                b.html(bt);
            }
        });
    },
    _getPage: function(page)
    {
        this.s._page = page;
        this._getAccounts();
    },
    _getAccounts: function()
    {
        _ajax({
            type: "POST",
            url: "/office/twitter/accounts?page=" + this.s._page,
            dataType: "json",
            success: function(obj, textStatus)
            {
                $("#_listTwAccounts").html(obj.list);
                $("#pagesList").html(obj.pages);
            },
            beforeSend: function()
            {
                $("#_listTwAccounts").html('<div class="_loading" style="text-align: center; padding-top: 10px;"><img src="/i/loads.gif"></div>');
            },
        });
    },
}

var timer, cCount = 0;

_M = {
    s: {
        _w: false,
        _s: 0,
        _query: '',
        _order:'',
        _oType:'DESC',
        page:0,
    },
    _getSettings: function(id, e)
    {
        var _c = $(e).find('i').attr('class');

        _ajax({
            url: "/office/twitter/settings?id=" + id,
            data: {"_t": "settings"},
            type: "POST",
            success: function(obj)
            {
                Dialog.open(obj.title, {content: obj.html});
                $('.ui-dialog-buttonset').prepend(obj.buttons);
                $('select').styler();
            },
            beforeSend: function()
            {
                $(e).find('i').removeClass('').addClass('fa fa-spinner fa-spin');
            },
            complete: function()
            {
                $(e).find('i').removeClass('fa fa-spinner fa-spin').addClass(_c);
            }
        });
    },
    change: function(e)
    {
        if (e.value == 2)
        {
            $('#_statusArea').show();
        }
        else
            $('#_statusArea').hide();
    },
    save: function(id, e)
    {
        if (this.s._w)
            return false;
        var b = $(e), bt = b.html();

        _ajax({
            url: "/office/twitter/_save?id=" + id,
            data: $('#_form_' + id).serialize(),
            type: "POST",
            success: function(obj)
            {
                if (obj.code == 200)
                {
                    _M._get(_M.s._s);
                }

                $('#_message').html(obj.message);
                setTimeout(function() {
                    $('#_message').fadeOut();
                }, 3000);
            },
            beforeSend: function()
            {
                b.html('<i class="fa fa-spinner fa-spin"></i>');
                _M.s._w = true;
            },
            complete: function()
            {
                b.html(bt);
                _M.s._w = false;
            }
        });
    },
    _getPage: function(n)
    {
        this.s.page = n;
        this._get(this.s._s);
    },
    _getFromQuery: function(e)
    {
        this.s._query = $("#setQuery").val();

        clearTimeout(timer);

        timer = setTimeout(function()
        {
            _M._get(_M.s._s);
        }, 300)
    },
    _setOrder: function(by,element)
    {
        $(".table_head_inside").find('i').removeClass('fa-caret-up').addClass('fa-caret-down');

        if (this.s._order == by && cCount == 0)
        {
            this.s._oType = "DESC";
            cCount = 1;

            $(element).find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
        }
        else {
            this.s._oType = "ASC";
            this.s._order = by;
            cCount = 0;


            $(element).find('i').removeClass('fa-caret-down').addClass('fa-caret-up');
        }

        this._get(this.s._s);
    },
    _get: function(s)
    {
        this.s._s = s;

        _ajax({
            url: "/office/twitter/accountsModeration?page=" + this.s.page + "&_s=" + s + "&_q=" + this.s._query+"&_o="+this.s._order+"&_a="+this.s._oType,
            type: "POST",
            dataType: "json",
            success: function(obj)
            {
                $('#_accountsList').html(obj.list);
                $('#pagesList').html(obj.pages);

                $('#_count_status_0').html(obj.counts._m);
                $('#_count_status_1').html(obj.counts._w);
                $('#_count_status_2').html(obj.counts._b);
            },
            beforeSend: function()
            {
                $('#pagesList').html('');
                $('#_accountsList').html('<tr><td style="text-align: center; padding: 7px;"><img src="/i/loading_11.gif" alt="Loading..."></td></tr>');
                $("#_searchButton").removeClass("fa-search").addClass("fa-spinner fa-spin");
            },
            complete: function()
            {
                $("#_searchButton").removeClass("fa-spinner fa-spin").addClass("fa-search");
            }
        });
    },
}