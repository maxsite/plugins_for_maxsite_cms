<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

require(getinfo('shared_dir') . 'main/main-start.php');
	  

// доступ к CodeIgniter
$CI = & get_instance();

// загружаем опции
$options = mso_get_option('plugin_dignity_soft', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'soft';
if ( !isset($options['limit']) ) $options['limit'] = 10;

soft_menu();

// добавляем заголовок «Категории»
echo '<h1><a href="' . getinfo('siteurl') . $options['slug'] . '">' . t('Новые приложения', __FILE__) . '</a></h1>';
        
// получаем доступ к CI
$CI = & get_instance();

// готовим пагинацию
$pag = array();
$pag['limit'] = $options['limit'];
$CI->db->select('dignity_soft_id');
$CI->db->from('dignity_soft');
if (!is_login())
{
	$CI->db->where('dignity_soft_approved', true);	
}
$query = $CI->db->get();
$pag_row = $query->num_rows();

if ($pag_row > 0)
{
	$pag['maxcount'] = ceil($pag_row / $pag['limit']);

	$current_paged = mso_current_paged();
	if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

	$offset = $current_paged * $pag['limit'] - $pag['limit'];
}
else
{
	$pag = false;
}
	
// берём данные из базы
$CI->db->from('dignity_soft');
if (!is_login())
{
	$CI->db->where('dignity_soft_approved', true);	
}
$CI->db->order_by('dignity_soft_datecreate', 'desc');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_soft.dignity_soft_comuser_id', 'left');
if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
else $CI->db->limit($pag['limit']);
$query = $CI->db->get();
	
// если есть что выводить
if ($query->num_rows() > 0)	
{	
	$entrys = $query->result_array();
	
	// обьявлем переменую
	$out = '';
	$catout = '';
	
	foreach ($entrys as $entry) 
	{
		// выводим названия категории и количество записей в ней
		$catout .= '<li>';
		$catout .= '<p><a href="' . getinfo('siteurl') . $options['slug'] . '/view/' . $entry['dignity_soft_id'] . '">' . $entry['dignity_soft_title'] . '</a>'
			. '<br>' . t(' от ', __FILE__) . '<a href="' . getinfo('siteurl') . 'users/' . $entry['dignity_soft_comuser_id'] . '">' . $entry['comusers_nik'] . '</a>' . t(' в ', __FILE__) . mso_date_convert($format = 'H:i → d.m.Y', $entry['dignity_soft_datecreate']) . '</p>';
		$catout .= '</li>';
	}
		
	// начиаем новый список
	$out .= '<ul>';
		
	// выводим назавания категорий и количетсов записей
	$out .= $catout;
	
	// заканчиваем список
	$out .= '</ul>';
	
	echo $out;
	
	mso_hook('pagination', $pag);
}
else
{
	echo '<p>' . t('Нет новых приложений.', __FILE__) . '</p>';
}

require(getinfo('shared_dir') . 'main/main-end.php');
	  

// конец файла