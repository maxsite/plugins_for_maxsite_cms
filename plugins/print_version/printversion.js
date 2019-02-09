$(function() {
	var ctrl_print = $('<div id="ctrl_print" style="padding:10px 0; text-align:center;"> <input type="submit" value="Распечатать" id="print_submit" /><div>');
    $("#printversion").click(function() {
        open(this.href + "print", "Winprint");
        return false;
    });
    if(location.hash == "#print") {
		$("link[media=screen]").attr("href", $("link[media=print]").attr('href'));
		$("div.b-print").css("display","none");
		ctrl_print.prependTo('body');
    }
	$("#print_submit").click(function() {
		window.print();
		return false;
    });
});


