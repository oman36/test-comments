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
            })
    });
});