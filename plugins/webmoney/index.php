<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function webmoney_autoload($args = array())
{
	mso_register_widget('webmoney_widget', 'Webmoney'); 
}

# функция выполняется при деинстяляции плагина
function webmoney_uninstall($args = array())
{	
	mso_delete_option_mask('webmoney_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

function webmoney_widget($num = 1)
{
	$widget = 'webmoney_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	if ( isset($options['header']) and $options['header'] ) 
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';

	return webmoney_widget_custom($options, $num);
}

# форма настройки виджета
function webmoney_widget_form($num = 1) 
{
	$widget = 'webmoney_widget_' . $num;
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = t('Помощь сайту', 'plugins');
	if ( !isset($options['wm_currency']) ) $options['wm_currency'] = '';
	if ( !isset($options['wm_amount']) ) $options['wm_amount'] = 10;
		else $options['wm_amount'] = (int) $options['wm_amount'];
	if ( !isset($options['wm_desc']) ) $options['wm_desc'] = t('Пожертвование на развитие сайта', 'plugins');

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<div class="t150">' . t('Заголовок:', 'plugins') . '</div><p>'. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<div class="t150">' . t('Номер кошелька:', 'plugins') . '</div><p>'. form_input( array( 'name'=>$widget . 'wm_currency', 'value'=>$options['wm_currency'] ) ) ;
	$form .= '<div class="t150">&nbsp;</div><p>Номер кошелька WebMoney на который будут поступать пожертвования.</p>';
	$form .= '<div class="t150">' . t('Сумма:', 'plugins') . '</div><p>'. form_input( array( 'name'=>$widget . 'wm_amount', 'value'=>$options['wm_amount'] ) ) ;
	$form .= '<div class="t150">&nbsp;</div><p>Сумма пожертвований по умолчанию.</p>';
	$form .= '<div class="t150">' . t('Описание платежа:', 'plugins') . '</div><p>'. form_input( array( 'name'=>$widget . 'wm_desc', 'value'=>$options['wm_desc'] ) ) ;
	$form .= '<div class="t150">&nbsp;</div><p>Назначение платежа (отображается при переводе денежных средств)</p>';

	return $form;
}

# получаем/обновляем опции
function webmoney_widget_update($num = 1) 
{
	$widget = 'webmoney_widget_' . $num;
	
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['wm_currency'] = mso_widget_get_post($widget . 'wm_currency');
	$newoptions['wm_amount'] = (int) mso_widget_get_post($widget . 'wm_amount');
	if ($newoptions['wm_amount'] < 1) $newoptions['wm_amount'] = 0;
	$newoptions['wm_desc'] = mso_widget_get_post($widget . 'wm_desc');

	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function webmoney_widget_custom($arg, $num)
{
	# параметры 
	if ( !isset($arg['wm_currency']) ) $arg['wm_currency'] = false;

	if (!$arg['wm_currency']) return false;

	# оформление виджета
	if ( !isset($arg['header']) ) $arg['header'] = '<h2 class="box"><span>Помощь сайту</span></h2>';
	if ( !isset($arg['block_start']) ) $arg['block_start'] = '<div class="webmoney">';
	if ( !isset($arg['block_end']) ) $arg['block_end'] = '</div>';

	$wm_currency = $arg['wm_currency'];
	$wm_amount = $arg['wm_amount'];
	$wm_desc = $arg['wm_desc'];

	$out = ''; //wmk:paylink payto

	$out .= '<form action="wmk:payto" style="padding:0; margin:1px" method="get">';
	$out .= '<input name="Purse" value="' .$wm_currency. '" type="hidden" />';
	$out .= '<input name="Amount" value="' .$wm_amount. '" size="4" type="text" />&nbsp';
	$out .= '<input value="Пожертвовать" type="submit" class="subbutton">';
	$out .= '<center>Пожертвование на кошелек: <b>' .$wm_currency. '</b></center>';
	$out .= '<input name="Desc" value="' .$wm_desc. '" type="hidden" />';
	$out .= '<input name="BringToFront" value="Y" type="hidden" />';
	$out .= '</form>';

	if ($out) 
	{	
		return $arg['header'] . $arg['block_start'] . $out . $arg['block_end'];
	}
}
?>
