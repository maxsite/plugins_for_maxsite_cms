<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_blogs (github)
 * License GNU GPL 2+
 */

// начало шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

// доступ к CI
$CI = & get_instance();

// загружаем опции
$options = mso_get_option('plugin_dignity_blogs', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = 10;
if ( !isset($options['slug']) ) $options['slug'] = 'blogs';
if ( !isset($options['title']) ) $options['title'] = t('Блоги', __FILE__);
if ( !isset($options['description']) ) $options['description'] = '';
if ( !isset($options['keywords']) ) $options['keywords'] = '';
if ( !isset($options['textdo']) ) $options['textdo'] = '';
if ( !isset($options['no_blog_name']))  $options['no_blog_name'] = true;

// мета данные
mso_head_meta('title', $options['title']);
mso_head_meta('description', $options['description']);
mso_head_meta('keywords', $options['keywords']);

// выводим меню
blogs_menu();

// выводим заголовок "блоги"
echo '<h1>' . $options['title'] . '</h1>';

// если есть текст до...
if ($options['textdo'])
{
	echo '<p>' . $options['textdo'] . '</p>';
}

// готовим пагинацию для записей
$pag = array();
$pag['limit'] = $options['limit'];
$CI->db->select('dignity_blogs_id');
$CI->db->from('dignity_blogs');
$CI->db->where('dignity_blogs_approved', true);
$CI->db->where('dignity_blogs_ontop', true);
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

// загружаем записи из базы
$CI->db->from('dignity_blogs');
$CI->db->where('dignity_blogs_approved', true);
$CI->db->where('dignity_blogs_ontop', true);
$CI->db->join('dignity_blogs_category', 'dignity_blogs_category.dignity_blogs_category_id = dignity_blogs.dignity_blogs_category', 'left');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_blogs.dignity_blogs_comuser_id', 'left');
$CI->db->order_by('dignity_blogs_datecreate', 'desc');
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
		
		$out .= '<div class="blogs_page_only">';
		
			// выводим заголовк записи
			$out .= '<div class="blogs_info">';
				$out .= '<h1>';
				$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_blogs_id'] . '">' . $onepage['dignity_blogs_title'] . '</a>';
				$out .= '</h1>';
			$out .= '</div>';

			// если вошел автор записи
	       	if ($onepage['dignity_blogs_comuser_id'] == getinfo('comusers_id'))
	       	{
	            // выводим ссылку «редактировать»
	            $out .= '<div class="blogs_info_edit">';
					$out .= '<p>';
					$out .= '<span>';
					$out .= '<img src="' . getinfo('plugins_url') . 'dignity_blogs/img/edit.png' . '" alt="">';
					$out .= '</span>';
					$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '/edit/' . $onepage['dignity_blogs_id'] . '" title="' . t('Редактировать статью', __FILE__) . '">' . t('Редактировать', __FILE__) . '</a>';
					$out .= '</p>';
				$out .= '</div>';
			}
			
			// выводим надпись и ссылку "блог им."
			$out .= '<div class="blogs_info_blog_name">';
				$out .= '<p>';
				$out .= '<span>';
				$out .= '<img src="' . getinfo('plugins_url') . 'dignity_blogs/img/user.png' . '" alt="">';
				$out .= '</span>';

				$hide_no_blog_name = '';
				if ($options['no_blog_name'])
				{
					$hide_no_blog_name = t('Блог им. ', __FILE__);
				}

				$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '/blog/' . $onepage['dignity_blogs_comuser_id'] . '" title="' . t('Перейти на блог пользователя', __FILE__) . '">' . $hide_no_blog_name . $onepage['comusers_nik'] . '</a>';
				
				$out .= '</p>';
			$out .= '</div>';
			
			// выводим анонс статьи
			$out .= '<div class="blogs_info_cuttext">';
				$out .= '<p>' . blogs_cleantext($onepage['dignity_blogs_cuttext']) . '</p>';
			$out .= '</div>';
			
			// если нет текста, скрываем ссылку «подробнее»
			if ($onepage['dignity_blogs_text'])
			{
				// показываем ссылку «подробнее»
				$out .= '<div class="blogs_info_text">';
					$out .= '<p>';
					$out .= '<a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $onepage['dignity_blogs_id'] . '" title="' . t('Показать всю статью', __FILE__) . '">' .
						t('Подробнее →', __FILE__) . '</a>';
					$out .= '</p>';
				$out .= '</div>';
			}
			
			$out .= '<div class="blogs_info">';

				// выводим дату
					$out .= '<span style="padding-right:5px;">';
					$out .= '<img src="' . getinfo('plugins_url') . 'dignity_blogs/img/public.png' . '" alt="">';
					$out .= '</span>';
					$out .= mso_date_convert($format = 'd.m.Y → H:i', $onepage['dignity_blogs_datecreate']);

				// выводим категорию
				if ($onepage['dignity_blogs_category_id'])
				{
					$out .= ' | ';
					$out .= '<span style="padding-right:0px;">';
					$out .= '<img src="' . getinfo('plugins_url') . 'dignity_blogs/img/ordner.png' . '" alt="">';
					$out .= '</span>';
					$out .= ' <a href="' . getinfo('site_url') . $options['slug'] . '/category/' . $onepage['dignity_blogs_category_id'] . '">' . $onepage['dignity_blogs_category_name'] . '</a>';
				}
				else
				{
					$out .= ' | ';
					$out .= '<span style="padding-right:0px;">';
					$out .= '<img src="' . getinfo('plugins_url') . 'dignity_blogs/img/ordner.png' . '" alt="">';
					$out .= '</span>';
					$out .= ' <a href="' . getinfo('site_url') . $options['slug'] . '" title="' . t('Все записи', __FILE__) . '">' . t('Все записи', __FILE__) . '</a>';
				}
			
				// подсчитываем количество комментарий
				$CI->db->from('dignity_blogs_comments');
				$CI->db->where('dignity_blogs_comments_approved', true);
				$CI->db->where('dignity_blogs_comments_thema_id', $onepage['dignity_blogs_id']);
				$out .= ' | ';
				$out .= '<span style="padding-right:5px;">';
				$out .= '<img src="' . getinfo('plugins_url') . 'dignity_blogs/img/comments.png' . '" alt="">';
				$out .= '</span>';
				$out .= $CI->db->count_all_results();
			
			$out .= '</div>';
			
			$out .= '<div class="blogs_break"></div>';

		$out .= '</div><!--div class="blogs_page_only"-->';
		
	}
	
	echo $out;
	
	// пагинация
	mso_hook('pagination', $pag);
}
else
{
	echo '<p>' . t('Нет записей. Ваша будет первой!', __FILE__) . '</p>';
}

// просьба не удалять эту строчку!
echo '<div class="blogs_info_copyright">';
	echo '<p>';
	echo t('Dignity Blogs by <a href="http://alexanderschilling.net" title="' . t('Перейти на сайт разработчика', __FILE__) . '">Alexander Schilling</a> | Source on <a href="https://github.com/dignityinside/dignity_blogs" title="' . t('Страница на github', __FILE__) . '">github</a>', __FILE__);
	echo '</p>';
echo '</div>';

// конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file
