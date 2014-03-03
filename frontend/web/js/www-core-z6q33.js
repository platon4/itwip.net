function _addFav(id, el) {
    if (id == 0 || id == 'undefined')
        return false;

    var btn = $("#favLoading").html();

    _ajax({
        type: "POST",
        url: "/ajax/favMenu",
        data: {"_fID": id},
        dataType: "json",
        success: function (obj, textStatus) {
            if (obj['status'] == 1) {
                $(el).addClass("fav-icon active").removeClass("fa fa-spinner fa-spin");
            }
            else {
                $(el).addClass("fav-icon").removeClass("fa fa-spinner fa-spin active");
                $('#fav_' + id).addClass("fav-icon").removeClass("fa fa-spinner fa-spin active");
            }

            $("#favMenu").html(obj['favMenu']);
        },
        beforeSend: function () {
            $(el).removeClass("fav-icon").addClass("fa fa-spinner fa-spin");
            $("#favLoading").html('<img src="/i/loads.gif">');
        },
        complete: function (obj, textStatus) {
            if (textStatus == "error") {
                $(el).addClass("fav-icon").removeClass("fa-spinner fa-spin");
                $("#favLoading").html(btn);
            }

            $('select').styler();
        }
    });
}

Affiliate = {
    s: {
        page: 1,
        _q: '',
        _o: '',
        _a: 'DESC',
        cCount: 0,
    },
    _getPage: function (p) {
        this.s.page = p;
        this._getContent();
    },
    _getFromQuery: function (q, b) {
        this.s.page = 1;
        this.s._q = q;
        this._getContent();
    },
    _setOrder: function (s, element) {
        $(".table_head_inside").find('i').removeClass('fa fa-caret-up').addClass('fa fa-caret-down');

        if (this.s._o == s && this.s.cCount == 0) {
            this.s._a = "DESC";
            this.s.cCount = 1;

            $(element).find('i').removeClass('fa fa-caret-up').addClass('fa fa-caret-down');
        }
        else {
            this.s._a = "ASC";
            this.s._o = s;
            this.s.cCount = 0;

            $(element).find('i').removeClass('fa fa-caret-down').addClass('fa fa-caret-up');
        }

        this._getContent();
    },
    _getContent: function () {
        _ajax({
            url: "/accounts/affiliateProgram/referral?act=list&page=" + this.s.page + "&_q=" + this.s._q + '&_o=' + this.s._o + '&_a=' + this.s._a,
            success: function (result) {
                $('#_referraList').html(result.html);
                $('#pagesListpagesList').html(result.pages);
            },
            beforeSend: function () {
                $('#_referraList').html('<tr><td style="text-align: center;"><img src="/i/loads.gif" alt="Loading..."></td></tr>');
            }
        });
    },
}

Subjects = {
    _params: {
        subjectCount: 1,
        wait: false,
    },
    _addSubject: function (e, l) {
        if (Subjects._params.wait == true) {
            return false;
        }
        var message = '';

        _ajax({
            data: _data($("#_subjectsBox").serializeAny('select'), {"t": this._params.subjectCount}),
            url: "/twitter/ajax/_getSubjects",
            success: function (obj) {
                if (obj.code == 200) {
                    $(l).parent().parent().append(obj.html);
                    Subjects._params.subjectCount++;
                    $('select').styler();
                }
                else {
                    if (obj.code == 203) {
                        message = obj.message;
                    }
                    else {
                        message = unknow_response;
                    }

                    Dialog.open(_error, {content: message, buttons: [
                        {text: _close, class: "button", click: function () {
                            $(this).dialog("close");
                        }}
                    ]});
                }

                $(l).children().removeClass('fa-spinner fa-spin').addClass('fa-plus');
            },
            beforeSend: function () {
                $(l).children().removeClass('fa-plus').addClass('fa-spinner fa-spin');
                Subjects._params.wait = true;
            },
            complete: function () {
                $(l).children().removeClass('fa-spinner fa-spin').addClass('fa-plus');
                Subjects._params.wait = false;
            }
        });
    },
    _removeSubject: function (e) {
        $(e).parent().remove();
        this._params.subjectCount--;
    }
}
$(function () {
    $(document).tooltip({
        position: {
            my: "center bottom-10",
            at: "center top",
            using: function (position, feedback) {
                $(this).css(position);
                $(this).css('display', 'test');
                $("<div>").addClass("arrow").addClass(feedback.vertical).addClass(feedback.horizontal).appendTo(this);
            }
        }
    });
});