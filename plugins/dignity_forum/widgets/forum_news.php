<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

# функция, которая берет настройки из опций виджетов
function dignity_forum_widget($num = 1) 
{
	$widget = 'dignity_forum_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	return dignity_forum_widget_custom($options, $num);
}

# функции плагина
function dignity_forum_widget_custom($options = array(), $num = 1)
{
	/*
	// кэш 
	$cache_key = 'dignity_forum_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	*/
	
	// получаем доступ к CI
	$CI = & get_instance();
	
	$out = '';
	
	// загружаем опции
	$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'forum';
	if ( !isset($options['forum_news_title']) ) $options['forum_news_title'] = t('Недавно обновленные темы на форуме', __FILE__);
	
	// добавляем заголовок «Новое на форуме»
	$out .= mso_get_val('widget_header_start', '<h2 class="box"><span>')
		. $options['forum_news_title'] . mso_get_val('widget_header_end', '</span></h2>');
	
	// берём данные из базы
	$CI->db->from('dignity_forum_topic');
	$CI->db->order_by('dignity_forum_topic_dateupdate', 'desc');
	$CI->db->limit(10);
	$query = $CI->db->get();
	
	// если есть что выводить
	if ($query->num_rows() > 0)	
	{	
		$topics = $query->result_array();
		
		$out .= '<ul>';
		
		foreach ($topics as $topic) 
		{
		
			$out .= '<li><a href="' . getinfo('siteurl') . $options['slug'] . '/topic/' . $topic['dignity_forum_topic_id'] . '">' . $topic['dignity_forum_topic_subject'] . '</a></li>';
		
		}
		
		$out .= '</ul>';
		
		$out .= '<p><a href="' . getinfo('siteurl') . $options['slug'] . '">' . t('Перейти на форум', __FILE__) . '»</a></p>';
		
	}
	else
	{
		$out .= t('Нет новых тем.', __FILE__);
	}
	
	#mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;	
}

#end of file
