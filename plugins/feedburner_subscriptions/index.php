<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Александр Шиллинг
 * (c) http://alexanderschilling.net
 */

# функция автоподключения плагина
function feedburner_subscriptions_autoload($args = array())
{
	mso_register_widget('feedburner_subscriptions_widget', t('Подписка', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function feedburner_subscriptions_uninstall($args = array())
{	
	mso_delete_option_mask('feedburner_subscriptions_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function feedburner_subscriptions_widget($num = 1) 
{
	$widget = 'feedburner_subscriptions_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if (isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	if (isset($options['login']) ) $options['login'] = $options['login'];
	else $options['login'] = '';

	if (isset($options['text']) ) $options['text'] = $options['text'];
	else $options['text'] = 'Введите свой email адрес:';

	if (isset($options['button']) ) $options['button'] = $options['button'];
	else $options['button'] = 'Подписаться';
	
	return feedburner_subscriptions_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function feedburner_subscriptions_widget_form($num = 1) 
{

	$widget = 'feedburner_subscriptions_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = 'Подписка';
	if ( !isset($options['login']) ) $options['login'] = '';
	if ( !isset($options['text']) ) $options['text'] = 'Введите свой email адрес:';
	if ( !isset($options['button']) ) $options['button'] = 'Подписаться';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;

	$form .= '<p><div class="t150">' . t('Логин (feedburner):', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'login', 'value'=>$options['login'] ) ) .  t('<br>Перед использованием виджета, необходимо включить подписку на сайте <a href="http://feedburner.google.com" target="_blank">Feedburner</a>. <br> (Публикуй -> Подписки по электронной почте -> Активировать)', 'plugins') ;

	$form .= '<p><div class="t150">' . t('Текст:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'text', 'value'=>$options['text'] ) ) ;

	$form .= '<p><div class="t150">' . t('Текст кнопки:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'button', 'value'=>$options['button'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function feedburner_subscriptions_widget_update($num = 1) 
{
	$widget = 'feedburner_subscriptions_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST

	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['login'] = mso_widget_get_post($widget . 'login');
	$newoptions['text'] = mso_widget_get_post($widget . 'text');
	$newoptions['button'] = mso_widget_get_post($widget . 'button');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина

function feedburner_subscriptions_widget_custom($options = array(), $num = 1)
{
	$header = $options['header'];
	$login = $options['login'];
	$text = $options['text'];
	$button = $options['button'];
	
	return $header . "<form action=\"http://feedburner.google.com/fb/a/mailverify\" method=\"post\" target=\"popupwindow\" onsubmit=\"window.open('http://feedburner.google.com/fb/a/mailverify?uri=" . $login . "', 'popupwindow', 'scrollbars=yes,width=580,height=580');return true\"><p>" . $text . "</p><p><input type=\"text\" style=\"width:180px\" name=\"email\" onfocus=\"if(this.value == 'Ваш E-Mail') { this.value = ''; }\" value=\"Ваш E-Mail\"/></p><input type=\"hidden\" value=\"" . $login . "\" name=\"uri\"/><input type=\"hidden\" name=\"loc\" value=\"ru_RU\"/><input type=\"submit\" value=\"" . $button . "\" /></form>";
}

#end of file
