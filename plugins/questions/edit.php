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

<h1><?= t('Редактирование вопросов', __FILE__) ?></h1>

<?php

$CI = & get_instance();

$options = mso_get_option('plugin_questions', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = 10; // вопросов на страницу

$CI->load->library('table');
$tmpl = array (
				'table_open'		  => '<br><table class="page" border="0" width="100%">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt" style="vertical-align: top;">',
				'cell_start'	  => '<td style="vertical-align: top;">',
		  );

$CI->table->set_template($tmpl); // шаблон таблицы

// заголовки
$CI->table->set_heading('ID', 'Статус, Дата, IP', 'Имя', 'Вопрос', 'E-mail', 'Возраст:', 'Город:'); 

// тут последние ответы с пагинацией
// нам нужна все поля таблицы
// вначале определим общее количество записей
$pag = array(); // пагинация
$pag['limit'] = $options['limit']; // записей на страницу
$pag['type'] = ''; // тип

$CI->db->select('questions_id');
$CI->db->from('questions');
$query = $CI->db->get();
$pag_row = $query->num_rows();

if ($pag_row > 0)
{
	$pag['maxcount'] = ceil($pag_row / $pag['limit']); // всего станиц пагинации

	$current_paged = mso_current_paged();
	if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

	$offset = $current_paged * $pag['limit'] - $pag['limit'];
}
else
{
	$pag = false;
}

// теперь получаем сами записи
$CI->db->from('questions');
$CI->db->order_by('questions_date', 'desc');
if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
	else $CI->db->limit($pag['limit']);
			
$query = $CI->db->get();

if ($query->num_rows() > 0)	
{	
	$books = $query->result_array();
	
	foreach ($books as $book) 
	{
		if ($book['questions_approved']) $approved = '';
			else $approved = '<a title="' . t('Редактировать', __FILE__) . '" style="color: red" href="' . getinfo('site_admin_url') . 'questions/editone/' . $book['questions_id'] . '">' . t('Ожидает одобрения!', __FILE__) . '</a><br><br>';
		
		$CI->table->add_row(
				'<a title="' . t('Редактировать', __FILE__) . '" href="' . getinfo('site_admin_url') . 'questions/editone/' . $book['questions_id'] . '">' 
					. $book['questions_id'] . '</a>',
					
				$approved
				. mso_date_convert('d-m-Y H:i:s', $book['questions_date'])
				. '<br><br>' . $book['questions_ip'],
				
				htmlspecialchars($book['questions_name']),
				str_replace("\n", "<br>", htmlspecialchars($book['questions_text'])),
				htmlspecialchars($book['questions_email']),
				htmlspecialchars($book['questions_age']),
				htmlspecialchars($book['questions_city']));
		
	}
	
	echo '<br>';
	mso_hook('pagination', $pag);
	echo $CI->table->generate(); // вывод подготовленной таблицы
	echo '<br>';
	mso_hook('pagination', $pag);
}



?>
