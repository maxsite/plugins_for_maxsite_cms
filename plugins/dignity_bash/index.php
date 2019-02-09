<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Александр Шиллинг
 * (c) http://alexanderschilling.net
 */

# функция автоподключения плагина
function dignity_bash_autoload($args = array())
{
	mso_register_widget('dignity_bash_widget', t('Цитаты Bash.Org.Ru', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function dignity_bash_uninstall($args = array())
{	
	mso_delete_option_mask('dignity_bash_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function dignity_bash_widget($num = 1) 
{
	$widget = 'dignity_bash_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	if (isset($options['text']) ) $options['text'] = '<p>' . $options['text'] . '</p>';
	else $options['text'] = '';
	
	return dignity_bash_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function dignity_bash_widget_form($num = 1) 
{

	$widget = 'dignity_bash_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = 'Цитаты Bash.Org.Ru';
	if ( !isset($options['text']) ) $options['text'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;

	$form .= '<p><div class="t150">' . t('Текст:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'text', 'value'=>$options['text'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function dignity_bash_widget_update($num = 1) 
{
	$widget = 'dignity_bash_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST

	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['text'] = mso_widget_get_post($widget . 'text');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина

function dignity_bash_widget_custom($options = array(), $num = 1)
{
	$header = $options['header'];
	$text = $options['text'];
	
	$context = '';
	$js = '<script type="text/javascript" src="http://bash.im/forweb/?u"></script>';

	return $header . $text . $js;
}

?>
