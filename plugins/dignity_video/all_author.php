<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_video (github)
 * License GNU GPL 2+
 */

// начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// доступ к CI
$CI = & get_instance();

// выводим меню
video_menu();

// загружаем опции
$options = mso_get_option('plugin_dignity_video', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = 10;
if ( !isset($options['slug']) ) $options['slug'] = 'video';

// готовим пагинацию для авторов
$pag = array();
$pag['limit'] = $options['limit'];
$CI->db->select('dignity_video_id');
$CI->db->from('dignity_video');
$CI->db->where('dignity_video_approved', true);
$CI->db->order_by('dignity_video_comuser_id', 'asc');
$CI->db->group_by('dignity_video_comuser_id');
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

// берем даныне из базы
$CI->db->from('dignity_video');
$CI->db->where('dignity_video_approved', true);
$CI->db->order_by('dignity_video_comuser_id', 'asc');
$CI->db->group_by('dignity_video_comuser_id');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_video.dignity_video_comuser_id', 'left');
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
		
		$out .= '<h2>';
		$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '/all_one_author/' . $onepage['dignity_video_comuser_id'] . '">' . t('Все видео записи ', __FILE__) . $onepage['comusers_nik'] . '</a>';
		$out .= '</h2>';
	}
	
	echo $out;
	
	// добавляем пагинацию
	mso_hook('pagination', $pag);
}
else
{
	echo t('Нет авторов.', __FILE__);
}

require(getinfo('shared_dir') . 'main/main-end.php');

// конец файла
