<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
* MaxSite CMS
* (c) http://max-3000.com/
*
* Евгений Мирошниченко
* zhenya.webdev@gmail.com
* (c) https://modern-templates.com
*
*/


function pagination_simple_autoload()
{

	//mso_hook_add('head_css', 'pagination_simple_head');
	mso_hook_add('pagination', 'pagination_simple_go', 10);
}

function pagination_simple_css($args = array())
{
	echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'pagination_simple/css/style.css">';

	return $args;
}

# функция выполняется при деинсталяции плагина
function pagination_simple_uninstall($args = array())
{
	mso_delete_option('plugin_pagination_simple', 'plugins' ); // удалим созданные опции
	return $args;
}

function pagination_simple_go($r = array())
{
	global $MSO;

	$r_orig = $r;

	if (!$r) return $r;
	if ( !isset($r['maxcount']) ) return $r;
	if ( !isset($r['limit']) ) return $r; // нужно указать сколько записей выводить
	if ( !isset($r['type']) )  $r['type'] = false; // можно задать свой тип

	if ( !isset($r['next_url']) ) $r['next_url'] = 'next';

	$options = mso_get_option('plugin_pagination_simple', 'plugins', array() ); // получаем опции



	if ( !isset($r['range']) )
		$r['range'] = isset($options['range']) ? (int) $options['range'] : 3;

	if ( !isset($r['sep']) )
		$r['sep'] = isset($options['sep']) ? $options['sep'] : ' ';

	if ( !isset($r['sep2']) )
		$r['sep2'] = isset($options['sep2']) ? $options['sep2'] : ' ';



	if ( !isset($r['format']) )
	{
		// $r['format'] =
		$r['format'][] = isset($options['format_first']) ? $options['format_first'] : '&lt;&lt;';
		$r['format'][] = isset($options['format_prev']) ? $options['format_prev'] : '&lt;';
		$r['format'][] = isset($options['format_next']) ? $options['format_next'] : '&gt;';
		$r['format'][] = isset($options['format_last']) ? $options['format_last'] : '&gt;&gt;';
	}

	# текущая пагинация вычисляется по адресу url
	# должно быть /next/6 - номер страницы

	$current_paged = mso_current_paged($r['next_url']);

	if ($current_paged > $r['maxcount']) $current_paged = $r['maxcount'];

	if ($r['type'] !== false) $type = $r['type'];
		else $type = $MSO->data['type'];

	// текущий урл сделаем
	$a_cur_url = $MSO->data['uri_segment'];

	// $cur_url = getinfo('site_url') . $type;
	if ($type != 'page_404') $cur_url = getinfo('site_url') . $type;
		else $cur_url = getinfo('site_url');

	// pr($cur_url);

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
	// pr($cur_url);


	if ($type == 'home')
		$home_url = getinfo('site_url');
	else
		$home_url = $cur_url;

	//pr($home_url);

	$out = _pagination_simple( $r['maxcount'],
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

		echo NR . '<div class="pagination">' . $out . '</div>' . NR;
	}

	return $r_orig;
}


function _pagination_simple($max, $page_number, $base_url, $diappazon = 4, $url_first = '', $page_u = '', $sep = ' &middot; ', $home_url = '', $sep2 = ' | ')
{
	# (c) http://www.ben-griffiths.com/php-pagination-function/
	# переделал MAX http://maxsite.org/

	if ($max < 2) return false;      // если менее 2х страниц — ну это все в _опу...
	if ($page_number == null) $page_number = 1; // открытая страница. если null, то первая
	if ($page_number > $max ) $page_number = $max; // открытая не мож. быть больше общего числа
	if ($diappazon < 1) $diappazon = 1;

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
    //$prev_text = '<span style="display:none" class="pag-prev-text">%PREVTEXT%: </span> ';
	for ($counter = $pages_start; $counter <= $count_to; $counter += 1)
	{
		$page_link = $counter;


		if ($counter != $page_number)
		{

			if ($counter == 1)
				$middle_page_links .= '<a href="' . $home_url . '">' . $counter . '</a>';
			else
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

     $first_link = '';
	if ($page_number == 1)
	{
		//$first_link = '<span class="pagination-first">%FIRST%</span>' . $sep . '<span class="pagination-prev">%PREV%</span>' . $sep2;
		$first_dots = ''; // без к-л добавок
	}
	else
	{
		if  ($prev_link_page == 1)
		    $first_link .=  '<a href="' . $home_url . '">%PREV%</a>' . $sep2;
		 else
			$first_link .=  '<a href="' . $base_url . $page_u . $prev_link_page.'">%PREV%</a>' . $sep2;

		if($page_number > 3)
			$first_dots = ' <a class="pagination-start" href="' . $home_url . '">1</a>';
		else
			$first_dots = '';
	}
    // ссылки после центральной части и добавка к центральной части справа
    $last_link = '';

	if($page_number == $total_pages)
	{
	  $last_dots = ''; // без к-л добавок
	}
	else
	{
	    $last_link .=  $sep2 . '<a class="pagination-next" href="' . $base_url . $page_u . $next_link_page. '">%NEXT%</a>';

		if ( $last_mid_link < $total_pages  )
			$last_dots = ' ... <a class="pagination-end" href="' . $base_url . $page_u . $total_pages . '">' . $total_pages . '</a> ';
		else
			$last_dots = '';
	}
    //$last_link .= '</div>';
	$output_page_link = $first_link . '' . $first_dots . $middle_page_links . '' . $last_link.$last_dots;


	if ($total_pages == -1)
		$output_page_link = '%FIRST%' . $sep . '%PREV%' . $sep2 . '<strong>1</strong>' . $sep2. '%NEXT%' . $sep . '%LAST%';

	return $output_page_link;
}


function pagination_simple_mso_options()
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_pagination_simple', 'plugins',
		array(
			'range' => array(
							'type' => 'text',
							'name' => t('Диапазон количества ссылок'),
							'description' => t('Задайте количество отображаемых ссылок на страницы.'),
							'default' => '3'
						),

			'format_prev' => array(
							'type' => 'text',
							'name' => t('Текст для «предыдущая»'),
							'description' => '',
							'default' => '&lt;'
							//'default' => 'назад'
						),
			'format_next' => array(
							'type' => 'text',
							'name' => t('Текст для «следующая»'),
							'description' => '',
							'default' => '&gt;'
							//'default' => 'туда'
						),

			),
		t('Настройки плагина pagination_simple'), // титул
		t('Укажите необходимые опции.' )  // инфо
	);
}


# end file