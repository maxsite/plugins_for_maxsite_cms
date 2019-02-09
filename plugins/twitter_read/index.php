<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Александр Шиллинг
 * (c) http://dignityinside.org
 */

# функция автоподключения плагина
function twitter_read_autoload($args = array())
{
	mso_register_widget('twitter_read_widget', t('Кнопка Твиттер', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function twitter_read_uninstall($args = array())
{	
	mso_delete_option_mask('twitter_read_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function twitter_read_widget($num = 1) 
{
	$widget = 'twitter_read_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if (isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';

	if (isset($options['twitter_account']) ) $options['twitter_account'] = $options['twitter_account'];
	else $options['twitter_account'] = '';
	
	return twitter_read_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function twitter_read_widget_form($num = 1) 
{

	$widget = 'twitter_read_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['twitter_account']) ) $options['twitter_account'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;

	$form .= '<p><div class="t150">' . t('Ваше имя пользователя?', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'twitter_account', 'value'=>$options['twitter_account'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function twitter_read_widget_update($num = 1) 
{
	$widget = 'twitter_read_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST

	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['twitter_account'] = mso_widget_get_post($widget . 'twitter_account');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function twitter_read_widget_custom($options = array(), $num = 1)
{

$header = $options['header'];
$twitter_account = $options['twitter_account'];
$twitter = '<a href="https://twitter.com/' . $options['twitter_account'] . '" class="twitter-follow-button" data-lang="ru">Читать @' . $options['twitter_account'] . '</a>
<script src="//platform.twitter.com/widgets.js" type="text_do/javascript"></script>';

	return $header . $twitter;
}
?>
