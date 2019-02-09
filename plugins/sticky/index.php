<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Plugin «Sticky» for maxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://vizr.ru/page/plugin-sticky
 */

# функция автоподключения плагина
function sticky_autoload( $args = array() )
{	
	# Определяем опции для управления правами доступа к плагину
	mso_create_allow(basename(dirname(__FILE__)).'_options', 'Админ-доступ к опциям плагина «Sticky»');
		
	mso_hook_add('admin_head', basename(dirname(__FILE__)).'_admin_head'); # хук для подключения стилей на внутренних страницах
		
	mso_hook_add('head', basename(dirname(__FILE__)).'_head'); # хук для подключения  необходимых скриптов и стилей на внешних страницах
	mso_hook_add('body_end', basename(dirname(__FILE__)).'_end'); # хук для вывода запускающего js-скрипта
}

# функция выполняется при активации (вкл) плагина
function sticky_activate( $args = array() )
{
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function sticky_deactivate( $args = array() )
{	
	return $args;
}

# функция выполняется при деинсталяции плагина
function sticky_uninstall( $args = array() )
{
	# удалим созданные опции
	mso_delete_option('plugin_'.basename(dirname(__FILE__)), 'plugins');
		
	# удалим созданные разрешения
	mso_remove_allow(basename(dirname(__FILE__)).'_options');
		
	return $args;
}

# функция подключения дополнительных стилей и скриптов для работы плагина в админке
function sticky_admin_head( $args = array() )
{
	echo NR.'<!-- плагин '.basename(dirname(__FILE__)).' -->'.NR;
		
	# Подключение необходимого на странице опций плагина
	# admin/plugin_options/sticky
	if( mso_segment(1) == 'admin' && mso_segment(2) == 'plugin_options' && mso_segment(3) == basename(dirname(__FILE__)) )
	{
		# стили для страницы опций
		if( $fn = mso_fe('backend-options.css', getinfo('plugins_dir').basename(dirname(__FILE__)).'/') ) echo mso_load_style( getinfo('plugins_url').basename(dirname(__FILE__)).'/backend-options.css' );
	}
		
	echo NR.'<!-- /плагин '.basename(dirname(__FILE__)).' -->'.NR;
		
	return $args;
}


# формирование страницы опций в админке
function sticky_mso_options()
{
	if( !mso_check_allow(basename(dirname(__FILE__)).'_options') )
	{
		echo 'Доступ запрещен';
		return;
	}
		
	# получаем опции
	$options = sticky_get_options();

	# подключаем файл с определением опций
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/settings.php' );
		
	# ключ, тип, ключи массива
	mso_admin_plugin_options2('plugin_'.basename(dirname(__FILE__)), 'plugins', 
		$settings,
		'Настройки плагина «Sticky»', # титул
		'Задайте необходимые значения указанным опциям'   # инфо
	);
		
	# подключаем файл информации об авторе плагина
	require( getinfo('plugins_dir').basename(dirname(__FILE__)).'/author-info.php' );
}

# вызов скрипта в начале страницы
function sticky_head( $args = array() )
{
	# получаем опции
	$options = sticky_get_options();
	if( count($options) == 0 || $options['embedhere'] == 2 || $options['widgets'] == '' )
	{
		return $args; # если опции не заданы, то выводить нечего - передаём разбор запроса дальше
	} 
	else 
	{
		sticky_show($options);
	}
	return $args;
}

# вызов скрипта в конце страницы
function sticky_end( $args = array() )
{
	# получаем опции
	$options = sticky_get_options();
	if( count($options) == 0 || $options['embedhere'] == 1 || $options['widgets'] == '' )
	{
		return $args; # если опции не заданы, то выводить нечего - передаём разбор запроса дальше
	} 
	else 
	{
		sticky_show($options);
	}
	return $args;
}

function sticky_show( $options )
{
	$plugin_url = getinfo('plugins_url').basename(dirname(__FILE__));
		
	if( $options['widgets'] == '' )
	{
		return;
	}
	else
	{
		$out = '';
		
		if( preg_match_all("/\[widget\](.*?)\[\/widget\]/msi", trim($options['widgets']), $sticks) )
		{
			foreach( $sticks[1] as $widget )
			{
				$widget = trim($widget);
				if( $widget != '' )
				{
					$params = array();
					$lines = explode(NR, trim($widget));
					foreach( $lines as $ln )
					{
						$param = array_map('trim', explode('=', trim($ln)));
						if( !is_null($param[1]) && $param[1] != '' ) // удаляем null и пустые строки, но оставляем 0, false и нормальные значения
						{
							$params[mb_strtolower($param[0])] = implode('=', array_slice($param, 1));
						}
					}
						
					if( count($params) && isset($params['selector']) ) #selector
					{
						$opt = array();
						foreach( $params as $k => $v )
						{
							if( $k != 'selector' )
							{
								$v = preg_replace("/^([\'\"]*)(.*?)([\'\"]*)$/msi", "\\2", $v);
							
								if( $k == 'stopper' || $k == 'styler' || $k == 'sticktype' )
								{
									$opt[] = $k.': \''.$v.'\'';
								}
								else
								{
									$opt[] = $k.': '.$v;
								}
							}
						}
						if( count($opt) > 0 )
						{
							$opt = '{'.implode(', ', $opt).'}';
						}
						else
						{
							$opt = '{}';
						}
							
						$out .= TAB . TAB . TAB . "$('".$params['selector']."').sticky(".$opt.");".NR;
					}
				}
			}
		}
			
		# Подключение файла скрипта и вызов jquery-плагина
		if( $out != '' )
		{
			echo
				NR . TAB . 
				'<!-- sticky -->' . NR .
				TAB . '<script src="'.$plugin_url.'/js/jquery.sticky.min.js" type="text/javascript"></script>' . NR .
				TAB . '<script type="text/javascript">' . NR .
				TAB . TAB . "$(window).load(function(){" . NR .
				$out .
				TAB . TAB . "});" . NR .
				TAB . '</script>' . NR .
				TAB . '<!-- /sticky -->' . NR;
		}
	}
}

# получение опций со значениями по-умолчанию
function sticky_get_options( $optskey = '', $optsfile = 'settings.php', $refresh = false )
{
	static $options;
		
	if( !isset($optskey) || $optskey == '' ) $optskey = 'plugin_'.basename(dirname(__FILE__));
		
	if( !isset($options[$optskey]) || count($options[$optskey]) == 0 || $refresh )
	{
		$options[$optskey] = mso_get_option($optskey, 'plugins', array());
			
		if( $fn = mso_fe($optsfile, getinfo('plugins_dir').basename(dirname(__FILE__)).'/') ) require($fn);
		if( isset($settings) && count($settings) > 0 )
		{
			foreach( $settings as $key => $opt )
			{
				if( !isset($options[$optskey][$key]) && $opt['type'] != 'checkbox' && isset($opt['default']) ) $options[$optskey][$key] = $opt['default'];
			}
		}
	}
		
	return $options[$optskey];
}

# подключаем файл с функцией mso_admin_plugin_options2
if( $fn = mso_fe('mso-admin-plugin-options2.php', getinfo('plugins_dir').basename(dirname(__FILE__)).'/') ) require($fn);

?>