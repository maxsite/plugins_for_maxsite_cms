<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * For MaxSite CMS
 * vgoban Plugin
 * Author: (c) Derian
 * Plugin URL: http://derian.isgreat.org
 */

# функция автоподключения плагина
function vgoban_autoload($args = array())
{
	mso_hook_add( 'content', 'vgoban_custom'); # хук на вывод контента

	$options_key = 'plugin_vgoban';
	$options = mso_get_option($options_key, 'plugins', array());
}

# функция выполняется при активации (вкл) плагина
function vgoban_activate($args = array())
{
	vgoban_mso_options();
	return $args;
}


function vgoban_deactivate($args = array())
{
	mso_delete_option('plugin_vgoban', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function vgoban_uninstall($args = array())
{
	// константа
	$options_key = 'plugin_vgoban';

	mso_delete_option($options_key,'plugins');
	return $args;
}

# функции плагина
function vgoban_custom($text)
{

	$text = preg_replace_callback('~\[vgoban=(.*?)\]~si', 'vgoban_callback', $text);

    return $text;
}

function vgoban_callback($matches)
{
	$url = $matches[1];

	$options_key = 'plugin_vgoban';
	$options = mso_get_option($options_key, 'plugins', array());

	if ( !isset($options['width'])  ) $options['width'] = '640';
	if ( !isset($options['height'])  ) $options['height'] = '480';
	if ( !isset($options['nav'])  ) $options['nav'] = '16AAD9';
	if ( !isset($options['bg'])  ) $options['bg'] = 'E8F3FD';
	if ( !isset($options['show_sgf_url'])  ) $options['show_sgf_url'] = '0';

	$out = '<center><EMBED src="'.getinfo('plugins_url').'vgoban/goswf.swf"
			flashVars="nav='. $options['nav'] .'&bg='. $options['bg'] .'&url='.$url.'"
			width="'. $options['width'] .'" height="'. $options['height'] .'"
			type="application/x-shockwave-flash"
			pluginspage="http://www.macromedia.com/go/getflashplayer"></EMBED></center>';

	if ($options['show_sgf_url'])
		$out .= '<br><a href="'. $url .'"><center>SGF файл</center></a>';

	return $out;
}


# функция отрабатывающая миниопции плагина (function плагин_mso_options)
function vgoban_mso_options()
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_vgoban', 'plugins',
		array(
			'width' => array(
							'type' => 'text',
							'name' => t('Ширина', __FILE__),
							'description' => t('Ширина гобана', __FILE__),
							'default' => '640'
						),


			'height' => array(
							'type' => 'text',
							'name' => t('Высота', __FILE__),
							'description' => t('Высота гобана', __FILE__),
							'default' => '480'
						),


			'nav' => array(
							'type' => 'text',
							'name' => t('Цвет', __FILE__),
							'description' => t('Цвет панели навигации', __FILE__),
							'default' => '16AAD9'
						),


			'bg' => array(
							'type' => 'text',
							'name' => t('Фон комментариев', __FILE__),
							'description' => t('Цвет фона поля комментариев', __FILE__),
							'default' => 'E8F3FD'
						),
			'show_sgf_url' => array(
							'type' => 'checkbox',
							'name' => t('Ссылка на файл', __FILE__),
							'description' => t('Выводит ссылку на sgf-файл', __FILE__),
							'default' => '0'
						),
			),
		'Настройки плагина vgoban', // титул
		'Укажите необходимые опции.'   // инфо
	);

}
?>