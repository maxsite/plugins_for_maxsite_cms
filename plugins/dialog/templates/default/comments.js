function addAnswer(id, oauthor){
		   comment = document.getElementById('comment-' + id);
		   comment_new = document.getElementById('comments_new');
		 
       if (comment)		 
       {
		     parent_id = document.getElementById('parent_id');
		     parent_id.value = id;
		     comment_new.innerHTML = 'Автор: ' + oauthor + '<blockquote>' + comment.innerHTML + '</blockquote>';
     
          $('#new_comment').fadeIn('slow');
		      $('#comment_submit').focus();
		    }
		  }  
		   
function addQuote(id, oauthor) {
  var comment = document.getElementById('comment-' + id);
  var comment_new = document.getElementById('comments_content');

  if (window.getSelection)
	 var sel = window.getSelection();
  else if (document.getSelection)
	 var sel = document.getSelection();
  else if (document.selection)
	 var sel = document.selection.createRange().text;
  if (comment.innerText){
	  if (sel != '') comment_new.value += '<blockquote ' + 'id="' + id + '">' + sel + '</blockquote>\n'; 
		else comment_new.value += '<blockquote ' + 'id="' + id + '">' + comment.innerText + '</blockquote>\n';
  }
  else { 
	  if (sel != '') comment_new.value += '<blockquote ' + 'id="' + id + '">' + sel + '</blockquote>\n' 
		else comment_new.value += '<blockquote ' + 'id="' + id + '">' + comment.textContent + '</blockquote>\n';
  }
  $('#comment_submit').focus();
}		   


function addNew() {
  $('#tips').html('Добавление нового комментария');
  $('#tips').delay(300).fadeIn() ;  
  $('#comment_submit').focus();
}	


function noAnswer(){
		   var comment_new = document.getElementById('comments_new');
		   var parent_id = document.getElementById('parent_id');
		   parent_id.value = '';
       comment_new.value = '';
       $("#new_comment").fadeOut("slow");}
		    

function showAnswers(id){
       $('#answers_on' + id).fadeOut() ;
       $('#answers_off' + id).delay(300).fadeIn() ;
       $('#answers_out' + id).delay(500).fadeIn() ;
       }	
       
function hideAnswers(id){
       $("#answers_out" + id).fadeOut() ;
       $('#answers_off' + id).fadeOut() ;
       $('#answers_on' + id).delay(300).fadeIn() ;}	
      
function showParent(id){
       $('#parent_on' + id).fadeOut() ;
       $('#parent_off' + id).delay(300).fadeIn() ;
       $('#parent_out' + id).delay(500).fadeIn() ;
       }	
       
function hideParent(id){
       $("#parent_out" + id).fadeOut() ;
       $('#parent_off' + id).fadeOut() ;
       $('#parent_on' + id).delay(300).fadeIn() ;}	      
       
 
  function d_send_vote(count, button, result,loader,data,ajax_path){
	
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
					result.hide();
					button.hide();
			    loader.show();
				},
			success:
				function(json,textStatus){
					
					// Если произошла ошибка
					if( 1 == json.error_code){
					  loader.hide();
					  button.show();
					  result.html(json.error_description);
					  result.show();
					}
					else{
						loader.hide();
						result.html(json.resp);
					  button.show();
					  result.hide();
						count.html( json.count);
					}
				},
			error:
				function(){
					loader.hide();
					button.show();
					result.show();
					result.html('Ошибка');
				}
		});
	}
	
function vote_plus(c_id , u_id , a_id){
		
		var data = 'type=vote_plus&c_id=' + c_id + '&u_id=' + u_id + '&a_id=' + a_id; 
		var result = $('#vote_result_' + c_id);
		var count = $('#vote_plus_count_' + c_id);
		var loader = $('#vote_loader_' + c_id);
		var button = $('#vote_plus_button_' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		
		if( ajax_path.length ){
			d_send_vote(count, button, result,loader,data,ajax_path);
		}	
		
	}

function vote_minus(c_id , u_id , a_id){
		
		var data = 'type=vote_minus&c_id=' + c_id + '&u_id=' + u_id + '&a_id=' + a_id; 
		var result = $('#vote_result_' + c_id);
		var count = $('#vote_minus_count_' + c_id);
		var loader = $('#vote_loader_' + c_id);
		var button = $('#vote_minus_button_' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		
		if( ajax_path.length ){
			d_send_vote(count, button, result,loader,data,ajax_path);
		}	
		
	}


 function d_send_query(count, button, result,loader,data,ajax_path){
	
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
					result.hide();
					button.hide();
			    loader.show();
				},
			success:
				function(json,textStatus){
					
					// Если произошла ошибка
					if( 1 == json.error_code){
					  loader.hide();
					  button.show();
					  result.html(json.error_description);
					  result.show();
					}
					else{
						loader.hide();
						result.html(json.resp);
						button.hide();
						result.show();
						count.html( json.count);
					}
				},
			error:
				function(){
					loader.hide();
					button.show();
					result.show();
					result.html('Ошибка');
				}
		});
	}



function aaa(c_id , u_id , a_id){
		
		var data = 'type=danke&c_id=' + c_id + '&u_id=' + u_id + '&a_id=' + a_id; 
		var result = $('#d_result_' + c_id);
		var count = $('#d_count_' + c_id);
		var loader = $('#d_loader_' + c_id);
		var button = $('#d_danke_button_' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		
		if( ajax_path.length ){
			d_send_query(count, button, result,loader,data,ajax_path);
		}	
	}

function showDanke(id){
       $('#d_list_' + id).fadeToggle() ;
       }	

function showQuotes(id){
       $('#d_q_list_' + id).fadeToggle() ;
       }	
              

function bad(c_id , u_id , a_id){
		
		var data = 'type=bad&c_id=' + c_id + '&u_id=' + u_id + '&a_id=' + a_id; 
		var result = $('#d_result_bad_' + c_id);
		var count = $('#d_count_bad_' + c_id);
		var loader = $('#d_loader_bad_' + c_id);
		var button = $('#d_bad_button_' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		
		if( ajax_path.length ){
			d_send_query(count, button, result,loader,data,ajax_path);
		}	
	}    
	

function spam(c_id , u_id){
		
		var data = 'type=spam&c_id=' + c_id + '&u_id=' + u_id; 
		var result_not_check = $('#d_result_spam_not_check_' + c_id);
		var result_spam = $('#d_result_spam_' + c_id);
		var result_not_spam = $('#d_result_not_spam_' + c_id);
		var loader = $('#d_loader_spam_check_' + c_id);
		var button_spam = $('#d_spam_button_' + c_id);
		var button_not_spam = $('#d_not_spam_button_' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		
		if( ajax_path.length ){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
					result_not_check.hide();
					result_spam.hide();
					button_spam.hide();
			    loader.show();
				},
			success:
				function(json,textStatus){
					
					// Если произошла ошибка
					if( 1 == json.error_code){
					  loader.hide();
					  button_spam.show();
					  result_spam.html(json.error_description);
					  result_spam.show();
					}
					else{
						loader.hide();
					//	result_not_spam.html(json.resp);
						button_not_spam.show();
						result_spam.show();
					}
				},
			error:
				function(){
					loader.hide();
					button_spam.show();
					result_spam.show();
					result_spam.html('Error');
				}
		});
		}	
	}  	

function not_spam(c_id , u_id){
		
		var data = 'type=not_spam&c_id=' + c_id + '&u_id=' + u_id; 
		var result_not_check = $('#d_result_spam_not_check_' + c_id);
		var result_spam = $('#d_result_spam_' + c_id);
		var result_not_spam = $('#d_result_not_spam_' + c_id);
		var loader = $('#d_loader_spam_check_' + c_id);
		var button_spam = $('#d_spam_button_' + c_id);
		var button_not_spam = $('#d_not_spam_button_' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		
		if( ajax_path.length ){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
					result_not_check.hide();
					result_spam.hide();
					button_not_spam.hide();
			    loader.show();
				},
			success:
				function(json,textStatus){
					
					// Если произошла ошибка
					if( 1 == json.error_code){
					  loader.hide();
					  button_not_spam.show();
					  result_not_spam.html(json.error_description);
					  result_not_spam.show();
					}
					else{
						loader.hide();
					//	result_not_spam.html(json.resp);
						button_spam.show();
					//	result_not_spam.show();
					}
				},
			error:
				function(){
					loader.hide();
					button_not_spam.show();
					result_not_spam.show();
					result_not_spam.html('Error');
				}
		});
		}	
	}  		  
	
	
function approved(c_id , u_id){
		
		var data = 'type=approved&c_id=' + c_id + '&u_id=' + u_id; 
		var result_approved = $('#d_result_approved_' + c_id);
		var loader = $('#d_loader_approved_' + c_id);
		var button_approved = $('#d_approved_button_' + c_id);
		var button_not_approved = $('#d_not_approved_button_' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		
		if( ajax_path.length ){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
					button_approved.hide();
					result_approved.hide();
			    loader.show();
				},
			success:
				function(json,textStatus){
					
					// Если произошла ошибка
					if( 1 == json.error_code){
					  loader.hide();
					  result_approved.html(json.error_description);
					  result_approved.show();
			      button_approved.show();
					}
					else{
						loader.hide();
						button_not_approved.show();
					}
				},
			error:
				function(){
					loader.hide();
					button_approved.show();
					result_approved.show();
					result_approved.html('Error');
				}
		});
		}	
	} 
	
	
function not_approved(c_id , u_id){
		
		var data = 'type=not_approved&c_id=' + c_id + '&u_id=' + u_id; 
		var result_approved = $('#d_result_approved_' + c_id);
		var loader = $('#d_loader_approved_' + c_id);
		var button_approved = $('#d_approved_button_' + c_id);
		var button_not_approved = $('#d_not_approved_button_' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		
		if( ajax_path.length ){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
					button_not_approved.hide();
					result_approved.hide();
			    loader.show();
				},
			success:
				function(json,textStatus){
					
					// Если произошла ошибка
					if( 1 == json.error_code){
					  loader.hide();
					  result_approved.html(json.error_description);
					  result_approved.show();
			      button_not_approved.show();
					}
					else{
						loader.hide();
						button_approved.show();
						result_approved.show();
					}
				},
			error:
				function(){
					loader.hide();
					button_not_approved.show();
					result_approved.show();
					result_approved.html('Error');
				}
		});
		}	
	} 
	

function deleted(c_id , u_id){
		
		var data = 'type=deleted&c_id=' + c_id + '&u_id=' + u_id; 
		var result_deleted = $('#d_result_deleted_' + c_id);
		var loader = $('#d_loader_deleted_' + c_id);
		var button_deleted = $('#d_deleted_button_' + c_id);
		var button_not_deleted = $('#d_not_deleted_button_' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		
		if( ajax_path.length ){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
					button_deleted.hide();
					result_deleted.hide();
			    loader.show();
				},
			success:
				function(json,textStatus){
					
					// Если произошла ошибка
					if( 1 == json.error_code){
					  loader.hide();
					  result_deleted.html(json.error_description);
					  result_deleted.show();
			      button_deleted.show();
					}
					else{
						loader.hide();
					  result_deleted.show();
						button_not_deleted.show();
					}
				},
			error:
				function(){
					loader.hide();
					button_deleted.show();
					result_deleted.show();
					result_deleted.html('Error');
				}
		});
		}	
	} 				   
	
	
function ban(c_id, a_id , u_id){
		
		var data = 'type=ban&c_id=' + c_id + '&a_id=' + a_id + '&u_id=' + u_id; 
		var result_ban = $('#d_result_ban_' + c_id);
		var loader = $('#d_loader_ban_' + c_id);
		var button_ban = $('#d_ban_button_' + c_id);
		
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		
		if( ajax_path.length ){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
					button_ban.hide();
					result_ban.hide();
			    loader.show();
				},
			success:
				function(json,textStatus){
					
					// Если произошла ошибка
					if( 1 == json.error_code){
					  loader.hide();
					  result_ban.html(json.error_description);
					  result_ban.show();
			          button_ban.show();
					  result_ban.html(json.resp);
					}
					else{
						loader.hide();
					    result_ban.show();
						button_ban.show();
					}
				},
			error:
				function(){
					loader.hide();
					button_ban.show();
					result_ban.show();
					result_ban.html('Error');
				}
		});
		}	
	} 	
	
function not_deleted(c_id , u_id){
		
		var data = 'type=not_deleted&c_id=' + c_id + '&u_id=' + u_id; 
		var result_deleted = $('#d_result_deleted_' + c_id);
		var loader = $('#d_loader_deleted_' + c_id);
		var button_deleted = $('#d_deleted_button_' + c_id);
		var button_not_deleted = $('#d_not_deleted_button_' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		
		if( ajax_path.length ){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
					button_not_deleted.hide();
					result_deleted.hide();
			    loader.show();
				},
			success:
				function(json,textStatus){
					
					// Если произошла ошибка
					if( 1 == json.error_code){
					  loader.hide();
					  result_deleted.html(json.error_description);
					  result_deleted.show();
			      button_not_deleted.show();
					}
					else{
						loader.hide();
						button_deleted.show();
					}
				},
			error:
				function(){
					loader.hide();
					button_not_deleted.show();
					result_deleted.show();
					result_deleted.html('Error');
				}
		});
		}	
	} 	
	
function flud(c_id , u_id){
		
		var data = 'type=flud&c_id=' + c_id + '&u_id=' + u_id; 
		var result_flud = $('#d_result_flud_' + c_id);
		var loader = $('#d_loader_flud_' + c_id);
		var button_flud = $('#d_flud_button_' + c_id);
		var button_not_flud = $('#d_not_flud_button_' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		
		if( ajax_path.length ){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
					button_flud.hide();
					result_flud.hide();
			    loader.show();
				},
			success:
				function(json,textStatus){
					
					// Если произошла ошибка
					if( 1 == json.error_code){
					  loader.hide();
					  result_flud.html(json.error_description);
					  result_flud.show();
			      button_flud.show();
					}
					else{
						loader.hide();
					  result_flud.show();
						button_not_flud.show();
					}
				},
			error:
				function(){
					loader.hide();
					button_flud.show();
					result_flud.show();
					result_flud.html('Error');
				}
		});
		}	
	} 		
	
function not_flud(c_id , u_id){
		
		var data = 'type=not_flud&c_id=' + c_id + '&u_id=' + u_id; 
		var result_flud = $('#d_result_flud_' + c_id);
		var loader = $('#d_loader_flud_' + c_id);
		var button_flud = $('#d_flud_button_' + c_id);
		var button_not_flud = $('#d_not_flud_button_' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		
		if( ajax_path.length ){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
					button_not_flud.hide();
					result_flud.hide();
			    loader.show();
				},
			success:
				function(json,textStatus){
					
					// Если произошла ошибка
					if( 1 == json.error_code){
					  loader.hide();
					  result_flud.html(json.error_description);
					  result_flud.show();
			      button_not_flud.show();
					}
					else{
						loader.hide();
						button_flud.show();
					}
				},
			error:
				function(){
					loader.hide();
					button_not_flud.show();
					result_flud.show();
					result_flud.html('Error');
				}
		});
		}	
	} 			
	
	
function font(u_id){
		var content_content = $('.comment_comment');
		var size = content_content.css("font-size");
		if (size == '10px') size='13px';
		else{ if (size == '13px') size='16px';
		       else{ if (size == '16px') size='20px';
		              else size='10px';
		            }}
		content_content.css("font-size" , size);

	 	var data = 'type=font&u_id=' + u_id + '&size=' + size; 

		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();

		if( ajax_path.length ){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){},
			success:
				function(json,textStatus){
					// Если произошла ошибка
					if( 1 == json.error_code){ }
					else{	}
				},
			error:
				function(){}
		});
		}	

	} 
	
function addPD(id, a_id, cat_id){
		 parent_form = document.getElementById('parent_form-' + id);
		 form_html = '<p>Создание порожденной дискуссии - ответвления</p>' + 
		      '<table class="t_parent_form"><tr><td class="t1">Заголовок дискуссии: </td><td><input type="text" id="child_disc_title-' + id + '" value=""></td></tr></table>' + 
		      '<p><input type="button" class="button_cancel" value="Создать" title="Создать" onClick="createPD(' + id + ',' + a_id + ',' + cat_id + ') "><input type="button" class="button_cancel" value="Отмена" title="Отмена" onClick="cancelPD(' + id + ') "></p>';
	     parent_form.innerHTML = form_html;
	     $('#button_parent-' + id).hide();
         $('#parent_form-' + id).show();
 }  
 
function cancelPD(id){
		 parent_form = document.getElementById('parent_form-' + id);
		 button_parent = document.getElementById('button_parent-' + id);
		 form_html = '';
	     parent_form.innerHTML = form_html;
         $('#parent_form-' + id).hide();
         $('#button_parent-' + id).show();
 } 
 
 
 function createPD(c_id , u_id, cat_id){
		
		var title = $('#child_disc_title-'  + c_id ).val();
		var data = 'type=child_disc&c_id=' + c_id + '&u_id=' + u_id + '&cat_id=' + cat_id + '&title=' + title; 
		var button_parent = $('#button_parent-' + c_id);
		parent_form = document.getElementById('parent_form-' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_ajax_path').val();
		button_parent.show();

		if( ajax_path.length ){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
                    parent_form.innerHTML = '';
				},
			success:
				function(json,textStatus){
					// Если произошла ошибка
					if( 1 == json.error_code){
					  parent_form.innerHTML = json.error_description;
					}
					else{
                       //ввобще то должен выполнится редирект
                       //выведем ссылку на созданную дискуссию
                       parent_form.innerHTML = json.resp;
					}
				},
			error:
				function(){
					parent_form.innerHTML = 'Error';
				}
		});
		}	
	} 			
  	
function cancelcommDisc(c_id){
		var commdisc = $("#commdisc" + c_id);
		var button_cancel = $('#cancel_move' + c_id);
		var button_move = $('#comment_move' + c_id);		
        commdisc.html('');
        commdisc.hide();
        button_cancel.hide();
        button_move.show();
}

function mComm(c_id,u_id,d_id){
		var data = 'type=commove&u_id=' + u_id + '&c_id=' + c_id + '&d_id=' + d_id; 
		var button_cancel = $('#cancel_move' + c_id);
		var button_move = $('#comment_move' + c_id);
		var commdisc = $("#commdisc" + c_id);
		var loader = $('#d_loader_move_' + c_id);
		
		// Отправляем POST запрос
		var ajax_path = $('#d_get_ajax_path').val();

		if( ajax_path.length ){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
		            button_cancel.hide();
		            commdisc.hide();
                    loader.show();
				},
			success:
				function(json,textStatus){
					// Если произошла ошибка
					if( 1 == json.error_code){
					  err = json.error_description;
					  commdisc.html(err);
					  commdisc.show();
					  loader.hide();
					  button_move.show();
					}
					else{
					   res = json.resp;
                       commdisc.html(res);
                       commdisc.show();
                       loader.hide();
					}
				},
			error:
				function(){
				      err = 'Error';
					  commdisc.html(err);
					  commdisc.show();
					  loader.hide();
					  button_move.show();					
				}
		});
		}			
}
  	
function commDisc(c_id , u_id, a_id, d_id){
		
		var data = 'type=getdisclist&u_id=' + u_id + '&c_id=' + c_id + '&d_id=' + d_id; 
		var button_cancel = $('#cancel_move' + c_id);
		var button_move = $('#comment_move' + c_id);
		var commdisc = $("#commdisc" + c_id);
		var loader = $('#d_loader_move_' + c_id);
		// Отправляем POST запрос
		var ajax_path = $('#d_get_ajax_path').val();

		if( ajax_path.length ){
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajax_path,
			data: data,
			beforeSend:
				function(){
				    button_move.hide();
                    loader.show();
				},
			success:
				function(json,textStatus){
					// Если произошла ошибка
					if( 1 == json.error_code){
					  err = json.error_description;
					  commdisc.html(err);
					  commdisc.show();
					  loader.hide();
					  button_move.show();
					}
					else{
					   res = json.resp;
                       commdisc.html(res);
                       commdisc.show();
                       loader.hide();
                       button_cancel.show();
					}
				},
			error:
				function(){
				    res = 'Error';
					commdisc.html(res);
					commdisc.show();
					  loader.hide();
					  button_move.show();					
				}
		});
		}	
	} 				