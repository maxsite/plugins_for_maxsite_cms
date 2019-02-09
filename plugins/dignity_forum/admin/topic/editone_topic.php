<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// заголовок админки и подсказка
echo '<h1>' . t('Форум', __FILE__) . '</h1>';
echo '<p class="info">' . t('Редактировать тему', __FILE__) . '</p>';

// загружаем меню
echo '<div class="admin-h-menu">';
require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;
$forum->admin_menu();
echo '</div>';

// задаём значения по умолчанию
if ( !isset($post['f_dignity_forum_topic_subject'])) $post['f_dignity_forum_category_subject'] = '';
if ( !isset($post['f_dignity_forum_topic_text'])) $post['f_dignity_forum_category_text'] = '';
if ( !isset($post['f_dignity_forum_category'])) $post['f_dignity_forum_category'] = 0;

// получаем доступ к CI
$CI = & get_instance();

// $id = 4 сегмент
$id = mso_segment(4);

// проверка
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

if ( !isset($options['slug']) ) $options['slug'] = 'forum'; 

echo '<p><a href="' . getinfo('siteurl') . $options['slug'] . '/topic/' . $id . '" target="_blank">' . t('Перейти на форум (к теме) →', __FILE__) . '</a></p>';

// если число
if ($id) {

	// удаление темы, если был пост
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_forum_topic_delete')) )
	{
		// проверяем реферала
		mso_checkreferer();
		
		// выбираем тему согласно $id и удаляем её
		$CI->db->where('dignity_forum_topic_id', $id);
		$CI->db->delete('dignity_forum_topic');
                
                // выбираем ответы согласно $id темы и удаляем их
                $CI->db->where('dignity_forum_reply_topic_id', $id);
		$CI->db->delete('dignity_forum_reply');
		
		// сбрасиываем кеш
		mso_flush_cache();
		
		// выводим сообщения
		echo '<div class="update">' . t('Удалено!', __FILE__) . '</div>';
		
		return;
	}

	// редактирование, если был пост
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_forum_topic')) )
	{
		// проверяем реферала
		mso_checkreferer();
	
		// формируем массив для добавления в базу
		$data = array (
				'dignity_forum_topic_subject' => $post['f_dignity_forum_topic_subject'],
				'dignity_forum_topic_text' => $post['f_dignity_forum_topic_text'],
				'dignity_forum_topic_approved' => isset($post['f_dignity_forum_topic_approved']) ? 1 : 0,
				'dignity_forum_topic_closed' => isset($post['f_dignity_forum_topic_closed']) ? 1 : 0,
				'dignity_forum_topic_category' => $post['f_dignity_forum_topic_category'],
                'dignity_forum_topic_ontop' => isset($post['f_dignity_forum_topic_ontop']) ? 1 : 0,
				);
		
		// выбираем таблицу согласно $id
		$CI->db->where('dignity_forum_topic_id', $id);
		
		// добавляем в базу
		if ($CI->db->update('dignity_forum_topic', $data ) )
			// если всё окей
			echo '<div class="update">' . t('Обновлено!', __FILE__) . '</div>';
		else
			// если ошибка
			echo '<div class="error">' . t('Ошибка обновления', __FILE__) . '</div>';
		
	}

	// выводим данные из базы для формы
	$CI->db->from('dignity_forum_topic');
	$CI->db->where('dignity_forum_topic_id', $id);
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0)	
	{	
		$all_topics = $query->result_array();
		
		// начало формы
		$form = '';
		$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
		
		foreach ($all_topics as $one_topic) 
		{
			$form .= '<p><b>' . t('Тема:', __FILE__) . '</b><span style="color:red;">*</span><br>
				<input name="f_dignity_forum_topic_subject" type="text"
				value="' . $one_topic['dignity_forum_topic_subject'] . '" style="width:50%" required="required"></p>';
			
			$form .= '<p><strong>' . t('Текст (можно использовать bb-code):', __FILE__) . '</strong><span style="color:red;">*</span><br>'
				. '<textarea name="f_dignity_forum_topic_text"
				cols="90" rows="20" value="" required="required">' . $one_topic['dignity_forum_topic_text'] . '</textarea></p>';
			
			// выбор категории из списка
			$CI->load->helper('form');
			$CI->db->from('dignity_forum_category');
			$q = $CI->db->get();
	
			$one_topic_list = array();
			$one_topic_list[] = t('Не задан.', __FILE__);
		
			foreach ($q->result_array() as $rw)
			{
				$one_topic_list[$rw['dignity_forum_category_id']] = $rw['dignity_forum_category_name'];
			}
	
			$form .= '<p>' . t('Категория: ', __FILE__) .
				form_dropdown('f_dignity_forum_topic_category', $one_topic_list, set_value($one_topic['dignity_forum_topic_category'],
				(isset($one_topic['dignity_forum_topic_category'])) ? $one_topic['dignity_forum_topic_category'] : '')) . '</p>';
			}
			
			// опубликовано?
			$chckout = ''; 
			if (!isset($one_topic['dignity_forum_topic_approved']))  $one_topic['dignity_forum_topic_approved'] = false;
			if ( (bool)$one_topic['dignity_forum_topic_approved'] )
			{
				$chckout = 'checked="true"';
			} 
			$form .= '<p>' . t('Опубликовать?', __FILE__) . ' <input name="f_dignity_forum_topic_approved" type="checkbox" ' . $chckout . '></p>';
			
			// заблокировать тему?
			$chckout = ''; 
			if (!isset($one_topic['dignity_forum_topic_closed']))  $one_topic['dignity_forum_topic_closed'] = false;
			if ( (bool)$one_topic['dignity_forum_topic_closed'] )
			{
				$chckout = 'checked="true"';
			} 
			$form .= '<p>' . t('Закрыть тему?', __FILE__) . ' <input name="f_dignity_forum_topic_closed" type="checkbox" ' . $chckout . '></p>';
                        
            // прекрипить
			$chckout = ''; 
			if (!isset($one_topic['dignity_forum_topic_ontop']))  $one_topic['dignity_forum_topic_ontop'] = false;
			if ( (bool)$one_topic['dignity_forum_topic_ontop'] )
			{
				$chckout = 'checked="true"';
			} 
			$form .= '<p>' . t('Прилепленная?', __FILE__) . ' <input name="f_dignity_forum_topic_ontop" type="checkbox" ' . $chckout . '></p>';
			
			// конец формы
			$form .= '<input type="submit" name="f_submit_dignity_forum_topic" value="' . t('Сохранить', __FILE__) . '" style="margin: 10px 0;">';
			$form .= ' <input type="submit" name="f_submit_dignity_forum_topic_delete" onClick="if(confirm(\'' . t('Удалить тему?', __FILE__) . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '">';
			$form .= '</form>';
		
		// выводим форму
		echo $form;
	}
	
}
else{
	// если не число
	echo t('Такой темы не существует, либо ошибочный номер.', __FILE__);
}

#end of file