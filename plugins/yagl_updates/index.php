<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Александр Шиллинг
 * (c) http://dignityinside.org
 */

# функция автоподключения плагина
function yagl_updates_autoload($args = array())
{
	mso_register_widget('yagl_updates_widget', t('Точные апдейты', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function yagl_updates_uninstall($args = array())
{	
	mso_delete_option_mask('yagl_updates_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function yagl_updates_widget($num = 1) 
{
	$widget = 'yagl_updates_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if (isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	if (isset($options['text']) ) $options['text'] = '<p>' . $options['text'] . '</p>';
	else $options['text'] = '';
	
	return yagl_updates_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function yagl_updates_widget_form($num = 1) 
{

	$widget = 'yagl_updates_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = 'Точные апдейты';
	if ( !isset($options['text']) ) $options['text'] = 'Яндекс и Google';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;

	$form .= '<p><div class="t150">' . t('Текст:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'text', 'value'=>$options['text'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function yagl_updates_widget_update($num = 1) 
{
	$widget = 'yagl_updates_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST

	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['text'] = mso_widget_get_post($widget . 'text');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина

function yagl_updates_widget_custom($options = array(), $num = 1)
{
	$header = $options['header'];
	$text = $options['text'];
	
	return $header . $text . '<a href="http://seobudget.ru/updates/" title="Точные апдейты Яндекса и Google"><img src="http://seobudget.ru/images/updates/yandex.gif" alt="Точные апдейты Яндекса и Google" width="192" height="32" border="0" /></a>';
}

?>
