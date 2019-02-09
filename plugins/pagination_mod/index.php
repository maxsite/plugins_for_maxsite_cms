<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Н. Громов (nicothin)
 * http://nicothin.ru/
 */


function pagination_mod_autoload($a = array()) 
{
	mso_hook_add('pagination', 'pagination_mod_go', 10);
	return $a;
}


function pagination_mod_go($r = array()) 
{
	global $MSO;
	
	$r_orig = $r; 
	
	if (!$r) return $r;
	if ( !isset($r['maxcount']) ) return $r;
	if ( !isset($r['limit']) ) return $r; // нужно указать сколько записей выводить
	if ( !isset($r['type']) )  $r['type'] = false; // можно задать свой тип
	
	if ( !isset($r['next_url']) ) $r['next_url'] = 'next';
	
	$options = mso_get_option('plugin_pagination_mod', 'plugins', array() ); // получаем опции
	
	if ( !isset($r['range']) ) 
		$r['range'] = isset($options['range']) ? (int) $options['range'] : 3;
		
	if ( !isset($r['sep']) ) 
		$r['sep'] = isset($options['sep']) ? $options['sep'] : ' &middot; ';	
	
	if ( !isset($r['sep2']) ) 
		$r['sep2'] = isset($options['sep2']) ? $options['sep2'] : ' | ';
		
	
	
	if ( !isset($r['format']) )
	{	
		// $r['format'] = 
		$r['format'][] = isset($options['prev_text']) ? $options['prev_text'] : t('Страницы', 'plugins');
		$r['format'][] = isset($options['format_first']) ? $options['format_first'] : t('первая', 'plugins');
		$r['format'][] = isset($options['format_prev']) ? $options['format_prev'] : t('предыдущая', 'plugins');
		$r['format'][] = isset($options['format_next']) ? $options['format_next'] : t('следующая', 'plugins');
		$r['format'][] = isset($options['format_last']) ? $options['format_last'] : t('последняя', 'plugins');
	}	
	
	# текущая пагинация вычисляется по адресу url
	# должно быть /next/6 - номер страницы
	
	$current_paged = mso_current_paged($r['next_url']);
	
	if ($current_paged > $r['maxcount']) $current_paged = $r['maxcount'];
	
	if ($r['type'] !== false) $type = $r['type'];
		else $type = $MSO->data['type'];
	
	// текущий урл сделаем
	$a_cur_url = $MSO->data['uri_segment'];
	
	//$cur_url = getinfo('site_url') . $type;
	
	if ($type != 'page_404') $cur_url = $MSO->config['site_url'] . $type;
	else $cur_url = $MSO->config['site_url'];
	
	//pr($cur_url);
	
	foreach ($a_cur_url as $val)
	{
		#if ($val == 'next') break; // next - дальше не нужно
		
		if ($val == $r['next_url']) break; // next - дальше не нужно
		else
		{
			if ($val != $type) $cur_url .= '/@@' . $val;
		}
	}
	
	$cur_url = str_replace('//@@', '/', $cur_url);
	$cur_url = str_replace('@@', '', $cur_url);
	
	// echo '$cur_url';
	// pr($cur_url);
	
	
	if ($type == 'home') 
		$home_url = $MSO->config['site_url'];
	else
		$home_url = $cur_url;
	
	// echo '$home_url';
	// pr($home_url);
	
	$out = _pagination_mod( $r['maxcount'], 
						$current_paged, 
						$cur_url . '/' . $r['next_url'] . '/', 
						$r['range'], 
						$cur_url,
						$r['sep'],
						$home_url,
						$r['sep2']
						);
	
	if ($out)
	{
		$out = str_replace(
				array('%PREVTEXT%', '%FIRST%', '%PREV%', '%NEXT%', '%LAST%'),
				$r['format'],
				$out);
		
		echo NR . '<div class="pagination_mod">' . $out . '</div>' . NR;
	}
	
	return $r_orig;
}


function _pagination_mod($total_pages, $page_number, $base_url, $diappazon = 3, $url_first = '', $sep = ' &middot; ', $home_url = '', $sep2 = ' | ') 
{
	# (c) http://www.ben-griffiths.com/php-pagination-function/
	# переделал MAX http://maxsite.org/
	# проапгрейдил Н. Громов (nicothin) http://nicothin.ru/
	
	if ($total_pages < 2) return false; 					// если менее 2х страниц — ну это все в _опу...
	if ($page_number == null) $page_number = 1;				// открытая страница. если null, то первая
	if ($page_number > $total_pages ) $page_number = $total_pages;	// открытая не мож. быть больше общего числа
	if ($diappazon < 1) $diappazon = 1;						// диапазон не может меньше 1

	$prev_link_page = $page_number - 1;						// номер предыдущей
	$next_link_page = $page_number + 1;						// номер следующей
	if ($prev_link_page < 1) $prev_link_page = 1;			// пред. не может быть меньше 1
	if ($next_link_page > $total_pages) $next_link_page = $total_pages;		// след. не может быть больше общего числа
	
	$pages_start = $page_number - $diappazon;	// начало счетчика для центральной части
	$count_to = $page_number + $diappazon;		// конец счетчика для центральной части пагинации
	
	// поправки для счетчика (нехватка с какой-либо из сторон)
	
	// начало счетчика меньше 1
	if ($pages_start < 1) { 
		$pages_start = 1;
		$count_to_mod = $page_number - $diappazon;
		$count_to_mod = -$count_to_mod;
		$count_to = $count_to + $count_to_mod + 1;
	}
	// конец счетчика больше числа страниц
	if ($page_number + $diappazon > $total_pages)
	{
		$pages_start_mod = $total_pages - ($page_number + $diappazon);
		$pages_start = $pages_start + $pages_start_mod;
		if ($pages_start < 1) $pages_start = 1;
	}
	// доп. проверка — убираем лишний номер
	if ($count_to > $total_pages) $count_to = $total_pages;
	
	$middle_page_links = '';	// центральная часть ссылок
	$first_mid_link = '';
	$last_mid_link = '';
	$first_dots = '';
	$last_dots = '';

	$prev_text = '<span style="display:none" class="pag-prev-text">%PREVTEXT%: </span> ';
	
	// формируем центральную часть ссылок
	for ($counter = $pages_start; $counter <= $count_to; $counter += 1) 
	{
		if ($counter != $page_number)
		{
			$middle_page_links .= '<a href="' . $base_url . $counter . '">' . $counter . '</a>';
			if ($counter < $count_to) $middle_page_links .= $sep; // разделитель, если не последняя интерация
		} 
		else 
		{
			$middle_page_links .= ' <strong>' . $counter . '</strong>';
			if ($counter < $count_to) $middle_page_links .= $sep;
		}
		if($first_mid_link == '') 
			$first_mid_link = $counter; // когда цикл закончится, тут будет номер нач. стр.
		$last_mid_link = $counter; // когда цикл закончится, тут будет номер посл. стр.
	}

	// ссылки до центральной части и добавка к центральной части слева
	$first_link = '<span class="pag-left">';
	if ($page_number == 1){
		$first_dots = ''; // без к-л добавок
	} 
	else {
		if  ($prev_link_page == 1) // если предыдущая — 1-я
			$first_link .=  '<a class="pag-prev" href="' . $home_url . '">%PREV%</a>' . $sep2;
			//$first_link .=  '<a class="pag-first" href="' . $home_url . '">%FIRST%</a> ' . $sep . '<a class="pag-prev" href="' . $home_url . '">%PREV%</a>' . $sep2;
		else
			$first_link .=  '<a class="pag-prev" href="' . $base_url . $prev_link_page.'">%PREV%</a>' . $sep2;
			//$first_link .=  '<a class="pag-first" href="' . $home_url . '">%FIRST%</a>' . $sep . '<a class="pag-prev" href="' . $base_url . $prev_link_page.'">%PREV%</a>' . $sep2;
		
		
		if($first_mid_link == 2)
			$first_dots = ' <a href="' . $home_url . '">1</a> ';
		elseif($first_mid_link > 2)
			$first_dots = ' <a href="' . $home_url . '">1</a> <span class="trt">...</span> ';
	}
	$first_link .= '</span>';
	
	// ссылки после центральной части и добавка к центральной части справа
	$last_link = '<span class="pag-right">';
	if($page_number == $total_pages){
		$last_dots = ''; // без к-л добавок
	} 
	else {
		$last_link .=  $sep2 . '<a class="pag-next" href="' . $base_url . $next_link_page . '">%NEXT%</a>';
		//$last_link .=  $sep2 . '<a class="pag-next" href="' . $base_url . $next_link_page . '">%NEXT%</a>' . $sep . '<a class="pag-last" href="' . $base_url . $total_pages . '">%LAST%</a>';
		
		if (($last_mid_link + 1) == $total_pages)
			$last_dots = ' <a href="' . $base_url . $total_pages . '">' . $total_pages . '</a>';
		elseif (($last_mid_link + 1) < $total_pages)
			$last_dots = ' <span class="trt">...</span> <a href="' . $base_url . $total_pages . '">' . $total_pages . '</a>'; 
	}
	$last_link .= '</span>';
	
	$output_page_link = $prev_text . $first_link . '<span class="pag-list">' . $first_dots . $middle_page_links. $last_dots . '</span>' . $last_link;
	
	return $output_page_link;
}	


function pagination_mod_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_pagination_mod', 'plugins', 
		array(
			'range' => array(
							'type' => 'text', 
							'name' => 'Количество ссылок с каждой стороны от активной страницы', 
							'description' => 'К примеру, число «3» в этой настройке сформирует пагинацию вида: 2 &middot; 3 &middot; 4 &middot; 5 (открытая страница) &middot; 6 &middot; 7 &middot; 8. То есть, по 3 с каждой стороны. При этом, если с одной из сторон ссылок «не хватает», с другой стороны будут выведены дополнительные. То есть, при нахождении на странице 1 будет выведено: 1 (открытая страница) &middot; 2 &middot; 3 &middot; 4 &middot; 5 &middot; 6 &middot; 7', 
							'default' => '3'
						),
			'prev_text' => array(
							'type' => 'text', 
							'name' => 'Текст перед пагинацией', 
							'description' => 'Выводится в скрытом через CSS виде. Предназначен для случаев просмотра сайта без стилей или с отличающимися от основных стилями (к примеру, на экране мобильных устройств).', 
							'default' => 'Страницы'
						),
/* 			'format_first' => array(
							'type' => 'text', 
							'name' => 'Текст для ссылки «Первая»', 
							'description' => '', 
							'default' => 'первая' 
						), */
			'format_prev' => array(
							'type' => 'text', 
							'name' => 'Текст для ссылки «предыдущая»', 
							'description' => '', 
							'default' => 'предыдущая'
						),
			'format_next' => array(
							'type' => 'text', 
							'name' => 'Текст для ссылки «следующая»', 
							'description' => '', 
							'default' => 'следующая'
						),
/* 			'format_last' => array(
							'type' => 'text', 
							'name' => 'Текст для ссылки «последняя»', 
							'description' => '', 
							'default' => 'последняя' 
						), */
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
		'Настройки плагина pagination_mod', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

	
?>