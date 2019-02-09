jQuery(document).ready(function(){
       getBlock(0);
    })
    
function getBlock(b_id){
	  var ajax_path = $("#b_get_ajax_path").val();
	  var c_id = $("#b_c_id").val();
	  var e_t_id = $("#b_e_t_id").val();
	  
	  if (b_id>0) e_id = b_id; else e_id = $("#b_e_id").val(); 
	  
	  var data = 'c_id=' + c_id + '&e_id=' + e_id + '&e_t_id=' + e_t_id + '&b_id=' +b_id; 
	  var result = $("#bookmaker_block" + b_id);
	  
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
	 
};  

function editBM(act , b_id){
	  var ajax_path = $("#b_edit_ajax_path").val();
	  var c_id = $("#b_c_id").val();
	  var e_id = $("#b_e_id").val();
	  var e_t_id = $("#b_e_t_id").val();
	  
	  if (b_id>0) e_id = b_id;
	  
	  var data = 'c_id=' + c_id + '&e_id=' + e_id + '&e_t_id=' + e_t_id + '&act=' + act + '&b_id=' +b_id;  
	  var result = $("#bookmaker_block" + b_id);
	  
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

   getBlock(b_id);	 
}; 