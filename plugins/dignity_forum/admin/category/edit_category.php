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

// получаем доступ к CI
$CI = & get_instance();

// готовим данные для добавления в базу
if ($post = mso_check_post(array('f_session_id', 'f_submit_dignity_forum_category')) )
{
	// проверяем реферала
	mso_checkreferer();	

	// готовим массив для добавления в базу данных
	$ins_data = array (
			'dignity_forum_category_name' => $post['f_dignity_forum_category_name'],
			'dignity_forum_category_description' => $post['f_dignity_forum_category_description'],
			'dignity_forum_category_order' => $post['f_dignity_forum_category_order'],
			'dignity_forum_category_parent_id' => $post['f_dignity_forum_category_parent_id']
			);
	
	// добавляем в базу
	$res = ($CI->db->insert('dignity_forum_category', $ins_data)) ? '1' : '0';

	// результат
	if ($res)
		{
			// если всё окей
			echo '<div class="update">' . t('Категория добавлена!', __FILE__) . '</div>';	
		}
		// если ошибки
		else echo '<div class="error">' . t('Ошибка добавления в базу данных...', __FILE__) . '</div>';
		
	// сбрасываем кеш
	mso_flush_cache();
}		

// начало формы
$form = '';
$form .= '<form action="" method="post">' . mso_form_session('f_session_id');

$form .= '<p><strong>' . t('Название:', __FILE__) . '</strong><span style="color:red;">*</span><br>
	<input name="f_dignity_forum_category_name" type="text" style="width:50%" required="required"></p>';
	
$form .= '<p><strong>' . t('Описание:', __FILE__) . '</strong><br>
	<input name="f_dignity_forum_category_description" type="text" style="width:50%"></p>';
	
$form .= '<p><strong>' . t('Родитель:', __FILE__) . '</strong><br>
	<input name="f_dignity_forum_category_parent_id" type="text" style="width:50%" value="0"></p>';
	
$form .= '<p><strong>' . t('Порядок:', __FILE__) . '</strong><br>
	<input name="f_dignity_forum_category_order" type="text" style="width:50%" value="0"></p>';

// конец формы
$form .= '<p><input type="submit" class="submit" name="f_submit_dignity_forum_category" value="' . t('Добавить', __FILE__) . '">';
$form .= '</form>';	

// выводим форму
echo $form;

// загружаем библиотеку table
$CI->load->library('table');

// массив с таблицей
$tmpl = array (
		'table_open' => '<br><table class="page" border="0" width="100%">',
		'row_alt_start' => '<tr class="alt">',
		'cell_alt_start' => '<td class="alt" style="vertical-align: top;">',
		'cell_start' => '<td style="vertical-align: top;">',
		);

// создаём табилицу
$CI->table->set_template($tmpl);

// заголовки таблицы
$CI->table->set_heading('id', t('Опции', __FILE__), t('Название', __FILE__), t('Родитель', __FILE__), t('Порядок', __FILE__));

// выводим данные из базы
$CI->db->from('dignity_forum_category');
$CI->db->order_by('dignity_forum_category_order', 'asc');
$query = $CI->db->get();

if ($query->num_rows() > 0)	
{	
	$categorys = $query->result_array();
	
	foreach ($categorys as $category) 
	{
		$CI->table->add_row(
				$category['dignity_forum_category_id'],
				'<a title="' . t('Редактировать', __FILE__) . '" href="' . getinfo('site_admin_url')
					. 'dignity_forum/editone_category/' . $category['dignity_forum_category_id'] . '">'
					. t('Редактировать', __FILE__) . '</a>',	
				$category['dignity_forum_category_name'],
				$category['dignity_forum_category_parent_id'],
				$category['dignity_forum_category_order']
				);
	}
	
	// генерируем таблицу
	echo $CI->table->generate();
}
// выводим сообщения
else echo t('Категорий нет.', __FILE__);

#end of file