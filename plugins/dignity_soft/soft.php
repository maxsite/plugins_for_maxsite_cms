<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 */

// начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// доступ к CI
$CI = & get_instance();

// выводим навигацию новостей
soft_menu();

// загружаем опции
$options = mso_get_option('plugin_dignity_soft', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = 10;
if ( !isset($options['slug']) ) $options['slug'] = 'soft';
if ( !isset($options['header']) ) $options['header'] = t('Софт', __FILE__);
if ( !isset($options['meta_description']) ) $options['meta_description'] = '';
if ( !isset($options['meta_keywords']) ) $options['meta_keywords'] = '';
if ( !isset($options['textdo']) ) $options['textdo'] = '';

// meta-тэги
mso_head_meta('title', $options['header']);
mso_head_meta('description', $options['meta_description']);
mso_head_meta('keywords', $options['meta_keywords']);

echo '<h1>' . $options['header'] . '</h1>';
echo '<p>' . $options['textdo'] . '</p>';

// готовим пагинацию
$pag = array();
$pag['limit'] = $options['limit'];
$CI->db->select('dignity_soft_id');
$CI->db->from('dignity_soft');
$CI->db->where('dignity_soft_approved', true);
$CI->db->where('dignity_soft_ontop', true);
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

// берём новости из базы
$CI->db->from('dignity_soft');
$CI->db->where('dignity_soft_approved', true);
$CI->db->where('dignity_soft_ontop', true);
$CI->db->join('dignity_soft_category', 'dignity_soft_category.dignity_soft_category_id = dignity_soft.dignity_soft_category', 'left');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_soft.dignity_soft_comuser_id', 'left');
$CI->db->order_by('dignity_soft_datecreate', 'desc');
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
		
		// если вошел автор
		if ($onepage['dignity_soft_comuser_id'] == getinfo('comusers_id')){
			// выводим ссылку «редактировать»
			$out .= '<p><a href="' . getinfo('site_url') . $options['slug'] . '/edit/' . $onepage['dignity_soft_id'] . '">' . t('Редактировать', __FILE__) . '</a></p>';
		}
		
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
		
		/*
		$CI->db->from('dignity_soft_comments');
		$CI->db->where('dignity_soft_comments_approved', true);
		$CI->db->where('dignity_soft_comments_thema_id', $onepage['dignity_soft_id']);
		$out .= ' | ' . t('Комментарий:', __FILE__) . ' ' . $CI->db->count_all_results();
		*/
		
		$out .= '</div>';
		$out .= '<div class="break"></div>';
		$out .= '</div><!--div class="page_only"-->';
		
	}

	$out .= '<p><i>' . $options['textposle'] . '</i></p>';
	
	echo $out;
	
	mso_hook('pagination', $pag);
}
else
{

	echo t('Приложений нет.', __FILE__);
}

require(getinfo('shared_dir') . 'main/main-end.php');
	  

# end of file
