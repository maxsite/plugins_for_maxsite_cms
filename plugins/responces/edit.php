<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
?>

<div class="admin-h-menu">
<?php
	# сделаем меню горизонтальное в текущей закладке
	// основной url этого плагина - жестко задается
	$plugin_url = getinfo('site_admin_url') . 'responces';
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройки отзывов и предложений'), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'edit', t('Редактирование отзывов'), 'select');
	echo $a;
?>
</div>

<h1><?= t('Редактирование отзывов') ?></h1>

<?php

$CI = & get_instance();

$options = mso_get_option('plugin_responces', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = 10; // отзывов на страницу

$CI->load->library('table');
$tmpl = array (
				'table_open'		  => '<br><table class="page" border="0" width="100%">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt" style="vertical-align: top;">',
				'cell_start'	  => '<td style="vertical-align: top;">',
		  );

$CI->table->set_template($tmpl); // шаблон таблицы

// заголовки
$CI->table->set_heading('id', 'date, ip, browser', 'name', 'text', 'title', 'email', 'icq', 'site', 'phone', 'custom1', 'custom2', 'custom3', 'custom4', 'custom5'); 


// тут последние отзывы с пагинацией
// нам нужна все поля таблицы
// вначале определим общее количество записей
$pag = array(); // пагинация
$pag['limit'] = $options['limit']; // записей на страницу
$pag['type'] = ''; // тип

$CI->db->select('responces_id');
$CI->db->from('responces');
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
$CI->db->from('responces');
$CI->db->order_by('responces_date', 'desc');
if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
	else $CI->db->limit($pag['limit']);
			
$query = $CI->db->get();

if ($query->num_rows() > 0)	
{	
	$books = $query->result_array();
	
	foreach ($books as $book) 
	{
		if ($book['responces_approved']) $approved = '';
			else $approved = '<a title="' . t('Редактировать') . '" style="color: red" href="' . getinfo('site_admin_url') . 'responces/editone/' . $book['responces_id'] . '">' . t('Ожидает одобрения!') . '</a><br><br>';
		
		$CI->table->add_row(
				'<a title="' . t('Редактировать') . '" href="' . getinfo('site_admin_url') . 'responces/editone/' . $book['responces_id'] . '">' 
					. $book['responces_id'] . '</a>',
					
				$approved
				. mso_date_convert('Y-m-d H:i:s', $book['responces_date'])
				. '<br><br>' . $book['responces_ip'] 
				. '<br><br>' . $book['responces_browser'],
				
				htmlspecialchars($book['responces_name']),
				str_replace("\n", "<br>", htmlspecialchars($book['responces_text'])),
				htmlspecialchars($book['responces_title']),
				htmlspecialchars($book['responces_email']),
				htmlspecialchars($book['responces_icq']),
				htmlspecialchars($book['responces_site']),
				htmlspecialchars($book['responces_phone']),
				htmlspecialchars($book['responces_custom1']),
				htmlspecialchars($book['responces_custom2']),
				htmlspecialchars($book['responces_custom3']),
				htmlspecialchars($book['responces_custom4']),
				htmlspecialchars($book['responces_custom5']));
		
	}
	
	echo '<br>';
	mso_hook('pagination', $pag);
	echo $CI->table->generate(); // вывод подготовленной таблицы
	echo '<br>';
	mso_hook('pagination', $pag);
}



?>