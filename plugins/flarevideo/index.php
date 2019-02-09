<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function flarevideo_autoload($args = array())
{
	$options = mso_get_option('plugin_flarevideo', 'plugins', array());
	mso_hook_add( 'head', 'flarevideo_head');
	mso_hook_add( 'content', 'flarevideo_content');
}

# функции плагина
function flarevideo_head($arg = array())
{
	$options = mso_get_option('plugin_flarevideo', 'plugins', array());

	echo '	<link rel="stylesheet" href="' . getinfo('plugins_url') . 'flarevideo/stylesheets/flarevideo.css" type="text/css">';
	
	if ($options['option1']=='1') 
	{ 
		echo '	<link rel="stylesheet" href="' . getinfo('plugins_url') . 'flarevideo/stylesheets/flarevideo.default.css" type="text/css">';
	} else
	if ($options['option1']=='2') 
	{
		echo '	<link rel="stylesheet" href="' . getinfo('plugins_url') . 'flarevideo/stylesheets/flarevideo.spotify.css" type="text/css">';
	} else
	{
		echo '	<link rel="stylesheet" href="' . getinfo('plugins_url') . 'flarevideo/stylesheets/flarevideo.vimeo.css" type="text/css">';
	}
	
	//echo '	<script src="' . getinfo('plugins_url') . 'flarevideo/javascripts/jquery.js" type="text/javascript"></script>';
	//echo '	<script src="' . getinfo('plugins_url') . 'flarevideo/javascripts/jquery.ui.slider.js" type="text/javascript"></script>';
	//echo '	<script src="' . getinfo('plugins_url') . 'flarevideo/javascripts/jquery.flash.js" type="text/javascript"></script>';
	echo '	<script src="' . getinfo('plugins_url') . 'flarevideo/javascripts/flarevideo.js" type="text/javascript"></script>';
	
	//pr($options['option1']);
	
	return $arg;
}

function flarevideo_mso_options() 
{
	if ( !mso_check_allow('flarevideo_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_flarevideo', 'plugins', 
		array(
			'option1' => array(
						'type' => 'select', //тип "текстовое поле"
						'name' => t('Шкурки плеера'), //название
						'description' => t('Выберите нужную обложку'), //пояснение
						'values' => t('1||Стандартная #2||Spotify #3||Vimeo'),
						'default' => '1' //по-умолчанию
						),
			),
		t('Настройки плагина flarevideo'), // титул
		t('Укажите необходимые опции.')   // инфо
	);
}

# функция выполняется при деактивации (выкл) плагина
function flarevideo_deactivate($args = array())
{	
	mso_delete_option('plugin_flarevideo', 'plugins' ); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function flarevideo_uninstall($args = array())
{	
	mso_delete_option('plugin_flarevideo', 'plugins' ); // удалим созданные опции
	mso_remove_allow('flarevideo_edit'); // удалим созданные разрешения 
	return $args;
}

# callback функция 
function flarevideo_content_callback($matches)
{	
	$url = $matches[1];
	
	//pr($matches[1]);

	$out = '<div id="video"></div>
	<script type="text/javascript" charset="utf-8">
			fv = $("#video").flareVideo({
			srcs:[
				{
				src:  \''. $url .'\',
				type: \'video/mp4\'
				}]});
				fv.load();
				</script> ';
	return $out;
}


# функции плагина
function flarevideo_content($text = '')
{	
	$text = preg_replace_callback('~\[video=(.*?)\]~si', 'flarevideo_content_callback', $text);

	return $text;
}

?>