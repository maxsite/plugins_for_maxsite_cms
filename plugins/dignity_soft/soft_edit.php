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
if ( !isset($post['f_dignity_soft_title'])) $post['f_dignity_soft_title'] = '';
if ( !isset($post['f_dignity_soft_cuttext'])) $post['f_dignity_soft_cuttext'] = '';
if ( !isset($post['f_dignity_soft_text'])) $post['f_dignity_soft_text'] = '';
if ( !isset($post['f_dignity_soft_category'])) $post['f_dignity_soft_category'] = 0;

if ($id && is_login_comuser())
{
	
	soft_menu();

	$CI->db->from('dignity_soft');
	$CI->db->where('dignity_soft_id', $id);
	$q = $CI->db->get();
	$article_comuser_id = '';
	foreach ($q->result_array() as $rw)
	{
		$article_comuser_id = $rw['dignity_soft_comuser_id'];
	}
	
	if (getinfo('comusers_id') != $article_comuser_id){
		echo t('Вы не можере редактировать чужие записи.', __FILE__);
	}
	else{
		echo '<h1>' . t('Редактировать', __FILE__) . '</h1>';
		
		# удаление
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_soft_delete')) )
		{
			mso_checkreferer();
			
			$CI->db->where('dignity_soft_id', $id);
			$CI->db->delete('dignity_soft');
			
			mso_flush_cache();
			
			echo '<div class="update">' . t('Удалено!', __FILE__) . '<p><a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Назад в блоги', __FILE__) . '</a>' . '</p></div>';
		}
		
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_soft')) )
		{
			mso_checkreferer();
	
			$data = array (
				'dignity_soft_title' => htmlspecialchars($post['f_dignity_soft_title']),
				'dignity_soft_cuttext' => htmlspecialchars($post['f_dignity_soft_cuttext']),
				'dignity_soft_text' => htmlspecialchars($post['f_dignity_soft_text']),
				'dignity_soft_weblink' => htmlspecialchars($post['f_dignity_soft_weblink']),
				'dignity_soft_category' => htmlspecialchars($post['f_dignity_soft_category']),
				'dignity_soft_dateupdate' => date('Y-m-d H:i:s'),
				'dignity_soft_approved' => isset($post['f_dignity_soft_approved']) ? 1 : 0,
				'dignity_soft_comments' => isset($post['f_dignity_soft_comments']) ? 1 : 0,
				'dignity_soft_rss' => 1,
				'dignity_soft_os' => htmlspecialchars($post['f_dignity_soft_os']),
				'dignity_soft_license' => $post['f_dignity_soft_license'],
				);
			
			$CI->db->where('dignity_soft_id', $id);
			if ($CI->db->update('dignity_soft', $data ) )
				echo '<div class="update">' . t('Обновлено! После проверки, ваше приложения будет опубликовано.', __FILE__) .
				'<p>' .
				'<a href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '">' . t('Мои приложения', __FILE__) . '</a><br>' .
				'<a href="' . getinfo('site_url') . $options['slug'] . '">' . t('Показать все приложения', __FILE__) . '</a>' .
				'</p>' . '</div>';
			else 
				echo '<div class="error">' . t('Ошибка обновления', __FILE__) . '</div>';
	
			mso_flush_cache();
		}
		else
		{
			// Берём данные из базы и вставляем их в форму
			$CI->db->from('dignity_soft');
			$CI->db->where('dignity_soft_id', $id);
			$query = $CI->db->get();
			
			if ($query->num_rows() > 0)	
			{	
				$articles = $query->result_array();
				
				$form = '';
				$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
				
				foreach ($articles as $article) 
				{
					dignity_soft_editor();
					
					$form .= '
					<script>
					$(document).ready(function(){	
						$("#soft_title").charCount({
						allowed: 70,		
						warning: 20,
						counterText: "<br>" + "Осталось: "	
						});
			
						$("#soft_anonce").charCount({
						allowed: 1000,		
						warning: 20,
						counterText: "Осталось: "	
						});
			
						$("#soft_text").charCount({
						allowed: 10000,		
						warning: 20,
						counterText: "Осталось: "	
						});
						
						$("#soft_weblink").charCount({
						allowed: 70,		
						warning: 20,
						counterText: "<br>" + "Осталось: "	
						});
					});
					</script>';
					
					$form .= '<p><b>' . t('Заголовок', __FILE__) . '</b><br>
						<input name="f_dignity_soft_title" type="text" value="' . $article['dignity_soft_title'] . '"
						maxlength="70" style="width:90%" required="required" id="soft_title"></p>';
					
					$form .= '<p><b>' . t('Краткое описания', __FILE__) . '</b><br>
						<textarea name="f_dignity_soft_cuttext" class="markItUp" style="width:100%"
						cols="30" rows="5" required="required" maxlength="1000" id="soft_anonce">' . $article['dignity_soft_cuttext'] . '</textarea></p>';
						
					$form .= '<p><b>' . t('Полное описания', __FILE__) . '</b><br>
						<textarea name="f_dignity_soft_text" class="markItUp" style="width:100%"
						cols="30" rows="10" maxlength="10000" id="soft_text">' . $article['dignity_soft_text'] . '</textarea></p>';
					
					$form .= '<p><b>' . t('Ссылка на приложения', __FILE__) . '</b><br>
						<input name="f_dignity_soft_weblink" type="text" value="' . $article['dignity_soft_weblink'] . '"
						maxlength="70" style="width:90%" required="required" id="soft_weblink"></p>';
					
					// Лицензия
					$CI->load->helper('form');
					$license_list = array();
					$license_list[0] = 'Freeware';
					$license_list[1] = 'Shareware';
					$license_list[2] = 'Open Source (GNU GPL, MIT, BSD...)';
					$license_list[3] = 'Non-Free';
					$license_list[4] = 'Другая лицензия';
	
					if ( !isset($post['f_dignity_soft_license'])) $post['f_dignity_soft_license'] = 0;
					$form .= '<p>' . 'Лицензия: ' .
					form_dropdown('f_dignity_soft_license', $license_list, set_value($article['dignity_soft_license'],
					(isset($article['dignity_soft_license'])) ? $article['dignity_soft_license'] : '')) . '</p>';
		
					// OS
					$os_list = array();
					$os_list[0] = 'Windows';
					$os_list[1] = 'Linux';
					$os_list[2] = 'Windows, Linux';
	
					if ( !isset($post['f_dignity_soft_os'])) $post['f_dignity_soft_os'] = 0;
					$form .= '<p>' . 'ОС: ' .
					form_dropdown('f_dignity_soft_os', $os_list, set_value($article['dignity_soft_os'],
					(isset($article['dignity_soft_os'])) ? $article['dignity_soft_os'] : '')) . '</p>';
					
					// опубликовать?	
					$chckout = ''; 
					if (!isset($article['dignity_soft_approved']))  $article['dignity_soft_approved'] = true;
					if ( (bool)$article['dignity_soft_approved'] )
					{
						$chckout = 'checked="true"';
					}    
					$form .= '<p>' . t('Опубликовать?', 'plugins') . ' <input name="f_dignity_soft_approved" type="checkbox" ' . $chckout . '></p>';
		
					// разрешить комментарии?	
					$chckout = ''; 
					if (!isset($article['dignity_soft_comments']))  $article['dignity_soft_comments'] = true;
					if ( (bool)$article['dignity_soft_comments'] )
					{
						$chckout = 'checked="true"';
					}    
					$form .= '<p>' . t('Разрешить комментирования?', 'plugins') . ' <input name="f_dignity_soft_comments" type="checkbox" ' . $chckout . '></p>';
				
					$CI->load->helper('form');
					$CI->db->from('dignity_soft_category');
					$q = $CI->db->get();
					$category_list = array();
					$category_list[] = 'Не задан.';
		
					foreach ($q->result_array() as $rw)
					{
						$category_list[$rw['dignity_soft_category_id']] = $rw['dignity_soft_category_name'];
					}
	
					$form .= '<p>' . 'Категория: ' .
					form_dropdown('f_dignity_soft_category', $category_list, set_value($article['dignity_soft_category'],
					(isset($article['dignity_soft_category'])) ? $article['dignity_soft_category'] : '')) . '</p>';
				
				}
				
				$form .= '<input type="submit" name="f_submit_dignity_soft" value="' . t('Сохранить', __FILE__) . '" style="margin: 10px 0;"> ';
				$form .= '<input type="submit" name="f_submit_dignity_soft_delete" onClick="if(confirm(\'' . t('Удалить?', __FILE__) . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '">';
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