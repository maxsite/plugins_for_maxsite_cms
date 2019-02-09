<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

require(getinfo('template_dir') . 'main-start.php');

// получаем доступ к CI
$CI = & get_instance();

// проверка сегмента
$id = mso_segment(3);
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

if ($id)
{
	// загружаем данные из базы
	$CI->db->from('dignity_joke');
	$CI->db->where('dignity_joke_id', $id);
	$CI->db->join('dignity_joke_category', 'dignity_joke_category.dignity_joke_category_id = dignity_joke.dignity_joke_category', 'left');
	$CI->db->join('comusers', 'comusers.comusers_id = dignity_joke.dignity_joke_comuser_id', 'left');
	if (!is_login())
	{
		$CI->db->where('dignity_joke_approved', 1);
	}
	$query = $CI->db->get();

	// если есть что выводить
	if ($query->num_rows() > 0)	
	{	
		$allpages = $query->result_array();
		
		// обьявляем переменую	
		$out = '';
		
		// возвращаем туда, откуда пришел
		$url = getinfo('site_url') . $options['slug'];
		
		// цикл
		foreach ($allpages as $onepage) 
		{
			
			// подсчёт количества просмотров через cookie
			global $_COOKIE;
			$name_cookies = 'dignity-joke';
			$expire = 2592000;
			$slug = getinfo('siteurl') . $options['slug'] . '/' . mso_segment(2) . '/' . mso_segment(3);
			$all_slug = array();
			
			if (isset($_COOKIE[$name_cookies]))
			{
				$all_slug = explode('|', $_COOKIE[$name_cookies]); // значения текущего кука
			}
			
			if (in_array($slug, $all_slug))
			{
				false; // уже есть текущий урл - не увеличиваем счетчик
			}
			else
			{
				// нужно увеличить счетчик
				$all_slug[] = $slug; // добавляем текущий slug
				$all_slug = array_unique($all_slug); // удалим дубли на всякий пожарный
				$all_slug = implode('|', $all_slug); // соединяем обратно в строку
				$expire = time() + $expire;
				
				@setcookie($name_cookies, $all_slug, $expire);
				
				$page_view_count = $onepage['dignity_joke_views'] + 1;
				
				$CI->db->where('dignity_joke_id', $id);
				$CI->db->update('dignity_joke', array('dignity_joke_views'=>$page_view_count));
			}
			
		// если админ
		if (is_login())
		{
			
			# удаление
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_joke_delete')) )
			{
				mso_checkreferer();
				
				if ( !isset($post['f_id'])) $post['f_id'] = $onepage['dignity_joke_id'];
				
				$CI->db->where('dignity_joke_id', $post['f_id']);
				$CI->db->delete('dignity_joke');
				
				mso_flush_cache();
				
				$out .= '<div class="update">' . t('Удалено!', __FILE__) . '<script>location.replace(window.location); </script></div>';
		
			}
			
			# опубликовать
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_joke_approved')) )
			{
				mso_checkreferer();
				
				if ( !isset($post['f_id'])) $post['f_id'] = $onepage['dignity_joke_id'];
				
				$data = array (
				'dignity_joke_approved' => 1,
				);
				
				$CI->db->where('dignity_joke_id', $post['f_id']);
				$CI->db->update('dignity_joke', $data );
				
				mso_flush_cache();
				
				$out .= '<div class="update">' . t('Опубликовано!', __FILE__) . '<script>location.replace(window.location); </script></div>';
		
			}
			
			# черновик
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_joke_no_approved')) )
			{
				mso_checkreferer();
				
				if ( !isset($post['f_id'])) $post['f_id'] = $onepage['dignity_joke_id'];
				
				$no_approved_data = array (
				'dignity_joke_approved' => 0,
				);
				
				$CI->db->where('dignity_joke_id', $post['f_id']);
				$CI->db->update('dignity_joke', $no_approved_data );
				
				mso_flush_cache();
				
				$out .= '<div class="update">' . t('Черновик!', __FILE__) . '<script>location.replace(window.location); </script></div>';
		
			}
			
			# на главную
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_joke_ontop')) )
			{
				mso_checkreferer();
				
				if ( !isset($post['f_id'])) $post['f_id'] = $onepage['dignity_joke_id'];
				
				$data = array (
				'dignity_joke_ontop' => 1,
				);
				
				$CI->db->where('dignity_joke_id', $post['f_id']);
				$CI->db->update('dignity_joke', $data );
				
				mso_flush_cache();
				
				$out .= '<div class="update">' . t('Опубликовано на главной!', __FILE__) . '<script>location.replace(window.location); </script></div>';
		
			}
			
			# убрать с главной
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_joke_drop_from_ontop')) )
			{
				mso_checkreferer();
				
				if ( !isset($post['f_id'])) $post['f_id'] = $onepage['dignity_joke_id'];
				
				$data = array (
				'dignity_joke_ontop' => 0,
				);
				
				$CI->db->where('dignity_joke_id', $post['f_id']);
				$CI->db->update('dignity_joke', $data );
				
				mso_flush_cache();
				
				$out .= '<div class="update">' . t('С главной убрано!', __FILE__) . '<script>location.replace(window.location); </script></div>';
		
			}
		
			$form = '';
			$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
			$form .= '<input type="hidden" name="f_id" value="' . $onepage['dignity_joke_id'] . '" />';
			$form .= '<p class="right">
			<input type="submit" name="f_submit_dignity_joke_ontop" onClick="if(confirm(\'' . t('Опубликовать на главной?', __FILE__) . ' ' . t('Запись №', __FILE__) . $onepage['dignity_joke_id'] . '\')) {return true;} else {return false;}" value="' . t('t', __FILE__) . '">
			<input type="submit" name="f_submit_dignity_joke_drop_from_ontop" onClick="if(confirm(\'' . t('Убрать с главной?', __FILE__) . ' ' . t('Запись №', __FILE__) . $onepage['dignity_joke_id'] . '\')) {return true;} else {return false;}" value="' . t('dt', __FILE__) . '">
			<input type="submit" name="f_submit_dignity_joke_delete" onClick="if(confirm(\'' . t('Удалить?', __FILE__) . ' ' . t('Запись №', __FILE__) . $onepage['dignity_joke_id'] . '\')) {return true;} else {return false;}" value="' . t('x', __FILE__) . '">
			<input type="submit" name="f_submit_dignity_joke_approved" onClick="if(confirm(\'' . t('Опубликовать?', __FILE__) . ' ' . t('Запись №', __FILE__) . $onepage['dignity_joke_id'] . '\')) {return true;} else {return false;}" value="' . t('v', __FILE__) . '">
			<input type="submit" name="f_submit_dignity_joke_no_approved" onClick="if(confirm(\'' . t('Черновик?', __FILE__) . ' ' . t('Запись №', __FILE__) . $onepage['dignity_joke_id'] . '\')) {return true;} else {return false;}" value="' . t('?', __FILE__) . '">
			</p>';
			$form .= '</form>';
			
			$out .= $form;
		}
		
		joke_menu();
			
		$out .= '<div class="page_only">';
		
		$out .= '<div class="info info-top">';
		
		$out .= '<h1><a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_joke_id'] . '">' . '#' . $onepage['dignity_joke_id'] . '</a></h2>';
		$out .= '</div>';
		
		$out .= '<p>' . joke_cleantext($onepage['dignity_joke_cuttext']) . '</p>';
		$out .= '<p>' . joke_cleantext($onepage['dignity_joke_text']) . '</p>';
		
		$out .= '<div class="info info-bottom">';

		$out .= '<p style="text-align:right;">';

		$out .= mso_date_convert($format = 'd.m.Y, H:i', $onepage['dignity_joke_datecreate']) . ' | ';
		if ($onepage['dignity_joke_category_id'])
		{
			$out .= t('Рубрика:', __FILE__) . ' <a href="' . getinfo('site_url') . $options['slug'] . '/category/' . $onepage['dignity_joke_category_id'] . '">' . $onepage['dignity_joke_category_name'] . '</a>';	
		}
		else
		{
			$out .= t('Рубрика:', __FILE__) . ' <a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Все анекдоты', __FILE__) . '</a>';
		}
		$out .= ' | ' . t('Просмотров: ', __FILE__) . $onepage['dignity_joke_views'];
		
		$out .= '<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>';
		$out .= '<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="icon" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki"></div>';
		
		$out .= '</p>';

		$out .= '</div>';
		$out .= '<div class="break"></div>';
		$out .= '</div><!--div class="page_only"-->';
		
		}
		
		// выводим всё
		echo $out;
		
		// meta-тэги
		mso_head_meta('title', $onepage['dignity_joke_cuttext']);
		
		#############################################################
		
		# Комментарии
		
		// готовим пагинацию
		$pag = array();
		$pag['limit'] = 10;
		$CI->db->select('dignity_joke_comments_id');
		$CI->db->from('dignity_joke_comments');
		$CI->db->where('dignity_joke_comments_approved', '1');
		$CI->db->where('dignity_joke_comments_thema_id', $id);
		$query = $CI->db->get();
		$pag_row = $query->num_rows();

		if ($pag_row > 0)
		{
			$pag['maxcount'] = ceil($pag_row / $pag['limit']);

			$current_paged = mso_current_paged();
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

			$offset = $current_paged * $pag['limit'] - $pag['limit'];
		}
		else
		{
			$pag = false;
		}

		$CI->db->from('dignity_joke_comments');
		$CI->db->where('dignity_joke_comments_approved', 1);
		$CI->db->where('dignity_joke_comments_thema_id', $id);
		$CI->db->order_by('dignity_joke_comments_datecreate', 'asc');
		$CI->db->join('comusers', 'comusers.comusers_id = dignity_joke_comments.dignity_joke_comments_comuser_id', 'left');
		if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
		else $CI->db->limit($pag['limit']);
		$query = $CI->db->get();

		// если есть что выводить...
		if ($query->num_rows() > 0)	
		{
			$allcomments = $query->result_array();
	
			// обьявляем переменую
			$comments_out = '';
			
			$comments_out .= '<div class="leave_a_comment">Комментарии:</div>';
			
			$comments_out .= '<ol>';
	
			// цикл
			foreach ($allcomments as $onecomment) 
			{
				
				// если админ
				if (is_login())
				{
			
					# удаление
					if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_joke_comments_delete')) )
					{
						mso_checkreferer();
				
						if ( !isset($post['f_id'])) $post['f_id'] = $onecomment['dignity_joke_comments_id'];
				
						$CI->db->where('dignity_joke_comments_id', $post['f_comments_id']);
						$CI->db->delete('dignity_joke_comments');
					
						mso_flush_cache();
				
						$comments_out .= '<div class="update">' . t('Удалено!', __FILE__) . '<script>location.replace(window.location); </script></div>';
		
					}
		
					$form = '';
					$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
					$form .= '<input type="hidden" name="f_comments_id" value="' . $onecomment['dignity_joke_comments_id'] . '" />';
					$form .= '<p class="right"><input type="submit" name="f_submit_dignity_joke_comments_delete" onClick="if(confirm(\'' . t('Удалить?', __FILE__) . ' ' . t('Комментарий №', __FILE__) . $onecomment['dignity_joke_comments_id'] . '\')) {return true;} else {return false;}" value="' . t('x', __FILE__) . '"></p>';
					$form .= '</form>';
			
					$comments_out .= $form;
				}
				
				$comments_out .= '<div class="type type_page_comments">
				<div class="comments">
				<li style="clear: both" class="users">
					<div class="comment-info">
					<span class="date">' .
					'Комментарий от ' . $onecomment['comuser_nik'] . ' в ' . mso_date_convert($format = 'H:i → d.m.Y', $onecomment['dignity_joke_comments_datecreate']) . '</span></div>
					<div class="comments_content"><p>' . $onecomment['dignity_joke_comments_text'] . '</p></div>
				</li></div>
				<div class="break"></div>
				</div><!-- class="type type_page_comments" -->';
		
			}
			
			$comments_out .= '</ol>';
			
			
			echo $comments_out;
	
			// добавляем пагинацию
			mso_hook('pagination', $pag);
		}
		else
		{
			echo '<p>' . t('Нет комментариев. Ваш будет первым!', __FILE__) . '</p>';
			
			if (!is_login_comuser())
			{
				echo '<a href="' . getinfo('site_url') . 'login' . '">' . t('Войти', __FILE__) . '</a>';
				echo ' | ';
				echo '<a href="' . getinfo('site_url') . 'register' . '">' . t('Регистрация', __FILE__) . '</a>';
			}
		}
		
		 // если комюзер
		if (is_login_comuser() && $onepage['dignity_joke_comments'])
		 {
            
			// если пост
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_joke_comments_add')) )
			{
				// id == 3 сегмент
				$id = mso_segment(3);
                        
				// проверяем реферала
				mso_checkreferer();
                        
				// готовим массив
				$ins_data = array (
				        'dignity_joke_comments_text' => htmlspecialchars($post['f_dignity_joke_comments_text']),
				        'dignity_joke_comments_datecreate' => date('Y-m-d H:i:s'),
				        'dignity_joke_comments_dateupdate' => date('Y-m-d H:i:s'),
				        'dignity_joke_comments_thema_id' => $id,
				        'dignity_joke_comments_approved' => 1,
				        'dignity_joke_comments_comuser_id' => getinfo('comusers_id'),
				);
                        
				// результат...
				$res = ($CI->db->insert('dignity_joke_comments', $ins_data)) ? '1' : '0';
                        
				if ($res)
				{
				         // всё окей
					echo '<div class="update">' . t('Комментарий добавлен!', __FILE__) . '</div>';
					echo '<script>location.replace(window.location); </script>';
				}
				// если ошибка
				else echo '<div class="error">' . t('Ошибка добавления в базу данных...', __FILE__) . '</div>';
		
				 // Сбрасываем кеш
				mso_flush_cache();
                        
			 }
			else
			{
			        // начало формы  
			        $form = '';     
				$form .= '<h2>' . t('Оставьте комментарий!', __FILE__) . '</h2>';     
				$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
				
				$form .= '<p><strong>' . t('Текст (можно использовать bb-code):', __FILE__) . '<span style="color:red;">*</span></strong><br><textarea name="f_dignity_joke_comments_text" class="markItUp"
					cols="80" rows="10" value="" required="required" style="margin-top: 2px; margin-bottom: 2px; "></textarea>';
			
				// конец формы
				$form .= '<p><input type="submit" class="submit" name="f_submit_dignity_joke_comments_add" value="' . t('Отправить', __FILE__) . '"></p>';
				$form .= '</form>';
                        
				// выводим форму
				echo $form;
			}
		}
		
	}
	else
		// если запись не найдена
		echo '<p>' . t('Анекдот не найдена.', __FILE__) . '<br>' . '<a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Перейти назад в блоги»', __FILE__) . '</a>' . '</p>';
}
else{
	// если запись не найдена
	echo '<p>' . t('Анекдот не найдена.', __FILE__) . '<br>' . '<a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Перейти назад в блоги»', __FILE__) . '</a>' . '</p>';
}

require(getinfo('template_dir') . 'main-end.php');

// конец файла
