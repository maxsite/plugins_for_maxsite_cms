<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// загружаем начало шаблона
require(getinfo('shared_dir') . 'main/main-start.php');
	  

// доступ к CI
$CI = & get_instance();

require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;

// скрывать сайтбар
$forum->hide_sidebar();

// меню
$forum->menu();

// загружаем опции и присваиваем значения по умолчанию
$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'forum';

$id = mso_segment(3);
// проверка
if (!is_numeric($id)) $id = false; // не число
else $id = (int) $id;

// готовим пингацию
$pag = array();
$pag['limit'] = 10;
$CI->db->select('dignity_forum_topic_id');
$CI->db->from('dignity_forum_topic');
if (mso_segment(3) && mso_segment(3) != 'next')
{
	if (mso_segment(3) == getinfo('comusers_id'))
	{
		$CI->db->where('dignity_forum_topic_сomusers_id', getinfo('comusers_id'));
	}
	elseif (mso_segment(3) == getinfo('users_id'))
	{
		$CI->db->where('dignity_forum_topic_users_id', getinfo('users_id'));
	}
	else
	{
		if ($id)
		{
			$CI->db->where('dignity_forum_topic_сomusers_id', $id);
		}
	}
	
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

// берём темы из базы
$CI->db->from('dignity_forum_topic');
$CI->db->order_by('dignity_forum_topic_datecreate', 'desc');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_forum_topic.dignity_forum_topic_сomusers_id', 'left');
$CI->db->join('users', 'users.users_id = dignity_forum_topic.dignity_forum_topic_users_id', 'left');
if (mso_segment(3) && mso_segment(3) != 'next')
{
	if (mso_segment(3) == getinfo('comusers_id'))
	{
		$CI->db->where('dignity_forum_topic_сomusers_id', getinfo('comusers_id'));
	}
	elseif (mso_segment(3) == getinfo('users_id'))
	{
		$CI->db->where('dignity_forum_topic_users_id', getinfo('users_id'));
	}
	else
	{
		if ($id)
		{
			$CI->db->where('dignity_forum_topic_сomusers_id', $id);
		}
	}
	
}
if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
else $CI->db->limit($pag['limit']);
$query = $CI->db->get();

// если есть что выводить
if ($query->num_rows() > 0)	
{
	$out = '';
	$topic_count = '';

	if (!mso_segment(3))
	{
		$out .= '<div class="forum_header_topic">';
		$out .= '<h1>' . t('Новые темы на форуме', __FILE__) . '</h1>';
		$out .= '</div>';
	}
	elseif (mso_segment(3) == getinfo('comusers_id') || getinfo('users_id'))
	{
		$out .= '<div class="forum_header_topic">';
		$out .= '<h1>' . t('Ваши темы', __FILE__) . '</h1>';
		$out .= '</div>';
	}
	else
	{
		$out .= '<div class="forum_header_topic">';
		$out .= '<h1>' . t('Новые темы пользователя', __FILE__) . '</h1>';
		$out .= '</div>';
	}
	
	foreach ($query->result_array() as $topic) 
	{

		// определяем комюзера, написавшего новую тему
		$topic_nik = '';
		if ($topic['dignity_forum_topic_сomusers_id'])
		{
			$topic_nik = $topic['comusers_nik'];
			$topic_site = getinfo('siteurl') . 'users/' . $topic['dignity_forum_topic_сomusers_id'];
		}
		else
		{
			$topic_nik = $topic['users_nik'];
			$topic_site = getinfo('siteurl') . 'author/' . $topic['dignity_forum_topic_сomusers_id'];
		}
		
		$topic_count = $topic_count + 1;

		$out .= '<p>';
		$out .= '<strong>' . $topic_count . '.</strong> <a href="' . getinfo('siteurl') . $options['slug'] . '/topic/' . $topic['dignity_forum_topic_id'] . '">' . $topic['dignity_forum_topic_subject'] . '</a><br>';
		$out .= t('от', __FILE__) . ' <a href="' . $topic_site . '">' . $topic_nik . '</a> ' . t('в', __FILE__) . ' ' . mso_date_convert($format = 'H:i → d.m.Y', $topic['dignity_forum_topic_datecreate']);
		$out .= '</p>';
	}
	
	echo $out;
	
	// хук на пагинацию
	mso_hook('pagination', $pag);
}
elseif (mso_segment(3) == getinfo('comusers_id') || mso_segment(3) == getinfo('users_id'))
{
	echo t('Создайте вашу первую тему на форуме.', __FILE__);
}
elseif (mso_segment(3))
{
	echo t('У пользователя нет тем.', __FILE__);
}
else
{
	echo t('Новых тем нет.', __FILE__);
}


// выводим конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file
