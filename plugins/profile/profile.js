function getEvents(id , oid , all , sbj){
	  var data = 'type=getevents&u_id=' + id + '&l_d=' + last_date + '&o_id=' + oid + '&all=' + all + '&sbj=' + sbj; 
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
					  last_date = json.last_date;
					  loader.hide();
					  if ( 1 == json.end){
					     result.innerHTML = 'Все.';
					     result.show();
					     }
					  else{
					     button.show();
					     result.hide();
					     loader.hide();
					     }
					}
				},
			error:
				function(){
					result.innerHTML = 'Error';
					result.show();
					loader.hide();
					//button.show();
				}
		});
}
   