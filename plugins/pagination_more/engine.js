$(document).ready(function(){
	
	$('.pagination.pages .button a + span').hide();
		
	$('.pagination.pages .button a').click(function(){
		var el = this;
		var caption = $(this).html(); // console.log(caption);
		var loader = $(this).next().html(); // console.log(loader);
		var current_pg = $(this).attr('data-current');
			
		$(this).html(loader); // изменяем текст кнопки (показываем прелоадер)
			
		$.ajax({
			url: $(el).attr('data-ajax'), // обработчик
			data: {"do": "loadmore", "type": $(el).attr('data-type'), "current": current_pg, "base": $(this).attr('data-base')}, // данные
			type: 'POST', // тип запроса
			dataType: "html",
			success:function(res){
				if( res ){
					$(el).html(caption);
					$(el).parents('.pagination').prevAll('div[class!="pagination"]').append(res); // вставляем скачанные записи
					current_pg++; $(el).attr('data-current', current_pg); // увеличиваем номер страницы на единицу
					if( current_pg == $(el).attr('data-max') ) $(".pagination").hide(); // если последняя страница, скрываем кнопку
				} else {
					$('.pagination').hide(); // если мы дошли до последней страницы записей, скроем кнопку
				}
			}
		});
	});	
});
