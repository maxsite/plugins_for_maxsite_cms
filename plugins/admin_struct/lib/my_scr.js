$(function() {
		/*ajax*/
		$('a.skrut-page').click(function (){ 
			//alert('работаю');			
			var id_page = $(this).attr('href'); //атриб элемента который был нажат
			var data = $(this).attr('title');
			//alert(id_page);
			var parli = $(this).closest('li');
			var chul = $(parli).children('ul.list_page').length;
			
			if(data == 'Удалить страницу') {
				if(!confirm('Вы действительно хотите удалить?')) {
					return false;
				}
			}
			
			if(chul) {
				$(parli).children('ul.list_page').remove();
				return false;
			}
			//alert(chul);
			//return false;
			$('<div class="load_image"></div>')
			  .appendTo('body')
			  .css('top', (event.pageY - 10) + 'px')
			  .css('left', (event.pageX + 20) + 'px')
			  .fadeIn(300);
			
			$.ajax({
					type: 'POST', 
					url: path_ajax, //ссылка на файл, который будет обрабатывать данные
					data: 'id=' + id_page + '&data=' + data, //данные которые отправляются
					dataType: 'text',
					success: function(msg){				  
						$(parli).append(msg);
						$('div.load_image').fadeOut(300);
						// alert(msg);
						// $('div.list-art').empty();
						 //$('div.list-art').append(msg);
						  //тут часть скрипта которая будет выполняться если данные были переданы и файл их обработал        
					//location.reload();
					},
					error: function()
					{		 
						  alert('Произошла ошибка!');//тут часть скрипта которая будет выполняться если по каким-нибудь причинам запрос не был отправлен  
					
					}
		 				});
			
 return false;
		});
		/*// ajax*/
		
		$('div.in-hov').click(function (){
			$(this).attr('class') 
		
		
		
		
		});
		
		
	});