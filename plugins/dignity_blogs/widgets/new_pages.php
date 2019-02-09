<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_blogs (github)
 * License GNU GPL 2+
 */

# функция, которая берет настройки из опций виджетов
function dignity_blogs_new_widget($num = 1) 
{
	$widget = 'dignity_blogs_new_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	return dignity_blogs_new_widget_custom($options, $num);
}

# функции плагина
function dignity_blogs_new_widget_custom($options = array(), $num = 1)
{
	$out = '';
	
	// загружаем опции
	$options = mso_get_option('plugin_blog_plugins', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'blogs';
	
	// добавляем заголовок «Категории»
	$out .= mso_get_val('widget_header_start', '<h2 class="box"><span>') . t('Новые записи в блогах', __FILE__) . mso_get_val('widget_header_end', '</span></h2>');
        
	// получаем доступ к CI
	$CI = & get_instance();
	
	// берём данные из базы
	$CI->db->from('dignity_blogs');
	$CI->db->where('dignity_blogs_approved', true);
	$CI->db->limit(5);
	$CI->db->order_by('dignity_blogs_datecreate', 'desc');
	$query = $CI->db->get();
	
	// если есть что выводить
	if ($query->num_rows() > 0)	
	{	
		$entrys = $query->result_array();
		
		// обьявлем переменую
		$catout = '';
		
		foreach ($entrys as $entry) 
		{
			// выводим названия категории и количество записей в ней
			$catout .= '<li><a href="' . getinfo('siteurl') . $options['slug'] . '/view/'
                        . $entry['dignity_blogs_id'] . '">' . $entry['dignity_blogs_title'] . '</a>' . '</li>';
		}
		
		// начиаем новый список
		$out .= '<ul>';
		
		// выводим назавания категорий и количетсов записей
		$out .= $catout;
	
		// заканчиваем список
		$out .= '</ul>';
		
		$out .= '<a href="' . getinfo('siteurl') . $options['slug'] . '">' . t('Все записи»', __FILE__) . '</a>';
	}
	else
	{
		$out .= t('Новых записей нет.', __FILE__);
	}
	
	return $out;	
}

#end of file
