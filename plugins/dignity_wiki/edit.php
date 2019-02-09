<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * Alexander Schilling
 * (c) http://dignityinside.org
 *
 */

require(getinfo('shared_dir') . 'main/main-start.php');
	  

$CI = & get_instance();

$id = mso_segment(3);
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

// Проверка
if ( !isset($post['f_dignity_wiki_title'])) $post['f_dignity_wiki_title'] = '';
if ( !isset($post['f_dignity_wiki_cuttext'])) $post['f_dignity_wiki_cuttext'] = '';
if ( !isset($post['f_dignity_wiki_text'])) $post['f_dignity_wiki_text'] = '';
if ( !isset($post['f_dignity_wiki_category'])) $post['f_dignity_wiki_category'] = 0;

if ($id && is_login_comuser())
{
	
	wiki_menu();

	$CI->db->from('dignity_wiki');
	$CI->db->where('dignity_wiki_id', $id);
	$q = $CI->db->get();
	$article_comuser_id = '';
	foreach ($q->result_array() as $rw)
	{
		$article_comuser_id = $rw['dignity_wiki_comuser_id'];
	}
	
	if (getinfo('comusers_id') != $article_comuser_id){
		echo t('Вы не можере редактировать чужие записи.', __FILE__);
	}
	else{
		echo '<h1>' . t('Редактировать', __FILE__) . '</h1>';
		
		# удаление
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_wiki_delete')) )
		{
			mso_checkreferer();
			
			$CI->db->where('dignity_wiki_id', $id);
			$CI->db->delete('dignity_wiki');
			
			mso_flush_cache();
			
			echo '<div class="update">' . t('Удалено!', __FILE__) . '<p><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Назад в блоги', __FILE__) . '</a>' . '</p></div>';
		}
		
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_wiki')) )
		{
			mso_checkreferer();
	
			$data = array (
				'dignity_wiki_title' => htmlspecialchars($post['f_dignity_wiki_title']),
				'dignity_wiki_cuttext' => htmlspecialchars($post['f_dignity_wiki_cuttext']),
				'dignity_wiki_text' => htmlspecialchars($post['f_dignity_wiki_text']),
				'dignity_wiki_category' => htmlspecialchars($post['f_dignity_wiki_category']),
				'dignity_wiki_dateupdate' => date('Y-m-d H:i:s'),
				);
			
			$CI->db->where('dignity_wiki_id', $id);
			if ($CI->db->update('dignity_wiki', $data ) )
				echo '<div class="update">' . t('Обновлено! После проверки, ваша статья будет опубликована.', __FILE__) .
				'<p>' .
				'<a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Показать все статьи', __FILE__) . '</a>' .
				'</p>' . '</div>';
			else 
				echo '<div class="error">' . t('Ошибка обновления', __FILE__) . '</div>';
	
			mso_flush_cache();
		}
		else
		{
			// Берём данные из базы и вставляем их в форму
			$CI->db->from('dignity_wiki');
			$CI->db->where('dignity_wiki_id', $id);
			$query = $CI->db->get();
			
			if ($query->num_rows() > 0)	
			{	
				$articles = $query->result_array();
				
				$form = '';
				$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
				
				foreach ($articles as $article) 
				{
					dignity_wiki_editor();
					
					$form .= '<p><b>' . t('Заголовок', __FILE__) . '</b><br>
						<input name="f_dignity_wiki_title" type="text" value="' . $article['dignity_wiki_title'] . '"
						maxlength="70" style="width:90%" required="required"></p>';
					
					$form .= '<p><b>' . t('Краткое описания', __FILE__) . '</b><br>
						<textarea name="f_dignity_wiki_cuttext" class="markItUp" style="width:100%"
						cols="30" rows="5" required="required" maxlength="1000">' . $article['dignity_wiki_cuttext'] . '</textarea></p>';
						
					$form .= '<p><b>' . t('Полное описания', __FILE__) . '</b><br>
						<textarea name="f_dignity_wiki_text" class="markItUp" style="width:100%"
						cols="30" rows="10" maxlength="10000">' . $article['dignity_wiki_text'] . '</textarea></p>';
		
					$CI->load->helper('form');
					$CI->db->from('dignity_wiki_category');
					$q = $CI->db->get();
					$category_list = array();
					$category_list[] = 'Не задан.';
		
					foreach ($q->result_array() as $rw)
					{
						$category_list[$rw['dignity_wiki_category_id']] = $rw['dignity_wiki_category_name'];
					}
	
					$form .= '<p>' . 'Категория: ' .
					form_dropdown('f_dignity_wiki_category', $category_list, set_value($article['dignity_wiki_category'],
					(isset($article['dignity_wiki_category'])) ? $article['dignity_wiki_category'] : '')) . '</p>';
				
				}
				
				$form .= '<input type="submit" name="f_submit_dignity_wiki" value="' . t('Сохранить', __FILE__) . '" style="margin: 10px 0;"> ';
				$form .= '<input type="submit" name="f_submit_dignity_wiki_delete" onClick="if(confirm(\'' . t('Удалить?', __FILE__) . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '">';
				$form .= '</form>';
				
				echo $form;
			
			}
		}
	}
}
else{
	echo t('Ошибочный номер.', __FILE__);
}

require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file