var _w = false;

Shop = {
    s: {
        _order: '',
        _oType: 'DESC',
        page: 1,
    },
    buyConfirm: function(_text, id, price)
    {
        Dialog.open(_confirm, {content: _text, buttons: [
                {
                    id: 'z3q652',
                    text: btn_yes,
                    class: "button btn_blue",
                    click: function() {
                        Shop.buy(id, price, 'referral', $('#z3q652'));
                    },
                },
                {
                    text: btn_no,
                    class: "button",
                    click: function() {
                        Dialog.close();
                    }}
            ]});
    },
    buy: function(id, price, act, e)
    {
        if (_w === true)
            return false;

        var b = e, btxt = b.html();

        _ajax({
            url: "/shop/referrals/buy",
            data: {"S[id]": id, "S[price]": price},
            success: function(obj)
            {
                if (obj.code == 200)
                {
                    _w = false;
                    $('#_refCounts').html(obj.count);
                    Shop._get();
                }

                Dialog.open(_info, {content: obj.message});
            },
            beforeSend: function()
            {
                b.html('<i class="fa fa-spin fa-spinner"></i>');
                $('#dialog-message').html(_buy_processing);
                _w = true;
            },
            complete: function(obj)
            {
                b.html(btxt);
                _w = false;
            }
        });
    },
    setOrder: function(prm, element)
    {
        $(".table_head_inside").find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
        if (this.s._order == prm && cCount == 0)
        {
            this.s._oType = "DESC";
            cCount = 1;
            $(element).find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
        }
        else {
            this.s._oType = "ASC";
            this.s._order = prm;
            cCount = 0;
            $(element).find('i').removeClass('fa-caret-down').addClass('fa-caret-up');
        }

        this.s.page = 0;
        this._get();
    },
    _getPage: function(page)
    {
        this.s.page = page;
        this._get();
    },
    _get: function()
    {
        if (_w === true)
            return false;

        _ajax({
            url: "/shop/referrals?page=" + this.s.page,
            data: {"R[sort]": this.s._order, "R[order]": this.s._oType},
            success: function(obj)
            {
                if (obj.code == 200)
                {
                    $('#_referraList').html(obj.html);
                }
            },
            beforeSend: function()
            {
                $('#_referraList').css('opacity', 0.5);
                _w = true;
            },
            complete: function(obj)
            {
                $('#_referraList').css('opacity', 1);
                _w = false;
            }
        });
    }
}