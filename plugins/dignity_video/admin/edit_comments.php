<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_video (github)
 * License GNU GPL 2+
 */

echo '<h1>' . t('Видео', __FILE__) . '</h1>';
echo '<p class="info">' . t('Список комментарий.', __FILE__) . '</p>';

// админ-меню
echo '<div class="admin-h-menu">';
	$plugin_url = getinfo('site_admin_url') . 'dignity_video';
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройки', __FILE__), 'select') . ' | ';
	$a  .= mso_admin_link_segment_build($plugin_url, 'edit_comments', t('Комментарии', __FILE__), 'select') . ' | ';
	$a  .= mso_admin_link_segment_build($plugin_url, 'edit_video', t('Видео', __FILE__), 'select');
	echo $a;
echo '</div>';

// получаем доступ к CI
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
$CI->table->set_heading(
						'id', 
						t('Опции', __FILE__),
						t('Автор', __FILE__), 
						t('Названия видео', __FILE__),    
						t('Комментарий', __FILE__),
						t('Опубликован', __FILE__)
			);

// выводим данные из базы
$CI->db->from('dignity_video_comments');
$CI->db->order_by('dignity_video_comments_datecreate', 'desc');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_video_comments.dignity_video_comments_comuser_id', 'left');
$CI->db->join('dignity_video', 'dignity_video.dignity_video_id = dignity_video_comments.dignity_video_comments_thema_id', 'left');
$query = $CI->db->get();

if ($query->num_rows() > 0)	
{	
	$all_reply = $query->result_array();
	
	foreach ($all_reply as $one_reply) 
	{
		
		// автор
		$autor = '';
		if ($one_reply['dignity_video_comments_comuser_id'])
		{
			$autor = $one_reply['comusers_nik'];
		}
		
		// автор
		$public = '';
		if ($one_reply['dignity_video_comments_approved'])
		{
			$public = 'Да';
		}
		else
		{
			$public = 'Нет';	
		}
		
		// обрезаем текст после 10 слов
		$reply_text = mso_str_word($one_reply['dignity_video_comments_text'], $counttext = 10);

		// добавляем столбцы
		$CI->table->add_row(
				$one_reply['dignity_video_comments_id'],
				'<a title="' . t('Изменить', __FILE__) . '" href="' . getinfo('site_admin_url')
					. 'dignity_video/editone_comment/' . $one_reply['dignity_video_comments_id'] . '">'
					. t('Изменить', __FILE__) . '</a>',
				$autor,	
				$one_reply['dignity_video_title'],
				$reply_text,
				$public
				);
	}
	
	// генерируем таблицу
	echo $CI->table->generate();
	
}
// выводим ошибку
else echo t('Нет комментарий.', __FILE__);

# конец файла
