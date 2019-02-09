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

require_once(getinfo('plugins_dir') . 'dignity_blogs/core/functions.php');
$blogs = new Blogs;

// выводим меню
$blogs->menu();

// загружаем опции
$options = mso_get_option('plugin_dignity_blogs', 'plugins', array());
if ( !isset($options['limit']) ) $options['limit'] = 10;
if ( !isset($options['slug']) ) $options['slug'] = 'blogs';
if ( !isset($options['no_blog_name']))  $options['no_blog_name'] = true;

// готовим пагинацию блогов
$pag = array();
$pag['limit'] = $options['limit'];
$CI->db->select('dignity_blogs_id');
$CI->db->from('dignity_blogs');
$CI->db->where('dignity_blogs_approved', true);
$CI->db->order_by('dignity_blogs_comuser_id', 'asc');
$CI->db->group_by('dignity_blogs_comuser_id');
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
$CI->db->from('dignity_blogs');
$CI->db->where('dignity_blogs_approved', true);
$CI->db->order_by('dignity_blogs_comuser_id', 'asc');
$CI->db->group_by('dignity_blogs_comuser_id');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_blogs.dignity_blogs_comuser_id', 'left');
if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
else $CI->db->limit($pag['limit']);
$query = $CI->db->get();

// если есть что выводить...
if ($query->num_rows() > 0)	
{
	$allpages = $query->result_array();
	
	$out = '';
	
	$out .= '<span style="width: 50%; float:left; font-weight:bold;">' . t('Блог', __FILE__) . '</span>';
    $out .= '<span style="width: 20%; float:left; font-weight:bold;">' . t('Тем', __FILE__) . '</span>';
    $out .= '<span style="width: 20%; float:left; font-weight:bold;">' . t('Комментарий', __FILE__) . '</span>';
    $out .= '<span style="width: 10%; float:left; font-weight:bold;">' . t('RSS', __FILE__) . '</span>';

	foreach ($allpages as $onepage) 
	{

		// узнаём количество тем
		$CI->db->from('dignity_blogs');
		$CI->db->where('dignity_blogs_approved', true);
		$CI->db->where('dignity_blogs_comuser_id', $onepage['comusers_id']);
		$topics_in_blogs = $CI->db->count_all_results();

		// узнаём количество комментарий
		$CI->db->join('dignity_blogs', 'dignity_blogs.dignity_blogs_id = dignity_blogs_comments.dignity_blogs_comments_thema_id');
		$CI->db->from('dignity_blogs_comments');
		$CI->db->where('dignity_blogs_comments_approved', true);
		$CI->db->where('dignity_blogs_comuser_id', $onepage['comusers_id']);
		$CI->db->order_by('dignity_blogs_comuser_id', $onepage['comusers_id']);
		$comments_in_blogs = $CI->db->count_all_results();

		// узнаём количество просмотров тем (позже реализую)
		#$CI->db->from('dignity_blogs');
		#$CI->db->where('dignity_blogs_approved', true);
		#$CI->db->where('dignity_blogs_comuser_id', $onepage['comusers_id']);
		#$all_topics_views = $CI->db->count_all_results();

		// путь к картинкам
		$path = getinfo('plugins_url') . 'dignity_blogs/img/';

		$hide_no_blog_name = '';
		if ($options['no_blog_name'])
		{
			$hide_no_blog_name = t('Блог им. ', __FILE__);
		}

       	$out .= '<span style="width: 50%; float:left;">' . '<a href="' . getinfo('site_url') . $options['slug'] . '/blog/' . $onepage['dignity_blogs_comuser_id'] . '">' 
			. $hide_no_blog_name . $onepage['comusers_nik'] . '</a>' . '</span>';
       	$out .= '<span style="width: 20%; float:left;">' . $topics_in_blogs . '</span>';
       	$out .= '<span style="width: 20%; float:left;">' . $comments_in_blogs . '</span>';
       	$out .= '<span style="width: 10%; float:left;"><a href="' . getinfo('site_url') . $options['slug'] . '/feed/' . $onepage['dignity_blogs_comuser_id'] . '"><img src="' . $path . 'feed.png' . '" alt="" title="RSS лента"></a></span>';
	}
	
	echo $out;
	
	mso_hook('pagination', $pag);
}
else
{
	echo t('Нет блогов.', __FILE__);
}

echo '<div class="clearfix"></div>';

// конец шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);

#end of file