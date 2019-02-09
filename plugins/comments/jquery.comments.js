$(document).ready(function(){

	if( $('.message.save').length > 0 ){ $.scrollTo("#comments", 500); }
	
	$('.pagination.comments .button a + span').hide();
	$('.mso-comment-more button + span').hide();

	// получение новой порции комментариев при общей пагинации
	$('.pagination.comments .button a').click(function(){
		var 
		el = this,
		caption = $(this).html(),
		loader = $(this).next().html(),
		current_pg = $(this).attr('data-current'),
		placeholder = $(this).attr('data-placeholder');
			
		$(this).html(loader); // изменяем текст кнопки (показываем прелоадер)
			
		$.ajax({
			url: $(el).attr('data-ajax'), // обработчик
			data: {"do": "loadmore", "current": current_pg, "base": $('#comments').attr('data-base')}, // данные
			type: 'POST', // тип запроса
			dataType: "html",
			success:function(res){
				if( res ){
					$(el).html(caption);
					$(placeholder).append(res); // вставляем скачанные комментарии
					current_pg++; $(el).attr('data-current', current_pg); // увеличиваем номер порции на единицу
					if( current_pg > $(el).attr('data-max') ) $(".pagination").hide(); // если последняя порция комментариев, скрываем кнопку
					SetReplyButtons();
					SetRatingButtons();		
				} else {
					$('.pagination').hide(); // если мы дошли до последней порции комментариев, скроем кнопку
				}
			}
		});
	});
		
	// получение новой порции комментариев внутри дерева
	$('.mso-comment-more button').click(function(){
		var 
		el = this,
		caption = $(this).html(),
		loader = $(this).next().html(),
		current_pg = $(this).attr('data-current'),
		placeholder = $(this).parents('.mso-comment-more').prev('ul');
			
		$(this).html(loader); // изменяем текст кнопки (показываем прелоадер)
			
		$.ajax({
			url: $('#comments').attr('data-ajax'), // обработчик $(el).attr('data-ajax')
			data: {"do": "getmore", "current": current_pg, "base": $('#comments').attr('data-base'), "parent_id": $(this).attr('data-parent')}, // данные
			type: 'POST', // тип запроса
			dataType: "json",
			success:function(json){
				if( json.res != '' ){
					$(el).html(caption);
					$(placeholder).append(json.res); // вставляем скачанные комментарии
					current_pg++; $(el).attr('data-current', current_pg); // увеличиваем номер порции на единицу
					if( json.hide ) $(el).parents('.mso-comment-more').hide(); // если последняя порция комментариев, скрываем кнопку
					SetReplyButtons();
					SetRatingButtons();		
				} else {
					$(el).parents('.mso-comment-more').hide(); // если мы дошли до последней порции коменатриев, скроем кнопку
				}
			}
		});
	});

	// блокируем отправку поля comments_reg для неактивной вкладки «Без регистрации/С регистрацией»
	$('.mso-comment-form.general form').on('submit', function(){
		$('.mso-tabs-box:hidden input[name="comments_reg"]', this).attr('disabled', 'disabled');
	});
		
	// функция установки реакции на кнопку Ответить
	function SetReplyButtons(){
		$('.mso-comment-reply button.cancel').hide();
			
		$('.mso-comment-reply button.reply').on('click', function(){

			var 
			el = this,
			parent = $(el).parent('.mso-comment-reply'),
			parent_id = $(el).attr('data-parent'),
			ctype = $('.mso-comments').hasClass('simple') ? 'simple' : 'complex',
			form = $('.mso-type-page-comments .mso-comment-form.general'),
			clon = $(form).clone(true, true).removeClass('general').show();
			
			$('.mso-comment-leave, .mso-comment-form.general').hide(); // скрываем основную форму
			$('.mso-comment-form.general textarea').attr('id', ''); // скрываем id=comments_content
			$('.mso-comment-reply ~ .mso-comment-form').remove(); // удаляем формы-клоны

			$(parent).after(clon); // переносим форму
			
			var reply_form = $(parent).next('.mso-comment-form').children('form');
			
			// ракировка кнопок и отмена комментирования
			$('button.cancel:visible').hide().prev('button.reply').show();
			$(el).hide().next('button.cancel').show().click(function(){
				$(this).hide().prev('button.reply').show();
				$(parent).next('.mso-comment-form').remove();
				$('.mso-comment-leave, .mso-comment-form.general').show();
				$('.mso-comment-form.general textarea').attr('id', 'comments_content'); // возвращаем id=comments_content
			});
				
			// добавляем информацию о родительском комментарии
			$('<input type="hidden" name="comments_parent_id" value="'+parent_id+'">').prependTo(reply_form);
			
			// добавляем имя кому ответ
			if( $('#comments').attr('data-replyto') )
			{
				var person = $(parent).prevAll('p.mso-comment-info').children('.mso-comment-author').text();
				$(parent).nextAll('.mso-comment-form').find('textarea').val('<b>'+person+'</b>, ');
			}

			$(reply_form).on('submit', function(){

				var form_data = $(this).serialize();
				form_data += '&do=save&comments_submit=1&base=' + $('#comments').attr('data-base');

				$.ajax({
					url: $('#comments').attr('data-ajax'),
					data: form_data, // данные
					type: 'POST', // тип запроса
					dataType: "json",
					success:function( json ){
						if( json && json.res != '' ){
							var art = $(reply_form).parents('.mso-comment-article');
							var lvl = $(el).parents('ul.comments').length; 
								
							$('.mso-comment-reply .cancel', art).trigger('click');

							if( ctype == 'complex' )
							{
								if( json.max_tree_level == 0 || lvl < json.max_tree_level )
								{
									if( $(art).next('ul.comments').length > 0 )
									{
										$(art).next('ul.comments').append(json.res);
									}
									else
									{
										$(art).after('<ul class="comments">'+json.res+'</ul>');
									}
										
									$(art).next('ul.comments').find('.mso-comment-reply').hide();
								}
								else
								{	
									$(art).parents('ul.comments').eq(0).append(json.res);
									$(art).parents('ul.comments').eq(0).find('li:last-child .mso-comment-reply').hide();
								}
							}
							else
							{
								var order = json.order !== undefined ? json.order : 'asc';

								if( order == 'asc' )
								{
									$('.mso-comments section').append(json.res);
									$('.mso-comment-article:last-child .mso-comment-reply').hide();
								}
								else
								{
									$('.mso-comments section').prepend(json.res);
									$('.mso-comment-article:first-child .mso-comment-reply').hide();
								}
							}
						}
							
						return false;
					}
				});
					
				return false;
			});
		});
	}
		
	SetReplyButtons();

	// функция установки реакции на кнопку Оценить («Лайк»)
	function SetRatingButtons(){
		$('.mso-comments button.rating').click( function(){
			var 
			el = this,
			old = $(this).html(),
			loader = $(this).next().html(),
			id = $(this).attr('data-id'),
			url = $('#comments').attr('data-ajax');
				
			$(this).html(loader); // изменяем текст кнопки (показываем прелоадер)
				
			if( url !== undefined )
			{
				$.ajax({
					url: url, // обработчик $(el).attr('data-ajax')
					data: {"do": "like", "id": $(this).attr('data-id')}, // данные
					type: 'POST', // тип запроса
					dataType: "html",
					success:function( res ){
						if( res != '' ){
							$(el).html(res);
						}
						else
						{
							$(el).html(old);
						}
					}
				});
			}
		});
	}
	
	SetRatingButtons();		
	
	$('.mso-comment-best button.context').click( function(){
		window.location = $(this).attr('data-context');
	});
});