

//Решение кросбраузерности IE

<script type="text/javascript">
$(document).ready(function() {
    /* Placeholder for IE */
    if($.browser.msie) { // Условие для вызова только в IE
        $("form").find("input[type='text']").each(function() {
            var tp = $(this).attr("placeholder");
            $(this).attr('value',tp).css('color','#999999');
        }).focusin(function() {
            var val = $(this).attr('placeholder');
            if($(this).val() == val) {
                $(this).attr('value','').css('color','#303030');
            }
        }).focusout(function() {
            var val = $(this).attr('placeholder');
            if($(this).val() == "") {
                $(this).attr('value', val).css('color','#999999');
            }
        });

        /* Protected send form */
        $("form").submit(function() {
            $(this).find("input[type='text']").each(function() {
                var val = $(this).attr('placeholder');
                if($(this).val() == val) {
                    $(this).attr('value','');
                }
            })
        });
    }
});
</script>