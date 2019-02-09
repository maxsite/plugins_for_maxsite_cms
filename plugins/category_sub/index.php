<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 

# функция автоподключения плагина
function category_autoload($args = array())
{
	# регистрируем виджет
	mso_register_widget('category_widget', t('Рубрики', 'plugins')); 
}

# функция выполняется при деинсталяции плагина
function category_uninstall($args = array())
{	
	mso_delete_option_mask('category_widget_', 'plugins'); // удалим созданные опции
	return $args;
}


# функция, которая берет настройки из опций виджетов
function category_widget($num = 1) 
{
	$widget = 'category_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) $options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
		else $options['header'] = '';
	
	if ( isset($options['include']) ) $options['include'] = mso_explode($options['include']);
		else $options['include'] = array();
		
	if ( isset($options['exclude']) ) $options['exclude'] = mso_explode($options['exclude']);
		else $options['exclude'] = array();
	
	
	return category_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function category_widget_form($num = 1) 
{

	$widget = 'category_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['format']) ) $options['format'] = '[LINK][TITLE]<sup>[COUNT]</sup>[/LINK]<br>[DESCR]';
	if ( !isset($options['format_current']) ) $options['format_current'] = '<span>[TITLE]<sup>[COUNT]</sup></span><br>[DESCR]';
	if ( !isset($options['include']) ) $options['include'] = '';
	if ( !isset($options['exclude']) ) $options['exclude'] = '';
	if ( !isset($options['hide_empty']) ) $options['hide_empty'] = '0';
	if ( !isset($options['order']) ) $options['order'] = 'category_name';
	if ( !isset($options['order_asc']) ) $options['order_asc'] = 'ASC';
	if ( !isset($options['include_child']) ) $options['include_child'] = '0';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Формат:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'format', 'value'=>$options['format'] ) ) 
			. '<br><div class="t150">&nbsp;</div>' . t('Например:', 'plugins') . ' [LINK][TITLE]&lt;sup&gt;[COUNT]&lt;/sup&gt;[/LINK]&lt;br&gt;[DESCR]';

	$form .= '<p><div class="t150">' . t('Формат текущей:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'format_current', 'value'=>$options['format_current'] ) ) 
			. '<br><div class="t150">&nbsp;</div>' . t('Например:', 'plugins') . ' &lt;span&gt;[TITLE]&lt;sup&gt;[COUNT]&lt;/sup&gt;&lt;/span&gt;&lt;br&gt;[DESCR]'
			. '<br><div class="t150">&nbsp;</div>' . t('Все варианты:', 'plugins') . ' [SLUG], [ID_PARENT], [ID], [MENU_ORDER], [TITLE], [COUNT], [DESCR], [LINK][/LINK], [URL]'
			
			
			;

	$form .= '<p><div class="t150">' . t('Включить только:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'include', 'value'=>$options['include'] ) ) 
			. '<br><div class="t150">&nbsp;</div>' . t('Укажите номера рубрик через запятую или пробел', 'plugins');
	
	$form .= '<p><div class="t150">' . t('Исключить:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'exclude', 'value'=>$options['exclude'] ) )
			. '<br><div class="t150">&nbsp;</div>' . t('Укажите номера рубрик через запятую или пробел', 'plugins');

	$form .= '<p><div class="t150">' . t('Если нет записей:', 'plugins') . '</div> '. 
		form_dropdown( $widget . 'hide_empty', array( 
		'0'=>t('Отображать рубрику (количество записей ведется без учета опубликованности)', 'plugins'), 
		'1'=>t('Скрывать рубрику (количество записей ведется только по опубликованным)', 'plugins')), 
		$options['hide_empty']);
	
	$form .= '<p><div class="t150">' . t('Сортировка:', 'plugins') . '</div> '. 
		form_dropdown( $widget . 'order', 
			array( 
				'category_name' => t('По имени рубрики', 'plugins'), 
				'category_id' => t('По ID рубрики', 'plugins'), 
				'category_menu_order' => t('По выставленному menu order', 'plugins'), 
				'pages_count' => t('По количеству записей', 'plugins')), 
				$options['order']);
	
	$form .= '<p><div class="t150">' . t('Порядок:', 'plugins') . '</div> '. 
		form_dropdown( $widget . 'order_asc', 
			array( 
				'ASC'=>t('Прямой', 'plugins'), 
				'DESC'=>t('Обратный', 'plugins')
				), $options['order_asc']);
	
	$form .= '<p><div class="t150">' . t('Включать потомков:', 'plugins') . '</div> '. 
			form_dropdown( $widget . 'include_child', 
				array( 
				'0'=>t('Всегда', 'plugins'), 
				'1'=>t('Только если явно указана рубрика', 'plugins')
				), $options['include_child']);	
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function category_widget_update($num = 1) 
{

	$widget = 'category_widget_' . $num; // имя для опций = виджет + номер
	
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
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}


function category_widget_custom($options = array(), $num = 1)
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
	
	$cache_key = 'category_widget' . serialize($options) . $num;
	
	$k = mso_get_cache($cache_key);
	if ($k) // да есть в кэше
	{
		$all = $k;
	}
	else 
	{
		$all = mso_cat_array('page', 0, $options['order'], $options['order_asc'], $options['order'], $options['order_asc'], $options['include'], $options['exclude'], $options['include_child'], $options['hide_empty'], true);
		mso_add_cache($cache_key, $all); // сразу в кэш добавим
	}
	
	//pr($all);
	
	// $type = 'page', $parent_id = 0, $order = 'category_menu_order', $asc = 'asc', $child_order = 'category_menu_order', $child_asc = 'asc', $ex = false, $in = false, $in_child = false $hide_empty = false
	
	$out = mso_create_list($all, array('childs'=>'childs', 'format'=>$options['format'], 'format_current'=>$options['format_current'], 'class_ul'=>'is_link', 'title'=>'category_name', 'link'=>'category_slug', 'current_id'=>false, 'prefix'=>'category/', 'count'=>'pages_count', 'slug'=>'category_slug', 'id'=>'category_id', 'menu_order'=>'category_menu_order', 'id_parent'=>'category_id_parent' ) );
	
	if ($out and $options['header']) $out = $options['header'] . $out;
	
	
	return $out;
}


?>