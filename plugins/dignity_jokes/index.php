<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * Александр Шиллинг
 * (c) http://alexanderschilling.net
 *
 */

# функция автоподключения плагина
function dignity_jokes_autoload($args = array())
{
	mso_register_widget('dignity_jokes_widget', t('Хорошие шутки', __FILE__)); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function dignity_jokes_uninstall($args = array())
{	
	mso_delete_option_mask('dignity_jokes_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function dignity_jokes_widget($num = 1) 
{
	$widget = 'dignity_jokes_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if (isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	return dignity_jokes_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function dignity_jokes_widget_form($num = 1) 
{

	$widget = 'dignity_jokes_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('Хорошие шутки', __FILE__);
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', __FILE__) . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function dignity_jokes_widget_update($num = 1) 
{
	$widget = 'dignity_jokes_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST

	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина

function dignity_jokes_widget_custom($options = array(), $num = 1)
{
	
	// кэш
	$cache_key = 'dignity_jokes_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	// загружаем опции
	if ( !isset($options['header']) ) $options['header'] = t('Хорошие шутки', __FILE__);
	
	$out = '';
	$out .= $options['header'];
	
	$out .= '<script language="JavaScript" type="text/javascript" src="http://thejokes.ru/forweb"></script>';
	
	// добавляем в кеш, сохраняем сутки
	mso_add_cache($cache_key, $out, 86400, true); // сразу в кэш добавим
	
	return  $out;
}

?>