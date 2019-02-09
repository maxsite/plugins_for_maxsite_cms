<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Н. Громов
 * http://nicothin.ru
 * На основе оригинального плагина MaxSite CMS
 * http://max-3000.com/
 */

# функция автоподключения плагина
function sitemap_mod_autoload($args = array())
{
   mso_hook_add( 'content', 'sitemap_mod_content'); # хук на обработку текста [sitemap]
   mso_hook_add( 'page_404', 'sitemap_mod404'); # хук на 404-страницу
   mso_hook_add( 'sitemap', 'sitemap_mod404'); # хук на 404-страницу
}


# оюработка текста на предмет в нем [sitemap]
function sitemap_mod_content($text = '')
{
	if (strpos($text, '[sitemap]') === false) {
		return $text;
	}
	else {
		return str_replace('[sitemap]', sitemap_mod(), $text);
	}
}


# обработка текста на предмет в нем [sitemap]
function sitemap_mod404($text = '')
{
	$options = mso_get_option('plugin_sitemap_mod', 'plugins', array() );
	if (!isset($options['s_text_do_404'])) $options['s_text_do_404'] = 'Воспользуйтесь картой.';
	if (trim($options['s_text_do_404']) != '')
		return  '<p>' . $options['s_text_do_404'] . '</p>' . sitemap_mod();
	else 
		return  sitemap_mod();
}

function sitemap_mod($arg = array())
{
	global $MSO;

 	// кэш строим по url, потому что он меняется от пагинации
 	$cache_key = 'sitemap_mod' . serialize($MSO->data['uri_segment']);
	$k = mso_get_cache($cache_key);
	if ($k) return $k; 
	
	// получим настройки
	$options = mso_get_option('plugin_sitemap_mod', 'plugins', array() );
	if (!isset($options['s_text_do'])) $options['s_text_do'] = '';
	if (!isset($options['s_text_do_404'])) $options['s_text_do_404'] = 'Воспользуйтесь картой.';
	if (!isset($options['s_pages_text'])) $options['s_pages_text'] = 'Страницы';
	if (!isset($options['s_home'])) $options['s_home'] = true;
	if (!isset($options['s_categories'])) $options['s_categories'] = true;
	if (!isset($options['s_all_categories_name'])) $options['s_all_categories_name'] = 'Рубрики';
	if (!isset($options['s_all_categories_page'])) $options['s_all_categories_page'] = false;
	if (!isset($options['s_categories_widget_set'])) $options['s_categories_widget_set'] = '0';
	if (!isset($options['s_tags'])) $options['s_tags'] = true;
	if (!isset($options['s_tags_name'])) $options['s_tags_name'] = 'Метки';
	if (!isset($options['s_tags_page'])) $options['s_tags_page'] = false;
	if (!isset($options['s_tags_numb'])) $options['s_tags_numb'] = '7';
	if (!isset($options['s_tags_format'])) $options['s_tags_format'] = '<a href="%URL%">%TAG%</a> <span>(%COUNT%)</span>';
	if (!isset($options['s_static_pages'])) $options['s_static_pages'] = true;
	if (!isset($options['s_user_pages'])) $options['s_user_pages'] = false;
	if (!isset($options['s_contact'])) $options['s_contact'] = true;
	if (!isset($options['s_blog_pages'])) $options['s_blog_pages'] = true;
	if (!isset($options['s_blog_pages_text'])) $options['s_blog_pages_text'] = 'Записи в блоге по датам';
	if (!isset($options['s_blog_pages_cat_include'])) $options['s_blog_pages_cat_include'] = '0';
	if (!isset($options['s_blog_pages_date_format'])) $options['s_blog_pages_date_format'] = 'new';
	if (!isset($options['s_blog_pages_numb'])) $options['s_blog_pages_numb'] = '40';
	
	$out = '';
	//$out = pr($options, false);

	$out .= '<div class="sitemap">' . NR;
	
	if ($options['s_text_do'] and mso_segment(1) == 'sitemap') $out .= $options['s_text_do'].NR;
	
	// а не пагинация ли это?
	$pages_2 = false;
	foreach ($MSO->data['uri_segment'] as $k=>$s) {
		if ($s == 'next' and $MSO->data['uri_segment'][$k+1] >1) { // да, это 2я и далее страница
			$pages_2 = true;
			break;
		}
	}
	
	if (!$pages_2) {
		
		$out .= '<h2>'.$options['s_pages_text'].'</h2>';
		
		// показываем ссылку на главную
		if ($options['s_home'])
			$out .=  '<ul><li><a href="'.getinfo('siteurl').'">'.t('Главная страница').'</a><ul>';
		else $out .=  '<ul>';
		
		// показываем список рубрик
		if ($options['s_categories']) {
			if ($options['s_categories_widget_set'] == '00') {
				$opt_cat_w['format'] = '[LINK][TITLE]<sup>[COUNT]</sup>[/LINK]';
				$opt_cat_w['include'] = '';
				$opt_cat_w['exclude'] = '';
				$opt_cat_w['hide_empty'] = '0';
				$opt_cat_w['order'] = 'category_name';
				$opt_cat_w['order_asc'] = 'ASC';
				$opt_cat_w['include_child'] = '0';
			}
			else { 
				$opt_cat_w = mso_get_option($options['s_categories_widget_set'], 'plugins', array() );
				if (!isset($opt_cat_w['format'])) $opt_cat_w['format'] = '[LINK][TITLE]<sup>[COUNT]</sup>[/LINK]';
				if (!isset($opt_cat_w['include'])) $opt_cat_w['include'] = '';
				if (!isset($opt_cat_w['exclude'])) $opt_cat_w['exclude'] = '';
				if (!isset($opt_cat_w['hide_empty'])) $opt_cat_w['hide_empty'] = '0';
				if (!isset($opt_cat_w['order'])) $opt_cat_w['order'] = 'category_name';
				if (!isset($opt_cat_w['order_asc'])) $opt_cat_w['order_asc'] = 'ASC';
				if (!isset($opt_cat_w['include_child'])) $opt_cat_w['include_child'] = '0';
			}
			//$out .= pr($opt_cat_w, false);
			// задан линк на страницу со всеми рубриками?
			if ($options['s_all_categories_page']) 
				$all_categories_page = '<a href="'.getinfo('site_url').$options['s_all_categories_page'].'">'.$options['s_all_categories_name'].'</a>';
			else
				$all_categories_page = $options['s_all_categories_name'];
			// показываем список
			$all = mso_cat_array('page', 0, $opt_cat_w['order'], $opt_cat_w['order_asc'], $opt_cat_w['order'], $opt_cat_w['order_asc'], $opt_cat_w['include'], $opt_cat_w['exclude'], $opt_cat_w['include_child'], $opt_cat_w['hide_empty'], true);
			$out .= '<li>'.$all_categories_page.mso_create_list($all, array('childs'=>'childs', 'format'=>$opt_cat_w['format'], 'format_current'=>$opt_cat_w['format'], 'class_ul'=>'is_link', 'title'=>'category_name', 'link'=>'category_slug', 'current_id'=>false, 'prefix'=>'category/', 'count'=>'pages_count', 'slug'=>'category_slug', 'id'=>'category_id', 'menu_order'=>'category_menu_order', 'id_parent'=>'category_id_parent' ) ).'</li>';
			
		}
		
		// показываем список частых меток
		if ($options['s_tags']){
			if ($options['s_tags_page'])
				$out .= '<li><a href="'.getinfo('siteurl').$options['s_tags_page'].'">'.$options['s_tags_name'].'</a><ul>';
			else
				$out .= '<li>'.$options['s_tags_name'].'<ul>';
			
			// проверим, число ли введено для количества меток
			if (!(int)$options['s_tags_numb']) $options['s_tags_numb'] = 7;

			// получим метки
			require_once( getinfo('common_dir') . 'meta.php' );
			$tagcloud = mso_get_all_tags_page();
			asort($tagcloud);
			arsort($tagcloud);
			
			// выведем сколько нужно
			$url = getinfo('siteurl') . 'tag/';
			$i = 0;
			foreach ($tagcloud as $tag => $count) {
				$out .= str_replace(array('%URL%', '%TAG%', '%COUNT%'), 
									array($url . urlencode($tag), $tag, $count), '<li>'.$options['s_tags_format'].'</li>');
				$i++;
				if ($i == $options['s_tags_numb']) break;
			}
			$out .= '</ul></li>';
		}
		
		// показываем список статичных страниц
		if ($options['s_static_pages']){
				$spar = array( 
					'no_limit' => true,
					'type'=> 'static', 
					'custom_type'=> 'home', 
					'content'=> false,
					'order'=>'page_menu_order',
					'get_page_categories'=>false,
					'get_page_meta_tags'=>false,
					'get_page_count_comments'=>false,
					); 
			$spages = mso_get_pages($spar, $spagination);
			if ($spages){
				foreach ($spages as $spage){
					//$out .= pr($spage, false);
					if ($spage['page_id_parent'] == 0){ // если страница не является дитем
						// построение карты страниц
						if ($spagemap = mso_page_map($spage['page_id'])) {
							// создание ul-списка со своими опциями
							$res = mso_create_list($spagemap, array('format_current'=>'[LINK][TITLE][/LINK]', 'class_ul'=>'nenujen',  'class_child'=>'', 'current_id'=>false ) );
							// уберем ненужные ul (что поделать, mso_create_list() пока не имеет для этого мех-ма)
							$res = str_replace('<ul class="nenujen">', '', $res);
							$res = str_replace(NR . '</ul>' . NR, NR, $res);
							$out .= $res;
						}
						else
							$out .= '<li><a href="'.getinfo('siteurl').$spage['page_slug'].'">'.$spage['page_title'].'</a></li>';
					}
				}
			}
		}
		
		// показываем свой список страниц
		if ($options['s_user_pages']){
			$out .= mso_menu_build($options['s_user_pages'], '', false);
		}
		
		// показываем ссылку на страницу Контакты
		if ($options['s_contact']){
			$out .=  '<li><a href="'.getinfo('siteurl').'contact">'.t('Контакт').'</a></li>';
		}
		
		if ($options['s_home'])
			$out .=  '</ul></li></ul>';
		else $out .=  '</ul>';
		
		if ($options['s_blog_pages'] and !$pages_2)
			$out .= '<hr>';
	}
	
	// все записи в блоге
	if ($options['s_blog_pages']) {
		$par = array( 
				'limit'=> (int)$options['s_blog_pages_numb'],
				'custom_type'=> 'home', 
				'content'=> false,
				'cat_order'=>'category_id_parent', 
				'cat_order_asc'=>'asc',
				'cat_id' => $options['s_blog_pages_cat_include'], 
				); 
		$pages = mso_get_pages($par, $pagination); // получим все
	
		if ($pages)
		{ 
			$out .= '<h2>'.$options['s_blog_pages_text'].'</h2>';
			$first = true;
			$date2 = '00';
			foreach ($pages as $page) {
				// формат даты для месяца и года
				if ($options['s_blog_pages_date_format'] == 'new')
					$date = mso_page_date($page['page_date_publish'], array('format' => 'F Y', 'month' => t('Январь Февраль Март Апрель Май Июнь Июль Август Сентябрь Октябрь Ноябрь Декабрь')), '', '', false);
				else
					$date = mso_date_convert('m/Y', $page['page_date_publish']);
				
				if ($first) {
					$out .= '<h3 class="first_month">' . $date . '</h3>' . NR . '<ul class="sitemap_blogpages">' . NR;
					$first = false;
				}
				elseif ($date1 != $date) {
					$out .= '</ul>' . NR . '<h3>' . $date . '</h3>' . NR . '<ul class="sitemap_blogpages">' . NR;
				}
				
				$slug = mso_slug($page['page_slug']);
				
				// формат даты для числа
				if ($options['s_blog_pages_date_format'] == 'new') {
					$cdate = mso_page_date($page['page_date_publish'], array('format' => 'd F', 'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), '', '', false);
					$cdate = preg_replace ('/^0/', '', $cdate);
				}
				else
					$cdate = mso_date_convert('d', $page['page_date_publish']);
				
				$out .= '<li';
				if ($date2 != $cdate)
					$out .= ' class="wdate"><span>' . $cdate . ': </span>';
				else 
					$out .= '>';
				$out .= '<a href="' . getinfo('siteurl') 
						. 'page/' . $slug . '" title="' . $page['page_title'] . '">' 
						. $page['page_title'] . '</a>';
				
				if ($page['page_categories'])
					$out .=  '<br><small class="gray">'.t('В рубрике')
							. mso_page_cat_link($page['page_categories'], mso_get_option('cat_sep', 'templates', ',').' ', ' ', '', false)
							. '</small>';

				$out .=  '</li>' . NR;
						
				$date1 = $date;
				if ($date2 != $cdate)
					$date2 = $cdate;
			}
			$out .= '</ul>' . NR;
		}
	}
	
	$out .= '</div><!-- .sitemap -->' . NR;

	$pagination['type'] = '';
	ob_start();
	mso_hook('pagination', $pagination);
	$out .=  ob_get_contents();
	ob_end_clean();

	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}



function sitemap_mod_mso_options() 
{
	global $MSO;
	// получим список category_widget
	if ($MSO->sidebars) {
		$res_cat_w = array(); // результат
		// обходим сайдбары
		foreach ($MSO->sidebars as $name => $sidebar) {
			$sboptions = mso_get_option('sidebars-' . mso_slug($name), 'sidebars', array());
			// обходим виджеты сайдбара
			foreach ($sboptions as $cat_w) {
				if (preg_match('/^category_widget/', $cat_w)) { // о, это какой то из category_widget
					$cat_w = explode(' ', $cat_w); // разобьем по пробелу
					// выявим номер или идентификатор
					if (count($cat_w) > 1) { 
						$num_orig = trim($cat_w[1]);
						$num = mso_slug($num_orig);
						$num = str_replace('--', '-', $num);
						$cat_w[1] = ' '.$cat_w[1];
					}
					else {
						$num = 0;
						$cat_w[1] = '';
					}
					// выявим название
					if ($num) $name_cat_w = $MSO->widgets[$cat_w[0]] . ' (' . $num_orig . ')';
						else $name_cat_w = $MSO->widgets[$cat_w[0]];
					// добавим строку в массив category_widget
					$res_cat_w[] = '# '.$cat_w[0].'_'.$num.'||'.$name_cat_w.' ('.$cat_w[0].$cat_w[1].')';
				}
			}
		}
		// формируем отформатированный нужным образом список виджетов рубрик
		$category_widget_list = '00||'.t('Не брать настройки из виджета');
		foreach ($res_cat_w as $cl) {
			$category_widget_list .= ' '.$cl;
		}
		//$out .= pr($category_widget_list ,false);
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_sitemap_mod', 'plugins', 
		array(
			's_text_do' => array(
							'type' => 'textarea', 
							'name' => 'Текст, выводимый перед картой сайта на странице <a href="'.getinfo('site_url').'sitemap">'.getinfo('site_url').'sitemap</a>', 
							'description' => 'Используйте html!', 
							'default' => ''
						),
			's_text_do_404' => array(
							'type' => 'text', 
							'name' => 'Текст, выводимый перед картой сайта на страницах с типом page_404', 
							'description' => 'На странице '.getinfo('site_url').'sitemap этот текст не выводится. При выводе текст оборачивается в теги параграфа.', 
							'default' => 'Воспользуйтесь картой.'
						),
			's_pages_text' => array(
							'type' => 'text', 
							'name' => 'Заголовок блока страниц', 
							'description' => '', 
							'default' => 'Страницы'
						),
			's_home'  => array(
							'type' => 'checkbox', 
							'name' => 'Показывать ссылку на главную страницу', 
							'description' => 'Все остальные страницы будут показаны как потомки главной.', 
							'default' => '1'
						),
			's_categories'  => array(
							'type' => 'checkbox', 
							'name' => 'Показывать список рубрик сайта', 
							'description' => 'Настройки списка (исключения и пр.) берутся из настроек указанного виджета рубрик (см. ниже).', 
							'default' => '1'
						),
			's_all_categories_name' => array(
							'type' => 'text', 
							'name' => 'Название корневого пункта списка рубрик', 
							'description' => '', 
							'default' => 'Рубрики'
						),
			's_all_categories_page' => array(
							'type' => 'text', 
							'name' => 'Адрес страницы, показывающей список всех рубрик сайта', 
							'description' => 'Введите адрес относительно '.getinfo('site_url').'.<br>Обычно, такой страницы в шаблоне нет, однако ее весьма просто написать самому или взять из другого шаблона (к примеру — <a href="http://nicothin.ru/demo_templates/?theme=BlueMania_nicothin">BlueMania_nicothin</a>)<br>Если оставить поле пустым, корневой пункт списка рубрик будет показан как простой текст, а не как ссылка.', 
							'default' => ''
						),
			's_categories_widget_set' => array(
							'type' => 'select', 
							'name' => 'Виджет, из которого берутся настройки списка рубрик', 
							'description' => 'Выберите из определенных Вами виджетов.', 
							'values' => $category_widget_list, 
							'default' => '0'
						),
			's_tags'  => array(
							'type' => 'checkbox', 
							'name' => 'Показывать список наиболее часто используемых меток', 
							'description' => 'Будет показана ссылка на страницу со всеми метками сайта и список из 7 наиболее часто используемых меток.', 
							'default' => '1'
						),
			's_tags_name' => array(
							'type' => 'text', 
							'name' => 'Название корневого пункта списка тегов', 
							'description' => '', 
							'default' => 'Метки'
						),
			's_tags_page' => array(
							'type' => 'text', 
							'name' => 'Адрес страницы, показывающей список меток сайта', 
							'description' => 'Введите адрес относительно '.getinfo('site_url').'.<br>Обычно, такой страницы в шаблоне нет, однако ее весьма просто написать самому или взять из другого шаблона (к примеру — <a href="http://nicothin.ru/demo_templates/?theme=BlueMania_nicothin">BlueMania_nicothin</a>)<br>Если оставить поле пустым, корневой пункт списка меток будет показан как простой текст, а не как ссылка.', 
							'default' => ''
						),
			's_tags_numb' => array(
							'type' => 'text', 
							'name' => 'Количество выводимых меток', 
							'description' => 'Количество меток в списке.', 
							'default' => '7'
						),
			's_tags_format' => array(
							'type' => 'text', 
							'name' => 'Формат вывода меток', 
							'description' => '', 
							'default' => '<a href="%URL%">%TAG%</a> <span>(%COUNT%)</span>'
						),
			's_static_pages'  => array(
							'type' => 'checkbox', 
							'name' => 'Показывать список статичных страниц', 
							'description' => 'Будет Выведен список статичных страниц с иерархией.', 
							'default' => '1'
						),
			's_user_pages' => array(
							'type' => 'textarea', 
							'name' => 'Собственный список выводимых страниц', 
							'description' => 'Укажите полные адреса в меню и через | название ссылки. Каждый пункт в одной строчке.<br>Пример: http://maxsite.org/ | Блог Макса<br> Для группы меню используйте [ для открытия и ] для закрытия группы выпадающих пунктов. Первый пункт после [ — родитель группы. Например:<pre>[<br> | Медиа<br>audio | Аудио<br>video | Видео<br>photo | Фото<br>]</pre>', 
							'default' => ''
						),
			's_contact'  => array(
							'type' => 'checkbox', 
							'name' => 'Показывать ссылку на страницу «Контакт»', 
							'description' => '', 
							'default' => '1'
						),
			's_blog_pages'  => array(
							'type' => 'checkbox', 
							'name' => 'Показывать список блоговых записей', 
							'description' => '', 
							'default' => '1'
						),
			's_blog_pages_text'  => array(
							'type' => 'text', 
							'name' => 'Заголовок списка блоговых страниц', 
							'description' => '', 
							'default' => 'Записи в блоге по датам'
						),
			's_blog_pages_cat_include'  => array(
							'type' => 'text', 
							'name' => 'Номера рубрик, записи которых нужно выводить', 
							'description' => 'Введите номера рубрик через запятую. Если указать «0», будут выведены записи всех рубрик.', 
							'default' => '0'
						),
			's_blog_pages_date_format' => array(
							'type' => 'select', 
							'name' => 'Формат вывода даты для блоговых записей', 
							'description' => '',
							'values' => 'new||С обозначением месяца текстом # old||С обозначением месяца числом (как в стандартном плагине sitemap)', 
							'default' => 'new'
						),
			's_blog_pages_numb'  => array(
							'type' => 'text', 
							'name' => 'Количество блоговых записей на странице', 
							'description' => 'Если общее число записей блогового типа больше этого числа, будет показана пагинация (при включенном плагине пагинации).', 
							'default' => '40'
						),
			),
		'Настройки плагина sitemap_mod', // титул
		''   // инфо
	);
	if ($_POST) mso_flush_cache();
}

?>