var delay_delay = 1000;
var delay_fadeOut = 1000;
var delay_timeout = 1000;
var delay_fadeIn = 500;

// проголосовать за пост
function vote(page_id, t) {
	if ( user_logged != 1 & only_logged == 1 ) {
		show_popup(page_id, "Необходимо войти в систему", "red");		 
	} else {
	
	$.ajax({
             type: "POST",
             url: path_ajax,
             data: "page_id=" + page_id + "&value=" + t,
             dataType: "text",
             success: function(msg, textStatus)
             {
				if ( msg == "allready_vote") {
					//var tt = "<span class=pagerate_value_voted>Вы уже голосовали!</span>";
					//send_email( send_mail );	

					//send_email( send_mail, t );
					show_popup(page_id, "Вы уже голосовали!", "red");		 
					//$("span#pagerate_" + page_id).html( tt ).fadeOut(delay_fadeOut);//.delay(3000);
					// сделать новый запрос на рейтинг и отобразить
					
					/*
					function getfunc(x){
					    return function(){
					    get_value(x);
					      }
					}
					*/
					//setTimeout( getfunc(page_id), delay_timeout ); 
					
				} else {
				    $("span#pagerate_" + page_id).html( msg );
				    show_popup(page_id, "Ваш голос учтен!", "green");	
						// отправить email
					send_email( send_mail, t );
				}	
				/* если уже голосовали, то отобразить это и через заданный интервал показать старое значение */
				
             }
         });
     }
		 
}

	// отправить запрос обновление базы и получить ответ
	function update(page_id, d) {
	  $("span#pagerate_" + page_id).html( d ).fadeIn(delay_fadeIn)
	}

	function get_value(page_id) {

	  $.ajax({
				type: "POST",
				url: path_ajax,
				data: "page_id=" + page_id + "&value=2",
				dataType: "text",
				success: function(msg2, textStatus)
						{
							update(page_id, msg2); 
						}

	  }); 
	}

function show_popup(page_id, text, color)
{
  		$("div.popup_" + page_id).css('color', color);  		
  		$("div.popup_" + page_id + " > div.popup_center").html( text );

  		$("div.bubbleInfo_" + page_id).fadeIn(800, function(){
  		
	  		$("div.bubbleInfo_" + page_id).css('display', 'block');
  		
  		});

  		
  		setTimeout( function(){
  						  		$("div.bubbleInfo_" + page_id).fadeOut(2000, function(){
	  						  		$("div.bubbleInfo_" + page_id).css('display', 'none');
	  						  		$("div.popup_" + page_id + " > div.popup_center").html('');
  						  		});
  						  		
 						  		
  					}, 1000 );

 
}

function send_email( send_mail, t ) {
	$.ajax({
             type: "POST",
             url: path_ajax,
             data: "send_mail_type=" + send_mail + "&title=" + page_title + "&slug=" + page_slug + "&change=" + t,
             dataType: "text",
             success: function(msg, textStatus)
             {
             }
         });

}

