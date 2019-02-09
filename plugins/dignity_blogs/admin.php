<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_blogs (github)
 * License GNU GPL 2+
 */

echo '<h1>' . t('Блоги', __FILE__) . '</h1>';
echo '<p class="info">' . t('Панель управления плагином.', __FILE__) . '</p>';

// админ-меню
echo '<div class="admin-h-menu">';
	$plugin_url = getinfo('site_admin_url') . 'dignity_blogs';
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройки', __FILE__), 'select') . ' | ';
	$a  .= mso_admin_link_segment_build($plugin_url, 'edit_comments', t('Комментарии', __FILE__), 'select') . ' | ';
	$a  .= mso_admin_link_segment_build($plugin_url, 'edit_article', t('Статьи', __FILE__), 'select');
	echo $a;
echo '</div>';

// получаем доступ к CI
$CI = & get_instance();
	
$options_key = 'plugin_dignity_blogs';
if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
{
	mso_checkreferer();
	
	$options = array();
	$options['slug'] = $post['f_slug'];
	$options['limit'] = $post['f_limit'];
	$options['ontop'] = isset($post['f_ontop']) ? 1 : 0;
	$options['noapproved'] = isset($post['f_noapproved']) ? 1 : 0;
	$options['title'] = $post['f_title'];
	$options['description'] = $post['f_description'];
	$options['keywords'] = $post['f_keywords'];
    $options['textdo'] = $post['f_textdo'];
	$options['cackle_code'] = $post['f_cackle_code'];
	$options['no_pagination'] = isset($post['f_no_pagination']) ? 1 : 0;
	$options['no_blog_name'] = isset($post['f_no_blog_name']) ? 1 : 0;
	
	mso_add_option($options_key, $options, 'plugins');
	echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
}

$options = mso_get_option($options_key, 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'blogs'; 
if ( !isset($options['limit']) ) $options['limit'] = '10';
if ( !isset($options['title']) ) $options['title'] = t('Блоги', __FILE__);
if ( !isset($options['description']) ) $options['description'] = '';
if ( !isset($options['keywords']) ) $options['keywords'] = '';
if ( !isset($options['textdo']) ) $options['textdo'] = '';
if ( !isset($options['cackle_code']) ) $options['cackle_code'] = '';

$form = '';
$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
$form .= '<h2>' . t('Настройки', __FILE__) . '</h2>';
$form .= '<p>' . t('Коротка ссылка:', __FILE__) . ' ' . ' <input name="f_slug" type="text" value="' . $options['slug'] . '"></p>';
$form .= '<p>' . t('Записей на страницу:', __FILE__) . ' ' . ' <input name="f_limit" type="text" value="' . $options['limit'] . '"></p>';
$form .= '<h2' . t('SEO:'. __FILE__) . '</h2>';
$form .= '<p>' . t('Титул страницы (title):', __FILE__) . ' ' . ' <input name="f_title" type="text" value="' . $options['title'] . '"></p>';
$form .= '<p>' . t('Описание страницы (meta-description):', __FILE__) . '<br>'
    . '<textarea name="f_description" cols="90" rows="5">' . $options['description'] . '</textarea></p>';
$form .= '<p>' . t('Ключевые слова страницы (meta-keywords):', __FILE__) . '<br>'
    . '<textarea name="f_keywords" cols="90" rows="5">' . $options['keywords'] . '</textarea></p>';
$form .= '<p>' . t('Текст до:', __FILE__) . '<br>'
    . '<textarea name="f_textdo" cols="90" rows="5">' . $options['textdo'] . '</textarea></p>';
$form .= '<p>' . t('Cackle код:', __FILE__) . '<br>'
    . '<textarea name="f_cackle_code" cols="90" rows="5">' . $options['cackle_code'] . '</textarea></p>';

// отключить пагинацию комментарий
$chckout = ''; 
if (!isset($options['no_pagination']))  $options['no_pagination'] = true;
if ( (bool)$options['no_pagination'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Отключить пагинацию комментарий?', __FILE__)
	. ' <input name="f_no_pagination" type="checkbox" ' . $chckout . '></p>';

// сразу на главную?
$chckout = ''; 
if (!isset($options['ontop']))  $options['ontop'] = true;
if ( (bool)$options['ontop'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Добавлять автоматически новые записи в избранное?', __FILE__)
	. ' <input name="f_ontop" type="checkbox" ' . $chckout . '></p>';
	
// не проверять комментарии
$chckout = ''; 
if (!isset($options['noapproved']))  $options['noapproved'] = true;
if ( (bool)$options['noapproved'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Не проверять комментарии', __FILE__)
	. ' <input name="f_noapproved" type="checkbox" ' . $chckout . '></p>';

// не показывать надпись "блог им."
$chckout = ''; 
if (!isset($options['no_blog_name']))  $options['no_blog_name'] = true;
if ( (bool)$options['no_blog_name'] )
{
	$chckout = 'checked="true"';
} 
$form .= '<p>' . t('Не показывать надпись "блог им."', __FILE__)
	. ' <input name="f_no_blog_name" type="checkbox" ' . $chckout . '></p>';

$form .= '<input type="submit" name="f_submit" value="' . t('Сохранить', 'plugins') . '" style="margin: 25px 0 5px 0;">';
$form .= '</form>';

// выводим форму
echo $form;

####################################################################################################

if ($post = mso_check_post(array('f_session_id', 'f_submit_dignity_blogs_category')) )
{
	// проверяем реферала
	mso_checkreferer();	

	// готовим массив для добавления в базу данных
	$ins_data = array (
			'dignity_blogs_category_name' => $post['f_dignity_blogs_category_name'],
			'dignity_blogs_category_description' => $post['f_dignity_blogs_category_description'],
			'dignity_blogs_category_position' => $post['f_dignity_blogs_category_position']
			);
	
	// добавляем в базу
	$res = ($CI->db->insert('dignity_blogs_category', $ins_data)) ? '1' : '0';

	// результат
	if ($res)
		{
			echo '<div class="update">' . t('Категория добавлена!', __FILE__) . '</div>';	
		}
		else echo '<div class="error">' . t('Ошибка добавления в базу данных...', __FILE__) . '</div>';
		
	// сбрасываем кеш
	mso_flush_cache();
}		

// начало формы
$form = '';
$form .= '<h2>' . t('Категории', __FILE__) . '</h2>';
$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
$form .= '<p>' . t('Название:', __FILE__) . '<span style="color:red;">*</span><br>
	<input name="f_dignity_blogs_category_name" type="text" style="width:50%" required="required"></p>';
$form .= '<p>' . t('Описание:', __FILE__) . '<br>
	<input name="f_dignity_blogs_category_description" type="text" style="width:50%"></p>';
$form .= '<p>' . t('Порядок:', __FILE__) . '<br>
	<input name="f_dignity_blogs_category_position" type="text" style="width:50%"></p>';
$form .= '<p><input type="submit" class="submit" name="f_submit_dignity_blogs_category" value="' . t('Добавить', __FILE__) . '">';
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
$CI->table->set_heading('id', t('Информация', __FILE__));

// выводим данные из базы
$CI->db->from('dignity_blogs_category');
$CI->db->order_by('dignity_blogs_category_position', 'desc');
$query = $CI->db->get();

if ($query->num_rows() > 0)	
{	
	$categorys = $query->result_array();
	
	foreach ($categorys as $rw) 
	{
		$id = $rw['dignity_blogs_category_id'];
		
		# удаление
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_blogs_category_delete')) )
		{
			mso_checkreferer();
			
			if ( !isset($post['f_id'])) $post['f_id'] = $id;
			
			$CI->db->where('dignity_blogs_category_id', $post['f_id']);
			$CI->db->delete('dignity_blogs_category');
			
			mso_flush_cache();
			
			echo '<div class="update">' . t('Удалено!', __FILE__) . '<script>location.replace(window.location); </script></div>';
			
			return;
		}
		
		# редактирование
		if ( $post = mso_check_post(array('f_session_id', 'f_submit_dignity_blogs_category_edit')) )
		{
			mso_checkreferer();
			
			if ( !isset($post['f_id'])) $post['f_id'] = $id;
			
			$data = array (
					'dignity_blogs_category_name' => htmlspecialchars($post['f_dignity_blogs_category_name']),
					'dignity_blogs_category_description' => htmlspecialchars($post['f_dignity_blogs_category_description']),
					'dignity_blogs_category_position' => htmlspecialchars($post['f_dignity_blogs_category_position']),
			);
			
			$CI->db->where('dignity_blogs_category_id', $post['f_id']);
			
			if ($CI->db->update('dignity_blogs_category', $data ) )
				echo '<div class="update">' . t('Обновлено!', __FILE__) . '<script>location.replace(window.location); </script></div>';
			else 
				echo '<div class="error">' . t('Ошибка обновления', __FILE__) . '</div>';
				
			mso_flush_cache();
			
			return;
		}
		
		$form = '';
		$form .= '<form action="" method="post">' . mso_form_session('f_session_id');
		$form .= '<p>' . t('Название:', __FILE__) . '<br> <input name="f_dignity_blogs_category_name" type="text" style="width:90%" value="' . $rw['dignity_blogs_category_name'] . '"></p>';
		$form .= '<p>' . t('Описание:', __FILE__) . '<br> <input name="f_dignity_blogs_category_description" type="text" style="width:90%" value="' . $rw['dignity_blogs_category_description'] . '"></p>';
		$form .= '<p>' . t('Позиция:', __FILE__) . '<br> <input name="f_dignity_blogs_category_position" type="text" style="width:90%" value="' . $rw['dignity_blogs_category_position'] . '"></p>';
		$form .= '<input type="hidden" name="f_id" value="' . $id . '" />';
		$form .= '<input type="submit" name="f_submit_dignity_blogs_category_edit" value="' . t('Сохранить', __FILE__) . '" style="margin: 10px 0;">';
		$form .= ' <input type="submit" name="f_submit_dignity_blogs_category_delete" onClick="if(confirm(\'' . t('Удалить?', __FILE__) . ' ' . t('Заявку №', __FILE__) . $rw['dignity_blogs_category_id'] . '\')) {return true;} else {return false;}" value="' . t('Удалить', __FILE__) . '">';
		$form .= '</form>';
		
		$CI->table->add_row(
				$id,
				$form
				);
	}
	
	// генерируем таблицу
	echo $CI->table->generate();
}
// выводим сообщения
else echo t('Категорий нет.', __FILE__);

# конец файла
