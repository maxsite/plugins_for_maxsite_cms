<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function mobile_theme_autoload($args = array())
{
	mso_hook_add( 'init', 'mobile_theme_init'); # хук на init
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function mobile_theme_mso_options() 
{
	$templates_dir = getinfo('templates_dir');
	$dirs = directory_map($templates_dir, true);
	$list = '';
	foreach ($dirs as $dir)
	{
		// обязательный файл index.php
		if (file_exists( $templates_dir . $dir . '/index.php' ))
		{		
			if ($list != '') $list .= ' # ';
			$list .= $dir;
		}
	}
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_mobile_theme', 'plugins', 
		array(
			'theme' => array(
						'type' => 'select', 
						'name' => 'Шаблон для мобильных устройств',
						'description' => 'Шаблон для мобильных устройств', 
						'values' => $list,
						'default' => 'mobile'
					),						
		),
		'Настройки плагина Mobile Theme', // титул
		'Укажите необходимые опции.'   // инфо
	);
}

# функция выполняется при init
function mobile_theme_init($args = array())
{	
	global $MSO;

	$mobile = false;
	require(getinfo('plugins_dir') . 'mobile_theme/mobile_detect.php');
	$mobile = mobile_detect();
	if ($mobile == true)
	{
		$options = mso_get_option('plugin_mobile_theme', 'plugins', array() ); // получаем опции
		if (!isset($options['theme'])) $options['theme'] = 'mobile';
		if (file_exists( getinfo('templates_dir') . $options['theme'] . '/index.php' )) 
		{
			$MSO->config['template'] = $options['theme'];
			$functions_file = $MSO->config['templates_dir'] . $options['theme'] . '/functions.php';
			if (file_exists($functions_file)) require_once($functions_file);
		}	
	}
	return $args;
}

# функция выполняется при деинсталяции плагина
function mobile_theme_uninstall($args = array())
{	
	mso_delete_option('mobile_theme', 'plugins'); // удалим созданные опции
	return $args;
}

?>