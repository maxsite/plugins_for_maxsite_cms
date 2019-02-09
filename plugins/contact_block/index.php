<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * (c) http://maxsite.thedignity.biz
 */

# функция автоподключения плагина
function contact_block_autoload($args = array())
{
	mso_register_widget('contact_block_widget', t('Блок обратной связи', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function contact_block_uninstall($args = array())
{	
	mso_delete_option_mask('contact_block_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function contact_block_widget($num = 1) 
{
	$widget = 'contact_block_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	$path = getinfo('plugins_url') . 'contact_block/img/'; # путь к картинкам
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if (isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';
	
	if (isset($options['text']) ) $options['text'] = '<p>' . $options['text'] . '</p>';
	else $options['text'] = '';
	
	if (isset($options['company']) ) $options['company'] = '<p>' . $options['company'] . '</p>';
	else $options['company'] = '';

	if (isset($options['adress']) ) $options['adress'] = '<p>' . $options['adress'] . '</p>';
	else $options['adress'] = '';

	if (isset($options['plz']) ) $options['plz'] = '<p>' . $options['plz'];
	else $options['plz'] = '';

	if (isset($options['city']) ) $options['city'] = ' ' . $options['city'] . '</p>';
	else $options['city'] = '';

	if (isset($options['tel']) and $options['tel']) 
	$options['tel'] = '<p>' . '<img src="' . $path . 'call.png"> ' . $options['tel'] . '</p>';
	else $options['tel'] = '';

	if (isset($options['mob']) and $options['mob']) 
	$options['mob'] = '<p>' . '<img src="' . $path . 'phone.png"> ' . $options['mob'] . '</p>';
	else $options['mob'] = '';

	if (isset($options['icq']) and $options['icq']) 
	$options['icq'] = '<p>' . '<img src="' . $path . 'icq.png"> ' . $options['icq'] . '</p>';
	else $options['icq'] = '';

	if (isset($options['skype']) and $options['skype']) 
	$options['skype'] = '<p>' . '<img src="' . $path . 'skype.png"> ' . $options['skype'] . '</p>';
	else $options['skype'] = '';

	if (isset($options['twitter']) and $options['twitter']) 
	$options['twitter'] = '<p>' . '<img src="' . $path . 'twitter.png"> ' . '<a href="http://twitter.com/' . $options['twitter'] . '" target="_blank">' . $options['twitter'] . '</a>' . '</p>';
	else $options['twitter'] = '';

	if (isset($options['facebook']) and $options['facebook'])
	$options['facebook'] = '<p>' . '<img src="' . $path . 'facebook.png"> ' . '<a href="http://facebook.com/' . $options['facebook'] . '" target="_blank">' . $options['facebook'] . '</a>' . '</p>';
	else $options['facebook'] = '';

	if (isset($options['jabber']) and $options['jabber']) 
	$options['jabber'] = '<p>' . '<img src="' . $path . 'jabber.png"> ' . $options['jabber'] . '</p>';
	else $options['jabber'] = '';

	if (isset($options['email']) and $options['email'])
	$options['email'] = '<p>' . '<img src="' . $path . 'mail.png"> ' . $options['email'] . '</p>';
	else $options['email'] = '';

	if (isset($options['textend']) ) $options['textend'] = '<p>' . $options['textend'] . '</p>';
	else $options['textend'] = '';
	
	return contact_block_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function contact_block_widget_form($num = 1) 
{

	$widget = 'contact_block_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = 'Обратная связь';
	if ( !isset($options['text']) ) $options['text'] = '';
	if ( !isset($options['company']) ) $options['company'] = '';
	if ( !isset($options['adress']) ) $options['adress'] = '';
	if ( !isset($options['plz']) ) $options['plz'] = '';
	if ( !isset($options['city']) ) $options['city'] = '';
	if ( !isset($options['tel']) ) $options['tel'] = '';
	if ( !isset($options['mob']) ) $options['mob'] = '';
	if ( !isset($options['icq']) ) $options['icq'] = '';
	if ( !isset($options['skype']) ) $options['skype'] = '';
	if ( !isset($options['twitter']) ) $options['twitter'] = '';
	if ( !isset($options['facebook']) ) $options['facebook'] = '';
	if ( !isset($options['jabber']) ) $options['jabber'] = '';
	if ( !isset($options['email']) ) $options['email'] = '';
	if ( !isset($options['textend']) ) $options['textend'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;

	$form .= '<p><div class="t150">' . t('Текст вначале:', 'plugins') . '</div> '. form_textarea( array( 'name'=>$widget . 'text', 'value'=>$options['text'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Названия:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'company', 'value'=>$options['company'] ) ) ;

	$form .= '<p><div class="t150">' . t('Адрес:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'adress', 'value'=>$options['adress'] ) ) ;

	$form .= '<p><div class="t150">' . t('Индекс:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'plz', 'value'=>$options['plz'] ) ) ;

	$form .= '<p><div class="t150">' . t('Город:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'city', 'value'=>$options['city'] ) ) ;

	$form .= '<p><div class="t150">' . t('Телефон:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'tel', 'value'=>$options['tel'] ) ) ;

	$form .= '<p><div class="t150">' . t('Мобильный:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'mob', 'value'=>$options['mob'] ) ) ;

	$form .= '<p><div class="t150">' . t('ICQ:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'icq', 'value'=>$options['icq'] ) ) ;

	$form .= '<p><div class="t150">' . t('Skype:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'skype', 'value'=>$options['skype'] ) ) ;

	$form .= '<p><div class="t150">' . t('Twitter:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'twitter', 'value'=>$options['twitter'] ) ) ;

	$form .= '<p><div class="t150">' . t('Facebook:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'facebook', 'value'=>$options['facebook'] ) ) ;

	$form .= '<p><div class="t150">' . t('Jabber:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'jabber', 'value'=>$options['jabber'] ) ) ;

	$form .= '<p><div class="t150">' . t('E-Mail:', 'plugins') . '</div> '. form_textarea( array( 'name'=>$widget . 'email', 'value'=>$options['email'] ) ) ;

	$form .= '<p><div class="t150">' . t('Текст в конце:', 'plugins') . '</div> '. form_textarea( array( 'name'=>$widget . 'textend', 'value'=>$options['textend'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function contact_block_widget_update($num = 1) 
{
	$widget = 'contact_block_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['text'] = mso_widget_get_post($widget . 'text');
	$newoptions['company'] = mso_widget_get_post($widget . 'company');
	$newoptions['adress'] = mso_widget_get_post($widget . 'adress');
	$newoptions['plz'] = mso_widget_get_post($widget . 'plz');
	$newoptions['city'] = mso_widget_get_post($widget . 'city');
	$newoptions['tel'] = mso_widget_get_post($widget . 'tel');
	$newoptions['mob'] = mso_widget_get_post($widget . 'mob');
	$newoptions['icq'] = mso_widget_get_post($widget . 'icq');
	$newoptions['skype'] = mso_widget_get_post($widget . 'skype');
	$newoptions['twitter'] = mso_widget_get_post($widget . 'twitter');
	$newoptions['facebook'] = mso_widget_get_post($widget . 'facebook');
	$newoptions['jabber'] = mso_widget_get_post($widget . 'jabber');
	$newoptions['email'] = mso_widget_get_post($widget . 'email');
	$newoptions['textend'] = mso_widget_get_post($widget . 'textend');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина

function contact_block_widget_custom($options = array(), $num = 1)
{
	$header = $options['header'];
	$text = $options['text'];
	$company = $options['company'];
	$adress = $options['adress'];
	$plz = $options['plz'];
	$city = $options['city'];
	$tel = $options['tel'];
	$mob = $options['mob'];
	$icq = $options['icq'];
	$skype = $options['skype'];
	$twitter = $options['twitter'];
	$facebook = $options['facebook'];
	$jabber = $options['jabber'];
	$email = $options['email'];
	$textend = $options['textend'];
	
	return $header . $text . $company . $adress . $plz . $city . $tel . $mob . $icq . $skype . $twitter . $facebook . $jabber . $email . $textend;
}

?>
