$(document).ready(function(){
	if( !$('select[name="plugin_wvolnorezonline-plugins[VolnorezOnline_GenreID]"]').length ) return false;
	if($('select[name="plugin_wvolnorezonline-plugins[VolnorezOnline_GenreID]"]').val() != 'All' )
	{
		$('input[name="plugin_wvolnorezonline-plugins[VolnorezOnline_GenreList]"]').removeAttr('checked');
		$('input[name="plugin_wvolnorezonline-plugins[VolnorezOnline_GenreList]"]').attr('disabled','disabled');
	}
	$(document).delegate('select[name="plugin_wvolnorezonline-plugins[VolnorezOnline_GenreID]"]','change',function(){
		if($(this).val() != 'All')
		{
			$('input[name="plugin_wvolnorezonline-plugins[VolnorezOnline_GenreList]"]').removeAttr('checked');
			$('input[name="plugin_wvolnorezonline-plugins[VolnorezOnline_GenreList]"]').attr('disabled','disabled');
		}
		else
		{
			$('input[name="plugin_wvolnorezonline-plugins[VolnorezOnline_GenreList]"]').attr('checked','checked');
			$('input[name="plugin_wvolnorezonline-plugins[VolnorezOnline_GenreList]"]').removeAttr('disabled');
		}
	});
});
