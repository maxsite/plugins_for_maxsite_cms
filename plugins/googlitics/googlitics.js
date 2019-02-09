$(document).ready(function(){
    $('#toggle_info').click(function() {
        if ($("#info").css('display') == 'none')
        {
            $("#info").show();
            $("#toggle_info").html('Скрыть справку');
        } else {
            $("#info").hide();
            $("#toggle_info").html('Показать справку');
        }
    })

    $("#stats").click(function() {
        if ($("#stat_graphs").css('display') == 'none')
        {
            $("#stat_graphs").show();
            $("#stats").html('Скрыть статистику');
            
        } else {
            $("#stat_graphs").hide();
            $("#stats").html('Показать статистику');
        }
    })

    $('#load_stats').click(function() {
        googlitics_load(1);
    })

    // try to reload data
    googlitics_load(0);
});

function googlitics_load(force)
{
    var requrl = $("#load_stats").attr('ref');

    var forceload = 0;
    sess = $("#manual_load :input").val();
    if (force == 1)
    {
        forceload = 1;
    }
    $("#stat_graphs").hide();
    $("#stats").html('Показать статистику');

    $('#srv_msg').empty();
    $('#srv_msg').text('Идет загрузка...');
    $.ajax({
        type: "POST",
        url: requrl,
        data: "sess="+sess+"&force="+forceload,
        dataType: "text",
        success: function(msg)
        {
            $('#srv_msg').empty();
            $('#srv_msg').html(msg);

            $("#stat_graphs").show();
            $("#stats").html('Скрыть статистику');
        }
    });
    
}
