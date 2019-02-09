<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

require(getinfo('template_dir') . 'main-start.php');

// доступ к CodeIgniter
$CI = & get_instance();

// загружаем опции
$options = mso_get_option('plugin_dignity_joke', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = 10;
if ( !isset($options['slug']) ) $options['slug'] = 'joke';

// готовим пагинацию
$pag = array();
$pag['limit'] = $options['limit'];
$CI->db->select('dignity_joke_id');
$CI->db->from('dignity_joke');
$CI->db->where('dignity_joke_category', mso_segment(3));
$CI->db->where('dignity_joke_approved', 1);

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

$CI->db->from('dignity_joke');
$CI->db->where('dignity_joke_approved', 1);
$CI->db->where('dignity_joke_category', mso_segment(3));
$CI->db->order_by('dignity_joke_datecreate', 'desc');
if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
else $CI->db->limit($pag['limit']);
$query = $CI->db->get();

// если есть что выводить...
if ($query->num_rows() > 0)	
{
	$allpages = $query->result_array();
	
	// обьявляем переменую
	$out = '';
	
	joke_menu();
	
	// цикл
	foreach ($allpages as $onepage) 
	{
		
		$out .= '<div class="page_only">';
		
		$out .= '<div class="info info-top">';
		$out .= '<h1><a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_joke_id'] . '">' . '#' . $onepage['dignity_joke_id'] . '</a></h2>';
		$out .= '</div>';
		
		$out .= '<p>' . joke_cleantext($onepage['dignity_joke_cuttext']) . '</p>';
		
		// если нет текста, скрываем ссылку «подробнее»
		if (!$onepage['dignity_joke_text'])
		{
			// ничего не показываем...
			$out .= '';
		}
		else{
			// показываем ссылку «подробнее»
			$out .= '<p style="padding-bottom:10px;"><a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_joke_id'] . '">' .
				t('Подробнее»', __FILE__) . '</a></p>';
		}
		
			$out .= '<div class="info info-bottom"></div><div class="break"></div></div><!--div class="page_only"-->';
		
	}
	
	echo $out;
	
	// добавляем пагинацию
	mso_hook('pagination', $pag);
}
else
{
	joke_menu();
	
	echo t('Нет анекдотов.', __FILE__);
}

require(getinfo('template_dir') . 'main-end.php');

# end of file