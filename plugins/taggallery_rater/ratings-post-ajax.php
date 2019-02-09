<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	global $_COOKIE;

	// if (!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) die('AJAX Error');

	mso_checkreferer(); // защищаем реферер

	if ( $post = mso_check_post(array('rating', 'slug')) )
	{
		// данные хранятся в куках посетителя - алгоримт тотже, что и в mso_page_view_count_first
		$name_cookies = 'maxsite_rating';
		$expire = 60 * 60 * 24 * 30; // 30 дней = 2592000 секунд

		if (isset($_COOKIE[$name_cookies]))	$all_slug = $_COOKIE[$name_cookies]; // значения текущего кука
			else $all_slug = ''; // нет такой куки вообще

		$slug = $post['slug']; // слаг страницы откуда пришел запрос

		$all_slug = explode(' ', $all_slug); // разделим в массив

		if ( in_array($slug, $all_slug) ) // уже есть текущий урл - не увеличиваем счетчик
		{
			echo '<span>' . t('Вы уже голосовали!', 'plugins') . '</span>';
			return;
		}

		$rating = (int) $post['rating']; // выставленная оценка

		if ($rating) // есть присланная оценка
		{
			// нужно обновить рейтинг
			$all_slug[] = $slug; // добавляем текущий id
			$all_slug = array_unique($all_slug); // удалим дубли на всякий пожарный
			$all_slug = implode(' ', $all_slug); // соединяем обратно в строку
			$expire = time() + $expire;
			@setcookie($name_cookies, $all_slug, $expire); // записали в куку


      $picture = taggallery_get_picture($slug); 
			$picture['rating'] = $picture['rating'] + $rating;
			$picture['rating_count']++;

      taggallery_set_picture($slug , array( 'rating' => $picture['rating'] , 'rating_count' => $picture['rating_count'] ));

			$sredn = round($picture['rating'] / $picture['rating_count']);

				echo '<span>' . t('Ваша оценка:', 'plugins') . '</span> ' . $rating . '<br><span>' 
							. t('Средняя оценка', 'plugins') . '</span>: ' . $sredn 
							. ' ' . t('из', 'plugins') . ' ' . $picture['rating_count'] . ' ' 
							. t('проголосовавших', 'plugins');
				
				mso_hook('global_cache_all_flush'); // сбрасываем весь html-кэш
			
			}
		}
	
?>