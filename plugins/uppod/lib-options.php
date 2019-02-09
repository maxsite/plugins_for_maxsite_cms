<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Plugin «Uppod-плеер» for MaxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://vizr.ru/page/plugin-uppod-player
 */

# получение опций со значениями по-умолчанию
function uppod_get_options( $defs = true )
{
	$options = mso_get_option('plugin_uppod', 'plugins', array());
	if( count($options) == 0 && !$defs )
	{
		return $options;
	}

	#значения по-умолчанию
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/options.php' ); # подключаем файл с определением опций
	foreach( $uppod_plugin_options as $key => $opt )
	{
		if( !isset($options[$key]) && isset($opt['default']) ) $options[$key] = $opt['default'];
	}
		
	return $options;
}

# Функция получения списка файлов стилей для подключения при отображении плеера
# используется в options.php для формирования значений опций
function uppod_styles()
{
	$out = array('||без стилей');
		
	foreach( glob(getinfo('plugins_dir').basename(dirname(__FILE__)).'/style/*.{js,txt}', GLOB_BRACE) as $name )
	{
		if( @is_file($name) )
		{
			$fname = basename($name);
			$out[] = $fname . '||' . $fname;
		}
	}
		
	return implode('#', $out); 
}
?>