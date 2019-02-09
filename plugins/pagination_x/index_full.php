<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * modified by jen http://jenweb.info/
 */


function pagination_x_autoload($a = array()) 
{
	mso_hook_add('pagination', 'pagination_x_go', 10);
	return $a;
}


function pagination_x_go($r = array()) 
{
	global $MSO;
	
	$r_orig = $r; 
	
	if (!$r) return $r;
	if ( !isset($r['maxcount']) ) return $r;
	if ( !isset($r['limit']) ) return $r; // нужно указать сколько записей выводить
	if ( !isset($r['type']) )  $r['type'] = false; // можно задать свой тип
	
	$options = mso_get_option('plugin_pagination_x', 'plugins', array() ); // получаем опции
		
	if ( !isset($r['pre_text']) ) 
		$r['pre_text'] = isset($options['pre_text']) ? $options['pre_text'] : t('Страницы: ', 'plugins');
	
	if ( !isset($r['next_url']) ) 
		$r['next_url'] = isset($options['next_url']) ? strtolower($options['next_url']) : 'next';
	
	if ( !isset($r['range']) ) 
		$r['range'] = isset($options['range']) ? (int) $options['range'] : 3;
		
	if ( !isset($r['sep']) ) 
		$r['sep'] = isset($options['sep']) ? $options['sep'] : ' &middot; ';	
	
	if ( !isset($r['sep2']) ) 
		$r['sep2'] = isset($options['sep2']) ? $options['sep2'] : ' | ';
		
	
	
	if ( !isset($r['format']) )
	{	
		// $r['format'] =
		$r['format'][] = (isset($options['format_title']) and $options['format_title'] == '1') ?
			(isset($options['format_first']) and $options['format_first'] != '') ?
				' title="' . $options['format_first'] . '"' : ' title="' . t('первая', 'plugins') . '"' : '';
		$r['format'][] = (isset($options['format_title']) and $options['format_title'] == '1') ?
			(isset($options['format_prev']) and $options['format_prev'] != '') ?
				' title="' . $options['format_prev'] . '"' : ' title="' . t('предыдущая', 'plugins') . '"' : '';
		$r['format'][] = isset($options['format_first']) ? $options['format_first'] : t('первая', 'plugins');
		$r['format'][] = isset($options['format_prev']) ? $options['format_prev'] : t('предыдущая', 'plugins');
		$r['format'][] = isset($options['format_next']) ? $options['format_next'] : t('следующая', 'plugins');
		$r['format'][] = isset($options['format_last']) ? $options['format_last'] : t('последняя', 'plugins');
		$r['format'][] = (isset($options['format_title']) and $options['format_title'] == '1') ? 
			(isset($options['format_next']) and $options['format_next'] != '') ?
				' title="' . $options['format_next'] . '"' : ' title="' . t('предыдущая', 'plugins') . '"' : '';
		$r['format'][] = (isset($options['format_title']) and $options['format_title'] == '1') ? 
			(isset($options['format_last']) and $options['format_last'] != '') ?
				' title="' . $options['format_last'] . '"' : ' title="' . t('последняя', 'plugins') . '"' : '';
	}
	
	// текущая пагинация вычисляется по адресу url
	// должно быть /next/6 - номер страницы
	$current_paged = mso_current_paged($r['next_url']);
	
	if ($current_paged > $r['maxcount']) $current_paged = $r['maxcount'];
	
	if ($r['type'] !== false) $type = $r['type'];
		else $type = $MSO->data['type'];
	
	// текущий урл сделаем
	$a_cur_url = $MSO->data['uri_segment'];
	
	// $cur_url = getinfo('site_url') . $type;
	if ($type != 'page_404') $cur_url = getinfo('site_url') . $type;
		else $cur_url = getinfo('site_url');
	//pr($cur_url);
	
	foreach ($a_cur_url as $val)
	{
		//if ($val == 'next') break; // next - дальше не нужно
		if ($val == $r['next_url']) break; // next - дальше не нужно
		else
		{
			if ($val != $type) $cur_url .= '/@@' . $val;
		}
	}
	
	$cur_url = str_replace('//@@', '/', $cur_url);
	$cur_url = str_replace('@@', '', $cur_url);
	//pr($cur_url);
	
	if ($type == 'home') 
		$home_url = getinfo('site_url');
	else
		$home_url = $cur_url;
	//pr($home_url);
	
	// текст перед пагинацией
	if ($r['pre_text'] == '' or $r['pre_text'] == ' ') //случайно оставленный пробел
		$pre_text = '';
	else
		$pre_text = '<span class="pagination-text">' . $r['pre_text'] . '</span> ';	
	//pr($pre_text);
	
	$out = _pagination( $pre_text,
						$r['maxcount'],
						$current_paged, 
						$cur_url . '/' . $r['next_url'] . '/', 
						$r['range'], 
						$cur_url,
						'',
						$r['sep'],
						$home_url,
						$r['sep2']
						);
	
	if ($out)
	{
		$out = str_replace(
				array('%TITLE_FIRST%', '%TITLE_PREV%', '%FIRST%', '%PREV%', '%NEXT%', '%LAST%', '%TITLE_NEXT%', '%TITLE_LAST%'),
				$r['format'],
				$out);
		
		echo NR . '<div class="pagination">' . $out . '</div>' . NR;
	}
	
	return $r_orig;
}


function _pagination($pre_text, $max, $page_number, $base_url, $diappazon = 4, $url_first = '', $page_u = '', $sep = ' &middot; ', $home_url = '', $sep2 = ' | ') 
{
	// (c) http://www.ben-griffiths.com/php-pagination-function/ ссылка битая! - jen
	// переделал MAX http://maxsite.org/
	// добавил текст перед пагинацией и изменил формат вывода jen http://jenweb.info
	
	if ($max < 2) return '';
	if ($page_number == null) $page_number = 1;
	if ($page_number > $max ) $page_number = $max;
	if ($diappazon < 3) $diappazon = 3;

	$total_pages = $max;
	$total_results_feedback = $max;
	
	$prev_link_page = $page_number - 1;
	$next_link_page = $page_number + 1;
	
	if ($prev_link_page < 1) $prev_link_page = 1;
	
	if ($next_link_page > $total_pages) $next_link_page = $total_pages;
	
	$middle_page_links = '';
	
	$pages_start = ($page_number - 3) + 1;
	
	if ($pages_start < 1) $pages_start = 1;
	//if ($pages_start == 2) $pages_start = 3;
	
	$count_to = $pages_start + $diappazon;
	
	if ($count_to > $total_pages) $count_to = $total_pages;
	
	$first_mid_link = '';
	$last_mid_link = '';
	
	for ($counter = $pages_start; $counter <= $count_to; $counter += 1) 
	{
		$page_link = $counter;
		//pr($counter);
			
		if ($counter != $page_number)
		{
			 
			if ($counter == 1) 
				$middle_page_links .= '<a class="pagination-link" href="' . $home_url . '">' . $counter . '</a>';
			else
				$middle_page_links .= '<a class="pagination-link" href="' . $base_url . $page_u . $page_link . '">' . $counter . '</a>';
			
			if ($counter < $count_to) $middle_page_links .= '<span style="display:none;">' . $sep . '</span>';
			
			if($first_mid_link == '') $first_mid_link = $page_link;
			
			$last_mid_link = $page_link;
		} 
		else 
		{
			$middle_page_links .= '<strong class="pagination-cur">' . $counter . '</strong>';
			if ($counter < $count_to) $middle_page_links .= '<span style="display:none;">' . $sep . '</span>';
		}
	}
	//pr($first_mid_link); pr($last_mid_link); pr($page_number);
	
	if ($page_number == 1 or $total_pages < 4)
	{
		$first_link = '<span class="pagination-first-nogo"%TITLE_FIRST%><span style="display:none;">%FIRST%</span></span><span style="display:none;">' . $sep . 
			'</span><span class="pagination-prev-nogo"%TITLE_PREV%><span style="display:none;">%PREV%</span></span><span style="display:none;">' . $sep2 . '</span>';
		$first_dots = '';
	} 
	else 
	{
		$prev = $page_number != 2 ? $base_url . $page_u . $prev_link_page : $home_url;
		
		$first_link =  '<a class="pagination-first" href="' . $home_url . '"%TITLE_FIRST%><span style="display:none;">%FIRST%</span></a><span style="display:none;">' . $sep . 
			'</span><a class="pagination-prev" href="' . $prev . '"%TITLE_PREV%><span style="display:none;">%PREV%</span></a><span style="display:none;">' . $sep2 . '</span>';
		
		if ($page_number == 4)
			$first_dots = '<a class="pagination-link pagination-start" href="' . $home_url . '">1</a><span style="display:none;">' . $sep . '</span>';
		else if ($page_number > 4)
			$first_dots = '<a class="pagination-link pagination-start" href="' . $home_url . '">1</a><span>&nbsp;&hellip;&nbsp;</span>';
		else 
			$first_dots = '';
	}
	
	if ($page_number == $total_pages or $total_pages < 4)
	{
		$last_link =  '<span style="display:none;">' . $sep2 . '</span><span class="pagination-next-nogo"%TITLE_NEXT%><span style="display:none;">%NEXT%</span></span>'
			 . $sep . '<span class="pagination-last-nogo"%TITLE_LAST%><span style="display:none;">%LAST%</span></span>';
		$last_dots = '';
	}
	else 
	{
		$last_link =  '<span style="display:none;">' . $sep2 . '</span><a class="pagination-next" href="' . $base_url . $page_u . $next_link_page . '"%TITLE_NEXT%><span style="display:none;">%NEXT%</span></a><span style="display:none;">' 
			. $sep . '</span><a  class="pagination-last" href="' . $base_url . $page_u . $total_pages . '"%TITLE_LAST%><span style="display:none;">%LAST%</span></a>';
		
		if ($last_mid_link == ($total_pages - 1))
			$last_dots = '<span style="display:none;">' . $sep . '</span><a class="pagination-link pagination-end" href="' . $base_url . $page_u . $total_pages . '">' . $total_pages . '</a> ';
		else if ($last_mid_link < ($total_pages - 1))
			$last_dots = '<span>&nbsp;&hellip;&nbsp;</span><a class="pagination-link pagination-end" href="' . $base_url . $page_u . $total_pages . '">' . $total_pages . '</a> ';
		else 
			$last_dots = '';
	}
	
	$output_page_link = $pre_text . $first_link . $first_dots . $middle_page_links. $last_dots . $last_link;

	if ($total_pages == -1)
		$output_page_link = $pre_text . '<strong class="pagination-cur">1</strong>';
	
	return $output_page_link;
}	


function pagination_x_mso_options() 
{
	// ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_pagination_x', 'plugins', 
		array(
			'pre_text' => array(
							'type' => 'text', 
							'name' => 'Текст перед пагинацией', 
							'description' => 'Например, "Страницы", или пустая строка', 
							'default' => 'Страницы:'
						),
			'next_url' => array(
							'type' => 'text', 
							'name' => 'Сегмент URL пагинации', 
							'description' => 'Только латница, дефисы и подчеркивания без пробелов. Пример: http://mysite/<strong style="color: #800">pages</strong>/22', 
							'default' => 'next'
						),
			'range' => array(
							'type' => 'text', 
							'name' => 'Диапазон количества ссылок', 
							'description' => 'Задайте количество отображаемых ссылок на страницы (3 и более).', 
							'default' => '3'
						),
			'format_first' => array(
							'type' => 'text', 
							'name' => 'Текст для «первая»', 
							'description' => '', 
							'default' => 'первая'
						),
			'format_prev' => array(
							'type' => 'text', 
							'name' => 'Текст для «предыдущая»', 
							'description' => '', 
							'default' => 'предыдущая'
						),
			'format_next' => array(
							'type' => 'text', 
							'name' => 'Текст для «следующая»', 
							'description' => '', 
							'default' => 'следующая'
						),
			'format_last' => array(
							'type' => 'text', 
							'name' => 'Текст для «последняя»', 
							'description' => '', 
							'default' => 'последняя'
						),
			'format_title' => array(
							'type' => 'checkbox', 
							'name' => 'title для «первая &middot; предыдущая / следующая &middot; последняя»', 
							'description' => '', 
							'default' => '0'
						),
			'sep' => array(
							'type' => 'text', 
							'name' => 'Разделитель между страницами', 
							'description' => '', 
							'default' => ' &middot; '
						),
			'sep2' => array(
							'type' => 'text', 
							'name' => 'Разделитель между блоком страниц и текстовыми ссылками', 
							'description' => '', 
							'default' => ' | '
						),

			),
		'Настройки плагина Pagination x', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

	
?>