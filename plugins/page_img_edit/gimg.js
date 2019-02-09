function getPI(p_no){
	  var data = 'type=getimg&p_id=' + p_no; 
      var res = document.getElementById("content_img");
      
      page_id = p_no;
      var result = document.getElementById("result");
	  var loader = $('#loader');

$(".page_link").css({backgroundColor: "white"});

$("#pg" +p_no).css({backgroundColor: "#F00"});
	  
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path_content,
			data: data,
			beforeSend:
				function(){
				  loader.show();
				},
			success:
				function(json,textStatus){
					// Если произошла ошибка
					if( 1 == json.error_code){
					  result.innerHTML = json.error_description;
					  loader.hide();
					}
					else{
					  result.innerHTML = '';
					  res.innerHTML = json.resp;
					  $("#select_dir").html(json.dirs);
					  var cur_dir = json.cur_dir;
					  getPDir(cur_dir);		

					  loader.hide();
					  if ( 1 == json.end){
					     result.hide();
					     }
					  else{
					     result.hide();
					     loader.hide();
					     }
					}
				},
			error:
				function(){
					result.innerHTML = 'Error';
					result.show;
					loader.hide();
				}
		});

				
}
   
   
function getPDir(dir){
	  var data = 'type=getimg&dir=' + dir + '/'; 
      var res = document.getElementById("file_img");
      var result = document.getElementById("result");
      
	  var loader = $('#loader');
	  
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path_files,
			data: data,
			beforeSend:
				function(){
				  loader.show();
				},
			success:
				function(json,textStatus){
					// Если произошла ошибка
					if( 1 == json.error_code){
					  result.innerHTML = json.error_description;
					  loader.hide();
					}
					else{
					  result.innerHTML = '';
					  res.innerHTML = json.resp;
					  loader.hide();
                      $("#goto_files").html(json.goto_files);					  
					  if ( 1 == json.end){
					     result.hide();
					     }
					  else{
					     result.hide();
					     loader.hide();
					     }
					}
				},
			error:
				function(){
					result.innerHTML = 'Error';
					result.show;
					loader.hide();
				}
		});
}
      
   