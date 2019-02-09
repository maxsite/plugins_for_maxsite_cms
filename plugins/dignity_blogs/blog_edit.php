<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_blogs (github)
 * License GNU GPL 2+
 */


// начало шаблона 
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// получаем доступ к CI
$CI = & get_instance();

// выводим меню
blogs_menu();

$id = mso_segment(3);
if (!is_numeric($id)) $id = false;
else $id = (int) $id;

if ( !isset($post['f_dignity_blogs_title'])) $post['f_dignity_blogs_title'] = '';
if ( !isset($post['f_dignity_blogs_cuttext'])) $post['f_dignity_blogs_cuttext'] = '';
if ( !isset($post['f_dignity_blogs_text'])) $post['f_dignity_blogs_text'] = '';
if ( !isset($post['f_dignity_blogs_category'])) $post['f_dignity_blogs_category'] = 0;

// если число и вошел комюзер
if ($id && is_login_comuser())
{
	$CI->db->from('dignity_blogs');
	$CI->db->where('dignity_blogs_id', $id);
	$q = $CI->db->get();
	$article_comuser_id = '';
	foreach ($q->result_array() as $rw)
	{
		$article_comuser_id = $rw['dignity_blogs_comuser_id'];
	}
	
	if (getinfo('comusers_id') != $article_comuser_id){
		echo t('Вы не можере редактировать чужие записи.', __FILE__);
	}
	else
	{
		
		# удаление
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_blogs_delete')) )
		{
			mso_checkreferer();
			
			$CI->db->where('dignity_blogs_id', $id);
			$CI->db->delete('dignity_blogs');
			
			mso_flush_cache();
			
			echo '<div class="update">' . t('Удалено!', __FILE__) . '<p><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Назад в блоги', __FILE__) . '</a>' . '</p></div>';
		}
		
		// если был пост
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_blogs')) )
		{
			mso_checkreferer();
	
			$data = array (
				'dignity_blogs_title' => htmlspecialchars(mso_xss_clean($post['f_dignity_blogs_title'])),
				'dignity_blogs_cuttext' => htmlspecialchars(mso_xss_clean($post['f_dignity_blogs_cuttext'])),
				'dignity_blogs_text' => htmlspecialchars(mso_xss_clean($post['f_dignity_blogs_text'])),
				'dignity_blogs_dateupdate' => date('Y-m-d H:i:s'),
				'dignity_blogs_approved' => isset($post['f_dignity_blogs_approved']) ? 1 : 0,
				'dignity_blogs_comments' => isset($post['f_dignity_blogs_comments']) ? 1 : 0,
				'dignity_blogs_rss' => 1,
				'dignity_blogs_category' => $post['f_dignity_blogs_category'],
				);
			
			$CI->db->where('dignity_blogs_id', $id);
			if ($CI->db->update('dignity_blogs', $data ) )
			{
				echo '<div class="update">' . t('Обновлено!', __FILE__);
				echo '<p><a href="' . getinfo('site_url') . $options['slug'] . '/view/' . mso_segment(3) . '">' . t('Назад к записи', __FILE__) . '</a>' . '</p>';
				echo '<p><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Назад к ленте записей', __FILE__) . '</a>' . '</p>';
				echo '</div>';
			}
			else 
				echo '<div class="error">' . t('Ошибка обновления', __FILE__) . '</div>';
	
			mso_flush_cache();
		}
		else
		{
			$CI->db->from('dignity_blogs');
			$CI->db->where('dignity_blogs_id', $id);
			$query = $CI->db->get();
			
			if ($query->num_rows() > 0)	
			{	
				$articles = $query->result_array();
				
				// начало формы
				$form = '';
				$form .= '<h1>' . t('Редактировать запись', __FILE__) . '</h1>';
				$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
				
				foreach ($articles as $article) 
				{
					dignity_blogs_editor();
					
					$form .= '
					<script>
					$(document).ready(function(){	
						$("#blogs_title").charCount({
						allowed: 70,		
						warning: 20,
						counterText: "<br>" + "Осталось: "	
						});
			
						$("#blogs_anonce").charCount({
						allowed: 1000,		
						warning: 20,
						counterText: "Осталось: "	
						});
			
						$("#blogs_text").charCount({
						allowed: 30000,		
						warning: 20,
						counterText: "Осталось: "	
						});
					});
					</script>';
					
					$form .= '<p><b>' . t('Заголовок:', __FILE__) . '</b><br>
						<input name="f_dignity_blogs_title" type="text" value="' . $article['dignity_blogs_title'] . '"
						maxlength="70" style="width:90%" required="required" id="blogs_title"></p>';
						
					$form .= '<p style="color:#b2b2b3;">' . t('Заголовок должен быть наполнен смыслом, чтобы можно было понять, о чем будет запись.', __FILE__) . '</p>';
					
					$form .= '<p><b>' . t('Анонс:', __FILE__) . '</b><br>
						<textarea name="f_dignity_blogs_cuttext" class="markItUp" style="width:100%"
						cols="30" rows="5" required="required" maxlength="1000" id="blogs_anonce">' . $article['dignity_blogs_cuttext'] . '</textarea></p>';
						
					$form .= '<p><b>' . t('Текст:', __FILE__) . '</b><br>
						<textarea name="f_dignity_blogs_text" class="markItUp" style="width:100%"
						cols="30" rows="10" maxlength="30000" id="blogs_text">' . $article['dignity_blogs_text'] . '</textarea></p>';
						
					// опубликовать?	
					$chckout = ''; 
					if (!isset($article['dignity_blogs_approved']))  $article['dignity_blogs_approved'] = true;
					if ( (bool)$article['dignity_blogs_approved'] )
					{
						$chckout = 'checked="true"';
					}    
					$form .= '<p>' . t('Опубликовать статью?', 'plugins') . ' <input name="f_dignity_blogs_approved" type="checkbox" ' . $chckout . '></p>';
					$form .= '<p style="color:#b2b2b3;">' . t('Если убрать эту галочку, то запись будет сохранена как черновик.', __FILE__) . '</p>';
		
					// разрешить комментарии?	
					$chckout = ''; 
					if (!isset($article['dignity_blogs_comments']))  $article['dignity_blogs_comments'] = true;
					if ( (bool)$article['dignity_blogs_comments'] )
					{
						$chckout = 'checked="true"';
					}    
					$form .= '<p>' . t('Разрешить комментирование?', 'plugins') . ' <input name="f_dignity_blogs_comments" type="checkbox" ' . $chckout . '></p>';
					$form .= '<p style="color:#b2b2b3;">' . t('Если убрать эту галочку, то нельзя будет оставлять комментарии к записи.', __FILE__) . '</p>';
				
					$CI->load->helper('form');
					$CI->db->from('dignity_blogs_category');
					$q = $CI->db->get();
					$category_list = array();
					$category_list[] = 'Не задан.';
					foreach ($q->result_array() as $rw)
					{
						$category_list[$rw['dignity_blogs_category_id']] = $rw['dignity_blogs_category_name'];
					}
	
					if ( !isset($article['f_dignity_blogs_category'])) $article['f_dignity_blogs_category'] = 0;
					$form .= '<p>' . 'Категория: ' .
					form_dropdown('f_dignity_blogs_category', $category_list, set_value($article['dignity_blogs_category'],
					(isset($article['dignity_blogs_category'])) ? $article['dignity_blogs_category'] : '')) . '</p>';
					$form .= '<p style="color:#b2b2b3;">' . t('Выберите более подходяшую категорию для вашей записи.', __FILE__) . '</p>';
					
				}
				
				// конец формы
				$form .= '<p><input type="submit" name="f_submit_dignity_blogs" value="' . t('Сохранить', __FILE__) . '" style="margin: 10px 0;"> ';
				$form .= '<input type="submit" name="f_submit_dignity_blogs_delete" onClick="if(confirm(\'' . t('Удалить?', __FILE__) . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '"></p>';
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
