
function show_hide ( elem, toggle ) {
	$(elem).toggleClass(toggle);
}

function show_exif_data() {
	var elem = $("div.foto-exif-data");
	var visible = elem.css('display');
	if ( visible == "none" ) {
		elem.slideDown( 500, function() {elem.css('display', '')} );
	} else {
		elem.slideUp( 500, function() {elem.css('display', 'none')} );
	}	
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
					var elem = $("div.foto-tags > span[tagid=" + tag_id + "]");
						elem.fadeOut( 1000, function(){ elem.remove()} );
						//} else alert( msg );
					}	
				}
		});		
	}
}


function rating_change( fotoid, value ) {
	if ( fotoid < 1 ) return false;
	$.ajax({
		type: "POST",
		url: path_ajax_fotki,
		data: "fotoid=" + fotoid + "&action=rating-change&value=" + value,
		dataType: "text",
		success: function (msg, textStatus)
		{
			if ( textStatus == "success" ) {
				if ( msg == "allready_vote" ) {
					// уже голосовали
					//alert("Вы уже голосовали за эту фотографию.");
					
				} else {
					var res = eval('(' + msg + ')');
					//alert( res );
					var rate_plus = res.rate_value_plus;
					var rate_minus = res.rate_value_minus;
					var rate_total = 0;
					//if ( rate_minus > rate_plus ) rate_total = 0;
					//else rate_total = rate_plus - rate_minus;
					rate_total = rate_plus - rate_minus;
					$("div.foto-rate span").html( rate_total );
					$("div.foto-rate-plus span").html( rate_plus );
					$("div.foto-rate-minus span").html( rate_minus );
					$("div.foto-rate-count span").html( res.rate_count);
					
					$('div.foto-rate-buttons').html('<span class="foto-rate-voted">Спасибо за ваш голос!</span>');
				}
			}
		}
	});
}

function add_description( ) {
  // покажем форму
  $('span.add-description').hide();
  $("div.description").show();
    //elem.css('display', '');
	//else elem.css('display', 'none');  
}


function cancel_description() {
  $('span.add-description').show();
  $("div.description").hide();
  $('textarea[name="description-text"]').val('');
}

function submit_description( fotoid ) {
  if ( fotoid < 1 ) {
  $('span.add-description').show();
  $("div.description").hide();	
	return false;
  } else {
	var elem = $('textarea[name="description-text"]').val();
	$.ajax({
			type: "POST",
			url: path_ajax_fotki,
			data: "fotoid=" + fotoid + "&action=add-description&value=" + elem,
				dataType: "text",
				success: function(msg, textStatus)
				{
					if ( textStatus == "success" ) {
						  $('span.add-description').remove();
						  $("div.description").remove();
						  //$('textarea[name="description-text"]').val('');
						var html = '<span><strong>Описание: </strong>' + elem + '</span>';
						$('div.foto-descr').append( html );
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
	
						$("div.foto-tags > span#admin-foto-tag").remove();
						
						elem = $("div.foto-tags");
						elem.fadeIn( 1000, function(){ 
							// удалим старые
							
							elem.append( msg ) 
						});
					}	
				}
		});	  

	
}
