<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// заголовок админки и подсказка
echo '<h1>' . t('Форум', __FILE__) . '</h1>';
echo '<p class="info">' . t('Редактировать категории', __FILE__) . '</p>';

// загружаем меню
echo '<div class="admin-h-menu">';
require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;
$forum->admin_menu();
echo '</div>';

if ( !isset($options['slug']) ) $options['slug'] = 'forum'; 

echo '<p><a href="' . getinfo('siteurl') . $options['slug'] . '" target="_blank">' . t('Перейти на форум →', __FILE__) . '</a></p>';

// задаём заничения по умолчанию
if ( !isset($post['f_dignity_forum_category_name'])) $post['f_dignity_forum_category_name'] = '';
if ( !isset($post['f_dignity_forum_category_description'])) $post['f_dignity_forum_category_description'] = '';
if ( !isset($post['f_dignity_forum_category_parent_id'])) $post['f_dignity_forum_category_parent_id'] = 0;
if ( !isset($post['f_dignity_forum_category_order'])) $post['f_dignity_forum_category_order'] = 0;

// получаем доступ к CI
$CI = & get_instance();

// $id = 4 сегмент
$id = mso_segment(4);

// проверяем
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

// если число...
if ($id) {

	// удаление категории, если был пост
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_forum_category_delete')) )
	{
		// проверяем реферала
		mso_checkreferer();
		
		// выбираем категориюю согласно $id и удаляем её
		$CI->db->where('dignity_forum_category_id', $id);
		$CI->db->delete('dignity_forum_category');
		
		// сбрасываем кеш
		mso_flush_cache();
		
		// выдаём сообщения
		echo '<div class="update">' . t('Удалено!', __FILE__) . '</div>';
		
		return;
	}

	// редактирование, если был пост
	if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_forum_category')) )
	{
		// проверяем реферала
		mso_checkreferer();
			
		// собираем массив для добавления в базу
		$data = array (
				'dignity_forum_category_name' => $post['f_dignity_forum_category_name'],
				'dignity_forum_category_description' => $post['f_dignity_forum_category_description'],
				'dignity_forum_category_parent_id' => $post['f_dignity_forum_category_parent_id'],
				'dignity_forum_category_order' => $post['f_dignity_forum_category_order']
				);
			
		// выбираем таблицу согласно $id
		$CI->db->where('dignity_forum_category_id', $id);
			
		// если всё окей, добавляем в базу
		if ($CI->db->update('dignity_forum_category', $data ) )
			echo '<div class="update">' . t('Обновлено!', __FILE__) . '</div>';
		else
			// если ошибка...
			echo '<div class="error">' . t('Ошибка обновления', __FILE__) . '</div>';
			
		// сбрасываем кеш
		mso_flush_cache();
	}

	// выводим данные из базы для формы
	$CI->db->from('dignity_forum_category');
	$CI->db->where('dignity_forum_category_id', $id);
	$query = $CI->db->get();
	
	if ($query->num_rows() > 0)	
	{	
		$categorys = $query->result_array();
		
		// начало формы
		$form = '';
		$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
		
		foreach ($categorys as $category) 
		{
			$form .= '<p><strong>' . t('Названия:', __FILE__) . '</strong><span style="color:red;">*</span><br>
				<input name="f_dignity_forum_category_name" type="text"
					value="' . $category['dignity_forum_category_name'] . '" style="width:50%" required="required"></p>';
			
			$form .= '<p><strong>' . t('Описание:', __FILE__) . '</strong><br>
				<input name="f_dignity_forum_category_description" type="text"
					value="' . $category['dignity_forum_category_description'] . '" style="width:50%"></p>';
					
			$form .= '<p><strong>' . t('Родитель:', __FILE__) . '</strong><br>
				<input name="f_dignity_forum_category_parent_id" type="text"
					value="' . $category['dignity_forum_category_parent_id'] . '" style="width:50%"></p>';
			
			$form .= '<p><strong>' . t('Порядок:', __FILE__) . '</strong><br>
				<input name="f_dignity_forum_category_order" type="text"
					value="' . $category['dignity_forum_category_order'] . '" style="width:50%"></p>';
		}
		
		// конец формы
		$form .= '<input type="submit" name="f_submit_dignity_forum_category" value="' . t('Изменить', 'admin') . '" style="margin: 10px 0;">';
		$form .= ' <input type="submit" name="f_submit_dignity_forum_category_delete" onClick="if(confirm(\'' . t('Удалить категорию?', __FILE__) . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '">';
		$form .= '</form>';
		
		// выводим форму
		echo $form;
	}
}
else{
	// если это не число, выводим...
	echo t('Такой категории не существует, либо ошибочный номер.', __FILE__);
}

#end of file