<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_blogs (github)
 * License GNU GPL 2+
 */

// начало шаблона
if ($fn = mso_find_ts_file('main/main-start.php')) require($fn);

require_once(getinfo('plugins_dir') . 'dignity_blogs/core/functions.php');
$blogs = new Blogs;

// выводим меню
$blogs->menu();

if (is_login_comuser())
{

	// загружаем опции
	$options = mso_get_option('plugin_dignity_blogs', 'plugins', array());
	if ( !isset($options['limit']) ) $options['limit'] = 10;
	if ( !isset($options['slug']) ) $options['slug'] = 'blogs';

	// получаем доступ к CI
	$CI = & get_instance();
		
	// загружаем библиотеку таблицы
	$CI->load->library('table');

	// массив с таблицей
	$tmpl = array (
			'table_open' => '<table class="page" border="0" width="100%">',
			'row_alt_start' => '<tr class="alt">',
			'cell_alt_start' => '<td class="alt" style="vertical-align: top;">',
			'cell_start' => '<td style="vertical-align: top;">',
			);

	$CI->table->set_template($tmpl);

	// заголовки
	$CI->table->set_heading( 
							t('Заголовок', __FILE__),
							t('Дата', __FILE__),
							t('Статус', __FILE__)
				);

	// проверка сегмента
	$id = mso_segment(3);
	if (!is_numeric($id)) $id = false;
	else $id = (int) $id;

	if ($id && $id == getinfo('comusers_id'))
	{
		// готовим пагинацию статей
		$pag = array();
		$pag['limit'] = $options['limit'];
		$CI->db->from('dignity_blogs');
		$CI->db->select('dignity_blogs_id');
		$CI->db->where('dignity_blogs_comuser_id', $id);
		$CI->db->where('dignity_blogs_approved', true);
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
		$CI->db->from('dignity_blogs');
		$CI->db->where('dignity_blogs_comuser_id', $id);
		$CI->db->order_by('dignity_blogs_datecreate', 'desc');
		if ($pag and $offset) $CI->db->limit($pag['limit'], $offset);
		else $CI->db->limit($pag['limit']);
		$query = $CI->db->get();

		if ($query->num_rows() > 0)	
		{	
			$all_reply = $query->result_array();
			
			foreach ($all_reply as $one_reply) 
			{
				
				// опубликована
				$public = '';
				if ($one_reply['dignity_blogs_approved'])
				{
					$public = 'Опубликован';
				}
				else
				{
					$public = 'Черновик';	
				}
				
				$title_and_text = '<p><a href="' . getinfo('site_url') . $options['slug'] . '/edit/' . $one_reply['dignity_blogs_id'] . '" title="редактировать">' . $one_reply['dignity_blogs_title'] . '</a></p>';
				$title_and_text .= '<p>[<a href="' . getinfo('site_url') . $options['slug'] . '/view/' . $one_reply['dignity_blogs_id'] . '" target="_blank">' . t('Просмотр', __FILE__) . '</a>] [<a href="' . getinfo('site_url') . $options['slug'] . '/edit/' . $one_reply['dignity_blogs_id'] . '" target="_blank">' . t('Редактировать', __FILE__) . '</a>]</p>'; 
				$title_and_text .= '<p>' . mso_str_word($one_reply['dignity_blogs_cuttext'], $counttext = 10) . '...</p>';

				// добавляем столбцы
				$CI->table->add_row(
						$title_and_text,
						$one_reply['dignity_blogs_datecreate'],
						$public
						);
			}
			
			// выводим пагинацию
			mso_hook('pagination', $pag);

			// генерируем таблицу
			echo $CI->table->generate();

			// выводим пагинацию
			mso_hook('pagination', $pag);
			
		}
		// выводим ошибку
		else echo t('У вас нет статей. Опубликуйте вашу первую статью!', __FILE__);

	}
	else
	{
		echo '<script>document.location.href="' . getinfo('site_url') . $options['slug'] . '/my/' . getinfo('comusers_id') . '";</script>';
	}

}
else
{
	echo '<h1>' . t('404. Ничего не найдено...') . '</h1>';
	echo '<p>' . t('Извините, ничего не найдено') . '</p>';
	echo mso_hook('page_404');
}

// конец шаблона
if ($fn = mso_find_ts_file('main/main-end.php')) require($fn);

#end of file
