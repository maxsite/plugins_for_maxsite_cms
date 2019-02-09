<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_blogs (github)
 * License GNU GPL 2+
 */

echo '<h1>' . t('Блоги', __FILE__) . '</h1>';
echo '<p class="info">' . t('Редактировать статью.', __FILE__) . '</p>';

// админ-меню
echo '<div class="admin-h-menu">';
	$plugin_url = getinfo('site_admin_url') . 'dignity_blogs';
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройки', __FILE__), 'select') . ' | ';
	$a  .= mso_admin_link_segment_build($plugin_url, 'edit_comments', t('Комментарии', __FILE__), 'select') . ' | ';
	$a  .= mso_admin_link_segment_build($plugin_url, 'edit_article', t('Статьи', __FILE__), 'select');
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
						t('Тема', __FILE__),    
						t('Анонс', __FILE__),
						t('Опубликован', __FILE__),
						t('Избранное', __FILE__)
			);

// выводим данные из базы
$CI->db->from('dignity_blogs');
$CI->db->order_by('dignity_blogs_datecreate', 'desc');
$CI->db->join('comusers', 'comusers.comusers_id = dignity_blogs.dignity_blogs_comuser_id', 'left');
$query = $CI->db->get();

if ($query->num_rows() > 0)	
{	
	$all_reply = $query->result_array();
	
	foreach ($all_reply as $one_reply) 
	{
		
		// автор
		$autor = '';
		if ($one_reply['dignity_blogs_comuser_id'])
		{
			$autor = $one_reply['comusers_nik'];
		}
		
		// опубликована
		$public = '';
		if ($one_reply['dignity_blogs_approved'])
		{
			$public = 'Да';
		}
		else
		{
			$public = 'Нет';	
		}

		// избранное
		$favorite = '';
		if ($one_reply['dignity_blogs_ontop'])
		{
			$favorite = 'Да';
		}
		else
		{
			$favorite = 'Нет';	
		}
		
		// обрезаем текст после 10 слов
		$reply_text = mso_str_word($one_reply['dignity_blogs_cuttext'], $counttext = 10);

		// добавляем столбцы
		$CI->table->add_row(
				$one_reply['dignity_blogs_id'],
				'<a title="' . t('Изменить', __FILE__) . '" href="' . getinfo('site_admin_url')
					. 'dignity_blogs/editone_article/' . $one_reply['dignity_blogs_id'] . '">'
					. t('Изменить', __FILE__) . '</a>',
				$autor,	
				$one_reply['dignity_blogs_title'],
				$reply_text,
				$public,
				$favorite
				);
	}
	
	// генерируем таблицу
	echo $CI->table->generate();
	
}
// выводим ошибку
else echo t('Нет статей.', __FILE__);

# конец файла
