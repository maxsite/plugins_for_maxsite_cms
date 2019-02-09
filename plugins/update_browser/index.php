<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function update_browser_autoload()
{
	mso_hook_add( 'admin_init', 'update_browser_admin_init'); # хук на админку
	mso_hook_add( 'head', 'update_browser_style');
	mso_hook_add( 'body_start', 'update_browser_custom');
}

# функция выполняется при деинстяляции плагина
function update_browser_uninstall($args = array())
{
	mso_delete_option('plugin_update_browser', 'plugins'); // удалим созданные опции
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function update_browser_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_update_browser', 'plugins', 
		array(
	'message' => array(
					'type' => 'text',
					'name' => 'Глобальное сообщение для пользователей устаревших браузеров',
					'description' => 'Применяется для всех браузеров, отмеченных флажками ниже. Если для выбранного браузера необходимо показывать свое сообщение, заполните индивидуальное поле.',
					'default' => '<span>Ваш браузер устарел!</span> Некоторые возможности сайта могут оказаться недоступны. Установите современный браузер:'
					),
		'ie6' => array(
					'type' => 'checkbox', 
					'name' => 'Internet Explorer 6 и ниже', 
					'description' => '', 
					'default' => '1'
					),
'ie6_message' => array(
					'type' => 'text', 
					'name' => 'Сообщение для пользователей Internet Explorer 6', 
					'description' => 'Текст, который увидят пользователи только Internet Explorer 6-. Если оставить поле пустым, показывается глобальное сообщение.', 
					'default' => ''
					),
		'ie7' => array(
					'type' => 'checkbox', 
					'name' => 'Internet Explorer 7', 
					'description' => '', 
					'default' => '0'
					),
'ie7_message' => array(
					'type' => 'text', 
					'name' => 'Сообщение для пользователей Internet Explorer 7', 
					'description' => 'Текст, который увидят пользователи только Internet Explorer 7. Если оставить поле пустым, показывается глобальное сообщение.', 
					'default' => ''
					),
		'ie8' => array(
					'type' => 'checkbox', 
					'name' => 'Internet Explorer 8', 
					'description' => '', 
					'default' => '0'
					),
'ie8_message' => array(
					'type' => 'text', 
					'name' => 'Сообщение для пользователей Internet Explorer 8', 
					'description' => 'Текст, который увидят пользователи только Internet Explorer 8. Если оставить поле пустым, показывается глобальное сообщение.', 
					'default' => ''
					),
		'fx2' => array(
					'type' => 'checkbox', 
					'name' => 'Mozilla Firefox 2 и ниже', 
					'description' => '', 
					'default' => '1'
					),
'fx2_message' => array(
					'type' => 'text', 
					'name' => 'Сообщение для пользователей Mozilla Firefox 2', 
					'description' => 'Текст, который увидят пользователи только Mozilla Firefox 2-. Если оставить поле пустым, показывается глобальное сообщение.', 
					'default' => ''
					),
		'fx3' => array(
					'type' => 'checkbox', 
					'name' => 'Mozilla Firefox 3', 
					'description' => '', 
					'default' => '0'
					),
'fx3_message' => array(
					'type' => 'text', 
					'name' => 'Сообщение для пользователей Mozilla Firefox 3', 
					'description' => 'Текст, который увидят пользователи только Mozilla Firefox 3. Если оставить поле пустым, показывается глобальное сообщение.', 
					'default' => ''
					),
		'o9' => array(
					'type' => 'checkbox', 
					'name' => 'Opera 9 и ниже', 
					'description' => '', 
					'default' => '0'
					),
'o9_message' => array(
					'type' => 'text', 
					'name' => 'Сообщение для пользователей Opera 9', 
					'description' => 'Текст, который увидят пользователи только Opera 9-. Если оставить поле пустым, показывается глобальное сообщение.', 
					'default' => ''
					),
'chrome_url' => array(
					'type' => 'checkbox', 
					'name' => 'Google Chrome', 
					'description' => 'Логотип-ссылка на сайт браузера', 
					'default' => '1'
					),
'firefox_url' => array(
					'type' => 'checkbox', 
					'name' => 'Mozilla Firefox', 
					'description' => 'Логотип-ссылка на сайт браузера', 
					'default' => '1'
					),
'opera_url' => array(
					'type' => 'checkbox', 
					'name' => 'Opera', 
					'description' => 'Логотип-ссылка на сайт браузера', 
					'default' => '1'
					),
'safari_url' => array(
					'type' => 'checkbox', 
					'name' => 'Safari', 
					'description' => 'Логотип-ссылка на сайт браузера', 
					'default' => '0'
					),
	'ie_url' => array(
					'type' => 'checkbox', 
					'name' => 'Internet Explorer', 
					'description' => 'Логотип-ссылка на сайт браузера', 
					'default' => '0'
					),
			),
		'Настройки плагина «Обновите браузер»',
		'Укажите необходимые опции.'   
	);
}

# функции плагина
function update_browser_style() # добавляем в "голову" стили для блока сообщения
{
	$options = mso_get_option('plugin_update_browser', 'plugins', array());

	if ( !isset($options['styles']) ) $options['styles'] = '.b-update-browser {width:100%; padding:12px 0; border-bottom:1px solid #a9a9a9; text-align:center; background:#fff;}
		.b-update-browser p {margin:5px 0 10px!important; font:11px/1.2 Verdana, Arial, sans-serif; color:#333;}
		.b-update-browser p span {color:#ff4500;}
		.b-update-browser a, .b-update-browser a:hover {margin:0 5px; border:0; background:transparent;}
		.b-update-browser img {border:0; vertical-align:middle;}';

	$style = $options['styles'];

	echo '<link rel="stylesheet" href="http://denis-skripnik.ru/application/maxsite/plugins/update_browser/style.css" type="text/css" media="screen">
';
}

# функции плагина
function update_browser_custom($arg = array(), $num = 1) # разбор версии браузера и вывод блока сообщения
{
	$CI = & get_instance();
	$CI->load->library('user_agent'); # браузеры будут определяться с помощью класса CodeIgniter 
	$browser = $CI->agent->browser(); # название браузера
	$version = $CI->agent->version(); # версия браузера

	$options = mso_get_option('plugin_update_browser', 'plugins', array());

	if ( !isset($options['message']) ) $options['message'] = '<span>Ваш браузер устарел!</span> Некоторые возможности сайта могут оказаться недоступны. Установите современный браузер:';
	if ( !isset($options['ie6']) ) $options['ie6'] = 1; # сразу показываем сообщение для IE6-
	if ( !isset($options['ie7']) ) $options['ie7'] = 0;
	if ( !isset($options['ie8']) ) $options['ie8'] = 0;
	if ( !isset($options['fx2']) ) $options['fx2'] = 1; # сразу показываем сообщение для Fx2-
	if ( !isset($options['fx3']) ) $options['fx3'] = 0;
	if ( !isset($options['o9']) ) $options['o9'] = 0;
	if ( !isset($options['ie6_message']) ) $options['ie6_message'] = '';
	if ( !isset($options['ie7_message']) ) $options['ie7_message'] = '';
	if ( !isset($options['ie8_message']) ) $options['ie8_message'] = '';
	if ( !isset($options['fx2_message']) ) $options['fx2_message'] = '';
	if ( !isset($options['fx3_message']) ) $options['fx3_message'] = '';
	if ( !isset($options['o9_message']) ) $options['o9_message'] = '';
	if ( !isset($options['chrome_url']) ) $options['chrome_url'] = 1;
	if ( !isset($options['firefox_url']) ) $options['firefox_url'] = 1;
	if ( !isset($options['opera_url']) ) $options['opera_url'] = 1;
	if ( !isset($options['safari_url']) ) $options['safari_url'] = 0;
	if ( !isset($options['ie_url']) ) $options['ie_url'] = 0;

	$url = getinfo('plugins_url') . 'update_browser/images/'; # путь к картинкам

	if ($options['chrome_url'] == 1) # в зависимости от значения опции присваиваем переменной картинку-ссылку или оставляем пустой
	{ $chrome_url = '<a href="http://www.google.com/chrome/" title="Google Chrome"><img  src="' . $url . 'chrome.png" alt="Google Chrome"></a>'; }
	else
	{ $chrome_url = ''; }
	if ($options['firefox_url'] == 1)
	{ $firefox_url = '<a href="http://www.mozilla-europe.org/" title="Mozilla Firefox"><img src="' . $url . 'firefox.png" alt="Mozilla Firefox"></a>'; }
	else
	{ $firefox_url = ''; }
	if ($options['opera_url'] == 1)
	{ $opera_url = '<a href="http://www.opera.com/" title="Opera"><img src="' . $url . 'opera.png" alt="Opera"></a>'; }
	else
	{ $opera_url = ''; }
	if ($options['safari_url'] == 1)
	{ $safari_url = '<a href="http://www.apple.com/ru/safari/" title="Safari"><img  src="' . $url . 'safari.png" alt="Safari"></a>'; }
	else
	{ $safari_url = ''; }
	if ($options['ie_url'] == 1)
	{ $ie_url = '<a href="http://www.microsoft.com/rus/windows/internet-explorer/" title="Internet Explorer"><img src="' . $url . 'ie.png" alt="Internet Explorer"></a>'; }
	else
	{ $ie_url = ''; }

	$msg = $options['message'];

	if ( isset($options['ie6']) && ($options['ie6'] == 1) && $browser == 'Internet Explorer' && $version <= 6 )
	{
		if ($options['ie6_message'] != '') # заполнено ли идивидуальное поле для браузера?
		{
			$msg = $options['ie6_message'];
			echo '<div class="b-update-browser"><p>' . $msg . '</p>' . $chrome_url . $firefox_url . $opera_url . $safari_url . $ie_url . '</div>';
		}
		else
		{
			echo '<div class="b-update-browser"><p>' . $msg . '</p>' . $chrome_url . $firefox_url . $opera_url . $safari_url . $ie_url . '</div>';
		}
	}
	if ( isset($options['ie7']) && ($options['ie7'] == 1) && $browser == 'Internet Explorer' && $version == 7 )
	{
		if ($options['ie7_message'] != '')
		{
			$msg = $options['ie7_message'];
			echo '<div class="b-update-browser"><p>' . $msg . '</p>' . $chrome_url . $firefox_url . $opera_url . $safari_url . $ie_url . '</div>';
		}
		else
		{
			echo '<div class="b-update-browser"><p>' . $msg . '</p>' . $chrome_url . $firefox_url . $opera_url . $safari_url . $ie_url . '</div>';
		}
	}
	if ( isset($options['ie8']) && ($options['ie8'] == 1) && $browser == 'Internet Explorer' && $version == 8 )
	{
		if ($options['ie8_message'] != '')
		{
			$msg = $options['ie8_message'];
			echo '<div class="b-update-browser"><p>' . $msg . '</p>' . $chrome_url . $firefox_url . $opera_url . $safari_url . $ie_url . '</div>';
		}
		else
		{
			echo '<div class="b-update-browser"><p>' . $msg . '</p>' . $chrome_url . $firefox_url . $opera_url . $safari_url . $ie_url . '</div>';
		}
	}
	if ( isset($options['fx2']) && ($options['fx2'] == 1) && $browser == 'Firefox' && $version <= 2 )
	{
		if ($options['fx2_message'] != '')
		{
			$msg = $options['fx2_message'];
			echo '<div class="b-update-browser"><p>' . $msg . '</p>' . $chrome_url . $firefox_url . $opera_url . $safari_url . $ie_url . '</div>';
		}
		else
		{
			echo '<div class="b-update-browser"><p>' . $msg . '</p>' . $chrome_url . $firefox_url . $opera_url . $safari_url . $ie_url . '</div>';
		}
	}
	if ( isset($options['fx3']) && ($options['fx3'] == 1) && $browser == 'Firefox' && $version == 3 )
	{
		if ($options['fx3_message'] != '')
		{
			$msg = $options['fx3_message'];
			echo '<div class="b-update-browser"><p>' . $msg . '</p>' . $chrome_url . $firefox_url . $opera_url . $safari_url . $ie_url . '</div>';
		}
		else
		{
			echo '<div class="b-update-browser"><p>' . $msg . '</p>' . $chrome_url . $firefox_url . $opera_url . $safari_url . $ie_url . '</div>';
		}
	}
	if ( isset($options['o9']) && ($options['o9'] == 1) && $browser == 'Opera' && $version <= 9 )
	{
		if ($options['o9_message'] != '')
		{
			$msg = $options['o9_message'];
			echo '<div class="b-update-browser"><p>' . $msg . '</p>' . $chrome_url . $firefox_url . $opera_url . $safari_url . $ie_url . '</div>';
		}
		else
		{
			echo '<div class="b-update-browser"><p>' . $msg . '</p>' . $chrome_url . $firefox_url . $opera_url . $safari_url . $ie_url . '</div>';
		}
	}
}

?>