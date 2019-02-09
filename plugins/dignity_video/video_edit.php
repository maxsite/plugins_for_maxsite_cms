<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_video (github)
 * License GNU GPL 2+
 */

// начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// выводим навигацию видео
video_menu();

// получаем доступ к CI
$CI = & get_instance();

// проверяем является ли 3 сегмент цислом
$id = mso_segment(3);
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

// Проверка
if ( !isset($post['f_dignity_video_title'])) $post['f_dignity_video_title'] = '';
if ( !isset($post['f_dignity_video_cuttext'])) $post['f_dignity_video_cuttext'] = '';
if ( !isset($post['f_dignity_video_text'])) $post['f_dignity_video_text'] = '';
if ( !isset($post['f_dignity_video_category'])) $post['f_dignity_video_category'] = 0;

if ($id && is_login_comuser())
{
	// берем данные из базы
	$CI->db->from('dignity_video');
	$CI->db->where('dignity_video_id', $id);
	$q = $CI->db->get();
	$article_comuser_id = '';
	foreach ($q->result_array() as $rw)
	{
		$article_comuser_id = $rw['dignity_video_comuser_id'];
	}
	
	// проверяем автора записи
	if (getinfo('comusers_id') != $article_comuser_id){
		echo t('Вы не можере редактировать чужие записи.', __FILE__);
	}
	else
	{
		echo '<h1>' . t('Редактировать', __FILE__) . '</h1>';
		
		# удаление
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_video_delete')) )
		{
			mso_checkreferer();
			
			$CI->db->where('dignity_video_id', $id);
			$CI->db->delete('dignity_video');
			
			mso_flush_cache();
			
			echo '<div class="update">' . t('Удалено!', __FILE__) . '<p><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Назад в блоги', __FILE__) . '</a>' . '</p></div>';
		}
		
		// обновляем данные
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_video')) )
		{
			// проверяем реферала
			mso_checkreferer();
			
			// готовим массив
			$data = array (
				'dignity_video_title' => htmlspecialchars($post['f_dignity_video_title']),
				'dignity_video_text' => htmlspecialchars($post['f_dignity_video_text']),
				'dignity_video_category' => htmlspecialchars($post['f_dignity_video_category']),
				'dignity_video_dateupdate' => date('Y-m-d H:i:s'),
				'dignity_video_approved' => isset($post['f_dignity_video_approved']) ? 1 : 0,
				'dignity_video_comments' => isset($post['f_dignity_video_comments']) ? 1 : 0,
				'dignity_video_rss' => 1,
				);
			
			$CI->db->where('dignity_video_id', $id);

			if ($CI->db->update('dignity_video', $data ) )
				echo '<div class="update">' . t('Обновлено! После проверки, ваше видео будет опубликовано.', __FILE__) .
				'<p>' .
				'<a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Мои видео', __FILE__) . '</a><br>' .
				'<a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Показать все видео', __FILE__) . '</a>' .
				'</p>' . '</div>';
			else 
				echo '<div class="error">' . t('Ошибка обновления', __FILE__) . '</div>';
	
			mso_flush_cache();

		}
		else
		{

			// берём данные из базы и вставляем их в форму
			$CI->db->from('dignity_video');
			$CI->db->where('dignity_video_id', $id);
			$query = $CI->db->get();
			
			if ($query->num_rows() > 0)	
			{	
				$articles = $query->result_array();
				
				$form = '';
				$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
				
				foreach ($articles as $article) 
				{
					$CI->load->helper('form');
					$CI->db->from('dignity_video_category');
					$q = $CI->db->get();
					$category_list = array();
					$category_list[] = 'Не задан.';
		
					foreach ($q->result_array() as $rw)
					{
						$category_list[$rw['dignity_video_category_id']] = $rw['dignity_video_category_name'];
					}
	
					$form .= '<p><strong>' . 'Категория: ' . '</strong><br />' .
					form_dropdown('f_dignity_video_category', $category_list, set_value($article['dignity_video_category'],
					(isset($article['dignity_video_category'])) ? $article['dignity_video_category'] : '')) . '</p>';

					dignity_video_editor();
					
					$form .= '<p><b>' . t('Заголовок', __FILE__) . '</b><br>
						<input name="f_dignity_video_title" type="text" value="' . $article['dignity_video_title'] . '"
						maxlength="70" style="width:90%" required="required"></p>';
						
					$form .= '<p><b>' . t('Текст и код видео', __FILE__) . '</b><br>
						<textarea name="f_dignity_video_text" class="markItUp" style="width:100%"
						cols="90" rows="10">' . $article['dignity_video_text'] . '</textarea></p>';
						
					// опубликовать?	
					$chckout = ''; 
					if (!isset($article['dignity_video_approved']))  $article['dignity_video_approved'] = true;
					if ( (bool)$article['dignity_video_approved'] )
					{
						$chckout = 'checked="true"';
					}    
					$form .= '<p>' . t('Опубликовать?', 'plugins') . ' <input name="f_dignity_video_approved" type="checkbox" ' . $chckout . '></p>';
		
					// разрешить комментарии?	
					$chckout = ''; 
					if (!isset($article['dignity_video_comments']))  $article['dignity_video_comments'] = true;
					if ( (bool)$article['dignity_video_comments'] )
					{
						$chckout = 'checked="true"';
					}    
					$form .= '<p>' . t('Разрешить комментирования?', 'plugins') . ' <input name="f_dignity_video_comments" type="checkbox" ' . $chckout . '></p>';
				
				}
				
				$form .= '<input type="submit" name="f_submit_dignity_video" value="' . t('Сохранить', __FILE__) . '" style="margin: 10px 0;"> ';
				$form .= '<input type="submit" name="f_submit_dignity_video_delete" onClick="if(confirm(\'' . t('Удалить?', __FILE__) . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '">';
				$form .= '</form>';
				
				echo $form;
			
			}
		}
	}
}
else
{
	echo t('Ошибочный номер.', __FILE__);
}

// конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file
