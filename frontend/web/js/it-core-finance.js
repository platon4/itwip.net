var loading_img = '<img src="/i/loads.gif" alt="Loading...">', cCount = 0;

Finance = {
    p: {
        _a: '',
        _o: '',
        _f: '',
        _t: '',
        _w: false,
        page: 0,
        url: '/finance/replenishment?',
    },
    _getGraph: function(_t)
    {
        _ajax({
            url: "/finance/reports/_graph?act=" + _t,
            type: "POST",
            dataType: "json",
            success: function(obj)
            {
                $('#_graph').html(obj.html);
            },
            beforeSend: function()
            {
                $('#_graph').html('<div style="text-align: center; padding: 12px;">' + loading_img + '</div>');
            }
        });
    },
    withdraw:function(e)
    {
        if(this.p._w===true) return false;
        
        var b=$(e),bt=b.html();
        
        _ajax({
            url: "/finance/ajax/withdraw",
            data: $('#_withdraw').serialize(),
            success: function(result)
            {
                if (result.code == 200)
                {
                  $('#_msg').fadeIn();  
                  setTimeout(function(){$('#_msg').fadeOut(function(){window.location.reload();});},3000);
                }
                else {
                    $('#_msg').fadeOut();
                    Dialog.open(_error,{content:result.msg});
                }
            },
            beforeSend: function()
            {
                Finance.p._w = true;
                b.html('<i class="fa fa-spin fa-spinner"></i>');
            },
            complete: function()
            {
                Finance.p._w = false;
                b.html(bt);
            }
        });
    },
    promoUse: function(e, t)
    {
        if (this.p._w)
            return false;

        var btn_text = $(t).html();

        _ajax({
            url: "/finance/ajax/_promo",
            data: {"_code": $('#' + e).val()},
            success: function(result)
            {
                if (result.code == 200)
                {
                    $('#promoMessage').html('<div style="padding: 10px;"><div class="line_info success"><button class="close" onclick="$(this).parent().fadeOut(\'fast\', function(){ $(this).parent().remove();});" type="button">×</button>' + result.message + '</div></div>');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1000);
                }
                else
                    $('#promoMessage').html('<div style="padding: 10px;"><div class="line_info alert"><button class="close" onclick="$(this).parent().fadeOut(\'fast\', function(){ $(this).parent().remove();});" type="button">×</button>' + result.message + '</div></div>');
            },
            beforeSend: function()
            {
                Finance.p._w = true;
                $(t).html('<i class="fa fa-spin fa-spinner"></i>');
            },
            complete: function()
            {
                Finance.p._w = false;
                $(t).html(btn_text);
                $('#' + e).val('');
            }
        });
    },
    _setParams: function(f, t, e)
    {
        $('#_period').find('.select').removeClass('select');
        $(e).addClass('select');
        this.p._f = f;
        this.p._t = t;
        $('#from').val('');
        $('#to').val('');
        this._getReport();
    },
    _setOrder: function(t, e)
    {
        $('.table_style_1').find('.fa-caret-up').removeClass('fa-caret-up').addClass('fa-caret-down');

        if (this.p._o == t && cCount == 0)
        {
            this.p._a = 'ASC';
            $(e).children().removeClass('fa-caret-up').addClass('fa-caret-down');
            cCount = 1;
        }
        else {
            this.p._a = 'DESC';
            $(e).children().removeClass('fa-caret-down').addClass('fa-caret-up');
            cCount = 0;
        }
        this.p._o = t;
        this._getReport();
    },
    _getPage: function(page)
    {
        this.p.page = page;
        this._getReport();
    },
    _from: function()
    {
        if (trim($('#from').val()) != '' && trim($('#to').val()) != '')
        {
            this.p._f = $('#from').val();
            this.p._t = $('#to').val();
            $('#_period').find('.select').removeClass('select');
            this._getReport();
        }
    },
    _getReport: function(u)
    {
        if (typeof u != 'undefined')
            this.p.url = "/finance"+ u;

        _ajax({
            url: this.p.url + "page=" + this.p.page,
            data: {"_o": this.p._o, "_f": this.p._f, "_a": this.p._a, "_t": this.p._t},
            success: function(result)
            {
                $('#_reportList').html(result.html);
                $('#_pages').html(result.pages);
                $('#_allAmount').html(result.amount);
            },
            beforeSend: function()
            {
                Finance.p._w = true;
                $('#_reportList').html('<div style="text-align: center; padding: 5px 0;">' + loading_img + '</div>');
                $('._loading').html(loading_img);
            },
            complete: function()
            {
                Finance.p._w = false;
            }
        });
    },
    _getBlockingPage: function(p)
    {
        this._getBlockingMoney(p);
    },
    _getBlockingMoney: function(page)
    {
        _ajax({
            url: "/finance/reports/_blocking?page=" + page,
            type: "POST",
            dataType: "json",
            success: function(obj)
            {
                $('#_blockingAmount').html(obj.html);
            },
            beforeSend: function()
            {
                $('#_blockingAmount').html('<div style="text-align: center; padding: 5px 0;">' + loading_img + '</div>');
            }
        });
    },
    _ejectSave: function(e)
    {
        if(this.p._w==true) return;
        
        var b=$(e),_b=b.html();
        
        _ajax({
            url: "/finance/ajax/AutoWithdraw",
            data: $('#_autoEjectForm').serialize(),
            type: "POST",
            dataType: "json",
            success: function(obj)
            {
                $('#_autoEjectMessage').html(obj.html);
            },
            beforeSend: function()
            {
                b.html('<i class="fa fa-spin fa-spinner"></i>');
                Finance.p._w = true;
            },
            complete: function()
            {
                 b.html(_b);
                Finance.p._w = false;
            }
        });

    }
};