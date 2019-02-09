<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Digg-style pagination plugin for MaxSite CMS
 * (c) http://www.moneymaker.ru/
 */

function digg_pagination_autoload($a = array()) 
{
	mso_hook_add('pagination', 'digg_pagination_go', 10);
	return $a;
}

function digg_pagination_uninstall($args = array())
{	
	mso_delete_option('digg_pagination', 'plugins'); // удалим созданные опции
	return $args;
}

function digg_pagination_go($r = array()) 
{
	global $MSO;

	$r_orig = $r; // сохраним исходный,	чтобы его же отдать дальше

	if (!$r) return $r;

	if ( !isset($r['maxcount']) ) return $r;
	if ( !isset($r['limit']) ) return $r; // нужно указать сколько записей выводить
	if ( !isset($r['type']) )  $r['type'] = false; // можно задать свой тип

	$options = mso_get_option('digg_pagination', 'plugins', array() );
	if (!isset($r['next_url'])){
		if (!isset($options['next_url']))
			$r['next_url'] = 'next';
		else
			$r['next_url'] = $options['next_url'];
	}

	if ( !isset($r['range']) ) 	$r['range'] = 3;

	//Setup format array
	if ( !isset($r['format']['full_tag_open']) ) 
		$r['format']['full_tag_open'] = isset($options['full_tag_open']) ? $options['full_tag_open'] : '<div class="pagination">';
	if ( !isset($r['format']['full_tag_close']) ) 
		$r['format']['full_tag_close'] = isset($options['full_tag_close']) ? $options['full_tag_close'] : '</div>';

	if ( !isset($r['format']['first_link']) ) 
		$r['format']['first_link'] = isset($options['first_link']) ? $options['first_link'] : t('‹ Первая', 'plugins');
	if ( !isset($r['format']['first_tag_open']) ) 
		$r['format']['first_tag_open'] = isset($options['first_tag_open']) ? $options['first_tag_open'] : '';
	if ( !isset($r['format']['first_tag_close']) ) 
		$r['format']['first_tag_close'] = isset($options['first_tag_close']) ? $options['first_tag_close'] : '&nbsp;';

	if ( !isset($r['format']['prev_link']) ) 
		$r['format']['prev_link'] = isset($options['prev_link']) ? $options['prev_link'] : '«';
	if ( !isset($r['format']['prev_tag_open']) ) 
		$r['format']['prev_tag_open'] = isset($options['prev_tag_open']) ? $options['prev_tag_open'] : '&nbsp;';
	if ( !isset($r['format']['prev_tag_close']) ) 
		$r['format']['prev_tag_close'] = isset($options['prev_tag_close']) ? $options['prev_tag_close'] : '';

	if ( !isset($r['format']['cur_tag_close']) ) 
		$r['format']['cur_tag_open'] = isset($options['cur_tag_open']) ? $options['cur_tag_open'] : '&nbsp;<strong>';
	if ( !isset($r['format']['cur_tag_close']) ) 
		$r['format']['cur_tag_close'] = isset($options['cur_tag_close']) ? $options['cur_tag_close'] : '</strong>';

	if ( !isset($r['format']['num_tag_open']) ) 
		$r['format']['num_tag_open'] = isset($options['num_tag_open']) ? $options['num_tag_open'] : '&nbsp;';
	if ( !isset($r['format']['num_tag_close']) ) 
		$r['format']['num_tag_close'] = isset($options['num_tag_close']) ? $options['num_tag_close'] : '';

	if ( !isset($r['format']['next_link']) ) 
		$r['format']['next_link'] = isset($options['next_link']) ? $options['next_link'] : '»';
	if ( !isset($r['format']['next_tag_open']) ) 
		$r['format']['next_tag_open'] = isset($options['next_tag_open']) ? $options['next_tag_open'] : '&nbsp;';
	if ( !isset($r['format']['next_tag_close']) ) 
		$r['format']['next_tag_close'] = isset($options['next_tag_close']) ? $options['next_tag_close'] : '&nbsp;';

	if ( !isset($r['format']['last_link']) ) 
		$r['format']['last_link'] = isset($options['last_link']) ? $options['last_link'] : t('Последняя ›', 'plugins');
	if ( !isset($r['format']['last_tag_open']) ) 
		$r['format']['last_tag_open'] = isset($options['last_tag_open']) ? $options['last_tag_open'] : '&nbsp;';
	if ( !isset($r['format']['last_tag_close']) ) 
		$r['format']['last_tag_close'] = isset($options['last_tag_close']) ? $options['last_tag_close'] : '';


	# текущая пагинация вычисляется по адресу url
	# должно быть /next/6 - номер страницы
	$current_paged = mso_current_paged($r['next_url']);

	if ($current_paged > $r['maxcount']) $current_paged = $r['maxcount'];

	if ($r['type'] !== false) $type = $r['type'];
		else $type = $MSO->data['type'];
	
	// текущий урл сделаем
	$a_cur_url = $MSO->data['uri_segment'];
	
	$cur_url = $MSO->config['site_url'] . $type;
	
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

	// pr($home_url);
	
	$out = _pagination( $r['maxcount'], 
						$current_paged, 
						$cur_url . '/' . $r['next_url'] . '/',
						$home_url, 
						$r['range'],
						$r['format']
						);
	
	if ($out) echo NR . $out . NR;
	 
	return $r_orig;
}


function _pagination($num_pages, $cur_page, $base_url, $home_url, $padding = 3, $format) 
{
	//Constants
	extract($format);
	// 0 or 1 page
	if ($num_pages < 2) return '';
	
	// Calculate the prev and next numbers.
	$prev = $cur_page - 1; //previous page is page - 1
	$next = $cur_page + 1; //next page is page + 1
	$lpm1 = $num_pages - 1; //last page minus 1

	// And here we go...
	$output = '';

	// Render the "First" link
	if  ($cur_page > 1)
	{
		$output .= $first_tag_open.'<a href="'.$home_url.'">'.$first_link.'</a>'.$first_tag_close;
	}
	else $output.= $first_tag_open.'<span class="disabled">'.$first_link.'</span>'.$first_tag_close;

	// Render the "previous" link
	if ($cur_page > 1)
	{
		$output .= $prev_tag_open.'<a href="'.get_link($prev, $base_url, $home_url).'">'.$prev_link.'</a>'.$prev_tag_close;
	}
	else $output.= $prev_tag_open.'<span class="disabled">'.$prev_link.'</span>'.$prev_tag_close;

	// Write the digit links
	if ($num_pages < 7 + ($padding * 2))
	{	//not enough pages to bother breaking it up
		for ($i = 1; $i <= $num_pages; $i++)
		{
			if ($i == $cur_page) $output.= $cur_tag_open.$i.$cur_tag_close;
			else $output .=  $num_tag_open.'<a href="'.get_link($i, $base_url, $home_url).'">'.$i.'</a>'.$num_tag_close;
		}
	} 
	elseif($num_pages > 5 + ($padding * 2))
	{	//enough pages to hide some
		//close to beginning; only hide later pages
		if($cur_page < 1 + ($padding * 2))
		{
			for ($i = 1; $i < 4 + ($padding * 2); $i++)
			{
				if ($i == $cur_page) $output.= $cur_tag_open.$i.$cur_tag_close;
				else $output .= $num_tag_open.'<a href="'.get_link($i, $base_url, $home_url).'">'.$i.'</a>'.$num_tag_close;
			}
			$output.= '...';
			$output.= $num_tag_open.'<a href="'.get_link($lpm1, $base_url, $home_url).'">'.$lpm1.'</a>'.$num_tag_close;
			$output.= $num_tag_open.'<a href="'.get_link($num_pages, $base_url, $home_url).'">'.$num_pages.'</a>'.$num_tag_close;
		} 
		elseif($num_pages - ($padding * 2) > $cur_page && $cur_page > ($padding * 2))
		{ 
			//in middle; hide some front and some back
			$output.= $num_tag_open.'<a href="'.$home_url.'">1</a>'.$num_tag_close;
			$output.= $num_tag_open.'<a href="'.get_link(2, $base_url, $home_url).'">2</a>'.$num_tag_close;
			$output.= '...';
			for ($i = $cur_page - $padding; $i <= $cur_page + $padding; $i++)
			{
				if ($i == $cur_page) $output.= $cur_tag_open.$i.$cur_tag_close;
				else $output.= $num_tag_open.'<a href="'.get_link($i, $base_url, $home_url).'">'.$i.'</a>'.$num_tag_close;					
			}
			$output.= '...';
			$output.= $num_tag_open.'<a href="'.get_link($lpm1, $base_url, $home_url).'">'.$lpm1.'</a>'.$num_tag_close;
			$output.= $num_tag_open.'<a href="'.get_link($num_pages, $base_url, $home_url).'">'.$num_pages.'</a>'.$num_tag_close;	
		}
		else 
		{ //close to end; only hide early pages
			$output.= $num_tag_open.'<a href="'.$home_url.'">1</a>'.$num_tag_close;
			$output.= $num_tag_open.'<a href="'.get_link(2, $base_url, $home_url).'">2</a>'.$num_tag_close;
			$output.= '...';
			for ($i = $num_pages - (2 + ($padding * 2)); $i <= $num_pages; $i++)
			{
				if ($i == $cur_page) $output.= $cur_tag_open.$i.$cur_tag_close;
				else $output.= $num_tag_open.'<a href="'.get_link($i, $base_url, $home_url).'">'.$i.'</a>'.$num_tag_close;					
			}
		}
	}

	// Render the "next" link
	if ($cur_page < $i - 1)
	{
		$output .= $next_tag_open.'<a href="'.get_link($next, $base_url, $home_url).'">'.$next_link.'</a>'.$next_tag_close;
	}
	else $output.= $next_tag_open.'<span class="disabled">'.$next_link.'</span>'.$next_tag_close;
		
	// Render the "Last" link
	if ($cur_page < $num_pages)
	{
		//$i = (($num_pages * $per_page) - $per_page);
		$i = $num_pages;
		$output .= $last_tag_open.'<a href="'.$base_url.$i.'">'.$last_link.'</a>'.$last_tag_close;
	}
	else $output.= $last_tag_open.'<span class="disabled">'.$last_link.'</span>'.$last_tag_close;
					
		
	// Kill double slashes.  Note: Sometimes we can end up with a double slash
	// in the penultimate link so we'll kill all double slashes.
	$output = preg_replace("#([^:])//+#", "\\1/", $output);
		
	// Add the wrapper HTML if exists
	return $full_tag_open.$output.$full_tag_close;
}

function get_link($page, $base_url = '', $home_url = '')
{
	if($page == 1) return $home_url;
	else return $base_url.$page;
}

function digg_pagination_mso_options() 
{
	mso_admin_plugin_options('digg_pagination', 'plugins', 
		array(
			'next_url' => array(
				'type' => 'text',
				'name' => 'Сегмент после которого указывается номер страницы',
				'description' => 'Например next или page.',
				'default' => 'next'
			),
			'full_tag_open' => array(
				'type' => 'text',
				'name' => 'Пагинация - открывающий тег',
				'description' => '',
				'default' => '<div class="pagination">'
			),
			'full_tag_close' => array(
				'type' => 'text',
				'name' => 'Пагинация - закрывающий тег',
				'description' => '',
				'default' => '</div>'
			),
			'first_link' => array(
				'type' => 'text',
				'name' => 'Первая страница - текст ссылки',
				'description' => '',
				'default' => t('‹ Первая', 'plugins')
			),
			'first_tag_open' => array(
				'type' => 'text',
				'name' => 'Первая страница - открывающий тег',
				'description' => '',
				'default' => ''
			),
			'first_tag_close' => array(
				'type' => 'text',
				'name' => 'Первая страница - закрывающий тег',
				'description' => '',
				'default' => '&nbsp;'
			),
			'prev_link' => array(
				'type' => 'text',
				'name' => 'Предыдущая страница - текст ссылки',
				'description' => '',
				'default' => '«'
			),
			'prev_tag_open' => array(
				'type' => 'text',
				'name' => 'Предыдущая страница - открывающий тег',
				'description' => '',
				'default' => '&nbsp;'
			),
			'prev_tag_close' => array(
				'type' => 'text',
				'name' => 'Предыдущая страница - закрывающий тег',
				'description' => '',
				'default' => ''
			),
			'cur_tag_open' => array(
				'type' => 'text',
				'name' => 'Текущая страница - открывающий тег',
				'description' => '',
				'default' => '&nbsp;<strong>'
			),
			'cur_tag_close' => array(
				'type' => 'text',
				'name' => 'Текущая страница - закрывающий тег',
				'description' => '',
				'default' => '</strong>'
			),
			'num_tag_open' => array(
				'type' => 'text',
				'name' => 'Числовые ссылки - открывающий тег',
				'description' => '',
				'default' => '&nbsp;'
			),
			'num_tag_close' => array(
				'type' => 'text',
				'name' => 'Числовые ссылки - закрывающий тег',
				'description' => '',
				'default' => ''
			),
			'next_link' => array(
				'type' => 'text',
				'name' => 'Следующая страница - текст ссылки',
				'description' => '',
				'default' => '»'
			),
			'next_tag_open' => array(
				'type' => 'text',
				'name' => 'Следующая страница - открывающий тег',
				'description' => '',
				'default' => '&nbsp;'
			),
			'next_tag_close' => array(
				'type' => 'text',
				'name' => 'Следующая страница - закрывающий тег',
				'description' => '',
				'default' => '&nbsp;'
			),
			'last_link' => array(
				'type' => 'text',
				'name' => 'Последняя страница - текст ссылки',
				'description' => '',
				'default' => t('Последняя ›', 'plugins')
			),
			'last_tag_open' => array(
				'type' => 'text',
				'name' => 'Последняя страница - открывающий тег',
				'description' => '',
				'default' => '&nbsp;'
			),
			'last_tag_close' => array(
				'type' => 'text',
				'name' => 'Последняя страница - закрывающий тег',
				'description' => '',
				'default' => ''
			)
		),
		'Настройки плагина пагинации', // титул
		'Укажите необходимые опции.'   // инфо
	);
}
?>