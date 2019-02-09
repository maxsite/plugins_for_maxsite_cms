<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Александр Шиллинг
 * (c) http://maxsite.thedignity.biz
 */

# функция автоподключения плагина
function di_yandex_money_autoload($args = array())
{
	mso_register_widget('di_yandex_money_widget', t('Блок Яндекс денег', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function di_yandex_money_uninstall($args = array())
{
	mso_delete_option_mask('di_yandex_money_widget_', 'plugins'); // удалим созданные опции
	return $args;
}

# функция, которая берет настройки из опций виджетов
function di_yandex_money_widget($num = 1)
{
	$widget = 'di_yandex_money_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	// заменим заголовок, чтобы был как в ms 0.9x
	if ( isset($options['header']) and $options['header'] )
		$options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
	else $options['header'] = '';

	if (isset($options['text']) ) $options['text'] = '<p>' . $options['text'] . '</p>';
	else $options['text'] = '';


	if (isset($options['textend']) ) $options['textend'] = '<p>' . $options['textend'] . '</p>';
	else $options['textend'] = '';

	return di_yandex_money_widget_custom($options, $num);
}


# форма настройки виджета
# имя функции = виджет_form
function di_yandex_money_widget_form($num = 1)
{

	$widget = 'di_yandex_money_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = mso_get_option($widget, 'plugins', array());

	if ( !isset($options['header']) ) $options['header'] = 'Яндекс деньги';
	if ( !isset($options['text']) ) $options['text'] = '';
	if ( !isset($options['textend']) ) $options['textend'] = '';

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');

	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;

	$form .= '<p><div class="t150">' . t('Текст:', 'plugins') . ' <smal>Можно использовать HTML</smal></div> '. form_textarea( array( 'name'=>$widget . 'text', 'value'=>$options['text'] ) ) ;

    $form .= '<p><div class="t150">' . t('Код кнопок:', 'plugins') . ' <a href="https://money.yandex.ru/embed/quickpay/small.xml">Брать тут.</a></div> '. form_textarea( array( 'name'=>$widget . 'textend', 'value'=>$options['textend'] ) ) ;

	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function di_yandex_money_widget_update($num = 1)
{
	$widget = 'di_yandex_money_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['text'] = mso_widget_get_post($widget . 'text');
	$newoptions['textend'] = mso_widget_get_post($widget . 'textend');

	if ( $options != $newoptions )
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина

function di_yandex_money_widget_custom($options = array(), $num = 1)
{
	$header = $options['header'];
	$text = $options['text'];
	$textend = $options['textend'];

	return $header . $text . $textend;
}