<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_video (github)
 * License GNU GPL 2+
 */

echo '<h1>' . t('Видео', __FILE__) . '</h1>';
echo '<p class="info">' . t('Редактировать видео запись.', __FILE__) . '</p>';

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

// загружаем опции
$options = mso_get_option('plugin_dignity_video', 'plugins', array());
if (!isset($options['slug']))  $options['slug'] = 'video';
	
// задаём значения по умолчению
if ( !isset($post['f_dignity_video_text'])) $post['f_dignity_video_text'] = '';

// $id = 4 сегмент
$id = mso_segment(4);

// проверка
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

// если число
if ($id) {

	// удаление статьи
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_video_delete')) )
	{
		// проверяем реферала
		mso_checkreferer();
		
		// выбираем статью согласно $id и удаляем её
		$CI->db->where('dignity_video_id', $id);
		$CI->db->delete('dignity_video');
		
		// сбрасиваем кеш
		mso_flush_cache();
		
		// выводим сообщения
		echo '<div class="update">' . t('Удалено!', __FILE__) . '</div>';
		
		return;
	}

	// редактирование
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_video_edit')) )
	{
		// проверяем реферала
		mso_checkreferer();
	
		// готовим массив для добавления в базу
		$data = array (
				'dignity_video_text' => htmlspecialchars($post['f_dignity_video_text']),
				'dignity_video_approved' => isset($post['f_dignity_video_approved']) ? 1 : 0,
				'dignity_video_ontop' => isset($post['f_dignity_video_ontop']) ? 1 : 0,
				);
		
		// выбираем табилицу согласно $id
		$CI->db->where('dignity_video_id', $id);
		
		// результат
		if ($CI->db->update('dignity_video', $data ) )
			echo '<div class="update">' . t('Обновлено!', __FILE__) . '</div>';
		else
			echo '<div class="error">' . t('Ошибка обновления', __FILE__) . '</div>';
	
		// сбрасываем кеш
		mso_flush_cache();
	}

	// Выводим данные из базы для формы
	$CI->db->from('dignity_video');
	$CI->db->where('dignity_video_id', $id);
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0)	
	{	
		$all_reply = $query->result_array();
		
		// начало формы
		$form = '';
		$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
		
		foreach ($all_reply as $one_reply) 
		{
			$form .= '<a href="' . getinfo('siteurl') . $options['slug'] . '/view/' . $one_reply['dignity_video_id'] . '" target="blank">' . t('Перейти к видео записи', __FILE__) . '</a>';
			
			$form .= '<p><strong>' . t('Текст (можно использовать bb-code):', __FILE__) . '</strong><br>'
				. '<textarea name="f_dignity_video_text"
				cols="85" rows="20" value="">' . $one_reply['dignity_video_text'] . '</textarea></p>';

			// опубликовано?
			$chckout = ''; 
			if (!isset($one_reply['dignity_video_approved']))  $one_reply['dignity_video_approved'] = false;
			if ( (bool)$one_reply['dignity_video_approved'] )
			{
				$chckout = 'checked="true"';
			} 
			$form .= '<p>' . t('Опубликовать?', __FILE__)
				. ' <input name="f_dignity_video_approved" type="checkbox" ' . $chckout . '></p>';

			// избранное?
			$chckout = ''; 
			if (!isset($one_reply['dignity_video_ontop']))  $one_reply['dignity_video_ontop'] = false;
			if ( (bool)$one_reply['dignity_video_ontop'] )
			{
				$chckout = 'checked="true"';
			} 
			$form .= '<p>' . t('Избранное?', __FILE__)
				. ' <input name="f_dignity_video_ontop" type="checkbox" ' . $chckout . '></p>';
		}
		
		// конец формы
		$form .= '<input type="submit" name="f_submit_dignity_video_edit" value="' . t('Изменить', 'admin') . '" style="margin: 10px 0;">';
		$form .= ' <input type="submit" name="f_submit_dignity_video_delete" onClick="if(confirm(\'' . t('Удалить видео?', __FILE__) . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '">';
		$form .= '</form>';
		
		// выводим форму
		echo $form;
	}
	
}
else
{
	// если не число...
	echo t('Такой видео записи не существует, либо ошибочный номер.', __FILE__);
}

# конец файла
