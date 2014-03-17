Settings = {
    _params: {
        subjectCount: 1,
        wait: false,
    },
    fReset: function (element) {
        $("#" + element).get(0).reset()
    },
    accountRemove: function (tid, title, messages) {
        Dialog.open(title, {content: messages, buttons: [
            {text: btn_yes, class: "button btn_red", click: function () {
                window.location.replace("/twitter/accounts/settings?tid=" + tid + "&remove=1");
            }},
            {text: btn_no, class: "button", click: function () {
                $(this).dialog("close");
            }}
        ]});
    },
    _credentials: function (el) {
        var _el = $(el).parent();

        _ajax({
            data: {"_check": _el.attr('data-check'), "tid": _el.attr('data-send')},
            url: "/twitter/ajax/_credentials?_return=1",
            beforeSend: function () {
                _el.html('<img src="/i/loads.gif" alt="Loading ...">');
            },
            success: function (obj) {
                _el.html(obj.html);

                switch (obj.code) {
                    case 200:
                        $("#" + _el.attr('data-check')).html(obj.result);
                        break;

                    default:
                        Dialog.open(_error, {content: obj.messages, buttons: [
                            {text: _close, class: "button", click: function () {
                                $(this).dialog("close");
                            }}
                        ]});
                }
            },
            complete: function (obj) {
                if (!isObject(obj)) {
                    _el.html('Error, please reload this page.');
                }
            }
        });
    }
}

var Accounts = {
    settings: {
        update: function (id, act, elm) {
            var _e = $(elm), _t = _e.html();

            _ajax({
                data: {},
                url: "/twitter/accounts/settings?tid=" + id + "&act=" + act,
                beforeSend: function () {
                    _e.html('<img src="/i/loads.gif" alt="Loading ...">');
                },
                success: function (obj) {
                    if (obj['code'] == 301) {
                        window.location.href = obj['url'];
                    } else {
                        Dialog.open(_info, {
                            content: obj['message'],
                            buttons: [
                                {
                                    text: _close,
                                    class: "button",
                                    click: function () {
                                        $(this).dialog("close");
                                    }
                                }
                            ]
                        });
                    }
                },
                complete: function () {
                    _e.html(_t);
                }
            });
        },
        remove: function (tid, title, messages) {
            Dialog.open(title, {content: messages, buttons: [
                {
                    id: 'remove-confirm',
                    text: btn_yes,
                    class: "button btn_red",
                    click: function () {
                        var _e = $('#remove-confirm'), _t = _e.html();
                        _ajax({
                            url: "/twitter/accounts/settings?tid=" + tid + "&act=remove",
                            beforeSend: function () {
                                _e.html('<i class="fa fa-spin fa-spinner"></i>');
                            },
                            success: function (obj) {
                                if (obj['code'] == 301) {
                                    Dialog.close();
                                    window.location.replace("/twitter/accounts");
                                } else {
                                    Dialog.open(_info, {
                                        content: obj['message'],
                                        buttons: [
                                            {
                                                text: _close,
                                                class: "button",
                                                click: function () {
                                                    $(this).dialog("close");
                                                }
                                            }
                                        ]
                                    });
                                }
                            },
                            complete: function () {
                                _e.html(_t);
                            }
                        });
                    }},
                {text: btn_no, class: "button", click: function () {
                    $(this).dialog("close");
                }}
            ]});
        },
        status: function (el, tid) {
            _ajax({
                data: {"tid": tid, "status": ($(el).is(':checked') == true) ? "on" : "off"},
                url: "/twitter/accounts/ajax/status",
                dateType: "POST",
                success: function (obj) {
                    if (obj['code'] == 200) {
                        $("#_status").html(obj['html']);
                    } else {
                        Dialog.open(_error, {content: obj['message'], buttons: [
                            {text: _close, class: "button", click: function () {
                                $(this).dialog("close");
                            }}
                        ]});
                    }
                }
            });
        }
    }
}