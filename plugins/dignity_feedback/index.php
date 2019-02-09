<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Александр Шиллинг
 * (c) http://alexanderschilling.net/
 */

# функция автоподключения плагина
function dignity_feedback_autoload($args = array())
{
	mso_register_widget('dignity_feedback_widget', t('Обратная связь', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function dignity_feedback_uninstall($args = array())
{	
	mso_delete_option_mask('dignity_feedback_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function dignity_feedback_widget($num = 1) 
{
	$widget = 'dignity_feedback_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	$path = getinfo('plugins_url') . 'dignity_feedback/img/'; # путь к картинкам
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if (isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	if (isset($options['text']) ) $options['text'] = '<p>' . $options['text'] . '</p>';
	else $options['text'] = '';

	if (isset($options['icq']) and $options['icq']) 
	$options['icq'] = '<p>' . '<a href="icq:' . $options['icq'] . '"><img src="' . $path . 'icq.png" alt="ICQ" title="Написать в ICQ"></a> ';
	else $options['icq'] = '';

	if (isset($options['skype']) and $options['skype']) 
	$options['skype'] = '<a href="skype:' . $options['skype'] . '?chat"><img src="' . $path . 'skype.png" alt="Skype" title="Написать в Skype"></a> ';
	else $options['skype'] = '';

	if (isset($options['jabber']) and $options['jabber']) 
	$options['jabber'] = '<a href="xmpp:' . $options['jabber'] . '?message"><img src="' . $path . 'jabber.png" alt="Jabber" title="Написать в Jabber"></a> ';
	else $options['jabber'] = '';

	if (isset($options['email']) and $options['email'])
	$options['email'] = '<a href="mailto:' . $options['email'] . '"><img src="' . $path . 'mail.png" alt="E-Mail" title="Написать E-Mail"></a> ';
	else $options['email'] = '';

	if (isset($options['twitter']) and $options['twitter']) 
	$options['twitter'] = '<a href="http://twitter.com/' . $options['twitter'] . '" target="_blank" rel="nofollow">' . '<img src="' . $path . 'twitter.png" alt="Твиттер" title="Читать Твиттер"></a> ';
	else $options['twitter'] = '';

	if (isset($options['facebook']) and $options['facebook'])
	$options['facebook'] = '<a href="http://facebook.com/' . $options['facebook'] . '" target="_blank" rel="nofollow"><img src="' . $path . 'facebook.png" alt="Facebook" title="Найти в Facebook"></a> ';
	else $options['facebook'] = '';

	if (isset($options['googleplus']) and $options['googleplus'])
	$options['googleplus'] = '<a href="http://plus.google.com/' . $options['googleplus'] . '" target="_blank" rel="nofollow"><img src="' . $path . 'googleplus.png" alt="Google+" title="Найти в Google+"></a> ';
	else $options['googleplus'] = '';

	if (isset($options['vkontakte']) and $options['vkontakte'])
	$options['vkontakte'] = '<a href="http://vkontakte.ru/' . $options['vkontakte'] . '" target="_blank" rel="nofollow"><img src="' . $path . 'vkontakte.png" alt="Вконтакте" title="Найти в Вконтакте"></a></p>';
	else $options['vkontakte'] = '';

	if (isset($options['textend']) ) $options['textend'] = '<p>' . $options['textend'] . '</p>';
	else $options['textend'] = '';
	
	return dignity_feedback_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function dignity_feedback_widget_form($num = 1) 
{

	$widget = 'dignity_feedback_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = 'Обратная связь';
	if ( !isset($options['text']) ) $options['text'] = '';
	if ( !isset($options['icq']) ) $options['icq'] = '';
	if ( !isset($options['skype']) ) $options['skype'] = '';
	if ( !isset($options['jabber']) ) $options['jabber'] = '';
	if ( !isset($options['email']) ) $options['email'] = '';
	if ( !isset($options['twitter']) ) $options['twitter'] = '';
	if ( !isset($options['facebook']) ) $options['facebook'] = '';
	if ( !isset($options['googleplus']) ) $options['googleplus'] = '';
	if ( !isset($options['vkontakte']) ) $options['vkontakte'] = '';
	if ( !isset($options['textend']) ) $options['textend'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;

	$form .= '<p><div class="t150">' . t('Текст вначале:', 'plugins') . '</div> '. form_textarea( array( 'name'=>$widget . 'text', 'value'=>$options['text'] ) ) ;

	$form .= '<p><div class="t150">' . t('ICQ:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'icq', 'value'=>$options['icq'] ) ) ;

	$form .= '<p><div class="t150">' . t('Skype:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'skype', 'value'=>$options['skype'] ) ) ;

	$form .= '<p><div class="t150">' . t('Jabber:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'jabber', 'value'=>$options['jabber'] ) ) ;

	$form .= '<p><div class="t150">' . t('E-Mail:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'email', 'value'=>$options['email'] ) ) ;

	$form .= '<p><div class="t150">' . t('Twitter:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'twitter', 'value'=>$options['twitter'] ) ) ;

	$form .= '<p><div class="t150">' . t('Facebook:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'facebook', 'value'=>$options['facebook'] ) ) ;

	$form .= '<p><div class="t150">' . t('Google Plus:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'googleplus', 'value'=>$options['googleplus'] ) ) ;

	$form .= '<p><div class="t150">' . t('Вконтакте:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'vkontakte', 'value'=>$options['vkontakte'] ) ) ;

	$form .= '<p><div class="t150">' . t('Текст в конце:', 'plugins') . '</div> '. form_textarea( array( 'name'=>$widget . 'textend', 'value'=>$options['textend'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function dignity_feedback_widget_update($num = 1) 
{
	$widget = 'dignity_feedback_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST

	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['text'] = mso_widget_get_post($widget . 'text');
	$newoptions['icq'] = mso_widget_get_post($widget . 'icq');
	$newoptions['skype'] = mso_widget_get_post($widget . 'skype');
	$newoptions['jabber'] = mso_widget_get_post($widget . 'jabber');
	$newoptions['email'] = mso_widget_get_post($widget . 'email');
	$newoptions['twitter'] = mso_widget_get_post($widget . 'twitter');
	$newoptions['facebook'] = mso_widget_get_post($widget . 'facebook');
	$newoptions['googleplus'] = mso_widget_get_post($widget . 'googleplus');
	$newoptions['vkontakte'] = mso_widget_get_post($widget . 'vkontakte');
	$newoptions['textend'] = mso_widget_get_post($widget . 'textend');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина

function dignity_feedback_widget_custom($options = array(), $num = 1)
{
	$header = $options['header'];
	$text = $options['text'];
	$icq = $options['icq'];
	$skype = $options['skype'];
	$jabber = $options['jabber'];
	$email = $options['email'];
	$twitter = $options['twitter'];
	$facebook = $options['facebook'];
	$googleplus = $options['googleplus'];
	$vkontakte = $options['vkontakte'];
	$textend = $options['textend'];
	
	return $header . $text . $icq . $skype . $jabber . $email . $twitter . $facebook . $googleplus . $vkontakte . $textend;
}

?>
