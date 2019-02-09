<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_video (github)
 * License GNU GPL 2+
 */

echo '<h1>' . t('Видео', __FILE__) . '</h1>';
echo '<p class="info">' . t('Редактировать комментарий.', __FILE__) . '</p>';

// админ-меню
echo '<div class="admin-h-menu">';
	$plugin_url = getinfo('site_admin_url') . 'dignity_video';
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройки', __FILE__), 'select') . ' | ';
	$a  .= mso_admin_link_segment_build($plugin_url, 'edit_comments', t('Комментарии', __FILE__), 'select') . ' | ';
	$a  .= mso_admin_link_segment_build($plugin_url, 'edit_video', t('Видео', __FILE__), 'select');
	echo $a;
echo '</div>';

// получаем доступ к CI
$CI = & get_instance();
	
// задаём значения по умолчению
if ( !isset($post['f_dignity_video_comments_text'])) $post['f_dignity_video_comments_text'] = '';

// $id = 4 сегмент
$id = mso_segment(4);

// проверка
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

// если число
if ($id)
{

	// удаление ответа
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_video_comments_delete')) )
	{
		// проверяем реферала
		mso_checkreferer();
		
		// выбираем ответ согласно $id и удаляем его
		$CI->db->where('dignity_video_comments_id', $id);
		$CI->db->delete('dignity_video_comments');
		
		// сбрасиваем кеш
		mso_flush_cache();
		
		// выводим сообщения
		echo '<div class="update">' . t('Удалено!', __FILE__) . '</div>';
		
		return;
	}

	// редактирование
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_video_comments')) )
	{
		// проверяем реферала
		mso_checkreferer();
	
		// готовим массив для добавления в базу
		$data = array (
				'dignity_video_comments_text' => htmlspecialchars($post['f_dignity_video_comments_text']),
				'dignity_video_comments_approved' => isset($post['f_dignity_video_comments_approved']) ? 1 : 0,
				);
		
		// выбираем табилицу согласно $id
		$CI->db->where('dignity_video_comments_id', $id);
		
		// результат
		if ($CI->db->update('dignity_video_comments', $data ) )
			// если всё окей
			echo '<div class="update">' . t('Обновлено!', __FILE__) . '</div>';
		else
			// если ошибка
			echo '<div class="error">' . t('Ошибка обновления', __FILE__) . '</div>';
	
		// сбрасываем кеш
		mso_flush_cache();
	}

	// Выводим данные из базы для формы
	$CI->db->from('dignity_video_comments');
	$CI->db->where('dignity_video_comments_id', $id);
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0)	
	{	
		$all_reply = $query->result_array();
		
		// начало формы
		$form = '';
		$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
		
		foreach ($all_reply as $one_reply) 
		{
			
			$form .= '<p><strong>' . t('Текст (можно использовать bb-code):', __FILE__) . '</strong><span style="color:red;">*</span><br>'
				. '<textarea name="f_dignity_video_comments_text"
				cols="90" rows="20" value="" required="required">' . $one_reply['dignity_video_comments_text'] . '</textarea></p>';
			
			// опубликовано?
			$chckout = ''; 
			if (!isset($one_reply['dignity_video_comments_approved']))  $one_reply['dignity_video_comments_approved'] = false;
			if ( (bool)$one_reply['dignity_video_comments_approved'] )
			{
				$chckout = 'checked="true"';
			} 
			$form .= '<p>' . t('Опубликовать?', __FILE__)
				. ' <input name="f_dignity_video_comments_approved" type="checkbox" ' . $chckout . '></p>';
		}
		
		// конец формы
		$form .= '<input type="submit" name="f_submit_dignity_video_comments" value="' . t('Изменить', 'admin') . '" style="margin: 10px 0;">';
		$form .= ' <input type="submit" name="f_submit_dignity_video_comments_delete" onClick="if(confirm(\'' . t('Удалить комментарий?', __FILE__) . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '">';
		$form .= '</form>';
		
		// выводим форму
		echo $form;
	}
	
}
else{
	// если не число...
	echo t('Такого комментария не существует, либо ошибочный номер.', __FILE__);
}

# конец файла
