<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 */

# функция автоподключения плагина
function yandex_donate_autoload($args = array())
{
    mso_hook_add('content', 'yandex_donate_content');
	mso_hook_add('admin_init', 'yandex_donate_admin_init'); # хук на админку
	mso_register_widget('yandex_donate_widget', t('Яндекс donate', 'plugins')); # регистрируем виджет
}

# функция выполняется при деинсталяции плагина
function yandex_donate_uninstall($args = array())
{
	mso_delete_option_mask('yandex_donate_widget_', 'plugins'); // удалим созданные опции
	mso_delete_option('plugin_yandex_donate', 'plugins' ); // удалим созданные опции
	mso_remove_allow('yandex_donate_edit'); // удалим созданные разрешения

	return $args;
}


# функция выполняется при указаном хуке admin_init
function yandex_donate_admin_init($args = array())
{
	if ( mso_check_allow('plugin_yandex_donate') )
	{
		$this_plugin_url = 'plugin_yandex_donate'; // url и hook

		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		# можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки

		mso_admin_menu_add('plugins', 'plugin_options/yandex_donate', t('Яндекс donate'));

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url)
		# связанную функцию именно она будет вызываться, когда
		# будет идти обращение по адресу http://сайт/admin/_null

	}

	return $args;
}


//Виджет:

# функция, которая берет настройки из опций виджетов
function yandex_donate_widget($num = 1)
{
	$widget = 'yandex_donate_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	// заменим заголовок, чтобы был как в ms 0.9x
	if ( isset($options['header']) and $options['header'] )
		$options['header'] = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></div>');
	else $options['header'] = '';

	if (isset($options['text']) ) $options['text'] = '<p>' . $options['text'] . '</p>';
	else $options['text'] = '';


	if (isset($options['textend']) ) $options['textend'] = '<p>' . $options['textend'] . '</p>';
	else $options['textend'] = '';

	return yandex_donate_widget_custom($options, $num);
}


# форма настройки виджета
# имя функции = виджет_form
function yandex_donate_widget_form($num = 1)
{

	$widget = 'yandex_donate_widget_' . $num; // имя для формы и опций = виджет + номер

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

    $form .= '<p><div class="t150">' . t('Код кнопок:', 'plugins') . ' <a href="https://money.yandex.ru/quickpay/form">Брать тут.</a></div> '. form_textarea( array( 'name'=>$widget . 'textend', 'value'=>$options['textend'] ) ) ;

	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function yandex_donate_widget_update($num = 1)
{
	$widget = 'yandex_donate_widget_' . $num; // имя для опций = виджет + номер

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

function yandex_donate_widget_custom($options = array(), $num = 1)
{
	$header = $options['header'];
	$text = $options['text'];
	$textend = $options['textend'];

	return $header . $text . $textend;
}




//КОНТЕНТ:
/*
Код взят из плагина "adinsertion" и подправлен под данный плагин:
*/

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function yandex_donate_mso_options()
{

	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_yandex_donate', 'plugins',
		array(

			'page_end' => array(
							'type' => 'textarea',
							'name' => 'Текст для вставки в конце каждой статьи',
							'description' => '<p>Добавьте необходимый текст для вставки в конце каждой статьи<br>Код формы доната можно сгенерировать тут<a href="https://money.yandex.ru/quickpay/form">https://money.yandex.ru/quickpay/form</a></p>',
							'default' => ''
			),

			'exclude_page' => array(
							'type' => 'textarea',
							'name' => 'Страницы без рекламы',
							'description' => 'Добавьте адреса страниц ( с новой строки) на которых не должно быть рекламы, к примеру: <b>about</b>. <br>Сравнение производится по имени страницы - "Короткая ссылка", то есть все что после "http://мой сайт/page/")',
							'default' => ''
			),

		),

		'Настройки плагина Яндекс donate', // титул
		'Укажите необходимые опции.'   // инфо
	);

}

# функции плагина
function yandex_donate_content($text = '')
{
	$toadd = '1';
	$divstart = '<div class="yandex_donate">';
	$divend = '</div>';

	$options = mso_get_option('plugin_yandex_donate', 'plugins', array());

	if ( !isset($options['page_end']) ) $options['page_end'] = '';
	if ( !isset($options['exclude_page']) ) $options['exclude_page'] = '';

	if (mso_segment(2) !='' and $options['exclude_page'] !='')
	{
	  if (substr_count($options['exclude_page'], mso_segment(2)) > 0) $toadd = '0';
	}

	if ($options['page_end']!='' and $toadd == '1') $text = $text . $divstart . $options['page_end'] . $divend;




	return $text;
}




