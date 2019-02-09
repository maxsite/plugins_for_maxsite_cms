<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * Alexander Schilling
 * (c) http://dignityinside.org
 *
 */

require(getinfo('template_dir') . 'main-start.php');

$CI = & get_instance();

$id = mso_segment(3);
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

// Проверка
if ( !isset($post['f_dignity_joke_cuttext'])) $post['f_dignity_joke_cuttext'] = '';
if ( !isset($post['f_dignity_joke_text'])) $post['f_dignity_joke_text'] = '';
if ( !isset($post['f_dignity_joke_category'])) $post['f_dignity_joke_category'] = 0;

if ($id && is_login_comuser())
{
	
	joke_menu();

	$CI->db->from('dignity_joke');
	$CI->db->where('dignity_joke_id', $id);
	$q = $CI->db->get();
	$article_comuser_id = '';
	foreach ($q->result_array() as $rw)
	{
		$article_comuser_id = $rw['dignity_joke_comuser_id'];
	}
	
	if (getinfo('comusers_id') != $article_comuser_id){
		echo t('Вы не можере редактировать чужие записи.', __FILE__);
	}
	else{
		echo '<h1>' . t('Редактировать', __FILE__) . '</h1>';
		
		# удаление
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_joke_delete')) )
		{
			mso_checkreferer();
			
			$CI->db->where('dignity_joke_id', $id);
			$CI->db->delete('dignity_joke');
			
			mso_flush_cache();
			
			echo '<div class="update">' . t('Удалено!', __FILE__) . '<p><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Назад в блоги', __FILE__) . '</a>' . '</p></div>';
		}
		
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_joke')) )
		{
			mso_checkreferer();
	
			$data = array (
				'dignity_joke_cuttext' => htmlspecialchars($post['f_dignity_joke_cuttext']),
				'dignity_joke_text' => htmlspecialchars($post['f_dignity_joke_text']),
				'dignity_joke_category' => htmlspecialchars($post['f_dignity_joke_category']),
				'dignity_joke_dateupdate' => date('Y-m-d H:i:s'),
				'dignity_joke_approved' => isset($post['f_dignity_joke_approved']) ? 1 : 0,
				'dignity_joke_comments' => isset($post['f_dignity_joke_comments']) ? 1 : 0,
				'dignity_joke_rss' => 1,
				);
			
			$CI->db->where('dignity_joke_id', $id);
			if ($CI->db->update('dignity_joke', $data ) )
				echo '<div class="update">' . t('Обновлено! После проверки, ваш анекдот будет опубликована.', __FILE__) .
				'<p>' .
				'<a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Мои анекдоты', __FILE__) . '</a><br>' .
				'<a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Показать все анекдоты', __FILE__) . '</a>' .
				'</p>' . '</div>';
			else 
				echo '<div class="error">' . t('Ошибка обновления', __FILE__) . '</div>';
	
			mso_flush_cache();
		}
		else
		{
			// Берём данные из базы и вставляем их в форму
			$CI->db->from('dignity_joke');
			$CI->db->where('dignity_joke_id', $id);
			$query = $CI->db->get();
			
			if ($query->num_rows() > 0)	
			{	
				$articles = $query->result_array();
				
				$form = '';
				$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
				
				foreach ($articles as $article) 
				{
					
					$form .= '<p><b>' . t('Текст', __FILE__) . '</b><br>
						<textarea name="f_dignity_joke_cuttext" style="width:100%"
						cols="30" rows="10" required="required">' . $article['dignity_joke_cuttext'] . '</textarea></p>';
						
					$form .= '<p><b>' . t('Полный текст', __FILE__) . '</b><br>
						<textarea name="f_dignity_joke_text" style="width:100%"
						cols="30" rows="10">' . $article['dignity_joke_text'] . '</textarea></p>';
						
					// опубликовать?	
					$chckout = ''; 
					if (!isset($article['dignity_joke_approved']))  $article['dignity_joke_approved'] = true;
					if ( (bool)$article['dignity_joke_approved'] )
					{
						$chckout = 'checked="true"';
					}    
					$form .= '<p>' . t('Опубликовать?', 'plugins') . ' <input name="f_dignity_joke_approved" type="checkbox" ' . $chckout . '></p>';
		
					// разрешить комментарии?	
					$chckout = ''; 
					if (!isset($article['dignity_joke_comments']))  $article['dignity_joke_comments'] = true;
					if ( (bool)$article['dignity_joke_comments'] )
					{
						$chckout = 'checked="true"';
					}    
					$form .= '<p>' . t('Разрешить комментирования?', 'plugins') . ' <input name="f_dignity_joke_comments" type="checkbox" ' . $chckout . '></p>';
				
					$CI->load->helper('form');
					$CI->db->from('dignity_joke_category');
					$q = $CI->db->get();
					$category_list = array();
					$category_list[] = 'Не задан.';
		
					foreach ($q->result_array() as $rw)
					{
						$category_list[$rw['dignity_joke_category_id']] = $rw['dignity_joke_category_name'];
					}
	
					$form .= '<p>' . 'Категория: ' .
					form_dropdown('f_dignity_joke_category', $category_list, set_value($article['dignity_joke_category'],
					(isset($article['dignity_joke_category'])) ? $article['dignity_joke_category'] : '')) . '</p>';
				
				}
				
				$form .= '<input type="submit" name="f_submit_dignity_joke" value="' . t('Сохранить', __FILE__) . '" style="margin: 10px 0;"> ';
				$form .= '<input type="submit" name="f_submit_dignity_joke_delete" onClick="if(confirm(\'' . t('Удалить?', __FILE__) . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '">';
				$form .= '</form>';
				
				echo $form;
			
			}
		}
	}
}
else{
	echo t('Ошибочный номер.', __FILE__);
}

require(getinfo('template_dir') . 'main-end.php');

#end of file