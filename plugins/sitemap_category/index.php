<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Wave
 * http://wave.fantregata.com/
 * На основе оригинального плагина MaxSite CMS
 * http://max-3000.com/
 */

# функция автоподключения плагина
function sitemap_category_autoload($args = array())
{
	mso_hook_add( 'content', 'sitemap_category_content'); # хук на обработку текста [sitemap]
	mso_hook_add( 'page_404', 'sitemap_category404'); # хук на 404-страницу
	mso_hook_add( 'sitemap', 'sitemap_category404'); # хук на сайтмап
	if ( mso_segment(1) == 'sitemap') mso_hook_add( 'head_meta', 'meta_sitemap_category');
}



function meta_sitemap_category($args = array())
{
	$options = sitemap_category_options();
	if ($options['sc_title'] and $args['info'] == 'title') return $options['sc_title'];
	if ($options['sc_keywords'] and $args['info'] == 'keywords') return $options['sc_keywords'];
	if ($options['sc_description'] and $args['info'] == 'description') return $options['sc_description'];
}



function sitemap_category_options()
{
	// получим настройки
	$options = mso_get_option('plugin_sitemap_category', 'plugins', array() );
	if (!isset($options['sc_limit'])) $options['sc_limit'] = 50;
	if (!$options['sc_limit']) $options['sc_limit'] = 50;
	if (!isset($options['sc_blogpages'])) $options['sc_blogpages'] = true;
	if ($options['sc_blogpages']) $options['sc_blogpages'] = 'blog'; else $options['sc_blogpages'] = false;
	if (!isset($options['sc_textpre'])) $options['sc_textpre'] = t('Раз уж запрашиваемой страницы на этом сайте не оказалось, воспользуйтесь картой.', 'plugins');
	if (!isset($options['sc_textpost'])) $options['sc_textpost'] = t('И многое-многое другое.', 'plugins');
	if (!isset($options['sc_cat_sort'])) $options['sc_cat_sort'] = 'category_id';
	if (!isset($options['sc_show_no_cat'])) $options['sc_show_no_cat'] = 1;
	if (!isset($options['sc_no_double'])) $options['sc_no_double'] = 1;
	if (!isset($options['sc_no_cat_top'])) $options['sc_no_cat_top'] = 0;
	if (!isset($options['sc_no_cat_title'])) $options['sc_no_cat_title'] = t('Без рубрики.', 'plugins');
	if (!isset($options['sc_title'])) $options['sc_title'] = '';
	if (!isset($options['sc_keywords'])) $options['sc_keywords'] = '';
	if (!isset($options['sc_description'])) $options['sc_description'] = '';
	return $options;
}



# оюработка текста на предмет в нем [sitemap]
function sitemap_category_content($text = '')
{
	if (strpos($text, '[sitemap]') === false) // нет в тексте
	{
		return $text;
	}
	else 
	{
		return str_replace('[sitemap]', sitemap_category(sitemap_category_options()), $text);
	}
}



function sitemap_category404($text = '')
{
	$options = sitemap_category_options();
	return  '<p>' . $options['sc_textpre'] . '</p>' . sitemap_category($options) . '<p>' . $options['sc_textpost'] . '</p>';
}



function exclude_category($par = array())
{
	$out = NR . '<ul class="home-cat-block">' . NR;
	$par['cat_id'] = false;
	$pages = mso_get_pages($par, $pagination);
	if (!$pages) return $out . '</ul>'. NR;
	foreach ($pages as $page)
	{
		extract ($page);
		if (empty($page_categories)) $out .= mso_page_title($page_slug, $page_title, '<li>', '', true, false);
	}
	return $out . '</ul>'. NR;
}



function sitemap_category($options = array())
{
	global $MSO;
	$i=0;

	// кэш строим по url, потому что он меняется от пагинации
	$cache_key = 'sitemap_category' . serialize($MSO->data['uri_segment']);
	$k = mso_get_cache($cache_key);
	if ($k) return $k;
	$ids = array();

	$out = '<div class="sitemap">' . NR;

	$par = array( 
				//'limit' => $options['sc_limit'], // колво записей
				'no_limit' => true,
				'content'=> 0, // полные ли записи (1) или только заголовки (0)
				'cat_order' => 'category_id_parent', // сортировка рубрик
				'cat_order_asc' => 'asc', // порядок сортировки
				'type' => $options['sc_blogpages'], //блоговые или все записи
				'custom_type'=> 'home',
				);

	if ($options['sc_show_no_cat'] and $options['sc_no_cat_top']) $out .= '<h2 class="home-cat-block">' . $options['sc_no_cat_title'] . '</h2>' . exclude_category($par);

	$all_cats = mso_cat_array_single('page', $options['sc_cat_sort'], 'ASC', false, true); // список всех рубрик
	foreach($all_cats as $cat_id)
	{
		$par['cat_id'] = $cat_id['category_id'];
		$pages = mso_get_pages($par, $pagination);
		if ($pages) // есть страницы
		{
			$out .= '<h2 class="home-cat-block"><a href="' . getinfo('siteurl') . 'category/' . $cat_id['category_slug'] . '">' . $cat_id['category_name'] . '</a></h2>' . NR;

			$out .= '<ul class="home-cat-block">' . NR;
			foreach ($pages as $page) : // выводим в цикле

				extract($page);
				if ($options['sc_no_double'])
				{
					if (array_key_exists($page_id, $ids)) continue;
					$ids[$page_id] = $page_id;
				}
				$out .= mso_page_title($page_slug, $page_title, '<li>', '', true, false);
				$out .= '</li>' . NR;
			endforeach;
			$out .= '</ul><!--ul class="home-cat-block"-->' . NR;
		}// endif $pages
		
	} # end foreach $home_cat_block

	//$par['function_add_custom_sql'] = 'exclude_category';
	//$par['cat_id'] = false;
	if ($options['sc_show_no_cat'] and !$options['sc_no_cat_top']) $out .= '<h2 class="home-cat-block">' . $options['sc_no_cat_title'] . '</h2>' . exclude_category($par);

	$pagination['type'] = '';
	$out .= '</div>' . NR;

	mso_add_cache($cache_key, $out); // сразу в кэш добавим

	return $out;
}



function sitemap_category_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_sitemap_category', 'plugins', 
		array(
			'sc_cat_sort' => array(
							'type' => 'select',
							'name' => 'Сортировать рубрики по',
							'description' => '',
							'values' => 'category_id||ID рубрик#category_name||Имени рубрик#category_id_parent||Родительским рубрикам#category_menu_order||Выставленному menu_order',
							'default' => 'category_id',
			),
			'sc_blogpages' => array(
							'type' => 'checkbox', 
							'name' => 'Только блоговые записи.', 
							'description' => 'Отметьте, если хотите отображать только блоговые страницы. Иначе будут показаны все.', 
							'default' => '1'
						),
			/*
			'sc_limit' => array(
							'type' => 'text',
							'name' => 'Количество записей на страницу',
							'description' => '',
							'default' => 50
						),
			*/
			'sc_no_double' => array(
							'type' => 'checkbox', 
							'name' => 'Не дублировать страницы', 
							'description' => 'Если отмечено, то если страница принадлежит двум и более рубрикам, то отображается только в первой из них', 
							'default' => '1'
						),
			'sc_show_no_cat' => array(
							'type' => 'checkbox', 
							'name' => 'Показывать записи без рубрик', 
							'description' => 'Отметьте, если хотите отображать страницы, не принадлежащие ни одной рубрике', 
							'default' => '1'
						),
			'sc_no_cat_top' => array(
							'type' => 'checkbox', 
							'name' => 'Записи без рубрик вверху', 
							'description' => 'Если не отмечено, они отображаются внизу карты сайта', 
							'default' => '0'
						),
			'sc_no_cat_title' => array(
							'type' => 'text',
							'name' => 'Заголовок блока без рубрик',
							'description' => '', 
							'default' => t('Без рубрики.', 'plugins'),
						),
			'sc_textpre' => array(
							'type' => 'text',
							'name' => 'Текст перед картой сайта',
							'description' => '', 
							'default' => t('Раз уж запрашиваемой страницы на этом сайте не оказалось, воспользуйтесь картой.', 'plugins')
						),
			'sc_textpost' => array(
							'type' => 'text',
							'name' => 'Текст после карты сайта',
							'description' => '', 
							'default' => t('И многое-многое другое.', 'plugins')
						),
			'sc_title' => array(
							'type' => 'text',
							'name' => 'Title карты сайта',
							'description' => 'Если пусто, дефолтный', 
							'default' => '',
						),
			'sc_keywords' => array(
							'type' => 'text',
							'name' => 'Keywords карты сайта',
							'description' => 'Если пусто, дефолтный', 
							'default' => '',
						),
			'sc_description' => array(
							'type' => 'text',
							'name' => 'Description карты сайта',
							'description' => 'Если пусто, дефолтный', 
							'default' => '',
						),
			),
		'Настройки плагина sitemap_category', // титул
		'После изменения настроек сбрасывайте кеш системы.'   // инфо
	);
}

