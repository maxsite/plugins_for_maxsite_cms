<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_video (github)
 * License GNU GPL 2+
 */

// начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// выводим меню
video_menu();

// доступ к CodeIgniter
$CI = & get_instance();

// загружаем опции
$options = mso_get_option('plugin_dignity_video', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = 10;
if ( !isset($options['slug']) ) $options['slug'] = 'video';

// проверка сегмента
$id = mso_segment(3);
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

if ($id && getinfo('comusers_id') == $id)
{

	// готовим пагинацию для видео записей
	$pag = array();
	$pag['limit'] = 15;
	$CI->db->from('dignity_video');
	$CI->db->select('dignity_video_id');
	$CI->db->where('dignity_video_comuser_id', $id);
	$CI->db->where('dignity_video_approved', true);
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
	
	// загружаем данные из базы
	$CI->db->from('dignity_video');
	$CI->db->where('dignity_video_comuser_id', $id);
	$CI->db->order_by('dignity_video_datecreate', 'desc');
	$CI->db->join('dignity_video_category', 'dignity_video_category.dignity_video_category_id = dignity_video.dignity_video_category', 'left');
	if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
	else $CI->db->limit($pag['limit']);	
	$query = $CI->db->get();

	// если есть что выводить
	if ($query->num_rows() > 0)	
	{	
		$allpages = $query->result_array();
		
		$out = '';
		
        foreach ($allpages as $onepage) 
        {
            
            $out .= '<div class="video_page_only">';
			
			$no_approved = '';
			if ($onepage['dignity_video_comuser_id'] == getinfo('comusers_id'))
			{
				if (!$onepage['dignity_video_approved'])
				{
					$no_approved .= '<span style="color:red;">?</span>';
				}
			}
		
            $out .= '<div class="video_info">';
            $out .= '<h1>' . $no_approved;
			
			if($onepage['dignity_video_approved'])
			{
				$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_video_id'] . '">';
			}
			else
			{
				$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '/edit/' . $onepage['dignity_video_id'] . '">';
			}
			
			$out .= $onepage['dignity_video_title'] . '</a> ';
                        
            $out .= '</h1>';
            $out .= '</div>';
		
            	// если вошел автор видео записи
				if ($onepage['dignity_video_comuser_id'] == getinfo('comusers_id'))
				{
					// выводим ссылку «редактировать»
					$out .= '<div class="video_info_edit">';
						$out .= '<p>';
						$out .= '<span style="padding-right:10px;">';
						$out .= '<img src="' . getinfo('plugins_url') . 'dignity_video/img/edit.png' . '" alt="">';
						$out .= '</span>';
						$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '/edit/' . $onepage['dignity_video_id'] . '">' . t('Редактировать', __FILE__) . '</a>';
						$out .= '</p>';
					$out .= '</div>';
				}
		
				$out .= '<div class="video_info"></div>';
				
				$out .= '<div class="video_break"></div>';

			$out .= '</div><!--div class="page_only"-->';
		
            }
		
		// выводим всё
		echo $out;

		// выводи пагинацию
		mso_hook('pagination', $pag);

	}
	else
	{ 
		// видео не найдено
		video_not_found();
	}
}
else
{
	// видео не найдено
	video_not_found();
}

// конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

// конец файла
