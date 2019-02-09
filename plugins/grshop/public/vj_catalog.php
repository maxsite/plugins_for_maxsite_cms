<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');

# оболочечная функция, которая берет настройки из опций виджетов
# эта ф-ция будет выполняться в сайдбаре !!!.
function catalog_widget($num = 1) 
{

	global $MSO;
	$widget = 'grshop_catalog_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	return catalog_widget_custom($options, $num);   # вызов ф-ции, выводящей каталог - суть виджета
}

# форма настройки виджета 
# имя функции = виджет_form
function catalog_widget_form($num = 1) 
{

	global $MSO;
	$widget = 'grshop_catalog_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('Каталог', 'plugins/grshop');

	$CI = & get_instance();
	$CI->load->helper('form');
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins/grshop') . '</div> '.form_input( array( 'name'=>$widget .'header', 'value'=>$options['header'] ) ) ;
	return $form;
}


# сюда приходят POST из формы настройки виджета
# в этой ф-ции обновление опций
# имя функции = виджет_update
function catalog_widget_update($num = 1) 
{

	global $MSO;
	$widget = 'grshop_catalog_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции сохраненные раньше
	$options = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST из предыдущей формы
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	
	# если есть изменения обновляем опции
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}



# собственно функция действия виджета, т.е. что там в нем выводится.
# то есть ф-ция должна выводить содержимое каталога, с учетом параметров
function catalog_widget_custom($options = array(), $num = 1)
	{
	global $MSO;
	require_once ($MSO->config['plugins_dir'].'grshop/common/common.php');	// подгружаем библиотеку
	require_once ($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные
	global $grsh;

	$out = '';

	// кэш 
	$cache_key = 'grshop_catalog_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	if ( isset($options['header']) )  $in['head'] = $options['header'];	// параметр заголовка

	$grsh_options = mso_get_option($grsh['main_key_options'], 'plugins', array()); // получение опций
	if ( !isset($grsh_options['main_slug']) ) $grsh_options['main_slug'] = 'catalog';
	$in['link'] = getinfo('siteurl').$grsh_options['main_slug'].'/cat/';	// параметр для формирования линка ф-ции каталинк


	$out.= catalink($in);	// ф-ция отрисовки каталога из библиотеки коммон
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	return $out;
	};
	
?>