$(document).ready(function(){
	// range filter's form
	if( $('.get_range input:checked').length == 0 ){ $('.get_range input[type="text"]').attr('disabled','disabled').addClass('disab'); $('.get_range strong').addClass('disab'); }
	$('.get_range input[type="checkbox"]').change( 
		function() {
			$(this).siblings('input[type="text"]').attr('disabled', !$(this).attr('checked')).val( function(){ return $(this).attr('data-value'); }).addClass('disab');
			$(this).siblings('strong').toggleClass('disab'); 
		});
		
	$('.get_range input[type="text"]').attr('data-value', function() { return $(this).val(); }).focus(function(){
		if( $(this).val() == $(this).attr('data-value') ) $(this).val('').toggleClass('disab');
	}).blur(function() {
		if( $(this).val() == '' ) $(this).val($(this).attr('data-value')).toggleClass('disab');
	});
		
	// button go
	$('#go').click( function(){
		$('#result').val(''); $('#loader').show();
			
		// ajax-запрос
		$.ajax({
			type: "POST",
			url: ajax_path,
			data: {"do":"getlist", "page_author":$('#page_author').val(), "page_type":$('#page_type').val(), "page_status":$('#page_status').val(), "page_category":$('#page_category').val(), "page_tag":$('#page_tag').val(), "get_range":( $('#get_range:checked').val() ? 1 : 0 ), "page_id_begin":$('#page_id_begin').val(), "page_id_end":$('#page_id_end').val(), "get_list":( $('#get_list:checked').val() ? 1 : 0 ), "pages_id":$('#pages_id').val(), "templ":$('#templ').val() },
			dataType: "json",
			success: function(json)
			{
				if( 1 == json.res)
				{
					$('#result').val(json.msg);
					$('#loader').hide();
				}
				else
				{
					alert("Возникла ошибка: " + json.msg);
					$('#loader').hide();
				}
			},
			error: function(xhr, str)
			{
				alert("Возникла ошибка: " + xhr.responseCode + "\n"+str);
				$('#loader').hide();
			}
		});
			
		return false;
	});
});
