$(document).ready(function () {
    $('.comment-form').each(function (num,el) {
        var needCollapse = false;
        var collapse = function () {
            if (needCollapse) {
                $(el).removeClass("comment-form--expose");
            }
        };
        var inputBlur = function (e) {
            needCollapse = true;
            setTimeout(collapse,333);
        };
        $(el).find('[name="comment"]')
           .on('focus',function (e) {
               needCollapse = false;
               $(el).addClass("comment-form--expose");
           })
           .on('blur',inputBlur);
        $(el).find('input')
            .on('blur',inputBlur)
            .on('focus',function (e) {
                needCollapse = false;
            });

        // Submit
        $(el).on('submit',function (ev) {
            ev.preventDefault();
            var data = $(this).serialize();
            var showErrors = function (data) {
                $(el).find(".form-group").each(function (num, group) {
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


            $(el).find(".error").removeClass('error');
            $(el).find('.comment-form__error-message').hide();

            $.ajax({
                url : "/comment",
                data : data,
                type : "post",
                dataType : "json",
                success : function() {
                    location.reload();
                },
                error : function(data) {
                    switch (data.status) {
                        case 400 : showErrors(data.responseJSON);
                    }
                }
            });
        });
    });
});