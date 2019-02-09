$(document).ready(function() {

	var loader = '<div class="loader"></div>';

	$('.fform button').on('click', function(){

		$('.loader,.update').remove();
		$(this).after( loader );

		if( $(this).hasClass('one') )
		{
			go('one');
		}
		else
		{
			$('.results').html('');
			go('all');
		}

		return false;
	});

	function go( target ){

		$.ajax({
			url: ajax_path,
			data: 'do=search', // ищем записи
			type: 'POST', // тип запроса
			dataType: "json",
			success:function( json ){
				if( 1 == json.res ){
					$('.results').prepend(json.html);
					if( target == 'all' )
					{
						go(target);
					}
					else
					{
						$('.loader').remove();
					}
				}
				else
				{
					$('.loader').remove();
					$('.results').prepend('<p>'+json.html+'</p>');
				}
			},
			error: function(xhr, str)
			{
				alert("Возникла ошибка: " + xhr.responseCode + "\n"+str);
			}

		});
	
	}

});
