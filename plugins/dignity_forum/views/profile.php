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

// показывать или скрывать сайдбар?
$forum->hide_sidebar();

// меню
$forum->menu();

$CI->load->library('table');

$tmpl = array (
		'table_open' => '<table class="forum" border="0" width="100%">',
		'row_alt_start' => '<tr class="alt">',
		'cell_alt_start' => '<td class="alt" style="vertical-align: top;">',
		'cell_start' => '<td style="vertical-align: top;">',
		);

$CI->table->set_template($tmpl);
$CI->table->set_heading(
						'#',
						t('Имя пользователя', __FILE__),
						t('Зарегистрирован', __FILE__), 
						t('Тем', __FILE__), 
						t('Сообщений', __FILE__),
						t('Всего сообщений', __FILE__)
						);

// готовим пагинацию для ответов
$pag = array();
$pag['limit'] = 25;
$CI->db->from('comusers');
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

// выводим данные из базы
$CI->db->from('comusers');
#$CI->db->order_by('comusers_last_visit', 'desc');
$CI->db->order_by('comusers_id', 'asc');	
if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
else $CI->db->limit($pag['limit']);		
$query = $CI->db->get();

// если есть что выводить
if ($query->num_rows() > 0)	
{	
	$categorys = $query->result_array();
	
	foreach ($categorys as $category) 
	{
		// количество ответов комюзера
		$CI->db->from('dignity_forum_reply');
		$CI->db->where('dignity_forum_reply_comusers_id', $category['comusers_id']);
		$count_reply = $CI->db->count_all_results();
	
		// количество тем комюзера
		$CI->db->from('dignity_forum_topic');
		$CI->db->where('dignity_forum_topic_сomusers_id', $category['comusers_id']);
		$count_topic = $CI->db->count_all_results();
		
		// количество ответов + количество тем
		$count_all = $count_reply + $count_topic;
		
		$comusers_url = '<a href="' . getinfo('siteurl') . 'users/' . $category['comusers_id'] . '" rel="nofollow" target="_blank">' . $category['comusers_nik'] . '</a>';
		
		$CI->table->add_row(
				$category['comusers_id'],
				$comusers_url,	
				mso_date_convert($format = 'd.m.Y, H:i', $category['comusers_date_registr']),
				$count_topic,
				$count_reply,
				$count_all
				);
	}
	
	echo '<h1>' . t('Пользователи форума', __FILE__) . '</h1>';
	
	echo $CI->table->generate();
    
    // добавляем пагинацию
	mso_hook('pagination', $pag);
	
}
else echo t('Нет пользователей для отображения.', __FILE__);

// загружаем конец шаблона
require(getinfo('shared_dir') . 'main/main-end.php');
	  

#end of file
