var _ajaxWait = false;

function rand(mi, ma) {
    return Math.random() * (ma - mi + 1) + mi;
}
function irand(mi, ma) {
    return Math.floor(rand(mi, ma));
}
function isFunction(obj) {
    return Object.prototype.toString.call(obj) === '[object Function]';
}
function isArray(obj) {
    return Object.prototype.toString.call(obj) === '[object Array]';
}
function isObject(obj) {
    return Object.prototype.toString.call(obj) === '[object Object]';
}
function isEmpty(o) {
    if (Object.prototype.toString.call(o) !== '[object Object]') {
        return false;
    }
    for (var i in o) {
        if (o.hasOwnProperty(i)) {
            return false;
        }
    }
    return true;
}
function trim(text) {
    return (text || '').replace(/^\s+|\s+$/g, '');
}
function stripHTML(text) {
    return text ? text.replace(/<(?:.|\s)*?>/g, '') : '';
}
function escapeRE(s) {
    return s ? s.replace(/([.*+?^${}()|[\]\/\\])/g, '\\$1') : '';
}
function str_replace(search, replace, subject) {
    return subject.split(search).join(replace);
}
function indexOf(arr, value, from) {
    for (var i = from || 0, l = (arr || []).length; i < l; i++) {
        if (arr[i] == value)
            return i;
    }
    return -1;
}
function inArray(value, arr) {
    return indexOf(arr, value) != -1;
}
function intval(value) {
    if (value === true)
        return 1;
    return parseInt(value) || 0;
}
function urldecode(str) {
    return decodeURIComponent((str + '').replace(/\+/g, '%20'));
}
function floatval(value) {
    if (value === true)
        return 1;
    return parseFloat(value) || 0;
}
function positive(value) {
    value = intval(value);
    return value < 0 ? 0 : value;
}
function winToUtf(text) {
    return text.replace(/&#(\d\d+);/g, function(s, c) {
        c = intval(c);
        return (c >= 32) ? String.fromCharCode(c) : s;
    }).replace(/&quot;/gi, '"').replace(/&lt;/gi, '<').replace(/&gt;/gi, '>').replace(/&amp;/gi, '&');
}
function replaceEntities(str) {
    return se('<textarea>' + ((str || '').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;')) + '</textarea>').value;
}
function clean(str) {
    return str ? str.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;') : '';
}
function _data(str, obj)
{
    var arr = [];

    if (typeof obj == "object")
    {
        jQuery.each(obj, function(k, v) {
            arr.push(k + "=" + v);
        })
    }

    if (typeof str == "object" || typeof str == "array")
    {
        jQuery.each(str, function(k, v) {
            arr.push(k + "=" + v);
        })
    }
    else {
        arr.push(str);
    }

    return arr.join("&");
}
function _ajax(options)
{
    var params = $.extend({type: "POST", url: "", data: [], beforeSend: function() {
        }, complete: function() {
            $('select').styler();
        }, success: function() {
        }, dataType: "json"}, options), sendData = _data(params.data, {_token: it._token, "ajax": "yes"});

    $.ajax({type: params.type, url: params.url, data: sendData, dataType: params.dataType, success: params.success, beforeSend: params.beforeSend, complete: params.complete, error: params.error});
}
function round(value, precision, mode)
{
    var m, f, isHalf, sgn; // helper variables
    precision |= 0; // making sure precision is integer
    m = Math.pow(10, precision);
    value *= m;
    sgn = (value > 0) | -(value < 0); // sign of the number
    isHalf = value % 1 === 0.5 * sgn;
    f = Math.floor(value);

    if (isHalf) {
        switch (mode) {
            case 'PHP_ROUND_HALF_DOWN':
                value = f + (sgn < 0); // rounds .5 toward zero
                break;
            case 'PHP_ROUND_HALF_EVEN':
                value = f + (f % 2 * sgn); // rouds .5 towards the next even integer
                break;
            case 'PHP_ROUND_HALF_ODD':
                value = f + !(f % 2); // rounds .5 towards the next odd integer
                break;
            default:
                value = f + (sgn > 0); // rounds .5 away from zero
        }
    }

    return (isHalf ? value : Math.round(value)) / m;
}

function inputActive(e)
{
    if ($('#' + e).is(':disabled') == true)
    {
        $('#' + e).removeAttr('disabled');
    }
    else {
        $('#' + e).attr('disabled', 'disabled');
    }
}
function _checkBox(_e)
{
    var $s = $(_e), $i = $s.prev();

    if (!$i.is(':disabled'))
    {
        if ($s.hasClass('checked') == true)
        {
            $s.removeClass('checked').prev().prop('checked', false);
        }
        else {
            $s.addClass('checked').prev().prop('checked', true);
        }

        $i.change();
    }

    return false;
}
function _radioBox(_e)
{
    var $s = $(_e), $i = $s.prev();

    if (!$i.is(':disabled')) {
        $('input[name="' + $i.attr('name') + '"]').prop('checked', false).next().removeClass('checked');
        $i.prop('checked', true);
        $s.addClass('checked');
        $i.change();
        return false;
    }

    $i.change();
    return false;
}
Dialog = {
    open: function(title, options)
    {
        var params = $.extend(
                {
                    buttons: [{text: _close, click: function() {
                                $(this).dialog("close");
                            }, class: "button"}],
                    content: '',
                    closeText: 'X',
                }, options);

        $('#dialog-message').remove();
        $('body').append('<div id="dialog-message" title="' + title + '" style="display: none;"><div class="ui-dialog-content-text">' + params.content + '</div></div>');

        $("#dialog-message").dialog({
            resizable: false,
            modal: true,
            buttons: params.buttons,
            closeText: params.closeText,
        });
    },
    close: function()
    {
        $('#dialog-message').dialog('close');
        $('#dialog-message').remove();
    },
    confirm: function(message, title, callback)
    {
        this.open(title, {
            content: message,
            buttons: [
                {text: btn_yes, click: function() {
                        $(this).dialog("close");
                        callback();
                    }, class: "button btn_blue"},
                {text: btn_no, click: function() {
                        $(this).dialog("close");
                    }, class: "button"}
            ]
        });
    }
}
function dinamicSize()
{
    $('#menu_left_l').css('height', ($('body').height() - $('#footer').height() - 87));
}

function tabClick(tab_id) {
    if (tab_id != $('#form_logged a.active').attr('id')) {
        $('#form_logged .tabs').removeClass('active');
        $('#' + tab_id).addClass('active');
        $('#con_' + tab_id).addClass('active');
    }
}
$('select').each(function() {
    $(this).parent().children('.select_main_txt').text($(this).val());
});
$('select').change(function() {
    $(this).parent().children('.selecttitle').text($(this).val());
});

function _iAction(_e)
{
    if (_ajaxWait === true)
        return false;

    var _b = $(_e), _bt = _b.html();

    _ajax({
        data: _b.parents("form").serialize(),
        type: 'POST',
        url: _b.attr('data-send'),
        success: function(obj)
        {
            var _c = $('#' + _b.attr('data-action'));

            if (obj.code == 200)
            {
                window.location.href = obj._url;
            } else {
                _c.html(obj.html);
            }
        },
        beforeSend: function()
        {
            _b.html('<img src="/i/loads.gif">');
            _ajaxWait = true;
        },
        complete: function()
        {
            _b.html(_bt);
            _ajaxWait = false;
        }
    });
}


function insertValue(e, t)
{
    var _v = $(e).html();

    $('#' + t).val(_v);
}
function _togglePassowrd(e, i)
{
    var input = $('#' + i);

    if (input.attr('type') == 'password')
    {
        input.attr('type', 'text');
        $(e).children().removeClass('fa-eye').addClass('fa-eye-slash');
    }
    else {
        input.attr('type', 'password');
        $(e).children().removeClass('fa-eye-slash').addClass('fa-eye');
    }
}

wMessages = {
    _removeMessages: function(_e)
    {
        var _count = $('#_messagesWidget').find("input:checkbox:checked").length;

        if (_count > 0)
        {
            Dialog.open(_confirm, {
                content: str_replace('{n}', _count, _cmessage_remove),
                buttons: [{text: btn_yes, click: function() {

                            var _b = $(_e).children(), _data = $('#_messagesWidget').serialize();

                            _ajax({
                                data: _data,
                                type: 'POST',
                                dataType: 'json',
                                url: '/ajax/_messages?act=remove',
                                success: function(obj)
                                {
                                    if (obj.code == 200)
                                    {
                                        $('#_messagesWidget').html(obj.html);

                                        if (obj.dcount > 0)
                                            $('#_all_mail_read').html(parseInt($('#_all_mail_read').html()) - obj.dcount);

                                        if (obj.count <= 0)
                                        {
                                            $('.wMremove').remove();
                                        }

                                        $('#wCountMessages').html(obj.count);
                                    }
                                    else
                                        Dialog.open(_error, {content: obj.html});
                                },
                                beforeSend: function()
                                {
                                    _b.removeClass('fa-trash-o').addClass('fa-spinner fa-spin');
                                },
                                complete: function()
                                {
                                    _b.removeClass('fa-spinner fa-spin').addClass('fa-trash-o');
                                }
                            });

                            $(this).dialog("close");

                        }, class: "button btn_blue"
                    },
                    {text: _cancel, click: function() {
                            $(this).dialog("close");
                        }, class: "button"}],
            });
        }
        else
            Dialog.open(_info, {content: _cmessage_remove_no_messages});
    },
    get: function()
    {
        _ajax({
            type: 'POST',
            url: '/ajax/_messages?act=messages',
            dataType: 'json',
            success: function(obj)
            {
                $('#_messagesWidget').html(obj.html);
            },
            beforeSend: function()
            {
                $('#_wall').prop('checked', false).next().removeClass('checked');
                $('#_messagesWidget').html('<div style="text-align: center;"><img src="/i/loading_11.gif" alt="Loading..."></div>');
            },
        });
    },
    getWidget: function()
    {
        _ajax({
            type: 'POST',
            url: '/ajax/_messages',
            dataType: 'json',
            success: function(obj)
            {
                $('#_messagesWBox').html(obj.html);
            },
            beforeSend: function()
            {
                $('#_messagesWBox').html('<div style="text-align: center; padding-bottom: 10px;"><img src="/i/loading_11.gif" alt="Loading..."></div>');
            },
        });
    }
}

Array.prototype.removeValue = function(val) {
    for (var i = 0; i < this.length; i++) {
        if (this[i] === val) {
            this.splice(i, 1);
            i--;
        }
    }

    return this;
}

function _all_ckbox(f, e) {
    var i;
    var inputs = $('#' + f).find('input');

    if ($(e).is(':checked') == true) {
        for (i = 0; i < inputs.length; i++) {
            if (inputs[i].type.toLowerCase() == 'checkbox') {
                inputs[i].checked = true;
                $(inputs[i]).next().addClass('checked');
            }
        }
    } else {
        for (i = 0; i < inputs.length; i++) {
            if (inputs[i].type.toLowerCase() == 'checkbox') {
                inputs[i].checked = false;
                $(inputs[i]).next().removeClass('checked');
            }
        }
    }
}

$( document ).ready(function() {
    $('select').styler();
});
$( document ).ajaxComplete(function() {
    $('select').styler();
});
