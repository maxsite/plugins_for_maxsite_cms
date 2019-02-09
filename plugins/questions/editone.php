<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * THE DIGNITY
 * (c) http://maxsite.thedignity.biz/
 */
?>
<div class="admin-h-menu">
<?php
	# сделаем меню горизонтальное в текущей закладке
	// основной url этого плагина - жестко задается
	$plugin_url = getinfo('site_admin_url') . 'questions';
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройки вопросов', __FILE__), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'edit', t('Редактирование вопросов', __FILE__), 'select');
	echo $a;
?>
</div>

<h1><?= t('Редактирование вопроса', __FILE__) ?></h1>

<?php
// проверим верность 4-го сектора

$id = mso_segment(4);
if (!is_numeric($id)) $id = false; // не число
	else $id = (int) $id;

if (!$id) 
{
	echo t('Ошибочный номер', __FILE__);
	return; // выходим
}

$CI = & get_instance();


# удаление
if ( $post = mso_check_post(array('f_session_id', 'f_submit_questions_delete', 'f_fields_questions')) )
{
	mso_checkreferer();
	
	if ($post['f_fields_questions']['id'] != $id)
	{
		echo t('Ошибочный номер', __FILE__);
		return;
	}

	$CI->db->where('questions_id', $id);
	$CI->db->delete('questions');
	
	mso_flush_cache();
	
	echo '<div class="update">' . t('Удалено!', __FILE__) . '</div>';
	return;
}

# редактирование
if ( $post = mso_check_post(array('f_session_id', 'f_submit_questions', 'f_fields_questions')) )
{
	mso_checkreferer();
	
	if ($post['f_fields_questions']['id'] != $id)
	{
		echo t('Ошибочный номер', __FILE__);
		return;
	}
	
	$CI->db->where('questions_id', $id);
	
	$data = array();
	$data['questions_approved'] = isset($post['f_fields_questions']['approved']) ? 1 : 0;
	
	foreach( $post['f_fields_questions'] as $key => $val )
	{
		if ($key != 'id' and $key != 'approved') $data['questions_' . $key] = $post['f_fields_questions'][$key];
	}
	
	// pr($data);
	
	mso_flush_cache();
	
	if ($CI->db->update('questions', $data ) )
		echo '<div class="update">' . t('Обновлено!', __FILE__) . '</div>';
	else 
		echo '<div class="error">' . t('Ошибка обновления', __FILE__) . '</div>';
}




$options = mso_get_option('plugin_questions', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'questions';
echo '<p><a href="' . getinfo('siteurl') . $options['slug']  . '#questions-' . $id. '" target="_blank">' . t('Посмотреть вопросы на сайте', __FILE__) . '</a></p>';
	


$CI->load->library('table');
$tmpl = array (
				'table_open'		=> '<table class="page" border="0" width="100%"><colgroup style="width: 100px;"/>',
				'row_alt_start'		=> '<tr class="alt">',
				'cell_alt_start'	=> '<td class="alt" style="vertical-align: top;">',
				'cell_start'		=> '<td style="vertical-align: top;">',
		  );

$CI->table->set_template($tmpl); // шаблон таблицы

// заголовки
$CI->table->set_heading(t('Поле', __FILE__), t('Значение', __FILE__)); 

// теперь получаем сами записи
$CI->db->from('questions');
$CI->db->where('questions_id', $id);
			
$query = $CI->db->get();

if ($query->num_rows() > 0)	
{	
	$books = $query->result_array();
	
	$out = '';
	foreach ($books as $book) 
	{
		// pr($book);
		// чтобы не париться с полями, выводим по циклу
		
		foreach ( $book as $key=>$val )
		{
			$key = str_replace('questions_', '', $key);
			
			$val = htmlspecialchars($val);
			
			if ($key == 'id') // id менять нельзя
			{
				$val_out = $val . '<input name="f_fields_questions[' . $key . ']" type="hidden" value="' . $val . '">';
			}
			elseif ($key == 'approved') 
			{
				$check = $val ? ' checked' : '';
				$val_out = '<label><input name="f_fields_questions[' . $key . ']" type="checkbox" ' . $check . '/> ' 
					. t('Опубликовать', __FILE__) . '</label>';
			}
			elseif ($key != 'text' && $key != 'answer') // для всех кроме text - input
			{
				$val_out = '<input name="f_fields_questions[' . $key . ']" type="text" style="width: 99%;" value="' . $val . '">';
			}
			else
			{
				$val_out = '<textarea name="f_fields_questions[' . $key . ']" style="width: 99%; height: 200px;">' . $val . '</textarea>';
			}
			
			$CI->table->add_row('<strong>' . $key . '</strong>', $val_out);
		}
	}
	

	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $CI->table->generate(); // вывод подготовленной таблицы
	echo '<input type="submit" name="f_submit_questions" value="' . t('Изменить', 'admin') . '" style="margin: 10px 0;">';
	echo ' <input type="submit" name="f_submit_questions_delete" onClick="if(confirm(\'' . t('Удалить вопрос?', __FILE__) . '\')) {return true;} else {return false;}" value="' . t('Удалить вопрос', __FILE__) . '">';
	echo '</form>';

}



?>
