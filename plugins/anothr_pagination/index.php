<?php
if ( !defined( 'BASEPATH' ) )
	exit( 'No direct script access allowed' );

/**
 * MaxSite CMS
 * (c) http://maxsite.org/
 * modified by lokee
 * jid: lokee@jabbim.com
 * mail: mail@lokee.rv.ua
 * @07.03.2009
 */


function pagination_go( $r = array() ) 
{
	global $MSO;
	
	if (!$r) return '';
	if ( !isset($r['maxcount']) ) return '';
	if ( !isset($r['limit']) ) return ''; // нужно указать сколько записей выводить
	if ( !isset($r['type']) )  $r['type'] = false; // можно задать свой тип
	
	if ( !isset($r['range']) ) 	$r['range'] = 3;
	if ( !isset($r['next_url']) ) $r['next_url'] = 'next';
	if ( !isset($r['format']) )	$r['format'] = array('« Первая', '« позже', 'раньше »', 'последняя »');
	if ( !isset($r['sep']) ) 	$r['sep'] = ' &middot; ';
	if ( !isset($r['sep2']) ) 	$r['sep2'] = ' | ';
	
	# текущая пагинация вычисляется по адресу url
	# должно быть /next/6 - номер страницы
	
	$current_paged = mso_current_paged();
	
	if ($current_paged > $r['maxcount']) $current_paged = $r['maxcount'];
	
	if ($r['type'] !== false) $type = $r['type'];
		else $type = $MSO->data['type'];
	
	// текущий урл сделаем
	$a_cur_url = $MSO->data['uri_segment'];
	
	$cur_url = $MSO->config['site_url'] . $type;
	
	foreach ($a_cur_url as $val)
	{
		if ($val == 'next') break; // next - дальше не нужно
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
	
	// pr($home_url);
	
	$out = _pagination( $r['maxcount'], 
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
				array('%FIRST%', '%PREV%', '%NEXT%', '%LAST%'),
				$r['format'],
				$out);
		
		return NR . '<div class="pagination">' . $out . '</div>' . NR;
	}
	else return '';
}


function _pagination($max, $page_number, $base_url, $diappazon = 4, $url_first = '', $page_u = '', $sep = ' &middot; ', $home_url = '', $sep2 = ' | ') 
{
	# (c) http://www.ben-griffiths.com/php-pagination-function/
	# переделал MAX http://maxsite.org/
	
	if ($max < 2) return '';
	if ($page_number == null) $page_number = 1;
	if ($page_number > $max ) $page_number = $max;
	if ($diappazon < 2) $diappazon = 2;
	
	$total_pages = $max;
	$total_results_feedback = $max;
	
	$prev_link_page = $page_number - 1;
	$next_link_page = $page_number + 1;
	
	if ($prev_link_page < 1) $prev_link_page = 1;
	
	if ($next_link_page > $total_pages) $next_link_page = $total_pages;
	
	$middle_page_links = '';
	
	$pages_start = ($page_number - 3) + 1;
	
	if ($pages_start < 1) $pages_start = 1;
	
	$count_to = $pages_start + $diappazon;
	
	if ($count_to > $total_pages) $count_to = $total_pages;
	
	$first_mid_link = '';
	$last_mid_link = '';
	
	for ($counter = $pages_start; $counter <= $count_to; $counter += 1) 
	{
		$page_link = $counter;

		if ($counter != $page_number)
		{
			$middle_page_links .= '<a href="' . $base_url . $page_u . $page_link . '">' . $counter . '</a>';
			
			if ($counter < $count_to) $middle_page_links .= $sep;
			
			if($first_mid_link == '') $first_mid_link = $page_link;
			
			$last_mid_link = $page_link;
		} 
		else 
		{
			$middle_page_links .= ' <strong>' . $counter . '</strong>';
			if ($counter < $count_to) $middle_page_links .= $sep;
		}
	}
	if ($page_number == 1)
	{
		$first_link = '%PREV%' . $sep2;
		$first_dots = '';
	} 
	else 
	{
		if  ($prev_link_page == 1)
			/* $first_link =  '<a href="' . $home_url . '">%FIRST%</a>' . $sep 
						. '<a href="' . $home_url . '">%PREV%</a>' . $sep2;
			*/
			$first_link = '<a href="' . $home_url . '">%PREV%</a>' . $sep2;
		else
			$first_link = '<a href="' . $base_url . $page_u . $prev_link_page.'/">%PREV%</a>' . $sep2;
		
		if($page_number > 3)
			$first_dots = ' <a href="' . $base_url . $page_u . '1">1</a> ... ';
		else 
			$first_dots = '';
	}
	
	if($page_number == $total_pages)
	{
		$last_link = '%NEXT%';
		$last_dots = '';
	} 
	else 
	{
		/* $last_link =  $sep2 . '<a href="' . $base_url . $page_u . $next_link_page 
					. '">%NEXT%</a>' . $sep . '<a href="' . $base_url . $page_u . $total_pages . '">%LAST%</a>';
		*/
		$last_link =  '<a href="' . $base_url . $page_u . $next_link_page . '">%NEXT%</a>';
		if ( $last_mid_link < $total_pages  )
			$last_dots = ' ... <a href="' . $base_url . $page_u . $total_pages . '">' . $total_pages . '</a> ';
		else 
			$last_dots = '';
	}
	
	//$output_page_link = $first_link . $first_dots . $middle_page_links. $last_dots . $last_link;
	$output_page_link = $first_link . $last_link;

	if ($total_pages == -1)
		$output_page_link = '%FIRST%' . $sep . '%PREV%' . $sep2 . '<strong>1</strong>' . $sep2. '%NEXT%' . $sep . '%LAST%';
	
	return $output_page_link;
}	


?>
