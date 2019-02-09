<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// заголовок админки и подсказка
echo '<h1>' . t('Форум', __FILE__) . '</h1>';
echo '<p class="info">' . t('Редактировать ответы', __FILE__) . '</p>';

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

// массив с таблицей
$tmpl = array (
		'table_open' => '<br><table class="page" border="0" width="100%">',
		'row_alt_start' => '<tr class="alt">',
		'cell_alt_start' => '<td class="alt" style="vertical-align: top;">',
		'cell_start' => '<td style="vertical-align: top;">',
		);

$CI->table->set_template($tmpl);

// заголовки
$CI->table->set_heading('id', t('Опции', __FILE__), t('Тема', __FILE__), t('Автор', __FILE__),  t('Опубликован', __FILE__), t('Комментарий', __FILE__));

// выводим данные из базы
$CI->db->from('dignity_forum_reply');
$CI->db->order_by('dignity_forum_reply_datecreate', 'desc');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_forum_reply.dignity_forum_reply_comusers_id', 'left');
$CI->db->join('users', 'users.users_id = dignity_forum_reply.dignity_forum_reply_users_id', 'left');
$CI->db->join('dignity_forum_topic', 'dignity_forum_topic.dignity_forum_topic_id = dignity_forum_reply.dignity_forum_reply_topic_id', 'left');
$query = $CI->db->get();

if ($query->num_rows() > 0)	
{	
	$all_reply = $query->result_array();
	
	foreach ($all_reply as $one_reply) 
	{
		
		// автор
		$autor = '';
		if ($one_reply['dignity_forum_reply_comusers_id'])
		{
			$autor = $one_reply['comusers_nik'];
		}
		elseif ($one_reply['dignity_forum_reply_users_id'])
		{
			$autor = $one_reply['users_nik'];	
		}
		
		// автор
		$public = '';
		if ($one_reply['dignity_forum_reply_approved'])
		{
			$public = 'Да';
		}
		else
		{
			$public = 'Нет';	
		}
		
		// обрезаем текст после 10 слов
		$reply_text = mso_str_word($one_reply['dignity_forum_reply_text'], $counttext = 10);

		// добавляем столбцы
		$CI->table->add_row(
				$one_reply['dignity_forum_reply_id'],
				'<a title="' . t('Изменить', __FILE__) . '" href="' . getinfo('site_admin_url')
					. 'dignity_forum/editone_reply/' . $one_reply['dignity_forum_reply_id'] . '">'
					. t('Изменить', __FILE__) . '</a>',	
				$one_reply['dignity_forum_topic_subject'],
				$autor,
				$public,
				$reply_text
				);
	}
	
	// генерируем таблицу
	echo $CI->table->generate();
	
}
// выводим ошибку
else echo t('Нет ответов для отображения.', __FILE__);

#end of file