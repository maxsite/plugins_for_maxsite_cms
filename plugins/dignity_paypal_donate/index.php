<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * Александр Шиллинг
 * (c) http://alexanderschilling.net
 *
 */

# функция автоподключения плагина
function dignity_paypal_donate_autoload($args = array())
{
	mso_register_widget('dignity_paypal_donate_widget', t('PayPal Donate', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function dignity_paypal_donate_uninstall($args = array())
{	
	mso_delete_option_mask('dignity_paypal_donate_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function dignity_paypal_donate_widget($num = 1) 
{
	$widget = 'dignity_paypal_donate_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	// заменим заголовок, чтобы был в  h2 class="box"
	if (isset($options['header']) and $options['header'] ) 
		$options['header'] = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>');
	else $options['header'] = '';
	
	if (isset($options['email']) and $options['email'] ) 
		$options['email'] = $options['email'];
	else $options['email'] = '';
	
	if (isset($options['item_name']) and $options['item_name'] ) 
		$options['item_name'] = $options['item_name'];
	else $options['item_name'] = '';
	
	if (isset($options['item_number']) and $options['item_number'] ) 
		$options['item_number'] = $options['item_number'];
	else $options['item_number'] = '';
	
	if (isset($options['amount']) and $options['amount'] ) 
		$options['amount'] = $options['amount'];
	else $options['amount'] = '';
	
	if (isset($options['currency_code']) and $options['currency_code'] ) 
		$options['currency_code'] = $options['currency_code'];
	else $options['currency_code'] = '';
	
	if (isset($options['img']) and $options['img'] ) 
		$options['img'] = $options['img'];
	else $options['img'] = '';
	
	if (isset($options['textdo']) and $options['textdo'] ) 
		$options['textdo'] = $options['textdo'];
	else $options['textdo'] = '';
	
	if (isset($options['textposle']) and $options['textposle'] ) 
		$options['textposle'] = $options['textposle'];
	else $options['textposle'] = '';
	
	return dignity_paypal_donate_widget_custom($options, $num);
}


# форма настройки виджета 
# имя функции = виджет_form
function dignity_paypal_donate_widget_form($num = 1) 
{

	$widget = 'dignity_paypal_donate_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('PayPal Donate', __FILE__);
	if ( !isset($options['email']) ) $options['email'] = 'admin@site.ru';
	if ( !isset($options['item_name']) ) $options['item_name'] = 'Please give me your money!!!';
	if ( !isset($options['item_number']) ) $options['item_number'] = 'Development of the project at www.site.ru';
	if ( !isset($options['amount']) ) $options['amount'] = 10;
	if ( !isset($options['currency_code']) ) $options['currency_code'] = 'USD';
	if ( !isset($options['img']) ) $options['img'] = 'https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif';
	if ( !isset($options['textdo']) ) $options['textdo'] = '';
	if ( !isset($options['textposle']) ) $options['textposle'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', __FILE__) . '</div> ' . form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('E-Mail:', __FILE__) . '</div> ' . form_input( array( 'name'=>$widget . 'email', 'value'=>$options['email'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Наименование:', __FILE__) . '</div> ' . form_input( array( 'name'=>$widget . 'item_name', 'value'=>$options['item_name'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Описание:', __FILE__) . '</div> ' . form_input( array( 'name'=>$widget . 'item_number', 'value'=>$options['item_number'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Сумма:', __FILE__) . '</div> ' . form_input( array( 'name'=>$widget . 'amount', 'value'=>$options['amount'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Валюта:', __FILE__) . '</div> ' . form_input( array( 'name'=>$widget . 'currency_code', 'value'=>$options['currency_code'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('URL картинки:', __FILE__) . '</div> ' . form_input( array( 'name'=>$widget . 'img', 'value'=>$options['img'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Текст до:', __FILE__) . '</div> ' . form_input( array( 'name'=>$widget . 'textdo', 'value'=>$options['textdo'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Текст после:', __FILE__) . '</div> ' . form_input( array( 'name'=>$widget . 'textposle', 'value'=>$options['textposle'] ) ) ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function dignity_paypal_donate_widget_update($num = 1) 
{
	$widget = 'dignity_paypal_donate_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST

	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['email'] = mso_widget_get_post($widget . 'email');
	$newoptions['item_name'] = mso_widget_get_post($widget . 'item_name');
	$newoptions['item_number'] = mso_widget_get_post($widget . 'item_number');
	$newoptions['amount'] = mso_widget_get_post($widget . 'amount');
	$newoptions['currency_code'] = mso_widget_get_post($widget . 'currency_code');
	$newoptions['img'] = mso_widget_get_post($widget . 'img');
	$newoptions['textdo'] = mso_widget_get_post($widget . 'textdo');
	$newoptions['textposle'] = mso_widget_get_post($widget . 'textposle');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина

function dignity_paypal_donate_widget_custom($options = array(), $num = 1)
{

	// кэш
	$cache_key = 'dignity_paypal_donate_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше
	
	// загружаем опции
	if ( !isset($options['header']) ) $options['header'] = 'PayPal Donate';
	if ( !isset($options['email']) ) $options['email'] = 'admin@site.ru';
	if ( !isset($options['item_name']) ) $options['item_name'] = 'Please give me your money!!!';
	if ( !isset($options['item_number']) ) $options['item_number'] = 'Development of the project at www.site.ru';
	if ( !isset($options['amount']) ) $options['amount'] = 10;
	if ( !isset($options['currency_code']) ) $options['currency_code'] = 'USD';
	if ( !isset($options['img']) ) $options['img'] = 'https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif';
	if ( !isset($options['textdo']) ) $options['textdo'] = '';
	if ( !isset($options['textposle']) ) $options['textposle'] = '';
	
	$out = '';
	$out .= $options['header'];
	
	$out .= '<p>' . $options['textdo'] . '</p>';

	$out .= '<p><form action="https://www.paypal.com/cgi-bin/webscr" method="post">  
	<input type="hidden" name="business" value="' . $options['email'] . '"> 
	<input type="hidden" name="cmd" value="_donations">  
	<input type="hidden" name="item_name" value="' . $options['item_name'] . '">
	<input type="hidden" name="item_number" value="' . $options['item_number'] . '">
	<input type="hidden" name="amount" value="' . $options['amount'] . '">
	<input type="hidden" name="currency_code" value="' . $options['currency_code'] . '">
	<input type="image" name="submit" border="0" src="' . $options['img'] . '" alt="PayPal - The safer, easier way to pay online">
	<img alt="" border="0" width="1" height="1" src="https://www.paypal.com/en_US/i/scr/pixel.gif" >
	</form></p>';
	
	$out .= '<p>' . $options['textposle'] . '</p>';
	
	// добавляем в кеш, сохраняем сутки
	mso_add_cache($cache_key, $out, 86400, true); // сразу в кэш добавим
	
	return  $out;
}

?>