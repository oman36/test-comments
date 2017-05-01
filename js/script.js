$(document).ready(function () {

    var eventsForComment = function (comment) {
        // Форма отправки дочерних комментариев
        var form = $(comment).find(">div>form");
        var needCollapse = false;
        var collapse = function () {
            if (needCollapse) {
                $(form).removeClass("comment-form--expose");
            }
        };
        var inputBlur = function (e) {
            needCollapse = true;
            setTimeout(collapse,333);
        };
        $(form).find('[name="comment"]')
            .on('focus',function (e) {
                needCollapse = false;
                $(form).addClass("comment-form--expose");
            })
            .on('blur',inputBlur);
        $(form).find('input')
            .on('blur',inputBlur)
            .on('focus',function (e) {
                needCollapse = false;
            });


        // Submit
        $(form).on('submit',function (ev) {
            ev.preventDefault();
            var data = $(this).serialize();
            var showErrors = function (data) {
                $(form).find(".form-group").each(function (num, group) {
                    for (var name in data) {
                        var input = $(group).find("[name=" + name +"]");
                        if (0 === input.length) {
                            continue;
                        }
                        $(group).addClass("error");
                        $(group).find('.comment-form__error-message')
                            .html(data[name])
                            .show();
                    }
                });
            };


            $(form).find(".error").removeClass('error');
            $(form).find('.comment-form__error-message').hide();

            $.ajax({
                url : "/comment",
                data : data,
                type : "post",
                dataType : "json",
                success : function(data) {
                    var comments =  $(comment)
                        .find(">.panel-footer")
                        .prepend(data.html)
                        .find(">div");
                    var newComment = comments.first();
                    var amount = comments.length;

                    eventsForComment(newComment);

                    // Отображаем всё, что ранее было скрыто (футер и кнопка)
                    var id = $(form).find('[name="parent_id"]').val();
                    var btn = $(comment).find('[data-target="#answer_' + id + '"]');
                    btn.find(".comment-children-amount").html(amount);
                    btn.show();
                    if ("none" === $('#answer_' + id).css('display')) {
                        btn.click();
                    }
                },
                error : function(data) {
                    switch (data.status) {
                        case 400 : showErrors(data.responseJSON);
                    }
                }
            });
        });
    };

    $('.comment').each(function (num,comment) {
        eventsForComment(comment);
    });
});