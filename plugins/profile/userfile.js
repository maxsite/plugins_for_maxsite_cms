jQuery(document).ready(function(){

		$("#f_sort_type").change(function(){
		  showfiles();
		});
		
		showfiles();		

    })




function showfiles(){
	 // не мешало бы проверить - открыт ли блок с файлами 		  
	  var ajax_path = $("#f_ajax_path").val();
	  var subdir = $("#f_subdir").val();
      var sort_type = document.getElementById("f_sort_type").value	  
	  var data = 'type=get_files&dir=' + subdir  + '&sort=' + sort_type; 
	  var result = $("#files_list");
	  
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
				},
			success:
				function(json,textStatus){
					// Если произошла ошибка
					if( 1 == json.error_code){
					  result.html(json.error_description);
					}
					else{
					  files = json.resp;
					  result.html(files);
					}
				},
			error:
				function(){
					result.html('Ошибка');
				}
		});
		
}
    

     