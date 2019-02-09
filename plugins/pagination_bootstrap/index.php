<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Н. Громов (nicothin)
 * http://nicothin.ru/
 */


function pagination_bootstrap_autoload($a = array()) 
{
	mso_hook_add('pagination', 'pagination_bootstrap_go', 10);
	return $a;
}


function pagination_bootstrap_go($r = array()) 
{
	global $MSO;
	
	$r_orig = $r; 
	
	if (!$r) return $r;
	if ( !isset($r['maxcount']) ) return $r;
	if ( !isset($r['limit']) ) return $r;
	if ( !isset($r['type']) )  $r['type'] = false;
	
	if ( !isset($r['next_url']) ) $r['next_url'] = 'next';
	
	$options = mso_get_option('plugin_pagination_bootstrap', 'plugins', array() ); // получаем опции
	
	if ( !isset($r['format']) )
	{	
		$r['format'][] = isset($options['format_prev']) ? $options['format_prev'] : '«';
		$r['format'][] = isset($options['format_next']) ? $options['format_next'] : '»';
	}
	$r['diapazon'] = isset($options['range']) ? $options['range'] : 1;
	
	# текущая пагинация вычисляется по адресу url
	# должно быть /next/6 - номер страницы
	
	$current_paged = mso_current_paged($r['next_url']);
	
	if ($current_paged > $r['maxcount']) $current_paged = $r['maxcount'];
	
	if ($r['type'] !== false) $type = $r['type'];
		else $type = $MSO->data['type'];
	
	// текущий урл сделаем
	$a_cur_url = $MSO->data['uri_segment'];
	
	if ($type != 'page_404') $cur_url = $MSO->config['site_url'] . $type;
	else $cur_url = $MSO->config['site_url'];
	
	foreach ($a_cur_url as $val)
	{
		if ($val == $r['next_url']) break; // next - дальше не нужно
		else
		{
			if ($val != $type) $cur_url .= '/@@' . $val;
		}
	}
	
	$cur_url = str_replace('//@@', '/', $cur_url);
	$cur_url = str_replace('@@', '', $cur_url);
	
	if ($type == 'home') 
		$home_url = $MSO->config['site_url'];
	else
		$home_url = $cur_url;
	
	/*
	pr($r['maxcount']);
	pr($current_paged);
	pr($cur_url . '/' . $r['next_url'] . '/');
	pr($r['diapazon']);
	pr($home_url); 
	*/
	
	$out = _pagination_bootstrap( 
						$r['maxcount'], 
						$current_paged, 
						$cur_url . '/' . $r['next_url'] . '/', 
						$r['diapazon'],
						$home_url
						);
	
	if ($out)
	{
		$out = str_replace(
				array('%PREV%', '%NEXT%'),
				$r['format'],
				$out);
		
		echo NR . '<div class="pagination pagination-centered"><ul>' . $out . '</ul></div>' . NR;
	}
	
	return $r_orig;
}


function _pagination_bootstrap($max, $page_number, $base_url, $diappazon = 3, $home_url = '') 
{
	if ($max < 2) return ''; // страниц менее 2-х, возвращаем  «пусто»
	if ($diappazon < 1) $diappazon = 1;
	if ($page_number < 1) $page_number = 1;
	
	// используемые переменные
	$first_link = '';			// «предыдущая»
	$first_dots = '';			// цифра перед центр. частью пагинации
	$middle_page_links = '';	// центр пагинации
	$last_dots = '';			// цифра после центр. части
	$last_link = '';			// «следующая»
	$prev_link_page = $page_number - 1;
	$next_link_page = $page_number + 1;
	if ($prev_link_page < 1) $prev_link_page = 1;
	if ($next_link_page > $max) $next_link_page = $max;


	$middle_dig_last = $page_number + $diappazon;		// — начало цикла
	$middle_dig_first = $page_number - $diappazon;		// — конец цикла
	if ($middle_dig_first < 1) 
	{
		$middle_dig_first = 1;
		$count_to_mod = $page_number - $diappazon;
		$count_to_mod = -$count_to_mod;
		$middle_dig_last = $middle_dig_last + $count_to_mod + 1;
	}
	if ($page_number + $diappazon > $max) 
	{
		$pages_start_mod = $max - ($page_number + $diappazon);
		$middle_dig_first = $middle_dig_first + $pages_start_mod;
		if ($middle_dig_first < 1) $middle_dig_first = 1;
	}

	if ($middle_dig_last > $max) $middle_dig_last = $max; 
	
	$middle_page_links = '';
	$first_mid_link = '';
	$last_mid_link = ''; 
	
	// формируем центральную часть цифр-ссылок
	for ($x = $middle_dig_first; $x <= $middle_dig_last; $x += 1) 
	{
		if ($x != $page_number)
		{
			if ($x == 1) 
				$middle_page_links .= '<li><a href="' . $home_url . '">' . $x . '</a></li>';
			else
				$middle_page_links .= '<li><a href="' . $base_url . $x . '">' . $x . '</a></li>';
			if($first_mid_link == '') 
				$first_mid_link = $x;
			$last_mid_link = $x;
		} 
		else 
		{
			$middle_page_links .= '<li class="active"><span>' . $x . '</span></li>';
		}
	} 
	
	// формируем правую текстовую ссылку
	$last_link = '';
	if($page_number == $max)
	{
		$last_link .=  '<li class="next"><span>%NEXT%</span></li>';
	} 
	else 
	{
		$last_link .=  '<li class="next"><a href="' . $base_url . $next_link_page . '">%NEXT%</a></li>';
		
		if (($last_mid_link + 1) == $max)
			$last_dots = '<li><a href="' . $base_url . $max . '">' . $max . '</a></li>';
		elseif (($last_mid_link + 1) < $max)
			$last_dots = '<li><span>…</span></li><li><a href="' . $base_url . $max . '">' . $max . '</a></li>';
		else 
			$last_dots = '';  
	}
	
	// предыдущие
	$first_link = '';
	if ($page_number == 1)
	{
		$first_link .= '<li class="prev"><span>%PREV%</span></li>';	
	} 
	else
	{
		if  ($prev_link_page == 1)
			$first_link .=  '<li class="prev"><a href="' . $home_url . '">%PREV%</a></li>';
		else
			$first_link .=  '<li class="prev"><a href="' . $base_url . $prev_link_page.'">%PREV%</a></li>';
		if ($first_mid_link == 2)
			$first_dots = '<li><a href="' . $base_url . '1">1</a></li>';
		elseif ($first_mid_link > 2)
			$first_dots = '<li><a href="' . $base_url . '1">1</a></li><li><span>…</span></li>';
	}
	
	$output_page_link = $first_link.$first_dots.$middle_page_links.$last_dots.$last_link;
	
	return '<ul>'.$output_page_link.'</ul>';
}


function pagination_bootstrap_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_pagination_bootstrap', 'plugins', 
		array(
			'range' => array(
							'type' => 'text', 
							'name' => 'Количество ссылок с каждой стороны от активной страницы', 
							'description' => 'К примеру, число «3» в этой настройке сформирует пагинацию вида: 2 3 4 5 (открытая страница) 6 7 8. То есть, по 3 с каждой стороны. При этом, если с одной из сторон ссылок «не хватает», с другой стороны будут выведены дополнительные. То есть, при нахождении на странице 1 будет выведено: 1 (открытая страница) 2 3 4 5 6 7', 
							'default' => '3'
						),
			'format_prev' => array(
							'type' => 'text', 
							'name' => 'Текст для ссылки «предыдущая»', 
							'description' => '', 
							'default' => '«'
						),
			'format_next' => array(
							'type' => 'text', 
							'name' => 'Текст для ссылки «следующая»', 
							'description' => '', 
							'default' => '»'
						),

			),
		'Настройки плагина pagination_bootstrap', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

	
?>