
function show_hide ( elem, toggle ) {
	$(elem).toggleClass(toggle);
}

function openNewWindow()
{
	/*
		$hide_window = '<p><span><strong>Название: </strong></span> <input type="text" name="f_edit_album_title"></p>';
	$hide_window .= '<p><span><strong>Ссылка: </strong></span> <input type="text" name="f_edit_album_slug"></p>';
	$hide_window .= '<p><input type="hidden" name="f_edit_album_parent_id" value=""></p>';
	*/
}

function edit_album( albumid, type, title, slug ) {

	if ( type == 0 ) {
		$('#edit_album_title').val( title );
		$('#edit_album_slug').val( slug );
		$('#edit_album_id').val( albumid );
		$('#album_edit').toggleClass('visible');
		
		openNewWindow();

    var result = hs.htmlExpand(_this, { objectType: 'iframe' } );
    return result;		
		
	} else if ( type == 1 ) {
		// удаление
		if ( window.confirm('Удалить альбом?') ) {
			$.ajax({
					 type: "POST",
					 url: path_ajax_fotki,
					 data: "album_id=" + albumid + "&action=delete",
					 dataType: "text",
					 success: function(msg, textStatus)
					 {
						if ( msg == 1 ) {
							// удалим элемент
							 $("div#album_" + albumid).remove();
						} else alert( msg );
					 }
				 });		
		}
	}
}

function del_foto() {
	var fotos_id = [];
	$('div.admin-foto > div.admin-foto-url > input:checkbox[name="f_foto_del[]"]:checked').each(
		function( index, Element ) {
			// удаляем
			var fotoid = $(this).attr( 'fotoid' );
			fotos_id.push(fotoid); 
		});
	if  ( fotos_id.length > 0 ) {
		if (window.confirm('Удалить выбранные фотографии?')) { 	

			for ( var i = 0; i < fotos_id.length; i++ ) 
			{ 	
				$.ajax({
					 type: "POST",
					 url: path_ajax_fotki,
					 data: "fotoid=" + fotos_id[i] + "&action=delete",
					 dataType: "text",
					 success: function(msg, textStatus)
					 {
						
						if ( msg > 0 ) {
							// удалим элемент
							var elem = $("div.admin-foto[fotoid=" + msg + "]");
							 elem.fadeOut( 1000, function(){ elem.remove()} );
						} else alert( msg );
					 }
				 });
			}	 
		}			
	} else {
		alert('Необходимо указать фотографии!');
	}
}

function check_all_foto ( ) {
$('div.admin-foto > div.admin-foto-url > input:checkbox[name="f_foto_del[]"]').each(
		function( index, Element ) {
			// чекаем
			$(this).attr('checked', 'checked');
		});
}

function uncheck_all_foto ( ) {
$('div.admin-foto > div.admin-foto-url > input:checkbox[name="f_foto_del[]"]').each(
		function( index, Element ) {
			// чекаем
			$(this).removeAttr('checked');
		});
}

function toggle_check_foto() {
$('div.admin-foto > div.admin-foto-url > input:checkbox[name="f_foto_del[]"]').each(
		function( index, Element ) {
			// чекаем
			var checked = $(this).attr('checked');
			if ( checked ) $(this).removeAttr('checked');
			else $(this).attr('checked', 'checked');
		});
}

function delete_tags( tag_id ) {
	if ( tag_id < 0 ) {
		return false;
	} else {
		$.ajax({
				type: "POST",
				url: path_ajax_fotki,
				data: "tagid=" + tag_id,
				dataType: "text",
				success: function(msg, textStatus)
				{
					if ( textStatus == "success" ) {
					alert( tag_class );
					var elem = $("div.foto-meta > span[tagid=" + tag_id + "]");
						elem.fadeOut( 1000, function(){ elem.remove()} );
						//} else alert( msg );
					}	
				}
		});		
	}
}

function add_new_tag() {
	// покажем поле для добавления или всплывающее окно???
	var elem = $("div#add-new-meta");
	var visible = elem.css('display');
	if ( visible == "none" ) elem.css('display', '');
	else elem.css('display', 'none');
}

function add_new_meta( fotoid ) {
  if ( fotoid < 1 ) return false;
  var elem = $('div#add-new-meta > input[name="add-new-meta-value"]');
  var value = elem.val();
		//alert( fotoid + '   ' + value );
		$.ajax({
				type: "POST",
				url: path_ajax_fotki,
				data: "fotoid=" + fotoid + '&metavalue=' + value,
				dataType: "text",
				success: function(msg, textStatus)
				{
					//alert( msg );
					if ( textStatus == "success" ) {
						var elem = $('div#add-new-meta > input[name="add-new-meta-value"]');
						elem.val('');
						elem = $("div#add-new-meta");
						elem.css('display', 'none');
	
						$("div.foto-meta > span#admin-foto-tag").remove();
						
						elem = $("div.foto-meta");
						elem.fadeIn( 1000, function(){ 
							// удалим старые
							
							elem.append( msg ) 
						});
					}	
				}
		});	  

	
}