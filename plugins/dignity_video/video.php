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

// доступ к CI
$CI = & get_instance();

// загружаем опции
$options = mso_get_option('plugin_dignity_video', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = 10;
if ( !isset($options['slug']) ) $options['slug'] = 'video';

// готовим пагинацию видео записей
$pag = array();
$pag['limit'] = $options['limit'];
$CI->db->select('dignity_video_id');
$CI->db->from('dignity_video');
$CI->db->where('dignity_video_approved', true);
$CI->db->where('dignity_video_ontop', true);
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

// берём видео из базы
$CI->db->from('dignity_video');
$CI->db->where('dignity_video_approved', true);
$CI->db->where('dignity_video_ontop', true);
$CI->db->join('dignity_video_category', 'dignity_video_category.dignity_video_category_id = dignity_video.dignity_video_category', 'left');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_video.dignity_video_comuser_id', 'left');
$CI->db->order_by('dignity_video_datecreate', 'desc');
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
		$out .= '<div class="video_page_only">';

			$out .= '<div class="video_info">';
				$out .= '<h1>';
				$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_video_id'] . '">' . $onepage['dignity_video_title'] . '</a>';
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
		
			// выводим видео запись
			$out .= '<div class="video_info_cuttext">';
	        $out .= '<p>' . video_cleantext($onepage['dignity_video_text']) . '</p>';
	        $out .= '</div>';
		
			$out .= '<div class="video_info">';

				// выводим дату
				$out .= '<span style="padding-right:5px;">';
				$out .= '<img src="' . getinfo('plugins_url') . 'dignity_video/img/public.png' . '" alt="" title="' . t('Количество просмотров', __FILE__) . '">';
				$out .= '</span>';
				$out .= mso_date_convert($format = 'd.m.Y, H:i', $onepage['dignity_video_datecreate']);

				// рубрика
				if ($onepage['dignity_video_category_id'])
				{
					$out .= ' | ';
					$out .= '<span style="padding-right:0px;">';
					$out .= '<img src="' . getinfo('plugins_url') . 'dignity_video/img/ordner.png' . '" alt="" title="' . t('Категория', __FILE__) . '">';
					$out .= '</span>';
					$out .= ' <a href="' . getinfo('site_url') . $options['slug'] . '/category/' . $onepage['dignity_video_category_id'] . '">' . $onepage['dignity_video_category_name'] . '</a>';
				}
				else
				{
					$out .= ' | ';
					$out .= '<span style="padding-right:0px;">';
					$out .= '<img src="' . getinfo('plugins_url') . 'dignity_video/img/ordner.png' . '" alt="" title="' . t('Категория', __FILE__) . '">';
					$out .= ' <a href="' . getinfo('site_url') . $options['slug'] .'">' . t('Все видео', __FILE__) . '</a>';	
					$out .= '</span>';
				}

				$CI->db->from('dignity_video_comments');
				$CI->db->where('dignity_video_comments_approved', true);
				$CI->db->where('dignity_video_comments_thema_id', $onepage['dignity_video_id']);
				$out .= ' | ';
				$out .= '<span style="padding-right:5px;">';
				$out .= '<img src="' . getinfo('plugins_url') . 'dignity_video/img/comments.png' . '">';
				$out .= '</span>';
				$out .= $CI->db->count_all_results();
				
			$out .= '</div>';
					
			$out .= '<div class="video_break"></div>';

		$out .= '</div><!--div class="video_page_only"-->';
		
	}
	
	// выводим всё
	echo $out;
	
	// добавляем пагинацию
	mso_hook('pagination', $pag);
}
else
{
	echo '<p>' . t('Видео нет.', __FILE__) . '</p>';
}

// просьба не удалять эту строчку!
echo '<div class="video_info_copyright">';
	echo '<p>';
	echo t('Dignity Video by <a href="http://alexanderschilling.net" title="' . t('Перейти на сайт разработчика', __FILE__) . '">Alexander Schilling</a> | Source on <a href="https://github.com/dignityinside/dignity_video" title="' . t('Страница на github', __FILE__) . '">github</a>', __FILE__);
	echo '</p>';
echo '</div>';

// конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

# end of file
