<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_blogs (github)
 * License GNU GPL 2+
 */

// начало шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

// получаем доступ к CI
$CI = & get_instance();

// загружаем опции
$options = mso_get_option('plugin_dignity_blogs', 'plugins', array());
if (!isset($options['noapproved']))  $options['noapproved'] = true;
if (!isset($options['slug']))  $options['slug'] = 'blogs';
if (!isset($options['cackle_code']) ) $options['cackle_code'] = '';
if (!isset($options['no_pagination']))  $options['no_pagination'] = true;
if (!isset($options['no_blog_name']))  $options['no_blog_name'] = true;

require_once(getinfo('plugins_dir') . 'dignity_blogs/core/functions.php');
$blogs = new Blogs;

// выводим меню
$blogs->menu();

// проверка сегмента
$id = mso_segment(3);
if (!is_numeric($id)) $id = false;
else $id = (int) $id;

// если число
if ($id)
{
	// берём запись из базы
	$CI->db->from('dignity_blogs');
	$CI->db->where('dignity_blogs_id', $id);
	$CI->db->where('dignity_blogs_approved', true);
	$CI->db->join('dignity_blogs_category', 'dignity_blogs_category.dignity_blogs_category_id = dignity_blogs.dignity_blogs_category', 'left');
	$CI->db->join('comusers', 'comusers.comusers_id = dignity_blogs.dignity_blogs_comuser_id', 'left');
	$query = $CI->db->get();

	// если есть что выводить
	if ($query->num_rows() > 0)	
	{	
		$allpages = $query->result_array();
		
		$out = '';
		
		foreach ($allpages as $onepage) 
		{
			
			//<- подсчёт количества просмотров через cookie
			
			global $_COOKIE;
			$name_cookies = 'dignity-blogs';
			$expire = 2592000; // 30 дней
			$slug = getinfo('siteurl') . $options['slug'] . '/' . mso_segment(2) . '/' . mso_segment(3);
			$all_slug = array();
			
			if (isset($_COOKIE[$name_cookies]))
			{
				$all_slug = explode('|', $_COOKIE[$name_cookies]);
			}
			
			if (in_array($slug, $all_slug))
			{
				false;
			}
			else
			{
				$all_slug[] = $slug;
				$all_slug = array_unique($all_slug);
				$all_slug = implode('|', $all_slug);
				$expire = time() + $expire;
				
				@setcookie($name_cookies, $all_slug, $expire);
				$page_view_count = $onepage['dignity_blogs_views'] + 1;
				
				$CI->db->where('dignity_blogs_id', $id);
				$CI->db->update('dignity_blogs', array('dignity_blogs_views'=>$page_view_count));
			}
			
			//-> конец подсчёта количества просмотров через cookie
			
			//<- выводим запись
			
			$out .= '<div class="blogs_page_only">';
		
				$out .= '<div class="blogs_info">';
					$out .= '<h1>';
					$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '">' . $onepage['dignity_blogs_title'] . '</a>';
					$out .= '</h1>';
				$out .= '</div>';
				
			// если вошел автор записи
	       	if ($onepage['dignity_blogs_comuser_id'] == getinfo('comusers_id'))
	       	{
	            // выводим ссылку «редактировать»
	            $out .= '<div class="blogs_info_edit">';
					$out .= '<p>';
					$out .= '<span>';
					$out .= '<img src="' . getinfo('plugins_url') . 'dignity_blogs/img/edit.png' . '" alt="">';
					$out .= '</span>';
					$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '/edit/' . $onepage['dignity_blogs_id'] . '" title="' . t('Редактировать статью', __FILE__) . '">' . t('Редактировать', __FILE__) . '</a>';
					$out .= '</p>';
				$out .= '</div>';
			}
		
			// выводим надпись и ссылку "блог им."
			$out .= '<div class="blogs_info_blog_name">';
				$out .= '<p>';
				$out .= '<span>';
				$out .= '<img src="' . getinfo('plugins_url') . 'dignity_blogs/img/user.png' . '" alt="">';
				$out .= '</span>';

				$hide_no_blog_name = '';
				if ($options['no_blog_name'])
				{
					$hide_no_blog_name = t('Блог им. ', __FILE__);
				}

				$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '/blog/' . $onepage['dignity_blogs_comuser_id'] . '" title="' . t('Перейти на блог пользователя', __FILE__) . '">' . $hide_no_blog_name . $onepage['comusers_nik'] . '</a>';
				$out .= '</p>';
			$out .= '</div>';
			
			// выводим анонс статьи
			$out .= '<div class="blogs_info_cuttext">';
				$out .= '<p>' . $blogs->bb_parser($onepage['dignity_blogs_cuttext']) . '</p>';
			$out .= '</div>';

			// выводим весь текст
			$out .= '<div class="blogs_info_text">';
				$out .= '<p>' . $blogs->bb_parser($onepage['dignity_blogs_text']) . '</p>';
			$out .= '</div>';
		
			$out .= '<div class="blogs_info">';

				// количество просмотров
				$out .='<span style="padding-right:5px;">';
				$out .= '<img src="' . getinfo('plugins_url') . 'dignity_blogs/img/views.png' . '" title="' . t('Просмотров', __FILE__) . '">';
				$out .= '</span>';
				$out .= $onepage['dignity_blogs_views'];

				$out .= ' | ';

				// дата
				$out .= '<span style="padding-right:5px;">';
				$out .= '<img src="' . getinfo('plugins_url') . 'dignity_blogs/img/public.png' . '" title="' . t('Дата публикации', __FILE__) . '">';
				$out .= '</span>';
				$out .= mso_date_convert($format = 'd.m.Y → H:i', $onepage['dignity_blogs_datecreate']);

				// выводим категорию
				if ($onepage['dignity_blogs_category_id'])
				{
					$out .= ' | ';
					$out .= '<span style="padding-right:0px;">';
					$out .= '<img src="' . getinfo('plugins_url') . 'dignity_blogs/img/ordner.png' . '" alt="">';
					$out .= '</span>';
					$out .= ' <a href="' . getinfo('site_url') . $options['slug'] . '/category/' . $onepage['dignity_blogs_category_id'] . '">' . $onepage['dignity_blogs_category_name'] . '</a>';
				}
				else
				{
					$out .= ' | ';
					$out .= '<span style="padding-right:0px;">';
					$out .= '<img src="' . getinfo('plugins_url') . 'dignity_blogs/img/ordner.png' . '" alt="">';
					$out .= '</span>';
					$out .= ' <a href="' . getinfo('site_url') . $options['slug'] . '" title="' . t('Все записи', __FILE__) . '">' . t('Все записи', __FILE__) . '</a>';
				}
					
				$out .= $blogs->yandex_share();
				
			$out .= '</div>';
			
			$out .= '<div class="blogs_break"></div>';
			
			$out .= '</div>';
			
			//-> конец вывода записи
		}
		
		// выводим запись
		echo $out;
		
		// meta-тэги
		mso_head_meta('title', $onepage['dignity_blogs_title']);
		mso_head_meta('description', $onepage['dignity_blogs_description']);
		mso_head_meta('keywords', $onepage['dignity_blogs_keywords']);

		//<- выводим комментарии
		
		// готовим пагинацию
		if ($options['no_pagination'])
		{
			$pag = array();
			$pag['limit'] = 10;
		}
		$CI->db->select('dignity_blogs_comments_id');
		$CI->db->from('dignity_blogs_comments');
		$CI->db->where('dignity_blogs_comments_approved', true);
		$CI->db->where('dignity_blogs_comments_thema_id', $id);
		$query = $CI->db->get();
		if ($options['no_pagination'])
		{
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

		}

		// берём комментарии из базы
		$CI->db->from('dignity_blogs_comments');
		$CI->db->where('dignity_blogs_comments_approved', true);
		$CI->db->where('dignity_blogs_comments_thema_id', $id);
		$CI->db->order_by('dignity_blogs_comments_datecreate', 'asc');
		$CI->db->join('comusers', 'comusers.comusers_id = dignity_blogs_comments.dignity_blogs_comments_comuser_id', 'left');
		if ($options['no_pagination'])
		{
			if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
			else $CI->db->limit($pag['limit']);
		}
		$query = $CI->db->get();

		// если есть что выводить...
		if ($query->num_rows() > 0)	
		{
			$allcomments = $query->result_array();
	
			$comments_out = '';
			
			$comments_out .= '<div class="blogs_leave_a_comment">';
				$comments_out .= t('Комментарии через наш сайт:', __FILE__);
			$comments_out .= '</div>';
			
			$comments_out .= '<ol>';
	
			foreach ($allcomments as $onecomment) 
			{
				$avatar = '';
				if ($onecomment['comusers_avatar_url'])
				{
					$avatar = $onecomment['comusers_avatar_url'];
				}
				else
				{
					$avatar = getinfo('plugins_url') . 'dignity_blogs/img/noavatar.jpg';
				}
				
				$comments_out .= '<div class="blogs_comments">';
					$comments_out .= '<li style="clear: both;" class="users">';
						$comments_out .= '<div class="blogs_comment_info">';
							$comments_out .= '<span class="date">';
							$comments_out .= '<img src="' . $avatar . '" height="40px" width="40px" style="padding:3px 15px 3px 0px;">';
							$comments_out .= t('Комментарий от ', __FILE__);
							$comments_out .= '<a href="' . getinfo('site_url') . 'users/' . $onecomment['comusers_id'] . '">' . $onecomment['comusers_nik'] . '</a>';
							$comments_out .= t(' в ', __FILE__);
							$comments_out .= mso_date_convert($format = 'H:i → d.m.Y', $onecomment['dignity_blogs_comments_datecreate']);
							$comments_out .= '</span>';
						$comments_out .= '</div>';
						$comments_out .= '<div class="blogs_comments_content">';
							$comments_out .= '<p>' . $blogs->bb_parser($onecomment['dignity_blogs_comments_text']) . '</p>';
						$comments_out .= '</div>';
					$comments_out .= '</li>';
				$comments_out .= '</div>';

				$comments_out .= '<div class="blogs_break"></div>';
			}
			
			$comments_out .= '</ol>';
			
			// выводим комментарии
			echo $comments_out;

			if ($options['no_pagination'])
			{
	
				// добавляем пагинацию
				mso_hook('pagination', $pag);

			}
		}
		else
		{
			echo '<div class="blogs_leave_a_comment">Комментарии через наш сайт:</div>';
			echo '<p>' . t('Нет комментариев. Ваш будет первым!', __FILE__) . '</p>';
		}
		
		 // если комюзер
		if (is_login_comuser() && $onepage['dignity_blogs_comments'])
		 {
            
			// если пост
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_blogs_comments_add')) )
			{
				// id == 3 сегмент
				$id = mso_segment(3);
                        
				// проверяем реферала
				mso_checkreferer();
				
				// смотрим опции - вкл или откл проверка комментарий
				$no_approved = '';
				if($options['noapproved'])
				{
					$no_approved = 1;
				}
				else
				{
					$no_approved = 0;
				}

				// массивы для добавления в базу данных
				$ins_data = array (
				        'dignity_blogs_comments_text' => htmlspecialchars(mso_xss_clean($post['f_dignity_blogs_comments_text'])),
				        'dignity_blogs_comments_datecreate' => date('Y-m-d H:i:s'),
				        'dignity_blogs_comments_dateupdate' => date('Y-m-d H:i:s'),
				        'dignity_blogs_comments_thema_id' => $id,
				        'dignity_blogs_comments_approved' => $no_approved,
				        'dignity_blogs_comments_comuser_id' => getinfo('comusers_id'),
				);
				
				$res = ($CI->db->insert('dignity_blogs_comments', $ins_data)) ? '1' : '0';
                        
				if ($res)
				{
					echo '<div class="update">';

					echo '<p>' . t('Комментарий добавлен!', __FILE__) . '</p>';
					
					if ($options['noapproved'])
					{
						echo '<script>location.replace(window.location); </script>';
					}
					else
					{
						echo '<p>' . t('После проверки он будет опубликован.', __FILE__) . '</p>';
					}

					echo '</div>';
				}
				else echo '<div class="error"><p>' . t('Ошибка добавления в базу данных...', __FILE__) . '</p></div>';
		
				 // сбрасываем кеш
				mso_flush_cache();
				
			 }
			else
			{
			        $form = '';     
					$form .= '<h2>' . t('Оставьте комментарий!', __FILE__) . '</h2>';     
					$form .= '<form action="" method="post">' . mso_form_session('f_session_id');

					// редактор для комментарий
					$blogs->comments_editor();
					
					$form .= '<p><strong>' . t('Текст (можно использовать bb-code):', __FILE__) . '<span style="color:red;">*</span></strong><br><textarea name="f_dignity_blogs_comments_text" class="markItUp"
						cols="80" rows="10" value="" required="required" style="margin-top: 2px; margin-bottom: 2px; "></textarea>';
					$form .= '<p><input type="submit" class="submit" name="f_submit_dignity_blogs_comments_add" value="' . t('Отправить', __FILE__) . '"></p>';
					$form .= '</form>';
                        
					// выводим форму
					echo $form;
			}

			if ($options['cackle_code'])
			{
					echo '<div class="blogs_leave_a_comment">Комментарии через социальные сети:</div>';
					echo $options['cackle_code'];
			}
		}
		else
		{
			// если не комюзер
			if (!is_login_comuser())
			{
					// предлагаем войти или зарегистироваться на сайте
					$please_login = '';
					$please_login .= '<div class="blogs_please_login">';
				    	$please_login .= '<p>';
				    	$please_login .= t('Чтобы оставить свой комментарий, вам нужно', __FILE__);
				    	$please_login .= ' <a href="' . getinfo('siteurl') . 'registration">' . t('зарегистироваться', __FILE__) . '</a> ';
				    	$please_login .= t('или',__FILE__);
				    	$please_login .= ' <a href="' . getinfo('siteurl') . 'login">' . t('войти на сайт', __FILE__) . '.</a>';
				    	$please_login .= '</p>';
			    	$please_login .= '</div>';
			    	echo $please_login;

			    	// выводим cackle комментарии
					if ($options['cackle_code'])
					{
						echo '<div class="blogs_leave_a_comment">';
						echo t('Комментарии через социальные сети:', __FILE__);
						echo '</div>';

						echo $options['cackle_code'];
					}

			}
			else
			{
				echo '<p>' . t('Запись нельзя комментировать.', __FILE__) . '</p>';
			}
		}
		
	}
	else
	{
		echo '<h1>' . t('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . t('Извините, ничего не найдено') . '</p>';
		echo mso_hook('page_404');
		
	}	
}
else
{
	echo '<h1>' . t('404. Ничего не найдено...') . '</h1>';
	echo '<p>' . t('Извините, ничего не найдено') . '</p>';
	echo mso_hook('page_404');
}

// конец шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);

#end of file
