<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Автор мода:
 * Жека Dark-Inside
 * http://di-modern.ru/
 */


# функция автоподключения плагина
function di_cat_accordion_autoload($args = array())
{
    #подключим css
    mso_hook_add('head_css', 'di_cat_accordion_head');

    #подключим js
	mso_hook_add('body_end', 'di_cat_accordion_body_end');

	# регистрируем виджет
	mso_register_widget('di_cat_accordion_widget', t('Рубрики Аккордеон'));
}

#подключим css
function di_cat_accordion_head($args = array())
{
	echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'di_cat_accordion/css/style.css">';

	return $args;
}

#подключим js
function di_cat_accordion_body_end($args = array())
{
	echo '<script src="' . getinfo('plugins_url') . 'di_cat_accordion/js/accordion.js"></script>' . NR;
}

# функция выполняется при деинсталяции плагина
function di_cat_accordion_uninstall($args = array())
{
	mso_delete_option_mask('di_cat_accordion_widget_', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция построения из массивов списка UL взята из common.php ибо там нормально правок не сделать.
# вход - массив из с [childs]=>array(...)
function mso_create_list_mod($a = array(), $options = array(), $child = false)
{
	if (!$a) return '';

	if (!isset($options['class_ul'])) $options['class_ul'] = ''; // класс UL
	if (!isset($options['class_ul_style'])) $options['class_ul_style'] = ''; // свой стиль для UL
	if (!isset($options['class_child'])) $options['class_child'] = 'child'; // класс для ребенка
	if (!isset($options['class_child_style'])) $options['class_child_style'] = ''; // свой стиль для ребенка

	if (!isset($options['class_current'])) $options['class_current'] = 'current-page'; // класс li текущей страницы
	if (!isset($options['class_current_style'])) $options['class_current_style'] = ''; // стиль li текущей страницы

	if (!isset($options['class_li'])) $options['class_li'] = ''; // класс LI
	if (!isset($options['class_li_style'])) $options['class_li_style'] = ''; // стиль LI

	if (!isset($options['format'])) $options['format'] = '[LINK][TITLE][/LINK]'; // формат ссылки
	if (!isset($options['format_current'])) $options['format_current'] = '<span>[TITLE]</span>'; // формат для текущей

	if (!isset($options['title'])) $options['title'] = 'page_title'; // имя ключа для титула
	if (!isset($options['link'])) $options['link'] = 'page_slug'; // имя ключа для слага
	if (!isset($options['descr'])) $options['descr'] = 'category_desc'; // имя ключа для описания
	if (!isset($options['id'])) $options['id'] = 'page_id'; // имя ключа для id
	if (!isset($options['slug'])) $options['slug'] = 'page_slug'; // имя ключа для slug
	if (!isset($options['menu_order'])) $options['menu_order'] = 'page_menu_order'; // имя ключа для menu_order
	if (!isset($options['id_parent'])) $options['id_parent'] = 'page_id_parent'; // имя ключа для id_parent

	if (!isset($options['count'])) $options['count'] = 'count'; // имя ключа для количества элементов

	if (!isset($options['prefix'])) $options['prefix'] = 'page/'; // префикс для ссылки
	if (!isset($options['current_id'])) $options['current_id'] = true; // текущая страница отмечается по page_id - иначе по текущему url
	if (!isset($options['childs'])) $options['childs'] = 'childs'; // поле для массива детей


	// если true, то главная рабрика выводится без ссылки в <span>
	if (!isset($options['group_header_no_link'])) $options['group_header_no_link'] = false;

	# функция, которая сработает на [FUNCTION]
	# эта функция получает в качестве параметра текущий массив $elem
	if (!isset($options['function'])) $options['function'] = false;


	if (!isset($options['nofollow']) or !$options['nofollow']) $options['nofollow'] = ''; // можно указать rel="nofollow" для ссылок
		else $options['nofollow'] = ' rel="nofollow"';


	$class_child = $class_child_style = $class_ul = $class_ul_style = '';
	$class_current = $class_current_style = $class_li = $class_li_style = '';

	// [LEVEL] - заменяется на level-текущий уровень вложенности
	if ($options['class_child']) $class_child = ' class="' . $options['class_child'] . ' [LEVEL]"';

	static $level = 0;
	$class_child = str_replace('[LEVEL]', 'level' . $level, $class_child);

	if ($options['class_child_style']) $class_child_style = ' style="' . $options['class_child_style'] . '"';
	if ($options['class_ul']) $class_ul = ' class="' . $options['class_ul'] . '"';
	if ($options['class_ul_style']) $class_ul_style = ' style="' . $options['class_ul_style'] . '"';

	if ($options['class_current']) $class_current = ' class="' . $options['class_current'] . '"';
	if ($options['class_current_style']) $class_current_style = ' style="' . $options['class_current_style'] . '"';

	//C меню основнысм протеворечит(тоже кнопочку в низ пилит на списках) вои и юзаем новый css класс
	if ($options['class_li']) $class_li = ' class="' . $options['class_li'] . ' accordion_ul"';
		else $class_li = ' class="accordion_ul"';

	if ($options['class_li_style']) $class_li_style = ' style="' . $options['class_li_style'] . '"';




	if ($child) $out = NR . '	<ul' . $class_child . $class_child_style . '>';
		else $out = NR . '<ul' . $class_ul . $class_ul_style . '>';

	$current_url = getinfo('siteurl') . mso_current_url(); // текущий урл


	// из текущего адресу нужно убрать пагинацию
	$current_url = str_replace('/next/' . mso_current_paged(), '', $current_url);

	foreach ($a as $elem)
	{
		$title = $elem[$options['title']];
		$elem_slug = mso_strip($elem[$options['link']]); // slug элемента

		$url = getinfo('siteurl') . $options['prefix'] . $elem_slug;

		// если это page, то нужно проверить вхождение этой записи в элемент рубрики
		// если есть, то ставим css-класс curent-page-cat
		$curent_page_cat_class = is_page_cat($elem_slug, false, false) ? ' class="curent-page-cat"' : '';

		$link = '<a' . $options['nofollow'] . ' href="' . $url . '" title="' . htmlspecialchars($title) . '"' .$curent_page_cat_class . '>';

		if (isset($elem[$options['descr']])) $descr = $elem[$options['descr']];
		else $descr = '';

		if (isset($elem[$options['count']])) $count = $elem[$options['count']];
		else $count = '';

		if (isset($elem[$options['id']])) $id = $elem[$options['id']];
		else $id = '';

		if (isset($elem[$options['slug']])) $slug = $elem[$options['slug']];
		else $slug = '';

		if (isset($elem[$options['menu_order']])) $menu_order = $elem[$options['menu_order']];
		else $menu_order = '';

		if (isset($elem[$options['id_parent']])) $id_parent = $elem[$options['id_parent']];
		else $id_parent = '';

		$cur = false;

		if ($options['current_id']) // текущий определяем по id страницы
		{
			if (isset($elem['current']))
			{
				$e = $options['format_current'];
				$cur = true;
			}
			else
				$e = $options['format'];
		}
		else // определяем по урлу
		{
			if ($url == $current_url)
			{
				$e = $options['format_current'];
				$cur = true;
			}
			else $e = $options['format'];

		}

		$e = str_replace('[LINK]', $link, $e);
		$e = str_replace('[/LINK]', '</a>', $e);
		$e = str_replace('[TITLE]', $title, $e);
		$e = str_replace('[TITLE_HTML]', htmlspecialchars($title), $e);
		$e = str_replace('[DESCR]', $descr, $e);
		$e = str_replace('[DESCR_HTML]', htmlspecialchars($descr), $e);
		$e = str_replace('[ID]', $id, $e);
		$e = str_replace('[SLUG]', $slug, $e);
		$e = str_replace('[SLUG_HTML]', htmlspecialchars($slug), $e);
		$e = str_replace('[MENU_ORDER]', $menu_order, $e);
		$e = str_replace('[ID_PARENT]', $id_parent, $e);
		$e = str_replace('[COUNT]', $count, $e);
		$e = str_replace('[URL]', $url, $e);

		if ($options['function'] and function_exists($options['function']))
		{
			$function = $options['function']($elem);
			$e = str_replace('[FUNCTION]', $function, $e);
		}
		else $e = str_replace('[FUNCTION]', '', $e);

		if (isset($elem[$options['childs']]))
		{

			if ($cur) $out .= NR . '<li' . $class_current . $class_current_style . '>' . $e;
				else
				{
					if ($options['group_header_no_link'])
						$out .= NR . '<li' . $class_li . $class_li_style . '><span class="group_header">' . $title . '</span>';
					else
						$out .= NR . '<li' . $class_li . $class_li_style . '>' . $e;
				}

			++$level;
			$out .= mso_create_list_mod($elem[$options['childs']], $options, true);
			--$level;
			$out .= NR . '</li>';
		}
		else
		{
			if ($child) $out .= NR . '	';
				else $out .= NR;

			// если нет детей, то уберем класс group
			$class_li_1 = str_replace('accordion_ul', '', $class_li);

			if ($cur) $out .= '<li' . $class_current . $class_current_style . '>' . $e . '</li>';
				else $out .= '<li' . $class_li_1 . $class_li_style . '>' . $e . '</li>';
		}
	}

	if ($child) $out .= NR . '	</ul>' . NR;
		else $out .= NR . '</ul>' . NR;

	$out = str_replace('<li class="">', '<li>', $out);

	return $out;
}


# функция, которая берет настройки из опций виджетов
function di_cat_accordion_widget($num = 1)
{
	$widget = 'di_cat_accordion_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
		else $options['header'] = '';

	if ( isset($options['include']) ) $options['include'] = mso_explode($options['include']);
		else $options['include'] = array();

	if ( isset($options['exclude']) ) $options['exclude'] = mso_explode($options['exclude']);
		else $options['exclude'] = array();


	return di_cat_accordion_widget_custom($options, $num);
}


# форма настройки виджета
# имя функции = виджет_form
function di_cat_accordion_widget_form($num = 1)
{

	$widget = 'di_cat_accordion_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = mso_get_option($widget, 'plugins', array());

	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['format']) ) $options['format'] = '[LINK][TITLE]<sup>[COUNT]</sup>[/LINK]';
	if ( !isset($options['format_current']) ) $options['format_current'] = '<span>[TITLE]<sup>[COUNT]</sup></span>';
	if ( !isset($options['include']) ) $options['include'] = '';
	if ( !isset($options['exclude']) ) $options['exclude'] = '';
	if ( !isset($options['hide_empty']) ) $options['hide_empty'] = '0';
	if ( !isset($options['order']) ) $options['order'] = 'category_name';
	if ( !isset($options['order_asc']) ) $options['order_asc'] = 'ASC';
	if ( !isset($options['include_child']) ) $options['include_child'] = '0';
	if ( !isset($options['nofollow']) ) $options['nofollow'] = 0;
	if ( !isset($options['group_header_no_link']) ) $options['group_header_no_link'] = 0;
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');

	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');

	$form .= mso_widget_create_form(t('Формат'), form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ), t('Например: [LINK][TITLE]&lt;sup&gt;[COUNT]&lt;/sup&gt;[/LINK]'));

	$form .= mso_widget_create_form(t('Формат текущей'), form_input( array( 'name'=>$widget . 'format_current', 'value'=>$options['format_current'] ) ), t('Например: &lt;span&gt;[TITLE]&lt;sup&gt;[COUNT]&lt;/sup&gt;&lt;/span&gt;&lt;br&gt;>Все варианты: [SLUG], [ID_PARENT], [ID], [MENU_ORDER], [TITLE], [TITLE_HTML], [COUNT], [DESCR], [DESCR_HTML], [LINK][/LINK], [URL]'));

	$form .= mso_widget_create_form(t('Включить только'), form_input( array( 'name'=>$widget . 'include', 'value'=>$options['include'] ) ), t('Укажите номера рубрик через запятую или пробел'));

	$form .= mso_widget_create_form(t('Исключить'), form_input( array( 'name'=>$widget . 'exclude', 'value'=>$options['exclude'] ) ), t('Укажите номера рубрик через запятую или пробел'));

	$form .= mso_widget_create_form(t('Если нет записей'), form_dropdown( $widget . 'hide_empty', array(
		'0'=>t('Отображать рубрику (количество записей ведется без учета опубликованности)'),
		'1'=>t('Скрывать рубрику (количество записей ведется только по опубликованным)')),
		$options['hide_empty']), '');

	$form .= mso_widget_create_form(t('Сортировка'), form_dropdown( $widget . 'order',
			array(
				'category_name' => t('По имени рубрики'),
				'category_id' => t('По ID рубрики'),
				'category_menu_order' => t('По выставленному menu order'),
				'pages_count' => t('По количеству записей')),
				$options['order']), '');

	$form .= mso_widget_create_form(t('Порядок'), form_dropdown( $widget . 'order_asc',
			array(
				'ASC'=>t('Прямой'),
				'DESC'=>t('Обратный')
				), $options['order_asc']), '');

	$form .= mso_widget_create_form(t('Включать потомков'), form_dropdown( $widget . 'include_child',
				array(
				'0'=>t('Всегда'),
				'1'=>t('Только если явно указана рубрика'),
				'-1'=>t('Исключить всех')
				), $options['include_child']), '');

	$form .= mso_widget_create_form(t('Ссылки рубрик'), form_dropdown( $widget . 'nofollow',
				array(
				'0'=>t('Обычные'),
				'1'=>t('Устанавливать как nofollow (неиндексируемые поисковиками)')
				), $options['nofollow']), '');

	$form .= mso_widget_create_form(t('Рубрика группы'), form_dropdown( $widget . 'group_header_no_link',
				array(
				'0'=>t('Ссылка'),
				'1'=>t('Текст')
				), $options['group_header_no_link']), '');

	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function di_cat_accordion_widget_update($num = 1)
{

	$widget = 'di_cat_accordion_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['format'] = mso_widget_get_post($widget . 'format');
	$newoptions['format_current'] = mso_widget_get_post($widget . 'format_current');
	$newoptions['include'] = mso_widget_get_post($widget . 'include');
	$newoptions['exclude'] = mso_widget_get_post($widget . 'exclude');
	$newoptions['hide_empty'] = mso_widget_get_post($widget . 'hide_empty');
	$newoptions['order'] = mso_widget_get_post($widget . 'order');
	$newoptions['order_asc'] = mso_widget_get_post($widget . 'order_asc');
	$newoptions['include_child'] = mso_widget_get_post($widget . 'include_child');
	$newoptions['nofollow'] = mso_widget_get_post($widget . 'nofollow');
	$newoptions['group_header_no_link'] = mso_widget_get_post($widget . 'group_header_no_link');
	if ( $options != $newoptions )
		mso_add_option($widget, $newoptions, 'plugins' );
}


function di_cat_accordion_widget_custom($options = array(), $num = 1)
{
	if ( !isset($options['include']) ) $options['include'] = array();
	if ( !isset($options['exclude']) ) $options['exclude'] = array();
	if ( !isset($options['format']) ) $options['format'] = '[LINK][TITLE]<sup>[COUNT]</sup>[/LINK]<br>[DESCR]';
	if ( !isset($options['format_current']) ) $options['format_current'] = '<span>[TITLE]<sup>[COUNT]</sup></span><br>[DESCR]';
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['hide_empty']) ) $options['hide_empty'] = 0;
	if ( !isset($options['order']) ) $options['order'] = 'category_name';
	if ( !isset($options['order_asc']) ) $options['order_asc'] = 'ASC';
	if ( !isset($options['include_child']) ) $options['include_child'] = 0;
	if ( !isset($options['nofollow']) ) $options['nofollow'] = false;
	if ( !isset($options['group_header_no_link']) ) $options['group_header_no_link'] = false;
	$cache_key = 'di_cat_accordion_widget' . serialize($options) . $num;

	$k = mso_get_cache($cache_key);
	if ($k) // да есть в кэше
	{
		$all = $k;
	}
	else
	{
		/*
			$type = 'page',
			$parent_id = 0,
			$order = 'category_menu_order',
			$asc = 'asc',
			$child_order = 'category_menu_order',
			$child_asc = 'asc',
			$in = false,
			$ex = false,
			$in_child = false,
			$hide_empty = false,
			$only_page_publish = false,
			$date_now = true,
			$get_pages = true
		*/

		$all = mso_cat_array(
			'page',
			0,
			$options['order'],
			$options['order_asc'],
			$options['order'],
			$options['order_asc'],
			$options['include'],
			$options['exclude'],
			$options['include_child'],
			$options['hide_empty'],
			true,
			true,
			false
			);

		//mso_add_cache($cache_key, $all); // сразу в кэш добавим
	}

	//pr($all);

	$out = mso_create_list_mod($all,
		array(
			'childs'=>'childs',
			'format'=>$options['format'],
			'format_current'=>$options['format_current'],
			'class_ul'=>'ul_cat_accordion',
			'title'=>'category_name',
			'link'=>'category_slug',
			'current_id'=>false,
			'prefix'=>'category/',
			'count'=>'pages_count',
			'slug'=>'category_slug',
			'id'=>'category_id',
			'menu_order'=>'category_menu_order',
			'id_parent'=>'category_id_parent',
			'nofollow'=>$options['nofollow'],
			'group_header_no_link' => $options['group_header_no_link'],
			)
	);
	    //нужно обезательно добавить div ибо это же js
	if ($out and $options['header']) $out = $options['header'] . '<div id="cat_accordion">' . $out. '</div>';


	return $out;
}


# end file