<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_blogs (github)
 * License GNU GPL 2+
 */

# функция, которая берет настройки из опций виджетов
function dignity_blogs_category_widget($num = 1) 
{
	$widget = 'dignity_blogs_category_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	return dignity_blogs_category_widget_custom($options, $num);
}

# функции плагина
function dignity_blogs_category_widget_custom($options = array(), $num = 1)
{
	// получаем доступ к CI
	$CI = & get_instance();
	
	$out = '';
	
	// загружаем опции
	$options = mso_get_option('plugin_blogs_plugins', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'blogs';
	
	// добавляем заголовок «категории»
	$out .= mso_get_val('widget_header_start', '<h2 class="box"><span>') . t('Категории', __FILE__) . mso_get_val('widget_header_end', '</span></h2>');
	
	// берём категори из базы
	$CI->db->from('dignity_blogs_category');
	$CI->db->order_by('dignity_blogs_category_position', 'asc');
	$query = $CI->db->get();
	
	// если есть что выводить
	if ($query->num_rows() > 0)	
	{	
		
        $catout = '';
		
		foreach ($query->result_array() as $entry) 
		{
			// узнаем количество записей в категории
			$CI->db->where('dignity_blogs_approved', true);
			$CI->db->where('dignity_blogs_category', $entry['dignity_blogs_category_id']);
			$CI->db->from('dignity_blogs');
			$entry_in_cat = $CI->db->count_all_results();
			
			// если есть записи в категории
			if ($entry_in_cat > 0)
			{
				// выводим названия категории и количество записей в ней
				$catout .= '<li><a href="' . getinfo('siteurl') . $options['slug'] . '/category/'
				    . $entry['dignity_blogs_category_id'] . '">' . $entry['dignity_blogs_category_name'] . '</a>' . ' (' . $entry_in_cat . ') ' . '</li>';
			}
		}
		
		// начиаем новый список
		$out .= '<ul>';
		
		// выводим назавания категорий и количетсов записей
		$out .= $catout;
		
		// количетсов записей всего
		$CI->db->where('dignity_blogs_approved', true);
		$CI->db->from('dignity_blogs');
		$all_entry_in_cat = $CI->db->count_all_results();
		
		// добавляем ссылку «все записи»
		$out .= '<li><a href="' . getinfo('site_url') . $options['slug'] . '/' . '">' . t('Все записи', __FILE__) . '</a>' . ' (' . $all_entry_in_cat . ') ' . '</li>';
		
		// заканчиваем список
		$out .= '</ul>';
	}
	
	return $out;	
}

#end of file
