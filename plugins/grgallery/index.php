<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function grgallery_autoload($args = array())
{
	mso_create_allow('grgallery_edit', t('Админ-доступ к редактированию GrGallery', 'plugins/grgallery')); # добавляем в раздачу прав
	mso_register_widget('grgallery_widget', t('комплексное дополнение erotic', 'plugins')); # регистрируем виджет
	mso_hook_add( 'admin_init', 'grgallery_admin_init'); # хук на админку
	mso_hook_add('admin_page_form_add_all_meta', 'unit_attachments_page_form'); #добавляет в форму редактирования
	mso_hook_add('admin_page_form_add_block_1', 'unit_add1_page_form'); #добавляет в форму редактирования свою форму тегов и пр.
	mso_hook_add('new_page', 'page_pict'); # хук на добавление данных нов страница
	mso_hook_add('edit_page', 'page_pict'); # хук на изменение данных едит
	mso_hook_add( 'main_menu', 'grgallery_main_menu'); # хук на обработку вывода главного меню
	mso_hook_add( 'admin_head', 'grgallery_add_admin_head'); # хук на включение своего css-файла в админку	
	mso_hook_add( 'content_content', 'grgallery_content'); # хук на обработку вывода текста

}

# функция выполняется при активации (вкл) плагина
# сохранили опции плагина и создали папку загрузки
function grgallery_activate($args = array())
{
	global $MSO;
	$CI = & get_instance(); // получаем доступ к CodeIgniter
	require_once ($MSO->config['plugins_dir'].'grgallery/config.php');	// подгружаем переменные	
	$new_dir = getinfo('uploads_dir').$grgll['uploads_pict_dir']; # создаем папку для загрузки картинок
			if ( !is_dir($new_dir) ) // уже есть
			{
				@mkdir($new_dir, 0777); // нет каталога, пробуем создать
				@mkdir($new_dir . '/_mso_i', 0777); // нет каталога, пробуем создать
				@mkdir($new_dir . '/mini', 0777); // нет каталога, пробуем создать
			}
	# устанавливаем опции по умолчанию
	if ( !isset($grgll_options['use_upload_id_dir'])) $grgll_options['use_upload_id_dir'] = $grgll['use_upload_id_dir'];
	if ( !isset($grgll_options['prefix'])) $grgll_options['prefix'] = $grgll['prefix'];
	if ( !isset($grgll_options['uploads_pict_dir'])) $grgll_options['uploads_pict_dir'] = $grgll['uploads_pict_dir'];
	if ( !isset($grgll_options['q_col_list'])) $grgll_options['q_col_list'] = $grgll['q_col_list'];
	if ( !isset($grgll_options['view_all_tags'])) $grgll_options['view_all_tags'] = $grgll['view_all_tags'];
	$grgll_options = mso_add_option($grgll['main_key_options'], $grgll_options, 'plugins'); // сохранение опций
	return $args;
}

# функция выполняется при деинсталяции плагина
function grgallery_uninstall($args = array())
{	
	global $MSO;
	require_once ($MSO->config['plugins_dir'].'grgallery/config.php');	// подгружаем переменные
	mso_remove_allow('grgallery_edit');									// удаляем запись в раздаче прав
	mso_delete_option($grgll['main_key_options'], 'plugins'); 			// удаляем опции плагина
	mso_delete_option_mask('grgallery_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при указаном хуке admin_init
function grgallery_admin_init($args = array()) 
{
	if ( mso_check_allow('grgallery_edit') ) 
	{
		$this_plugin_url = 'grgallery';

		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		# можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		mso_admin_menu_add('plugins', $this_plugin_url, 'GrGallery');

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/grshop
		mso_admin_url_hook ($this_plugin_url, 'grgallery_admin_page');
	}
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook в ф-ции grgallery_admin_init
# ф-ция общих настроек плагина
function grgallery_admin_page($args = array()) 
{
	global $MSO;
	if ( !mso_check_allow('grgallery_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins/grgallery');
		return $args;
	}
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('GrGallery', 'plugins/grgallery') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('GrGallery', 'plugins/grgallery') . ' - " . $args; ' );
	require($MSO->config['plugins_dir'] . 'grgallery/common/submenu.php');
}

function unit_attachments_page_form($args = '') 
	{
	global $MSO;
	$out = '';
	if ( !mso_check_allow('grgallery_edit') ) return $args;
	require_once($MSO->config['plugins_dir'] . 'grgallery/admin/unit_form.php');
	$out .= unit_form($args = '');
	return $args.$out;
	}
	
function unit_add1_page_form ($args = '')
	{
	global $MSO;
	$out = '';
	if ( !mso_check_allow('grgallery_edit') ) return $args;
	require_once($MSO->config['plugins_dir'] . 'grgallery/admin/unit_add1_form.php');
	$out .= unit_add1_form ($args = '');
	return $args.$out;
	}
	
Function page_pict ($args = array())
	{
	global $MSO;
	if ( !mso_check_allow('grgallery_edit') ) return $args;	
	require_once($MSO->config['plugins_dir'] . 'grgallery/admin/grgall.php');
	$res = edit_grgall($args);
	return $args;
	}

	
#ф-ция во весь контент добавляет код блока картинок
#и таблицы тегов еще в работе

function grgallery_content($arg = array())
	{
	
	global $MSO;
	require_once($MSO->config['plugins_dir'] . 'grgallery/common/pubcom.php');
	$out = grgallpicts().$arg.grgalltags();
	return $out;
	
	}
	
#ф-ция формирует горизонтальный блок меню

function grgallery_main_menu($arg = array())
	{
	
	global $MSO;
	$all_cats = mso_cat_array_single('page', 'category_name', 'ASC', 'blog', true);
	
	$slug = mso_segment (2);	
	$category_this = mso_get_option('home_cat_id', 'templates', '0'); // по умолчанию id главной
	$main_cat_name = $all_cats[$category_this]['category_name'];
	
	$arg = '/ |'.$main_cat_name.'_NR_';	

	foreach ($all_cats as $key => $cat)
	if ($cat['category_slug'] == $slug)
		{ 
			$category_this = $key;
			break;
		}
	if ($all_cats[$category_this]['level'] != 0) $category_this = $all_cats[$category_this]['parents'];
			
	$category_childs  = $all_cats[$category_this]['childs'];
	$category_childs = explode(" ", $category_childs);
	
	// пересортируем массив, если указана последовательность подрубрик
	foreach ($category_childs as $key => $child_cat_id)
		{ 
			if (isset($all_cats[$child_cat_id]['category_menu_order']) and  $all_cats[$child_cat_id]['category_menu_order'] > 0) 
				{
					$category_childs1[$all_cats[$child_cat_id]['category_menu_order']] = $child_cat_id;
				}
			else
				{
					$category_childs1[$key] = $category_childs[$key];
				}
		}
	sort($category_childs1);
	if ($category_childs1[0] != '')
		{
			foreach ($category_childs1 as $key => $child_cat_id)
				{ 
					if (isset($all_cats[$child_cat_id]['pages'][0]))
					$arg .= '_NR_'.$all_cats[$child_cat_id]['category_slug'].' | '.$all_cats[$child_cat_id]['category_name'].'';
				} 	
		}
	$arg .= mso_hook('main_menu_custom');
	echo ('<div id="MainMenu" class="MainMenu"><ul class="menu">');
	if ($arg) echo mso_menu_build($arg, 'selected', false);
	echo ('</div></ul>');	
	//return ('меню из блока'.$arg);
	
	}	
	
	
# ф-ция включает свой css в админку	
function grgallery_add_admin_head ($arg = array())
	{
	echo '<link rel="stylesheet" href="'.getinfo('plugins_url').'grgallery/grgallery.css" type="text/css" media="screen">'.NR;
	}
	
function grgallery_widget($num = 1) 
{
	$widget = 'grgallery_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return grgallery_widget_custom($options, $num);
}

# форма настройки виджета 
# имя функции = виджет_form
function grgallery_widget_form($num = 1) 
{
	$widget = 'grgallery_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['onligroup']) ) $options['onligroup'] = 1;
			else $options['onligroup'] = (int) $options['onligroup'];
	
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
	
	
	$form .= '<p><div class="t150">' . t('Выводим только группы услуг:', 'plugins') . '</div> '. form_dropdown($widget . 'onligroup', 
								array( '0'=>t('Все услуги', 'plugins'), 
										'1'=>t('Только группы', 'plugins')), 
								$options['onligroup'] ) ;	
	
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
function grgallery_widget_update($num = 1) 
{
	$widget = 'grgallery_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['onligroup'] = mso_widget_get_post($widget . 'onligroup');	
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

# функции плагина
function grgallery_widget_custom($options = array(), $num = 1)
{
	global $MSO;
	// кэш 
	$cache_key = 'grgallery_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	// формат вывода  %SIZE% %URL% %TAG% %COUNT% 
	// параметры $min_size $max_size $block_start $block_end
	// сортировка 
	if ( !isset($options['header']) ) $options['header'] = '';
	
	if ( !isset($options['onligroup']) ) $options['onligroup'] = 1;
	
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
		$options['format'] = '<span style="font-size: %SIZE%%"><a href="%URL%">%TAG%</a></span>';
	
	if ( !isset($options['sort']) ) $sort = 0;
		else $sort = (int) $options['sort'];
		
	require_once( getinfo('common_dir') . 'meta.php' ); // функции мета
	require_once ($MSO->config['plugins_dir'].'grgallery/common/common.php');	// подгружаем библиотеку
	$grouptags = get_group_tag(array('cache' => true));
	
	if ($options['onligroup'] == 1) $tagcloud = get_group_tag();
	else 	$tagcloud = mso_get_all_tags_page();
	
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
		if (isset($grouptags[$tag]) and $options['onligroup'] != 1) continue;
		if ($min_count) 
			if ($count < $min_count) continue;

		if (is_int($count)) $font_size = round( (($count - $min)/($max - $min)) * ($max_size - $min_size) + $min_size );
			
		$af = str_replace(array('%URL%', '%TAG%'), 
							array($url . urlencode($tag), $tag), $options['format']);
		
		// альтернативный синтаксис с []
		$af = str_replace(array( '[URL]', '[TAG]'), 
							array( $url . urlencode($tag), $tag), $af);

		$out .= $af . ' ';
		$i++;
		if ( $max_num != 0 and $i == $max_num ) break;
    }
	
	if ($out) $out = $options['header'] . $options['block_start'] . $out . $options['block_end'] ;
	
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	
	return $out;
}
?>