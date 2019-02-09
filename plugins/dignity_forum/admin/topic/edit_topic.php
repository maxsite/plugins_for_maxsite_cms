<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// заголовок админки и подсказка
echo '<h1>' . t('Форум', __FILE__) . '</h1>';
echo '<p class="info">' . t('Редактировать темы', __FILE__) . '</p>';

// загружаем меню
echo '<div class="admin-h-menu">';
require_once(getinfo('plugins_dir') . 'dignity_forum/core/functions.php');
$forum = new Forum;
$forum->admin_menu();
echo '</div>';

if ( !isset($options['slug']) ) $options['slug'] = 'forum'; 

echo '<p><a href="' . getinfo('siteurl') . $options['slug'] . '" target="_blank">' . t('Перейти на форум →', __FILE__) . '</a></p>';

// доступ к CI
$CI = & get_instance();

// загружаем библиотеку таблицы
$CI->load->library('table');

// формируем массив с таблицей
$tmpl = array (
		'table_open' => '<br><table class="page" border="0" width="100%">',
		'row_alt_start' => '<tr class="alt">',
		'cell_alt_start' => '<td class="alt" style="vertical-align: top;">',
		'cell_start' => '<td style="vertical-align: top;">',
		);

$CI->table->set_template($tmpl);

// заголовки
$CI->table->set_heading(
			'id',
			t('Опции', __FILE__),
			t('Категория', __FILE__),
			t('Название', __FILE__),
			t('Автор темы', __FILE__),
			t('Опубликована', __FILE__),
			t('Статус', __FILE__)
			);

// выводим данные из базы
$CI->db->from('dignity_forum_topic');
$CI->db->order_by('dignity_forum_topic_datecreate', 'desc');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_forum_topic.dignity_forum_topic_сomusers_id', 'left');
$CI->db->join('users', 'users.users_id = dignity_forum_topic.dignity_forum_topic_users_id', 'left');
$CI->db->join('dignity_forum_category', 'dignity_forum_category.dignity_forum_category_id = dignity_forum_topic.dignity_forum_topic_category', 'left');
$query = $CI->db->get();

// если есть что выводить
if ($query->num_rows() > 0)	
{	
	$all_topics = $query->result_array();
	
	foreach ($all_topics as $one_topic) 
	{
		// статус темы
		$status = '';
		if ($one_topic['dignity_forum_topic_closed'])
		{
			$status = t('Закрытая тема', __FILE__);
		}
		else
		{
			$status = t('Открытая тема', __FILE__);	
		}
		
		// автор
		$autor = '';
		if ($one_topic['dignity_forum_topic_сomusers_id'])
		{
			$autor = $one_topic['comusers_nik'];
		}
		elseif ($one_topic['dignity_forum_topic_users_id'])
		{
			$autor = $one_topic['users_nik'];	
		}
		
		// категория
		$category = $one_topic['dignity_forum_category_name'];
		
		// автор
		$public = '';
		if ($one_topic['dignity_forum_topic_approved'])
		{
			$public = t('Да', __FILE__);
		}
		else
		{
			$public = t('Нет', __FILE__);	
		}

		$CI->table->add_row(
				$one_topic['dignity_forum_topic_id'],
				'<a title="' . t('Изменить', __FILE__) . '" href="' . getinfo('site_admin_url') . 'dignity_forum/editone_topic/' . $one_topic['dignity_forum_topic_id'] . '">' . t('Изменить', __FILE__) . '</a>',	
				$category,
				$one_topic['dignity_forum_topic_subject'],
				$autor,
				$public,
				$status
				);
	}
	
	// генерируем таблицу и выводим
	echo $CI->table->generate();
}
// выводи ошибку
else echo t('Нет тем для отображения.', __FILE__);

#end of file