var cCount = 0, loading_img = '<img src="/i/loads.gif" alt="Loading...">', _timer, _tmTho, mouse_is_inside = false, _w = false;

Request = {
    s: {
        _order: '',
        _oType: 'DESC',
        page: 1,
        tid: 0,
    },
    setID: function (id) {
        this.s.tid = id;
    },
    refuse: function (id, e) {

        Dialog.open('Подтверждение заявки', {
            content: '<div style="padding: 7px 15px 0px;">Вы действительно хотите отклонить заказ ?</div>',
            buttons: [
                {text: _cancel, click: function () {
                    $(this).dialog("close");
                }, class: "button"},
                {
                    text: 'Отклонить',
                    id: 'aass5df65g',
                    click: function () {
                        if (_w === true)
                            return false;

                        var b = $('#aass5df65g'), btxt = b.html();

                        _ajax({
                            url: "/twitter/tweets/request?act=refuse&id=" + id,
                            success: function (obj) {
                                Dialog.open(_info, {content: obj.messages});
                                Request._get();
                            },
                            beforeSend: function () {
                                b.html('<i class="fa fa-spin fa-spinner"></i>');
                                _w = true;
                            },
                            complete: function () {
                                b.html(btxt);
                                _w = false;
                            }
                        });
                    },
                    class: "button btn_red"
                }
            ],
        });
    },
    approve: function (id, e) {
        Dialog.open('Подтверждение заявки', {
            content: '<div style="padding: 7px 15px 0px;">Вы действительно хотите подтвердить данную заявку ?</div>',
            buttons: [
                {text: _cancel, click: function () {
                    $(this).dialog("close");
                }, class: "button"},
                {
                    text: 'Подтвердить',
                    id: 'as5df65g',
                    click: function () {
                        if (_w === true)
                            return false;

                        var b = $('#as5df65g'), btxt = b.html();

                        _ajax({
                            url: "/twitter/tweets/request?act=approve&id=" + id,
                            success: function (obj) {
                                Dialog.open(_info, {content: obj.messages});
                                Request._get();
                            },
                            beforeSend: function () {
                                b.html('<i class="fa fa-spin fa-spinner"></i>');
                                _w = true;
                            },
                            complete: function () {
                                b.html(btxt);
                                _w = false;
                            }
                        });
                    },
                    class: "button btn_blue"
                }
            ],
        });
    },
    _getPage: function (page) {
        this.s.page = page;
        this._getFulfilled();
    },
    setOrder: function (prm, element) {
        $(".table_head_inside").find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
        if (this.s._order == prm && cCount == 0) {
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
        this._getFulfilled();
    },
    _getFulfilled: function () {
        _ajax({
            url: "/twitter/tweets/fulfilled?tid=" + this.s.tid + "&page=" + this.s.page + '&_o=' + this.s._order + '&_a=' + this.s._oType,
            success: function (obj) {
                $('#_listTwAccounts').html(obj.html);
                $('#_listTwAccounts').css('opacity', 1);
            },
            beforeSend: function () {
                $('#_listTwAccounts').css('opacity', 0.5);
            },
            complete: function () {
                $('#_listTwAccounts').css('opacity', 1);
            }
        });
    },
    _get: function () {
        _ajax({
            url: "/twitter/tweets/request?page=" + this.s.page + '&_o=' + this.s._order + '&_a=' + this.s._oType,
            success: function (obj) {
                $('#_listTwRequest').html(obj.html);
                $('#_listTwRequest').css('opacity', 1);
            },
            beforeSend: function () {
                $('#_listTwRequest').css('opacity', 0.5);
                $('#loadingIndicator').show();
            },
            complete: function () {
                $('#_listTwRequest').css('opacity', 1);
                $('#loadingIndicator').hide();
            }
        });
    }
}

Order = {
    s: {
        _order: '',
        _oType: 'DESC',
        page: 1
    },
    removeTweet: function (id, e) {
        Dialog.open('Подтверждение удаления', {
            content: '<div style="padding: 7px 15px 0px;">Вы действительно хотите удалить заказ ?</div>',
            buttons: [
                {text: _cancel, click: function () {
                    $(this).dialog("close");
                }, class: "button"},
                {
                    text: 'Удалить',
                    id: 'as523se5',
                    click: function () {
                        if (_w === true)
                            return false;

                        var b = $('#as523se5'), btxt = b.html();

                        _ajax({
                            url: "/twitter/ajaxTweets/order?act=removeTweet&id=" + id,
                            success: function (obj) {
                                Dialog.open(_info, {content: obj.content});

                                if (obj.code == 199)
                                    window.location.href = '/twitter/tweets/status';

                                if (obj.code == 200)
                                    Order.getTweets();
                            },
                            beforeSend: function () {
                                b.html('<i class="fa fa-spin fa-spinner"></i>');
                                _w = true;
                            },
                            complete: function () {
                                b.html(btxt);
                                _w = false;
                            }
                        });
                    },
                    class: "button btn_orange"
                }
            ],
        });
    },
    remove: function (id, e) {
        var b = $(e), btxt = b.html();
        Dialog.open('Подтверждение удаления', {
            content: '<div style="padding: 7px 15px 0px;">Вы действительно хотите удалить заказ ?</div>',
            buttons: [
                {text: _cancel, click: function () {
                    $(this).dialog("close");
                }, class: "button"},
                {
                    text: 'Удалить',
                    id: 'aser3S523se5',
                    click: function () {
                        if (_w === true)
                            return false;

                        var b = $('#aser3S523se5'), btxt = b.html();

                        _ajax({
                            url: "/twitter/ajaxTweets/order?act=remove&id=" + id,
                            success: function (obj) {
                                Dialog.open(_info, {content: obj.content});
                                if (obj.code == 200)
                                    window.location.reload("true");
                            },
                            beforeSend: function () {
                                b.html('<i class="fa fa-spin fa-spinner"></i>');
                                _w = true;
                            },
                            complete: function () {
                                b.html(btxt);
                                _w = false;
                            }
                        });
                    },
                    class: "button btn_orange"
                }
            ],
        });
    }
}

Tweets = {
    s: {
        checkWait: false,
        uWait: false,
        oldCount: 0,
        pCount: 0,
        urlWait: false,
        lastCode: 0,
        isSend: false,
        selectAll: false,
        group: '',
        timer: false,
        sbtn: false,
        afterSave: '',
        lastSave: '',
        preventTweet: false,
        page: 0,
        timer: false,
        sendData: {},
        queryString: '',
        limit: 10,
        _order: 'date',
        _oType: 'ASC',
        parseTemplate: '',
        _tid: null,
        params: []
    },
    remvoeList: function (uid) {
        Dialog.open(_confirm, {
            content: 'Вы дейстивтельно хотите удалить список твитов ?',
            buttons: [
                {
                    text: btn_yes,
                    id: "_confirmRemove",
                    click: function () {
                        var btn = $('#_confirmRemove'), btxt = btn.html(), _w = false;
                        if (_w === true)
                            return false;
                        _ajax({
                            url: "/twitter/ajaxTweets/prepared?act=remove&id=" + uid,
                            success: function (result) {
                                Dialog.close();
                                Dialog.open(_info, {content: 'Список успешно удален'});
                                Tweets._getPrepared();
                            },
                            beforeSend: function () {
                                btn.html(loading_img);
                                _w = true;
                            },
                            complete: function () {
                                btn.html(btxt);
                                _w = false;
                            }
                        });
                    },
                    class: "button btn_blue"
                },
                {text: btn_no, click: function () {
                    $(this).dialog("close");
                }, class: "button"}
            ]
        });
    },
    getPreparedPage: function (page) {
        this.s.page = page;
        this._getPrepared();
    },
    _getPrepared: function () {
        _ajax({
            url: "/twitter/tweets/prepared?page=" + this.s.page + '&search=' + this.s.queryString + '&order=' + this.s._order + '&sort=' + this.s._oType,
            success: function (result) {
                $('#preparedList').html(result.html);
                $('#_all_count').html(result.count);
            },
            beforeSend: function () {
                $('#preparedList').html('<div style="text-align: center; padding: 5px;">' + loading_img + '</div>');
                $('#_searchButton').find('i').removeClass('fa-search').addClass('fa-spin fa-spinner');
            },
            complete: function () {
                $('#_searchButton').find('i').removeClass('fa-spin fa-spinner').addClass('fa-search');
            }
        });
    },
    filterRun: function (i, t, k) {
        $('#_filterRun').next().slideDown('fast', function () {
            if ($(this).is(":visible") == true) {
                $('#_filterRun').children().children().removeClass('fa-caret-down').addClass('fa-caret-up');
            }
            else {
                $('#_filterRun').children().children().removeClass('fa-caret-up').addClass('fa-caret-down');
            }
        });
        $('#_filterRun').siblings('.line_title').next().slideUp('fast', function () {
            $(this).prev().children().children().removeClass('fa-caret-up').addClass('fa-caret-down');
        });
        if (t == 0) {
            $('#sfilter_manual').attr("checked", true);
            $('#sfilter_manual-styler').addClass('checked');
        }
        else {
            $('#sfilter_automat').attr("checked", true);
            $('#sfilter_automat-styler').addClass('checked');
        }

        this.pType(t, k, i);
    },
    saveFilter: function (_tp, tid) {
        Dialog.open(_filter_save_title, {
            content: '<div id="fcontent"><div id="_fmessages"></div><div class="filterRow"><input type="text" id="_filter_title" name="_filter_title" value="" class="filterFormInput" placeholder="Введите название фильтра"></div><div class="filterRow"><textarea placeholder="Введите краткое описание фильтра" class="filterFormArea" id="_filter_description" name="_filter_description"></textarea></div></div>',
            buttons: [
                {
                    text: _save,
                    id: "btn_save_filter",
                    click: function () {
                        var btn = $('#btn_save_filter'), btxt = btn.html();
                        _ajax({
                            data: $('#_filterForm').serialize(),
                            url: "/twitter/tweets/ajax/saveFilter?filter[tid]=" + tid + "&filter[_tp]=" + _tp + "&filter[title]=" + encodeURIComponent($('#_filter_title').val()) + "&filter[description]=" + encodeURIComponent($('#_filter_description').val()),
                            success: function (result) {
                                if (result.code == 200) {
                                    $('#fcontent').html(result.html);
                                    $('#btn_save_filter').remove();
                                    $('#block_filter').html(result.flist);
                                }
                                else {
                                    $('#_fmessages').html('<div class="line_info alert" style="margin-bottom: 7px;">' + result.message + '</div>');
                                }
                            },
                            beforeSend: function () {
                                btn.html(loading_img);
                            },
                            complete: function () {
                                btn.html(btxt);
                            }
                        });
                    },
                    class: "button btn_blue"
                },
                {text: _close, click: function () {
                    $(this).dialog("close");
                }, class: "button"}
            ]
        });
    },
    removeFilter: function (id, e) {
        if (!id)
            return false;
        _ajax({
            url: "/twitter/tweets/ajax/removeFilter?id=" + id,
            success: function (result) {
                if (result.code == 200) {
                    $('#filter_' + id).fadeOut('slow', function () {
                        $(this).remove();
                    });
                }
            },
            beforeSend: function () {
                $(e).children().removeClass('fa-trash').addClass('fa-spin fa-spinner');
            },
            complete: function () {
                $(e).children().removeClass('fa-spin fa-spinner').addClass('fa-trash');
            }
        });
    },
    setParams: function (b) {
        this.s.sendData = $('#fParams').serialize();
        this.s.page = 0;
        this.getAccountList(b);
    },
    _setLimit: function (e) {
        this.s.limit = e.value;
        this.getAccountList();
    },
    resetParams: function (b) {
        this.s.sendData = {};
        _ajax({
            data: this.s.sendData,
            url: "/twitter/resetParams",
            success: function (result) {
                $('#fParams').html(result.html);
            },
            beforeSend: function () {
                $('#fParams').html('<div style="text-align: center;">' + loading_img + '</div>');
            }
        });
    },
    resetList: function (b) {
        this.s.sendData = {};
        this.s.page = 0;
        this.getAccountList();
    },
    _getFromQuery: function (element) {
        this.s.queryString = $("#setQuery").val();
        clearTimeout(timer);
        timer = setTimeout(function () {
            Tweets.getAccountList();
        }, 300)
    },
    _getPreparedFromQuery: function (element) {
        this.s.queryString = $("#setQuery").val();
        clearTimeout(timer);
        timer = setTimeout(function () {
            Tweets._getPrepared();
        }, 300)
    },
    _setOrder: function (prm, element) {
        this.s.page = 0;
        $(".table_head_inside").find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
        if (this.s._order == prm && cCount == 0) {
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

        this.getAccountList();
    },
    _setPreparedOrder: function (prm, element) {
        $(".table_head_inside").find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
        if (this.s._order == prm && cCount == 0) {
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

        this._getPrepared();
    },
    getAccountList: function (b) {
        var data;

        if (typeof this.s.sendData === 'string')
            data = this.s.sendData + '&Twitter[_q]=' + this.s.queryString + '&Twitter[limit]=' + this.s.limit + '&Twitter[_o]=' + this.s._order + '&Twitter[_a]=' + this.s._oType;
        else
            data = 'Twitter[_q]=' + this.s.queryString + '&Twitter[limit]=' + this.s.limit + '&Twitter[_o]=' + this.s._order + '&Twitter[_a]=' + this.s._oType;

        _ajax({
            data: data,
            url: "/twitter?page=" + this.s.page,
            success: function (result) {
                if (result['code'] === 200) {
                    $('#_stats').html(result['stats']);
                    $('#lContent').html(result['html']);
                }
                else
                    Dialog.open(_error, {content: result['message']});
            },
            beforeSend: function () {
                $('#lContent').css('opacity', '0.5');
            },
            complete: function () {
                $('#lContent').css('opacity', '1');
            }
        });
    },
    accordion: function (e, hideAll) {
        $(e).next().slideToggle('fast', function () {
            if ($(this).is(":visible") == true) {
                $(e).children().children().removeClass('fa-caret-down').addClass('fa-caret-up');
            }
            else {
                $(e).children().children().removeClass('fa-caret-up').addClass('fa-caret-down');
            }
        });
        if (hideAll) {
            $(e).siblings(hideAll).next().slideUp('fast', function () {
                $(this).prev().children().children().removeClass('fa-caret-up').addClass('fa-caret-down');
            });
        }
    },
    _bwlist: function (e) {
        if (_w === true) return false;

        var $e = $(e), $td = $e.parent(), $id = $td.attr('data-id'), classes = $e.children().attr('class');

        _ajax({
            data: {"id": $id, 'bw': $e.attr('data-type')},
            url: "/twitter/bwlist",
            success: function (obj) {
                if (obj['code'] === 200) {
                    if ($e.hasClass('selected')) {
                        $e.removeClass('selected');
                    }
                    else {
                        $td.find('.selected').removeClass('selected');
                        $e.addClass('selected');
                    }

                    $('#blackStat_' + $id).html(obj['black_count']);
                    $('#whiteStat_' + $id).html(obj['white_count']);
                    $('#_stats').html(obj['stats']);
                }
                else
                    Dialog.open(_error, {"content": obj['message']});
            },
            beforeSend: function () {
                $e.children().removeClass(classes).addClass('fa fa-spin fa-spinner');
                _w = true;
            },
            complete: function () {
                $e.children().removeClass('fa fa-spin fa-spinner').addClass(classes);
                _w = false;
            }
        });
    },
    _getPage: function (p) {
        this.s.page = p;
        this.getAccountList();
    },
    getFromLis: function (t) {
        this.s.sendData = '_tlist=' + t;
        this.getAccountList();
    },
    getAccountInfo: function (tid, title) {
        Dialog.open(title, {'content': '<div id="_content_' + tid + '"><div style="margin-top: 15px; text-align: center;">' + loading_img + '</div><div>', buttons: [
            {text: _close, click: function () {
                $(this).dialog("close");
            }, class: "button"}
        ]});
        _ajax({
            data: {"tid": tid},
            url: "/twitter/ajax/getInfo",
            dataType: "html",
            success: function (result) {
                $('#_content_' + tid).html(result);
            }
        });
    },
    check: function (e) {
        var count = 0, str = e.value, list = [], cCount = parseInt($('#postCount').html());

        list = str.split("\n");

        for (var i = 0; i < list.length; i++) {
            if (trim(list[i]) !== '')
                count++;
        }

        if (cCount < count)
            Tweets.collectionStats(count - cCount);
        else
            Tweets.collectionStats(cCount - count, true);

        $('#postCount').html(count);
    },
    loadCancel: function (id, el) {
        var txt = $(el).html();
        _ajax({
            data: {"uid": id},
            url: "/twitter/ajaxTweets/_removeUpload",
            success: function (obj) {
                if (obj.code == 200) {
                    $('#sitemap_progress').fadeOut(function () {
                        $('#sitemap_progress').empty();
                    });
                    Tweets.s.urlWait = false;
                    clearInterval(Tweets.s.timer);
                }
                else
                    Dialog.open(_error, {content: obj.html});
                $(el).html(txt);
            },
            beforeSend: function () {
                $(el).html(loading_img);
            },
            complete: function (obj) {
                $(el).html(txt);
            },
        });
    },
    remove: function (aid) {
        Tweets.collectionStats($('#_list_' + aid).attr('data-count'), true);
        $('#_list_' + aid).remove();
        $('#tweet_' + aid).remove();
    },
    floadUrl: function (el) {
        if (this.s.urlWait == true)
            return false;
        var button = $(el), txt = button.html();
        if (trim($('#' + button.attr('data-url')).val()) == "") {
            Dialog.open(_error, {content: _input_empty});
            return false;
        }

        _ajax({
            data: {"parseTemplate[words]": $('#parseWords').val(), "parseTemplate[exclude]": $('#parseExclide').val(), "parseTemplate[url]": $('#parseUrl').val(), "_url": $('#' + button.attr('data-url')).val(), "_type": button.attr('data-type'), "only_new": ($('#' + button.attr('data-only')).is(':checked')) ? 1 : 0},
            url: "/twitter/ajaxTweets/_upload",
            success: function (obj) {
                switch (obj.code) {
                    case 200:
                        $("#_sitemaplist").append(obj.html);
                        $('#_data').append('<textarea id="tweet_' + obj.areaID + '" name="Tweets[]" style="visibility: hidden; height: 0px;">' + obj.tweets + '</textarea>');
                        Tweets.collectionStats(obj.count - Tweets.s.oldCount);
                        Tweets.s.oldCount = obj.count;
                        Tweets.s.urlWait = false;
                        break;
                    case 201:
                        Tweets.progress(obj.uid, $('#sitemap_progress'), obj.html);
                        break;
                    default:
                        Dialog.open(_error, {content: obj.html});
                        Tweets.s.urlWait = false;
                }

                button.html(txt);
            },
            beforeSend: function () {
                button.html(loading_img);
                Tweets.s.urlWait = true;
            },
            complete: function (obj) {
                button.html(txt);
                Tweets.s.urlWait = false;
            },
        });
    },
    progress: function (uid, e, phtml) {
        var el = e;
        el.fadeIn(function () {
            el.html(phtml).append('<button type="button" name="_yt1" class="button icon" onclick="Tweets.loadCancel(\'' + uid + '\', this);">Отменить</button>');
        });
        Tweets.s.timer = setInterval(function () {
            _ajax({
                url: "/twitter/ajaxTweets/getprogress?uid=" + uid,
                success: function (obj) {
                    if (obj.code == 200) {
                        $('#progress').css('width', obj.procent + '%');
                        $('#pcount').html(obj.ecount);
                        $('#rcount').html(obj.rcount);
                    }
                    else {

                        if (obj.code == 201) {
                            $('#_sitemaplist').append(obj.html);
                            $('#_data').append('<textarea id="tweet_' + obj.areaID + '" name="Tweets[]" style="visibility: hidden; height: 0px;">' + obj.tweets + '</textarea>');
                            Tweets.collectionStats(obj.ecount);
                            Tweets.s.urlWait = false;
                        }
                        else {
                            Dialog.open(_error, {content: obj.html});
                        }

                        Tweets.s.urlWait = false;
                        el.fadeOut(function () {
                            el.empty();
                        });
                        clearInterval(Tweets.s.timer);
                    }
                }
            })
        }, 2000);
    },
    send: function (e, f) {
        if (this.s.isSend === true)
            return false;

        var b = $(e), _text = $(e).html();

        _ajax({
            url: "/twitter/tweets/collection",
            data: $('#' + f).serialize(),
            success: function (obj) {
                if (obj.code == 301) {
                    window.location.href = obj.url;
                }
                else {
                    Dialog.open(_error, {content: obj.message});
                }
            },
            beforeSend: function () {
                Tweets.s.isSend = true;
                b.html(loading_img);
                $('#_loadingMsg').show();
            },
            complete: function () {
                b.html(_text);
                $('#_loadingMsg').hide();
                Tweets.s.isSend = false;
            }
        });
    },
    selectAll: function () {
        $("#block_2_list input:checkbox").each(function () {
            if ($(this).is(':checked')) {
                Tweets.s.selectAll = true;
            }
        });
        if (Tweets.s.selectAll == true) {
            $("#block_2_list input:checkbox").prop('checked', false).next().removeClass("checked");
            Tweets.s.selectAll = false;
        }
        else {
            $("#block_2_list input:checkbox").prop('checked', true).next().addClass("checked");
        }
    },
    saveRoster: function (element) {
        Dialog.open('Сохранение списка постов', {
            content: '<div id="fcontent"><div id="_fmessages" class="line_info alert" style="margin-bottom: 9px; display: none;"></div><div class="filterRow"><input type="text" id="_list_title" placeholder="Введите название списка постов" name="_filter_title" value="" class="filterFormInput"></div></div>',
            buttons: [
                {
                    text: _save,
                    id: "btn_save_filter",
                    click: function () {
                        var btn = $('#btn_save_filter'), btxt = btn.html();
                        if (_w === true)
                            return false;

                        _ajax({
                            url: "/twitter/tweets/roster?_tid=" + Tweets.s._tid + "&action=saveRoster",
                            data: {"title": $('#_list_title').val()},
                            success: function (result) {
                                if (result.code === 200) {
                                    $(element).remove();
                                    Dialog.open(_info, {content: 'Список твитов успешно сохранён на странцу - "Сохранёные твиты"'});
                                }
                                else {
                                    clearTimeout(timer);
                                    $('#_fmessages').html(result.message).fadeIn(function () {
                                        timer = setTimeout(function () {
                                            $('#_fmessages').fadeOut('slow');
                                        }, 3000)
                                    });
                                }
                            },
                            beforeSend: function () {
                                btn.html(loading_img);
                                _w = true;
                            },
                            complete: function () {
                                btn.html(btxt);
                                _w = false;
                            }
                        });
                    },
                    class: "button btn_blue"
                },
                {text: _close, click: function () {
                    $(this).dialog("close");
                }, class: "button"}
            ]
        });
    },
    listToogle: function (e) {
        Tweets.s.params = ['group=' + e];
        this._getContent(0);
    },
    edit: function (obj) {
        Dialog.open(obj.title, {
            content: '<div style="width: 425px;"><div id="msgBoxInfo" style="display: none; margin-top: -5px; margin-bottom: 7px" class="line_info alert">' + $('#msg_' + obj.tid).clone().html() + '</div><div><textarea id="_tweetArea" class="modal">' + obj.tweet + '</textarea></div><div class="symbols">' + _charset + ': <span id="charset_' + obj.tid + '">0</span></div></div>',
            buttons: [
                {text: _cancel_changes, id: 'afterSave', click: function () {
                    $('#_tweetArea').val(Tweets.s.lastSave);
                    $('#afterSave').button({disabled: true});
                    $('#bSave').button({disabled: false});
                    $('#_tweetArea').change();
                },
                    class: "button btn_orange", disabled: true},
                {text: _save, id: 'bSave', click: function (event) {
                    Tweets.tweetSave(event.target, obj);
                }, class: "button btn_blue", disabled: true},
                {text: _close, click: function () {
                    $(this).dialog("close");
                }, class: "button btn_red"},
            ]
        });

        if (obj.msg === true) {
            $('#msgBoxInfo').show();
            clearTimeout(_timer);
            _timer = setTimeout(function () {
                $('#msgBoxInfo').fadeOut();
            }, 5000);
        }

        this.s.preventTweet = obj.tweet;

        Tweets.allowSaveTweet();
        $('#_tweetArea').focusEnd();
        $('#_tweetArea').character({counter: '#charset_' + obj.tid, overClass: '_symbolsLimit', countType: 'tweet'});

        $('body').on("dialogclose", function (event, ui) {
            clearTimeout(_tmTho);
            Tweets.s.lastSave = '';
            Tweets.s.preventTweet = '';
            $('#afterSave').button({disabled: true});
        });
    },
    allowSaveTweet: function () {
        var tweet = $('#_tweetArea').val();

        if (this.s.preventTweet === tweet || trim(tweet) === '')
            $('#bSave').button({disabled: true});
        else
            $('#bSave').button({disabled: false});

        if (this.s.lastSave !== tweet && this.s.lastSave !== '')
            $('#afterSave').button({disabled: false});
        else
            $('#afterSave').button({disabled: true});

        _tmTho = setTimeout('Tweets.allowSaveTweet();', 50);
    },
    tweetSave: function (btn, obj) {
        var tweet = $('#_tweetArea').val(), e = $(btn), box = $('#msgBoxInfo');

        if (trim(tweet) === "")
            return false;

        p = ['_tid=' + this.s._tid, 'action=tweetEdit'];

        _ajax({
            url: "/twitter/tweets/roster?" + p.join('&'),
            data: {"Edit[id]": obj.tid, "Edit[tweet]": encodeURIComponent(tweet)},
            success: function (obj) {
                box.fadeIn().html(obj.message);

                if (obj.code === 200) {
                    if (obj.info !== undefined)
                        $('#_stats').html(obj.info);

                    $('#block_2_list').html(obj.tweets);

                    if (obj.hidde === 1)
                        $('.aHidden').css('display', 'none');

                    if (obj.next === true)
                        $('#_nextButton').attr("disabled", false);
                    else
                        $('#_nextButton').attr("disabled", true);

                    Tweets.s.lastSave = Tweets.s.preventTweet;
                    Tweets.s.preventTweet = tweet;
                    $('#afterSave').button({disabled: false});

                    box.removeClass('warn alert');

                    clearTimeout(Tweets.s.timer);
                    Tweets.s.timer = setTimeout(function () {
                        box.fadeOut();
                    }, 5000);

                    $('#tw_action').styler();
                }
                else
                    box.addClass('alert');
            },
            beforeSend: function () {
                e.html(loading_img);
                $('#block_2_list').css('opacity', '0.5');
            },
            complete: function () {
                $('#block_2_list').css('opacity', '1');
                e.html(_save);
            }
        });
    },
    _getContent: function (page) {
        this.s.page = page;

        p = ['_tid=' + this.s._tid, 'page=' + page];
        params = p.concat(this.s.params);

        _ajax({
            url: "/twitter/tweets/roster?" + params.join('&'),
            data: this.s.sendData,
            success: function (obj) {
                if (obj.code === 200) {
                    if (obj.info !== undefined)
                        $('#_stats').html(obj.info);

                    $('#block_2_list').html(obj.tweets);

                    if (obj.hidde === 1) {
                        $('.aHidden').css('display', 'none');
                    }

                    if (obj.next === true)
                        $('#_nextButton').attr("disabled", false);
                    else
                        $('#_nextButton').attr("disabled", true);

                    $('#tw_action').styler();
                }
                else if (obj.code === 301) {
                    window.location.href = obj.url;
                } else {
                    Dialog.open(_error, {content: obj.message});
                }
            },
            beforeSend: function () {
                $('#block_2_list').css('opacity', '0.5');
            },
            complete: function () {
                $('#block_2_list').css('opacity', '1');
            }
        });
    },
    removeTweets: function (title, text, tid, isGroup) {
        var dataSend;

        if (tid === 0) {
            dataSend = $('#_twForm').serialize();
        } else {
            dataSend = {'tweets': tid};
        }

        Dialog.confirm(text, title, function () {
            Tweets.s.params = ['action=remove', 'group=' + isGroup];
            Tweets.s.sendData = dataSend;

            Tweets._getContent(0);

            Tweets.s.params = [];
            Tweets.s.sendData = {};
        });

    },
    action: function (e, a) {
        if (!a)
            return false;

        switch (a) {
            case "remove":
                Tweets.removeTweets(_title_remove_tweets, _text_remove_tweets, 0, 'all');
                break;
            default:
                Dialog.open(_error, {content: _unknown_action});
        }
    },
    PlacementMethod: function (m, t, f, e) {
        _ajax({
            url: "/twitter/tweets/ajax/PlacementMethod?m[tid]=" + t + "&m[method]=" + m + "&m[filter]=" + f,
            success: function (obj) {
                if (obj.code === 200) {
                    $('#pTweets').html(obj.html);
                }
                else {
                    Dialog.open(_error, {content: obj.message});
                    $(e).attr('checked', false).next().removeClass('checked');
                    $('#pTweets').empty();
                }

            },
            beforeSend: function () {
                $('#pTweets').html('<div style="text-align: center; padding: 4px;">' + loading_img + '</div>');
            }
        });
    },
    parseTemplate: function (e) {
        _ajax({
            url: "/twitter/ajaxTweets/parseTemplate",
            type: "POST",
            dataType: "html",
            data: $('#_parseTemplate').serialize(),
            success: function (result) {
                Dialog.open(_info, {content: result, buttons: [
                    {text: _save, click: function () {
                        $(this).dialog("close");
                    }, class: "button"}
                ]});
            },
            beforeSend: function () {
                $(e).find('.fa-cog').removeClass('fa-cog').addClass('fa-spin fa-spinner');
            },
            complete: function () {
                $(e).find('.fa-spin').removeClass('fa-spin fa-spinner').addClass('fa-cog');
            }
        });
    },
    parseChange: function (id, v) {
        $('#' + id).val(v);
    },
    collectionStats: function (c, m) {
        var cCount = parseInt($('#all_tweets_add').html()), count = parseInt(c);

        if (m === true)
            $('#all_tweets_add').html(cCount - count);
        else
            $('#all_tweets_add').html(cCount + count);
    }
};

var Twitter = {
    o: {
        data: {
            page: 1,
            limit: 10
        },
        m: {
            p: {
                ping: 0,
                id: 0,
                payment: 0
            },
            s: {
                _tweets: 0,
                _accounts: 0,
                _amount: 0,
                to_pay: 0
            },
            t: {
                params: {
                    days: {"week": [0, 1, 2, 3, 4, 5, 6], 'workday': [1, 2, 3, 4, 5], 'weekend': [0, 6]},
                    hours: {
                        "24": [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23],
                        "morning": [6, 7, 8, 9, 10, 11, 12],
                        "daytime": [12, 13, 14, 15, 16, 17, 18],
                        "evenings": [18, 19, 20, 21, 22, 23, 0],
                        "night": [0, 1, 2, 3, 4, 5, 6]
                    },
                    ds: 'week',
                    hs: '24'
                },
                range: function (t, v, e) {
                    var $e = $(e), $nav;

                    if (t == 'hours') {
                        $nav = $('.hours');

                        if (this.params.hours[v] !== undefined)
                            this.params.hs = v;
                    }
                    else {
                        $nav = $('.days');

                        if (this.params.days[v] !== undefined)
                            this.params.ds = v;
                    }

                    var days = this.params.days[this.params.ds], hours = this.params.hours[this.params.hs];

                    $('.time_targeting').find('.select').removeClass('select').addClass('no_select');

                    $.each(days, function (index, value) {
                        $.each(hours, function (i, v) {
                            v = v + 1;
                            $('#days_' + value + '_' + v).removeClass('no_select').addClass('select').children().attr('checked', 'checked');
                        });
                    });

                    $('.time_targeting').find('.no_select').children().attr('checked', false);
                    $nav.find('.selection').removeClass('selection');
                    $e.addClass('selection');
                },
                update: function () {

                },
                toggle: function (e) {
                    var $e = $(e);

                    if ($e.hasClass('select')) {
                        $e.removeClass('select').addClass('no_select');
                        $e.children().attr('checked', false);
                    }
                    else {
                        $e.removeClass('no_select').addClass('select');
                        $e.children().attr('checked', true);
                    }
                }
            },
            d: {
                data: {
                    page: 1,
                    limit: 10,
                    order: 'group',
                    sort: 'DESC',
                    accounts: new Array(),
                    sendData: {},
                    $r: {},
                    _q: '',
                    _sl: 'no_use'
                },
                addTweets: function (id, e) {
                    this.data.$r = $('#_accountData_' + id);
                    if (!$('#_twList').is(':visible')) {
                        $('#_twList').tPosition(e, {
                            anchor: ['cr', 'rc'],
                            offset: [50, 85]
                        });
                        $('#_twList').show();
                        Twitter.o.m.p.id = id;
                        this.getTweetsList();
                    }
                    else {
                        $('#_twList').hide();
                        $('#tweetsList').empty();
                        Twitter.o.m.d.data.$r.attr('data-count', Twitter.o.m.d.data.$r.attr('data-old'));
                    }

                    $(document).ready(function () {
                        $('#_twList').hover(function () {
                            mouse_is_inside = true;
                        }, function () {
                            mouse_is_inside = false;
                        });
                        $("body").mouseup(function () {
                            if (!mouse_is_inside) {
                                $('#_twList').hide();
                                $('#tweetsList').empty();
                                Twitter.o.m.d.data.$r.attr('data-count', Twitter.o.m.d.data.$r.attr('data-old'));
                            }
                        });
                    });

                    Twitter.o.m.update();
                },
                tweet: function (id, e) {
                    var $i = $(e).find('input:checkbox'), $d = $('#_accountData_' + Twitter.o.m.p.id);
                    if ($(e).find('a').hasClass('select')) {
                        $(e).find('a').removeClass('select');
                        $i.prop('checked', false);
                        $d.attr('data-count', parseInt($d.attr('data-count')) - 1);
                    }
                    else {
                        $(e).find('a').addClass('select');
                        $i.prop('checked', true);
                        $d.attr('data-count', parseInt($d.attr('data-count')) + 1);
                    }
                },
                getTweetsList: function (a, e) {
                    if (_w === true) return false;

                    if (a !== undefined)
                        this.data._sl = a;

                    _ajax({
                        data: Tweets.s.sendData,
                        url: "/twitter/tweets/ajax/getList?act=" + this.data._sl + "&rid=" + Twitter.o.m.p.id + "&_tid=" + _tid + "&_q=" + this.data._q,
                        success: function (obj) {
                            if (obj.code == 200) {
                                $('#tweetsList').html(obj.tweets);
                            }
                            else {
                                $('#tweetsList').html('<div class="line_info alert" style="margin: 5px;">' + obj.message + '</div>');
                            }
                        },
                        beforeSend: function () {
                            $('#tweetsList').html('<div style="text-align:center; padding:7px;">' + loading_img + '</div>');
                            _w = true;
                        },
                        complete: function () {
                            if (a !== undefined)
                                Twitter.o.m.d.data.$r.attr('data-count', Twitter.o.m.d.data.$r.attr('data-old'));
                            _w = false;
                        }
                    });
                },
                save: function (e) {
                    if (_w === true) return false;
                    _ajax({
                        data: $('#tweetsListForm').serialize(),
                        url: "/twitter/tweets/ajax/saveList?rid=" + Twitter.o.m.p.id + "&_tid=" + _tid,
                        success: function (obj) {
                            if (obj.code == 200) {
                                $('#_twList').hide();
                                $('#tweetsList').empty();
                                var $c = $('#accounts_' + Twitter.o.m.p.id), $d = Twitter.o.m.d.data.$r, $id = Twitter.o.m.p.id, $t = Twitter.o.m;

                                if (obj.count > 0) {
                                    $('#_tweets_' + $id).html('<span title="Добавлено твитов на размещение в аккаунт">' + obj.count + '</span> - <a onclick="Twitter.o.m.d.addTweets(\'' + $id + '\',this);" href="javascript:void(0);" class="here">Показать твиты <i class="fa fa-caret-down"></i></a>');
                                    if ($d.attr('data-select') == 'no' && !$c.is(':checked')) {
                                        $t.s._accounts += 1;
                                    }

                                    $d.attr('data-select', 'yes');
                                }
                                else {
                                    $d.attr('data-count', '0');
                                    $t.s._accounts -= 1;
                                    $('#_tweets_' + $id).html('<span title="Автоматический подбор твита">А</span> - <a onclick="Twitter.o.m.d.addTweets(\'' + $id + '\',this);" href="javascript:void(0);" class="here">Выбрать твит <i class="fa fa-caret-down"></i></a>');
                                }

                                if ($d.attr('data-count') > $d.attr('data-old')) {
                                    if (!$c.is(':checked') || $d.attr('data-old') > 1) {
                                        $t.s._amount += ($d.attr('data-count') - $d.attr('data-old')) * $d.attr('data-price');
                                        $t.s._tweets += ($d.attr('data-count') - $d.attr('data-old'));
                                    }
                                    else {
                                        $t.s._amount += (($d.attr('data-count') - 1) - $d.attr('data-old')) * $d.attr('data-price');
                                        $t.s._tweets += (($d.attr('data-count') - 1) - $d.attr('data-old'));
                                    }
                                }
                                else {
                                    $t.s._amount -= ($d.attr('data-old') - $d.attr('data-count')) * $d.attr('data-price');
                                    $t.s._tweets -= ($d.attr('data-old') - $d.attr('data-count'));
                                    $d.attr('data-select', 'no').attr('data-old', '0');
                                }

                                $d.attr('data-old', $d.attr('data-count'));
                                if ($d.attr('data-old') > 0 || $d.attr('data-count') > 0) {
                                    $t.d.put($c.attr('data-id'));
                                    $c.prop('checked', true).next().addClass('checked');
                                }
                                else {
                                    $c.prop('checked', false).next().removeClass('checked');
                                    $t.d.remove($c.attr('data-id'));
                                }
                            }
                            else {
                                Dialog.open(_error, {content: obj.message});
                            }

                            Twitter.o.m.update();
                        },
                        beforeSend: function () {
                            $(e).find('i').removeClass('fa-save').addClass('fa-spin fa-spinner');
                            _w = true;
                        },
                        complete: function () {
                            $(e).find('i').removeClass('fa-spin fa-spinner').addClass('fa-save');
                            _w = false;
                        }
                    });
                },
                closeList: function () {
                    $('#_twList').hide();
                },
                setListQuery: function (q) {
                    this.data._q = q;
                    clearTimeout(_timer);
                    _timer = setTimeout(function () {
                        Twitter.o.m.d.getTweetsList();
                    }, 300);
                },
                getBySelect: function (p, e) {
                    $(".select_view_tweet").find('.select').removeClass('select');
                    $(e).addClass('select');
                    this.getTweetsList(p, e);
                },
                getPage: function (page) {
                    this.data.page = page;
                    this.get();
                },
                setLimit: function (limit) {
                    this.data.page = 1;
                    this.data.limit = limit;
                    this.get();
                },
                setOrder: function (prm, element) {
                    $(".table_head_inside").find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
                    if (this.data.order == prm && cCount == 0) {
                        this.data.sort = "DESC";
                        cCount = 1;
                        $(element).find('i').removeClass('fa-caret-up').addClass('fa-caret-down');
                    }
                    else {
                        this.data.sort = "ASC";
                        this.data.order = prm;
                        cCount = 0;
                        $(element).find('i').removeClass('fa-caret-down').addClass('fa-caret-up');
                    }

                    this.data.page = 1;
                    this.get();
                },
                get: function () {
                    if (_w === true) return false;

                    _ajax({
                        data: this.data.sendData,
                        url: "/twitter/tweets/ajax/getAccounts?act=rows&page=" + this.data.page + '&a[limit]=' + this.data.limit + '&a[_o]=' + this.data.order + '&a[_a]=' + this.data.sort,
                        success: function (obj) {
                            if (obj.code == 200) {
                                $('#_tweetsFormPlace').html(obj.html);
                                $('#_all_select').prop('checked', false).next().removeClass('checked');

                                if (Twitter.o.m.d.data.accounts.length > 0) {
                                    $.each(Twitter.o.m.d.data.accounts, function (key, value) {
                                        $('#accounts_' + value).prop('checked', true).next().addClass('checked');
                                    });

                                    if ($('#_tweetsFormPlace').find('input:checked').length > 0)
                                        $('#_all_select').prop('checked', true).next().addClass('checked');
                                }

                                $('#_limit').styler();
                            }
                            else {
                                $('#block_accounts').html('<div class="line_info alert" style="margin: 5px;">' + obj['message'] + '</div>');
                            }

                            $('#_tweetsFormPlace').css('opacity', 1);
                        },
                        beforeSend: function () {
                            _w = true;
                            $('#_tweetsFormPlace').css('opacity', 0.5);
                        },
                        complete: function () {
                            _w = false;
                        }
                    });

                    Twitter.o.m.update();
                },
                getWithFilter: function () {
                    this.data.sendData = {};
                    Twitter.o.m.s._amount = 0;
                    Twitter.o.m.s._tweets = 0;
                    Twitter.o.m.s._accounts = 0;
                    Twitter.o.m.p.ping = 0;
                    Twitter.o.m.s.to_pay = 0;
                    Twitter.o.m.d.data.accounts = new Array();

                    this.data.sendData = $('#_filterForm').serialize();

                    _ajax({
                        url: "/twitter/tweets/ajax/getAccounts",
                        data: this.data.sendData,
                        success: function (obj) {
                            if (obj.code == 200) {
                                $('#block_accounts').html(obj.html);
                                Twitter.o.m.p.payment = $("input:radio[name ='Manual[pay_method]']:checked").val();
                                $('#_tweetsCount').html(tweetsCount);
                            }
                            else
                                $('#block_accounts').html('<div style="padding: 10px;"><div class="line_info alert"><button class="close" onclick="$(this).parent().fadeOut(\'fast\', function(){ $(this).parent().remove();});" type="button">×</button>' + obj.message + '</div></div>');
                        },
                        beforeSend: function () {
                            $('#block_accounts').html('<div style="text-align: center; padding: 5px;">' + loading_img + '</div>');
                        }
                    });
                },
                toggleAll: function (e) {
                    var $c = $(e);
                    $("#_tweetsFormPlace").find('input:checkbox').each(function (index) {
                        var $i = $(this).attr('data-id'), $d = $('#_accountData_' + $i);
                        if ($d.attr('data-select') == 'no') {
                            if ($c.is(':checked') === true) {
                                if ($(this).is(':checked') === false) {
                                    $(this).attr('checked', true).next().addClass('checked');
                                    Twitter.o.m.s._accounts++;
                                    Twitter.o.m.d.put($i);
                                    Twitter.o.m.s._amount += parseFloat($d.attr('data-price'));
                                    Twitter.o.m.s._tweets += 1;
                                }
                            }
                            else {
                                $(this).attr('checked', false).next().removeClass('checked');
                                Twitter.o.m.s._accounts--;
                                Twitter.o.m.d.remove($i);
                                Twitter.o.m.s._amount -= parseFloat($d.attr('data-price'));
                                Twitter.o.m.s._tweets -= 1;
                            }
                        }
                    });

                    Twitter.o.m.update();
                },
                toggle: function (e) {
                    var $c = $(e), $id = $c.attr('data-id'), $d = $('#_accountData_' + $c.attr('data-id'));
                    if ($c.is(':checked') == true) {
                        $c.attr('checked', true).next().addClass('checked');
                        Twitter.o.m.s._accounts++;
                        Twitter.o.m.d.put($id);
                        Twitter.o.m.s._amount += round($d.attr('data-price'), 2);
                        Twitter.o.m.s._tweets += 1;
                    }
                    else {
                        $c.attr('checked', false).next().removeClass('checked');
                        Twitter.o.m.s._accounts--;
                        Twitter.o.m.d.remove($id);

                        if ($d.attr('data-count') > 0) {
                            Twitter.o.m.s._amount -= round($d.attr('data-count'), 2) * round($d.attr('data-price'), 2);
                            Twitter.o.m.s._tweets -= $d.attr('data-count');
                        }
                        else {
                            Twitter.o.m.s._amount -= round($d.attr('data-price'), 2);
                            Twitter.o.m.s._tweets -= 1;
                        }

                        if ($d.attr('data-select') == 'yes') {
                            _ajax({
                                data: {},
                                url: "/twitter/tweets/ajax/saveList?rid=" + $id + "&_tid=" + _tid,
                                success: function (obj) {
                                    if (obj['count'] > 0) {
                                        $('#_tweets_' + Twitter.o.m.id).html('<span title="Добавлено твитов на размещение в аккаунт">' + obj['count'] + '</span> - <a onclick="Twitter.o.m.d.addTweets(\'' + $id + '\',this);" href="javascript:void(0);" class="here">Показать твиты <i class="fa fa-caret-down"></i></a>');
                                        $('#_accountData_' + Twitter.o.m.id).attr('data-select', 'yes');
                                    }
                                    else {
                                        $d.attr('data-select', 'no');
                                        $d.attr('data-old', '0');
                                        $d.attr('data-count', '0');
                                        $('#_tweets_' + $id).html('<span title="Автоматический подбор твита">А</span> - <a onclick="Twitter.o.m.d.addTweets(\'' + $id + '\',this);" href="javascript:void(0);" class="here">Выбрать твит <i class="fa fa-caret-down"></i></a>');
                                    }
                                },
                                beforeSend: function () {
                                    _w = true;
                                },
                                complete: function () {
                                    _w = false;
                                }
                            });
                        }
                    }

                    Twitter.o.m.update();
                },
                put: function (id) {
                    this.data.accounts.push(id);
                },
                remove: function (id) {
                    if (this.data.accounts.length <= 1)
                        $('#_all_select').prop('checked', false).next().removeClass('checked');

                    this.data.accounts.removeValue(id);
                }
            },
            update: function () {
                if (this.s._tweets <= 0)
                    this.s._tweets = 0;
                if (this.s._accounts <= 0)
                    this.s._accounts = 0;
                if (this.s._amount <= 0)
                    this.s._amount = 0;

                $('.allTweetsPlacement').html(this.s._tweets);
                $('#_all_accounts').html(this.s._accounts);

                if ($('#_ping').is(':checked')) {
                    $('#_all_amount').html(round(this.s._amount + (this.s._tweets * 0.50), 2));
                }
                else {
                    if (this.p.ping > 0) {
                        this.s.to_pay = round(this.s._amount - (this.s._tweets * 0.50), 2);
                        $('#_all_amount').html(this.s.to_pay);
                    }
                    else {
                        this.s.to_pay = round(this.s._amount, 2);
                        $('#_all_amount').html(this.s.to_pay);
                    }
                }

                if (this.s._accounts >= 1)
                    $('#embedButton').attr('disabled', false);
                else
                    $('#embedButton').attr('disabled', true);
            },
            confirm: function () {
                var pay_text = this.p.payment == 1 ? this.s.to_pay + ' руб.Б.' : this.s.to_pay + ' руб.';
                Dialog.open('Подтверждение заказа', {
                    content: '<div style="padding: 7px 15px 0px;" id="_orderProcessing"><strong>Подтвердение заказа на сумму:</strong> ' + pay_text + '</div>',
                    buttons: [
                        {
                            text: _cancel, click: function () {
                            $(this).dialog("close");
                        },
                            class: "button"
                        },
                        {
                            text: 'Создать, и оплатить',
                            id: "_afs1d4s54d",
                            click: function () {
                                Twitter.o.make('_afs1d4s54d', 'now');
                            },
                            class: "button btn_green"
                        },
                        {
                            text: 'Создать, но оплатить позже',
                            id: "_afg4s54d",
                            click: function () {
                                Twitter.o.make('_afg4s54d', 'later');
                            },
                            class: "button btn_orange"
                        }
                    ]
                });
            },
            remove: function (id, e) {
                Dialog.open('Подтверждение удаления', {
                    content: '<div style="padding: 7px 15px 0px;">Вы действительно хотите удалить заказанный твит ?</div>',
                    buttons: [
                        {text: _cancel, click: function () {
                            $(this).dialog("close");
                        }, class: "button"},
                        {
                            text: 'Удалить',
                            id: 'as523se5',
                            click: function () {
                                if (_w === true)
                                    return false;

                                var b = $('#as523se5'), btxt = b.html();

                                _ajax({
                                    url: "/twitter/orders/status?h=" + Twitter.o.g.d.h + "&t=manual&act=remove&id=" + id,
                                    success: function (obj) {
                                        Dialog.open(_info, {content: obj.content});

                                        if (obj.code == 199)
                                            window.location.href = '/twitter/orders/status';

                                        if (obj.code == 200)
                                            Order.getTweets();
                                    },
                                    beforeSend: function () {
                                        b.html('<i class="fa fa-spin fa-spinner"></i>');
                                        _w = true;
                                    },
                                    complete: function () {
                                        b.html(btxt);
                                        _w = false;
                                    }
                                });
                            },
                            class: "button btn_orange"
                        }
                    ]
                });
            }
        },
        f: {
            d: {
                page: 1,
                limit: 10,
                hash: ''
            },
            urls: {
                d: {
                    id: 0
                },
                remove: function (id, elm) {
                    if (id === this.d.id) return false;

                    var count = $('#urlList').find('li').length - 1, amount = $('#dropdownPrices').val();

                    if (count > 0) {
                        $(elm).fadeOut(function () {
                            $(this).parent().remove();
                        });

                        $('#urlsCount').html(count);
                        $('#urlsPricesAll').html(count * amount);
                        $('#_orderCreate').append('<input type="hidden" name="Order[data][urlsExclude][]" value="' + id + '">');
                        this.d.id = id;
                    } else {
                        $('#block_fast').fadeOut('fast', function () {
                            $(this).remove();
                            $('#PlacementMethod_fast').attr('checked', false).next().removeClass('checked');
                        });

                        Dialog.close();
                    }
                },
                get: function () {
                    Dialog.open('Список сслок', {content: '<div id="_urlsList"></div>'});

                    _ajax({
                        url: "/twitter/tweets/ajax/getFastUrls?_id=" + _tid,
                        success: function (obj) {
                            if (obj.code === 200)
                                $('#_urlsList').html(obj.html);
                            else
                                $('#_urlsList').html(obj.message);
                        },
                        beforeSend: function () {
                            $('#_urlsList').html('<div style="padding: 4px; text-align:center;">' + loading_img + '</div>');
                        }
                    });
                }
            },
            update: function () {
                var count = parseInt($('#urlsCount').html()), amount = $('#dropdownPrices').val();

                $('#urlsPricesAll').html(count * amount);
                $('#urlsPrices').html(amount);
            },
            confirm: function () {
                Dialog.open('Подтверждение заказа', {
                    content: '<div style="padding: 7px 15px 0;" id="_orderProcessing"><strong>Подтвердение заказа на сумму:</strong> ' + parseInt($('#urlsPricesAll').html()) + ' руб.</div>',
                    buttons: [
                        {
                            text: _cancel, click: function () {
                            Dialog.close("close");
                        },
                            class: "button"
                        },
                        {
                            text: 'Создать, и оплатить',
                            id: "_afs1d4s54d",
                            click: function () {
                                Twitter.o.make('_afs1d4s54d', 'now');
                            },
                            class: "button btn_green"
                        },
                        {
                            text: 'Создать, но оплатить позже',
                            id: "_afg4s54d",
                            click: function () {
                                Twitter.o.make('_afg4s54d', 'later');
                            },
                            class: "button btn_orange"
                        }
                    ]
                });
            }
        },
        make: function (e, w) {
            var btn = $('#' + e), btxt = btn.html(), additional = '', q = new Array(), c = 0;
            if (_w === true) return false;

            if (this.m.d.data.accounts.length > 0) {
                $.each(this.m.d.data.accounts, function (i, v) {
                    if (v !== undefined)
                        q[i] = v;
                });

                additional = '&Order[data][accounts]=' + q.join(':');
            }

            _ajax({
                url: "/twitter/tweets/ajax/create?pay=" + w,
                data: $('#_orderCreate').serialize() + additional,
                success: function (obj) {
                    if (obj['code'] === 200) {
                        $("#_orderProcessing").html(obj['message']);
                        window.location.href = obj['url'];
                    }
                    else {
                        $("#_orderProcessing").html(obj['message']);
                    }
                },
                beforeSend: function () {
                    $("#_orderProcessing").html('Подождите идёт обработка заказа');
                    btn.html('<i class="fa fa-spin fa-spinner"></i>');
                    _w = true;
                },
                complete: function () {
                    btn.html(btxt);
                    _w = false;
                }
            });
        },
        getPage: function (page) {
            this.data.page = page;
            this.get();
        },
        setLimit: function (n) {
            this.data.page = 0;
            this.data.limit = n;
            this.get();
        },
        get: function () {
            _ajax({
                url: "/twitter/orders/status?page=" + this.data.page + "&limit=" + this.data.limit,
                success: function (obj) {
                    $("#_orderList").html(obj['html']);
                },
                beforeSend: function () {
                    $("#_orderList").css('opacity', 0.5);
                    _w = true;
                },
                complete: function () {
                    $("#_orderList").css('opacity', 1);
                    _w = false;
                }
            });
        },
        remove: function (id, e) {
            var b = $(e), btxt = b.html();
            Dialog.open('Подтверждение удаления', {
                content: '<div style="padding: 7px 15px 0px;">Вы действительно хотите удалить заказ "ID: ' + id + '" ?</div>',
                buttons: [
                    {
                        text: _cancel, click: function () {
                        $(this).dialog("close");
                    },
                        class: "button"
                    },
                    {
                        text: 'Удалить',
                        id: 'aser3S523se5',
                        click: function () {
                            if (_w === true)
                                return false;

                            var b = $('#aser3S523se5'), btxt = b.html();

                            _ajax({
                                url: "/twitter/orders/remove?id=" + id,
                                success: function (obj) {
                                    Dialog.open(_info, {content: obj.message});
                                    if (obj.code == 200)
                                        Twitter.o.get();
                                },
                                beforeSend: function () {
                                    b.html('<i class="fa fa-spin fa-spinner"></i>');
                                    _w = true;
                                },
                                complete: function () {
                                    b.html(btxt);
                                    _w = false;
                                }
                            });
                        },
                        class: "button btn_orange"
                    }
                ]
            });
        },
        confirmPay: function (id, e) {
            var $t = $('#order_' + id);
            Dialog.open('Подтверждение заказа', {
                    content: '<div id="orderMessage">Вы действительно хотите оплатить заказ на сумму: ' + $t.attr('data-amount') + ' ' + ($t.attr('data-atype') == 1 ? ' руб.Б.' : 'руб.') + ' ?</div>',
                    buttons: [
                        {
                            text: _cancel,
                            click: function () {
                                $(this).dialog("close");
                            },
                            class: "button"
                        },
                        {
                            text: 'Оплатить',
                            id: 'p66De',
                            click: function () {
                                if (_w === true) return false;

                                var b = $('#p66De'), btxt = b.html();
                                _ajax({
                                    url: "/twitter/orders/paid?id=" + id,
                                    type: "GET",
                                    success: function (obj) {
                                        Dialog.open(_info, {content: obj.message});
                                        if (obj.code == 200) {
                                            Twitter.o.get();
                                        }
                                    },
                                    beforeSend: function () {
                                        b.html('<i class="fa fa-spin fa-spinner"></i>');
                                        _w = true;
                                    },
                                    complete: function () {
                                        b.html(btxt);
                                        _w = false;
                                    }
                                });
                            },
                            class: "button btn_blue"
                        }
                    ]
                }
            );
        },
        g: {
            d: {
                page: 1,
                limit: 10,
                hash: ''
            },
            set: function (options) {
                this.d = $.extend(this.d, options);
            },
            getPage: function (page) {
                this.d.page = page;
                this.get();
            },
            setLimit: function (n) {
                this.d.page = 0;
                this.d.limit = n;
                this.get();
            },
            get: function () {
                _ajax({
                    url: "/twitter/orders/status?h=" + this.d.hash + "&t=" + this.d.t + "&page=" + this.d.page + "&limit=" + this.d.limit,
                    success: function (obj) {
                        if (obj.code === 200)
                            $("#_orderList").html(obj['html']);
                        else
                            Dialog.open(_error, {content: obj.message});
                    },
                    beforeSend: function () {
                        $("#_orderList").css('opacity', 0.5);
                        _w = true;
                    },
                    complete: function () {
                        $("#_orderList").css('opacity', 1);
                        _w = false;
                    }
                });
            }
        }
    }
}