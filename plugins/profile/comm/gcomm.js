function getEvents(pid , uid , cid , srt){
	  var data = 'type=getevents&p_id=' + pid + '&u_id=' + uid + '&c_id=' + cid + '&sort=' + srt + '&pag_no=' + pag_no + '&limit=' + limit + '&pag_c=' + pag_c; 
      var res = document.getElementById("events");
      var result = document.getElementById("result");
      var button = $("#get_events_button");
	  var loader = $('#loader');
	  
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
				  loader.show();
				  button.hide();
				},
			success:
				function(json,textStatus){
					// Если произошла ошибка
					if( 1 == json.error_code){
					  result.innerHTML = json.error_description;
					  loader.hide();
					  button.show();
					}
					else{
					  res.innerHTML += json.resp;
					  pag_c = json.pag_c;
					  pag_no++;
					  
					  loader.hide();
					  if ( 1 == json.end){
					     result.innerHTML = 'Все';
					     result.show();
					     }
					  else{
					     button.show();
					     result.hide;
					     loader.hide;
					     }
					}
				},
			error:
				function(){
					result.innerHTML = 'Error';
					result.show;
					loader.hide;
					//button.show();
				}
		});
}
   