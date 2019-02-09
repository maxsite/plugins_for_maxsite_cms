<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

require(getinfo('shared_dir') . 'main/main-start.php');
	  

// доступ к CodeIgniter
$CI = & get_instance();

soft_menu();

// загружаем опции
$options = mso_get_option('plugin_dignity_soft', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = 10;
if ( !isset($options['slug']) ) $options['slug'] = 'soft';

// готовим пагинацию
$pag = array();
$pag['limit'] = $options['limit'];
$CI->db->select('dignity_soft_id');
$CI->db->from('dignity_soft');
$CI->db->where('dignity_soft_category', mso_segment(3));
$CI->db->where('dignity_soft_approved', true);

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

$CI->db->from('dignity_soft');
$CI->db->where('dignity_soft_approved', true);
$CI->db->where('dignity_soft_category', mso_segment(3));
$CI->db->order_by('dignity_soft_datecreate', 'desc');
$CI->db->join('dignity_soft_category', 'dignity_soft_category.dignity_soft_category_id = dignity_soft.dignity_soft_category', 'left');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_soft.dignity_soft_comuser_id', 'left');
if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
else $CI->db->limit($pag['limit']);
$query = $CI->db->get();

// если есть что выводить...
if ($query->num_rows() > 0)	
{
	$allpages = $query->result_array();
	
	$out = '';
	
	foreach ($allpages as $onepage) 
	{
		
		$out .= '<div class="page_only">';
		
		$out .= '<div class="info info-top">';
		$out .= '<h1><a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_soft_id'] . '">' . $onepage['dignity_soft_title'] . '</a></h1>';
		$out .= '</div>';
		
		$out .= '<p>' . soft_cleantext($onepage['dignity_soft_cuttext']) . '</p>';
		
		// если нет текста, скрываем ссылку «подробнее»
		if (!$onepage['dignity_soft_text'])
		{
			$out .= '';
		}
		else
		{
			$out .= '<p style="padding-bottom:10px;"><a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_soft_id'] . '">' .
				t('Подробнее»', __FILE__) . '</a></p>';
		}
		
		$out .= '<div class="info info-bottom">';
		
		$out .= $onepage['comusers_nik'] . ', ';
		$out .= mso_date_convert($format = 'd.m.Y, H:i', $onepage['dignity_soft_datecreate']) . ' | ';
		$out .= t('Рубрика:', __FILE__) . ' <a href="' . getinfo('site_url') . $options['slug'] . '/category/' . $onepage['dignity_soft_category_id'] . '">' . $onepage['dignity_soft_category_name'] . '</a>';
		
		$os = '';
		if ($onepage['dignity_soft_os'] == 0)
		{
			$os = 'Windows';
		}
		elseif ($onepage['dignity_soft_os'] == 1)
		{
			$os = 'Linux';
		}
		else
		{
			$os = 'Windows, Linux';
		}
		
		$out .= ' | ' . t('ОС:', __FILE__) . ' ' . $os;
		
		$license = '';
		if ($onepage['dignity_soft_license'] == 0)
		{
			$license = 'Freeware';
		}
		elseif ($onepage['dignity_soft_license'] == 1)
		{
			$license = 'Shareware';
		}
		elseif ($onepage['dignity_soft_license'] == 2)
		{
			$license = 'Open Source (GNU GPL, MIT, BSD...)';
		}
		elseif ($onepage['dignity_soft_license'] == 3)
		{
			$license = 'Non-Free';
		}
		else
		{
			$license = 'Другая лицензия';
		}
		
		$out .= ' | ' . t('Лицензия:', __FILE__) . ' ' . $license;
		
		$out .= '</div>';
		$out .= '<div class="break"></div>';
		$out .= '</div><!--div class="page_only"-->';
		
	}
	
	echo $out;
	
	mso_hook('pagination', $pag);
}
else
{
	echo t('Нет приложений.', __FILE__);
}

require(getinfo('shared_dir') . 'main/main-end.php');
	  

# end of file