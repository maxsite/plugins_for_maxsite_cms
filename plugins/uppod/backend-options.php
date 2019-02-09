<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin «Uppod-плеер» for MaxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://vizr.ru/page/plugin-uppod-player
 */
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/backend-options-lib.php' ); # подключаем файл с функцией mso_admin_plugin_options2
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/options.php' ); # подключаем файл с определением опций

	# ключ, тип, ключи массива
	mso_admin_plugin_options2('plugin_uppod', 'plugins', 
		$uppod_plugin_options,
		'Настройки плагина «Uppod-плеер»', # титул
		'Задайте необходимые значения указанным опциям'   # инфо
	);
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/author-info.php' ); # подключаем файл информации об авторе плагина
?>