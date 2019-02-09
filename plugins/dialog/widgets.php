<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

// виджеты: меток, дискуссий,

 // виджет дискуссий форума_____________________________________________________________


# функция, которая берет настройки из опций виджетов
function dialog_disc_widget($num = 1)
{
	$widget = 'dialog_disc_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return dialog_disc_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function dialog_disc_widget_form($num = 1) 
{
	$widget = 'dialog_disc_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['limit']) ) $options['limit'] = 3;
	if ( !isset($options['format']) ) $options['format'] = '<li>[A][TITLE][/A]</li>';
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="last-pages"><ul class="is_link">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</ul></div>';	
	
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Количество дискуссий:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'limit', 'value'=>$options['limit'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Формат:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ) ;
	
	$form .= '<p><div class="t150">&nbsp;</div><strong>[TITLE]</strong> - ' . t('заголовок дискуссий', 'plugins');
	$form .= '<br><div class="t150">&nbsp;</div><strong>[A]</strong>' . t('ссылка', 'plugins') . '<strong>[/A]</strong></p>';
	
	$form .= '<p><div class="t150">' . t('Начало блока:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'block_start', 'value'=>$options['block_start'] ) );
	
	$form .= '<p><div class="t150">' . t('Конец блока:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'block_end', 'value'=>$options['block_end'] ) );
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function dialog_disc_widget_update($num = 1) 
{
	$widget = 'dialog_disc_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['limit'] = (int) mso_widget_get_post($widget . 'limit');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['block_start'] = mso_widget_get_post($widget . 'block_start');
	$newoptions['block_end'] = mso_widget_get_post($widget . 'block_end');
		
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}


# функции плагина
function dialog_disc_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'dialog_disc_widget_custom'. serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	$dialog_options = mso_get_option('dialog', 'plugins', array());
	if ( !isset($dialog_options['discussion_slug']) ) $dialog_options['discussion_slug'] = 'discussion';

	
	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['limit']) ) $options['limit'] = 3;
	if ( !isset($options['format']) ) $options['format'] = '<li>[A][TITLE][/A]</li>';
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="last-pages"><ul class="is_link">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</ul></div>';

	
	
	$CI = & get_instance();
	
	$CI->db->select('discussion_title , discussion_id');
	$CI->db->order_by('discussion_date_last_active', 'desc');
	$CI->db->limit($options['limit']);

	  // присоединим автора
	$CI->db->join('dprofiles', 'dprofiles.profile_user_id = ddiscussions.discussion_creator_id');
	$CI->db->where('dprofiles.profile_spam_check', '0');
	
	$query = $CI->db->get('ddiscussions');
	
	if ($query->num_rows() > 0)	
	{	
		$discussions = $query->result_array();
		
		$link = '<a href="' . getinfo('siteurl') . $dialog_options['discussion_slug'] . '/';
		$out .= $options['block_start'] . NR;
		foreach ($discussions as $discussion) 
		{
				$out1 = $options['format'];

				$out1 = str_replace('[TITLE]', $discussion['discussion_title'], $out1);
				
				$out1 = str_replace('[A]', $link . $discussion['discussion_id'] . '" title="Дискуссия">' , $out1);
						
				$out .= str_replace('[/A]', '</a>', $out1) . NR;
		}
		
		$out .= $options['block_end'] . NR;
		if ($options['header']) $out = $options['header'] . $out;
	}
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;	
}

 // виджет меток форума_____________________________________________________________

# функция, которая берет настройки из опций виджетов
function dialog_tagclouds_widget($num = 1) 
{
	$widget = 'dialog_tagclouds_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return dialog_tagclouds_widget_custom($options, $num);
}



# форма настройки виджета 
# имя функции = виджет_form
function dialog_tagclouds_widget_form($num = 1) 
{
	$widget = 'dialog_tagclouds_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="tagclouds">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';
	
	if ( !isset($options['min_size']) ) $options['min_size'] = 90;
		else $options['min_size'] = (int) $options['min_size'];
		
	if ( !isset($options['max_size']) ) $options['max_size'] = 230;
		else $options['max_size'] = (int) $options['max_size'];
		
	if ( !isset($options['max_num']) ) $options['max_num'] = 50;
		else $options['max_num'] = (int) $options['max_num'];
		
	if ( !isset($options['min_count']) ) $options['min_count'] = 0;
		else $options['min_count'] = (int) $options['min_count'];
		
	if ( !isset($options['format']) ) 
		$options['format'] = '<span style="font-size: %SIZE%%"><a href="%URL%">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>';
	
	if ( !isset($options['sort']) ) $options['sort'] = 0;
		else $options['sort'] = (int) $options['sort'];
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Формат:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ) ;
	$form .= '<br><div class="t150">&nbsp;</div> %SIZE% %URL% %TAG% %COUNT%';
	
	$form .= '<p><div class="t150">' . t('Мин. размер (%):', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'min_size', 'value'=>$options['min_size'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Макс. размер (%):', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'max_size', 'value'=>$options['max_size'] ) ) ;

	$form .= '<p><div class="t150">' . t('Макс. меток:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'max_num', 'value'=>$options['max_num'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Миним. меток:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'min_count', 'value'=>$options['min_count'] ) ) ;
	
	$form .= '<p><div class="t150">&nbsp;</div>' . t('Отображать только метки, которых более указанного количества. (0 - без ограничений)', 'plugins');

	$form .= '<p><div class="t150">' . t('Начало блока:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'block_start', 'value'=>$options['block_start'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Конец блока:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'block_end', 'value'=>$options['block_end'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Сортировка:', 'plugins') . '</div> '. form_dropdown($widget . 'sort', 
								array( '0'=>t('По количеству записей (обратно)', 'plugins'), 
										'1'=>t('По количеству записей', 'plugins'), 
										'2'=>t('По алфавиту', 'plugins'), 
										'3'=>t('По алфавиту (обратно)', 'plugins')), 
								$options['sort'] ) ;
	
	return $form;
}



# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function dialog_tagclouds_widget_update($num = 1) 
{
	$widget = 'dialog_tagclouds_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['block_start'] = mso_widget_get_post($widget . 'block_start');
	$newoptions['block_end'] = mso_widget_get_post($widget . 'block_end');
	$newoptions['min_size'] = mso_widget_get_post($widget . 'min_size');
	$newoptions['max_size'] = mso_widget_get_post($widget . 'max_size');
	$newoptions['max_num'] = mso_widget_get_post($widget . 'max_num');
	$newoptions['min_count'] = mso_widget_get_post($widget . 'min_count');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['sort'] = mso_widget_get_post($widget . 'sort');

	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}



function dialog_tagclouds_widget_custom($options = array(), $num = 1)
{
	// кэш 
	$cache_key = 'dialog_tagclouds_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	// формат вывода  %SIZE% %URL% %TAG% %COUNT% 
	// параметры $min_size $max_size $block_start $block_end
	// сортировка 
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['block_start']) ) $options['block_start'] = '<div class="tagclouds">';
	if ( !isset($options['block_end']) ) $options['block_end'] = '</div>';
	
	if ( !isset($options['min_size']) ) $min_size = 90;
		else $min_size = (int) $options['min_size'];
		
	if ( !isset($options['max_size']) ) $max_size = 230;
		else $max_size = (int) $options['max_size'];
		
	if ( !isset($options['max_num']) ) $max_num = 50;
		else $max_num = (int) $options['max_num'];
		
	if ( !isset($options['min_count']) ) $min_count = 0;
		else $min_count = (int) $options['min_count'];
		
	if ( !isset($options['format']) ) 
		$options['format'] = '<span style="font-size: %SIZE%%"><a href="%URL%">%TAG%</a><sub style="font-size: 7pt;">%COUNT%</sub></span>';
	
	if ( !isset($options['sort']) ) $sort = 0;
		else $sort = (int) $options['sort'];
		
	if (function_exists('dialog_get_all_tags') )
	    $tagcloud = dialog_get_all_tags();
	else
	{
	  $CI = & get_instance();

	  $CI->db->select('meta_value, COUNT(meta_value) AS meta_count');
	  $CI->db->where( array (	'meta_key' => 'tags', 'meta_table' => 'ddiscussions' ) );
	  $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dmeta.meta_id_obj' );

	  $CI->db->where( 'discussion_approved', '1'); // только разрешенные
	  $CI->db->where( 'discussion_private', '0'); // только публичные

	  $CI->db->group_by('meta_value');
	  $query = $CI->db->get('dmeta');

		$tagcloud = array();
	  // переделаем к виду [метка] = кол-во
	  if ($query->num_rows() > 0)
	  {
		  foreach ($query->result_array() as $row)
			$tagcloud[$row['meta_value']] = $row['meta_count'];
	  }
	}
	
	asort($tagcloud);
	$min = reset($tagcloud);
    $max = end($tagcloud);
    
    if ($max == $min) $max++;
    
    // сортировка перед выводом
    if ($sort == 0) arsort($tagcloud); // по количеству обратно
    elseif ($sort == 1) asort($tagcloud); // по количеству 
    elseif ($sort == 2) ksort($tagcloud); // по алфавиту
    elseif ($sort == 3) krsort($tagcloud); // обратно по алфавиту
    else arsort($tagcloud); // по умолчанию
    
    $url = getinfo('siteurl') . 'tag/';
    $out = '';
    $i = 0;
    foreach ($tagcloud as $tag => $count) 
    {
		if ($min_count) 
			if ($count < $min_count) continue;

		$font_size = round( (($count - $min)/($max - $min)) * ($max_size - $min_size) + $min_size );
			
		$af = str_replace(array('%SIZE%', '%URL%', '%TAG%', '%COUNT%'), 
							array($font_size, $url . urlencode($tag), $tag, $count), $options['format']);
		
		// альтернативный синтаксис с []
		$af = str_replace(array('[SIZE]', '[URL]', '[TAG]', '[COUNT]'), 
							array($font_size, $url . urlencode($tag), $tag, $count), $af);

		$out .= $af . ' ';
		$i++;
		if ( $max_num != 0 and $i == $max_num ) break;
    }
	
	if ($out) $out = $options['header'] . $options['block_start'] . $out . $options['block_end'] ;
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}

?>