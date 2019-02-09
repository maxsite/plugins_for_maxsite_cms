<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

require(getinfo('shared_dir') . 'main/main-start.php');
	  

// получаем доступ к CI
$CI = & get_instance();

soft_menu();

// проверка сегмента
$id = mso_segment(3);
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

if ($id)
{
	// загружаем данные из базы
	$CI->db->from('dignity_soft');
	$CI->db->where('dignity_soft_id', $id);
	$CI->db->join('dignity_soft_category', 'dignity_soft_category.dignity_soft_category_id = dignity_soft.dignity_soft_category', 'left');
	$CI->db->join('comusers', 'comusers.comusers_id = dignity_soft.dignity_soft_comuser_id', 'left');
	if (!is_login())
	{
		$CI->db->where('dignity_soft_approved', true);
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
			$name_cookies = 'dignity-soft';
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
				
				$page_view_count = $onepage['dignity_soft_views'] + 1;
				
				$CI->db->where('dignity_soft_id', $id);
				$CI->db->update('dignity_soft', array('dignity_soft_views'=>$page_view_count));
			}
			
            $out .= '<div class="page_only">';
		
            $out .= '<div class="info info-top">';
            $out .= '<h1><a href="' . $url . '">' . $onepage['dignity_soft_title'] . '</a></h1>';
            $out .= '</div>';
		
		// если вошел автор
		if ($onepage['dignity_soft_comuser_id'] == getinfo('comusers_id')){
			// выводим ссылку «редактировать»
			$out .= '<p><a href="' . getinfo('site_url') . $options['slug'] . '/edit/' . $onepage['dignity_soft_id'] . '">' . t('Редактировать', __FILE__) . '</a></p>';
		}
		
		$out .= '<p>' . soft_cleantext($onepage['dignity_soft_cuttext']) . '</p>';
		$out .= '<p>' . soft_cleantext($onepage['dignity_soft_text']) . '</p>';
		
        // ОС
		$os = '';
		if ($onepage['dignity_soft_os'] == 0)
		{
			$os = 'Windows';
		}
		elseif ($onepage['dignity_soft_os'] == 1)
		{
			$os = 'Linux';
		}
		else
		{
			$os = 'Windows, Linux';
		}
		
		$out .= '<p><strong>' . t('ОС:', __FILE__) . '</strong> ' . $os . '</p>';
		
        // лицензия
		$license = '';
		if ($onepage['dignity_soft_license'] == 0)
		{
			$license = 'Freeware';
		}
		elseif ($onepage['dignity_soft_license'] == 1)
		{
			$license = 'Shareware';
		}
		elseif ($onepage['dignity_soft_license'] == 2)
		{
			$license = 'Open Source (GNU GPL, MIT, BSD...)';
		}
		elseif ($onepage['dignity_soft_license'] == 3)
		{
			$license = 'Non-Free';
		}
		else
		{
			$license = 'Другая лицензия';
		}
		
		$out .= '<p><strong>' . t('Лицензия:', __FILE__) . '</strong> ' . $license . '</p>';
		
		$out .= '<p><strong>' . t('Ссылка на загрузку', __FILE__) . '</strong><br>';
		$out .= '<a href="' . $onepage['dignity_soft_weblink'] . '">' . $onepage['dignity_soft_weblink'] . '</a></p>';
		
		$out .= '<div class="info info-bottom">';
		$out .= $onepage['comusers_nik'] . ', ';
			$out .= mso_date_convert($format = 'd.m.Y, H:i', $onepage['dignity_soft_datecreate']) . ' | ';
			$out .= t('Рубрика:', __FILE__) . ' <a href="' . getinfo('site_url') . $options['slug'] . '/category/' . $onepage['dignity_soft_category_id'] . '">' . $onepage['dignity_soft_category_name'] . '</a> | ';
		
		$out .= t('Просмотров: ', __FILE__) . $onepage['dignity_soft_views'];
		
		$out .= '<script type="text/javascript" src="//yandex.st/share/share.js" charset="utf-8"></script>';
		$out .= '<div class="yashare-auto-init" data-yashareL10n="ru" data-yashareType="icon" data-yashareQuickServices="yaru,vkontakte,facebook,twitter,odnoklassniki"></div>';

		$out .= '</div>';
		$out .= '<div class="break"></div>';
		$out .= '</div><!--div class="page_only"-->';
		
		}
        
		// выводим всё
		echo $out;
		
		// meta-тэги
		mso_head_meta('title', $onepage['dignity_soft_title']);
		mso_head_meta('description', $onepage['dignity_soft_keywords']);
		mso_head_meta('keywords', $onepage['dignity_soft_description']);
		
		#############################################################
		
		# Комментарии
		
		// готовим пагинацию
		$pag = array();
		$pag['limit'] = 10;
		$CI->db->select('dignity_soft_comments_id');
		$CI->db->from('dignity_soft_comments');
		$CI->db->where('dignity_soft_comments_approved', true);
		$CI->db->where('dignity_soft_comments_thema_id', $id);
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

		$CI->db->from('dignity_soft_comments');
		$CI->db->where('dignity_soft_comments_approved', true);
		$CI->db->where('dignity_soft_comments_thema_id', $id);
		$CI->db->order_by('dignity_soft_comments_datecreate', 'asc');
		$CI->db->join('comusers', 'comusers.comusers_id = dignity_soft_comments.dignity_soft_comments_comuser_id', 'left');
		if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
		else $CI->db->limit($pag['limit']);
		$query = $CI->db->get();

		// если есть что выводить...
		if ($query->num_rows() > 0)	
		{
			$allcomments = $query->result_array();
	
			// обьявляем переменую
			$comments_out = '';
			
			$comments_out .= '<div class="leave_a_comment">' . t('Комментарии:', __FILE__) . '</div>';
			
			$comments_out .= '<ol>';
	
			// цикл
			foreach ($allcomments as $onecomment) 
			{
				
				// если админ
				if (is_login())
				{
			
					# удаление
					if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_soft_comments_delete')) )
					{
						mso_checkreferer();
				
						if ( !isset($post['f_id'])) $post['f_id'] = $onecomment['dignity_soft_comments_id'];
				
						$CI->db->where('dignity_soft_comments_id', $post['f_comments_id']);
						$CI->db->delete('dignity_soft_comments');
					
						mso_flush_cache();
				
						$comments_out .= '<div class="update">' . t('Удалено!', __FILE__) . '<script>location.replace(window.location); </script></div>';
		
					}
		
					$form = '';
					$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
					$form .= '<input type="hidden" name="f_comments_id" value="' . $onecomment['dignity_soft_comments_id'] . '" />';
					$form .= '<p class="right"><input type="submit" name="f_submit_dignity_soft_comments_delete" onClick="if(confirm(\'' . t('Удалить?', __FILE__) . ' ' . t('Комментарий №', __FILE__) . $onecomment['dignity_soft_comments_id'] . '\')) {return true;} else {return false;}" value="' . t('x', __FILE__) . '"></p>';
					$form .= '</form>';
			
					$comments_out .= $form;
				}
				
				$avatar = '';
				if ($onecomment['comusers_avatar_url'])
				{
					$avatar = $onecomment['comusers_avatar_url'];
				}
				else
				{
					$avatar = getinfo('plugins_url') . 'dignity_soft/img/noavatar.jpg';
				}
				
				$comments_out .= '<div class="type type_page_comments">
				<div class="comments">
				<li style="clear: both" class="users">
					<div class="comment-info">
					<span class="date"><img src="' . $avatar . '" height="40px" width="40px" style="padding:3px 15px 3px 0px;">' .
					'Комментарий от ' . $onecomment['comusers_nik'] . ' в ' . mso_date_convert($format = 'H:i → d.m.Y', $onecomment['dignity_soft_comments_datecreate']) . '</span></div>
					<div class="comments_content"><p>' . soft_cleantext($onecomment['dignity_soft_comments_text']) . '</p></div>
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
                echo '<p style="border:solid 1px #DBE0E4; padding:10px; background:#FFFFE1;">';
                echo t('Чтобы оставить свой комментарий, вам нужно', __FILE__);
                echo ' <a href="' . getinfo('site_url') . 'registration' . '">' . t('зарегистироваться', __FILE__) . '</a>';
				echo t(' или ', __FILE__);
				echo '<a href="' . getinfo('site_url') . 'login' . '">' . t('войти', __FILE__) . '</a>.';
                echo '</p>';
			}
		}
		
		 // если комюзер
		if (is_login_comuser() && $onepage['dignity_soft_comments'])
		 {
            
			// если пост
			if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_soft_comments_add')) )
			{
				// id == 3 сегмент
				$id = mso_segment(3);
                        
				// проверяем реферала
				mso_checkreferer();
                        
				// готовим массив
				$ins_data = array (
				        'dignity_soft_comments_text' => htmlspecialchars($post['f_dignity_soft_comments_text']),
				        'dignity_soft_comments_datecreate' => date('Y-m-d H:i:s'),
				        'dignity_soft_comments_dateupdate' => date('Y-m-d H:i:s'),
				        'dignity_soft_comments_thema_id' => $id,
				        'dignity_soft_comments_approved' => 1,
				        'dignity_soft_comments_comuser_id' => getinfo('comusers_id'),
				);
                        
				// результат...
				$res = ($CI->db->insert('dignity_soft_comments', $ins_data)) ? '1' : '0';
                        
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
				
				dignity_soft_editor();
				
				$form .= '
				<script>
					$(document).ready(function(){	
					$("#soft_comment").charCount({
						allowed: 1000,		
						warning: 20,
						counterText: "<br>" + "Осталось: "	
					});
				});
				</script>';
				
				$form .= '<h2>' . t('Оставьте комментарий!', __FILE__) . '</h2>';     
				$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
				
				$form .= '<p><strong>' . t('Текст (можно использовать bb-code):', __FILE__) . '<span style="color:red;">*</span></strong><br><textarea name="f_dignity_soft_comments_text" class="markItUp"
					cols="80" rows="5" maxlength="1000" value="" required="required" id="soft_comment" style="margin-top: 2px; margin-bottom: 2px; "></textarea>';
			
				// конец формы
				$form .= '<p><input type="submit" class="submit" name="f_submit_dignity_soft_comments_add" value="' . t('Отправить', __FILE__) . '"></p>';
				$form .= '</form>';
                        
				// выводим форму
				echo $form;
			}
		}
		
	}
	else
		// если запись не найдена
		echo '<p>' . t('Приложения не найдено.', __FILE__) . '</p>';
}
else{
	// если запись не найдена
	echo '<p>' . t('Приложения не найдено.', __FILE__) . '</p>';
}

require(getinfo('shared_dir') . 'main/main-end.php');
	  

// конец файла