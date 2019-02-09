$(document).ready(function() 
{
	$("a[down-counter]").each(function(){
		var href = $(this).attr('href');
		$(this).attr('href', $(this).attr('down-counter'));
		$(this).attr('down-counter', href);
	}).on('click', function(){
		$(this).attr('href', $(this).attr('down-counter')); //return false;
	});
});
