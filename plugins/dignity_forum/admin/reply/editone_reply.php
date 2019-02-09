<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// заголовок админки и подсказка
echo '<h1>' . t('Форум', __FILE__) . '</h1>';
echo '<p class="info">' . t('Редактировать ответ', __FILE__) . '</p>';

// загружаем меню
echo '<div class="admin-h-menu">';
require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;
$forum->admin_menu();
echo '</div>';

if ( !isset($options['slug']) ) $options['slug'] = 'forum'; 

echo '<p><a href="' . getinfo('siteurl') . $options['slug'] . '" target="_blank">' . t('Перейти на форум →', __FILE__) . '</a></p>';

// задаём значения по умолчению
if ( !isset($post['f_dignity_forum_reply_text'])) $post['f_dignity_forum_reply_text'] = '';

// получаем доступ к CI
$CI = & get_instance();

// $id = 4 сегмент
$id = mso_segment(4);

// проверка
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

// если число
if ($id) {

	// удаление ответа
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_forum_reply_delete')) )
	{
		// проверяем реферала
		mso_checkreferer();
		
		// выбираем ответ согласно $id и удаляем его
		$CI->db->where('dignity_forum_reply_id', $id);
		$CI->db->delete('dignity_forum_reply');
		
		// сбрасиваем кеш
		mso_flush_cache();
		
		// выводим сообщения
		echo '<div class="update">' . t('Удалено!', __FILE__) . '</div>';
		
		return;
	}

	// редактирование
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_forum_reply')) )
	{
		// проверяем реферала
		mso_checkreferer();
	
		// готовим массив для добавления в базу
		$data = array (
				'dignity_forum_reply_text' => htmlspecialchars($post['f_dignity_forum_reply_text']),
				'dignity_forum_reply_approved' => isset($post['f_dignity_forum_reply_approved']) ? 1 : 0,
				);
		
		// выбираем табилицу согласно $id
		$CI->db->where('dignity_forum_reply_id', $id);
		
		// результат
		if ($CI->db->update('dignity_forum_reply', $data ) )
			// если всё окей
			echo '<div class="update">' . t('Обновлено!', __FILE__) . '</div>';
		else
			// если ошибка
			echo '<div class="error">' . t('Ошибка обновления', __FILE__) . '</div>';
	
		// сбрасываем кеш
		mso_flush_cache();
	}

	// Выводим данные из базы для формы
	$CI->db->from('dignity_forum_reply');
	$CI->db->where('dignity_forum_reply_id', $id);
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
				. '<textarea name="f_dignity_forum_reply_text"
				cols="90" rows="20" value="" required="required">' . $one_reply['dignity_forum_reply_text'] . '</textarea></p>';
			
			// опубликовано?
			$chckout = ''; 
			if (!isset($one_reply['dignity_forum_reply_approved']))  $one_reply['dignity_forum_reply_approved'] = false;
			if ( (bool)$one_reply['dignity_forum_reply_approved'] )
			{
				$chckout = 'checked="true"';
			} 
			$form .= '<p>' . t('Опубликовать?', __FILE__)
				. ' <input name="f_dignity_forum_reply_approved" type="checkbox" ' . $chckout . '></p>';
		}
		
		// конец формы
		$form .= '<input type="submit" name="f_submit_dignity_forum_reply" value="' . t('Сохранить', 'admin') . '" style="margin: 10px 0;">';
		$form .= ' <input type="submit" name="f_submit_dignity_forum_reply_delete" onClick="if(confirm(\'' . t('Удалить ответ?', __FILE__) . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '">';
		$form .= '</form>';
		
		// выводим форму
		echo $form;
	}
	
}
else{
	// если не число...
	echo t('Такого ответа не существует, либо ошибочный номер.', __FILE__);
}

#end of file