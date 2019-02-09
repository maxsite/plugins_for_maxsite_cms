<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function snow_autoload()
{
	mso_hook_add('head', 'snow_head');
	
	$options = mso_get_option('plugin_snow', 'plugins', array() ); // получаем опции
	if (isset($options['admin']) and $options['admin']) mso_hook_add('admin_head', 'snow_head');
}

# функция выполняется при деинсталяции плагина
function snow_uninstall($args = array())
{	
	mso_delete_option('plugin_snow', 'plugins'); // удалим созданные опции
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function snow_mso_options() 
{
	// получение всех картинок в каталоге images
	$CI = & get_instance();
	$CI->load->helper('directory'); 
	
	$path = getinfo('plugins_dir') . 'snow/images/';
	$path_url = getinfo('plugins_url') . 'snow/images/';
	$files = directory_map($path, true);
	
	$images = '';
	
	foreach ($files as $file)
	{
		if (@is_dir($path . $file)) continue; // это каталог
		$images .= $file . '||<img src="' . $path_url . $file . '" style="vertical-align: middle">'. '  ';
	}
	
	$images = str_replace('  ', '#', trim($images));
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_snow', 'plugins', 
		array(
		
			'admin' => array(
							'type' => 'checkbox', 
							'name' => t('Включить снег для админки', __FILE__), 
							'description' => t('После включения ещё раз обновите страницу', __FILE__), 
							'default' => '0'
						),
			'temp' => array(
						'type' => 'info', # нужно указать такой тип!
						'title' => 'Хлопья', 
						'text' => '', 
					),
					
			'hlopya' => array(
							'type' => 'checkbox', 
							'name' => t('Включить снег хлопьями', __FILE__), 
							'description' => '', 
							'default' => '0'
						),

			'temp1' => array(
						'type' => 'info', # нужно указать такой тип!
						'title' => 'Снежинки', 
						'text' => '', 
						),
					
			'sneg' => array(
							'type' => 'checkbox', 
							'name' => t('Включить снежинки', __FILE__), 
							'description' => '', 
							'default' => '0'
						),
						
			'sneg_kolvo' => array(
							'type' => 'text', 
							'name' => t('Количество снежинок', __FILE__), 
							'description' => 'Чем больше, тем сильней будет тормозить сайт', 
							'default' => '10'
						),
								
			'sneg_speed' => array(
							'type' => 'text', 
							'name' => t('Скорость падения', __FILE__), 
							'description' => 'Чем меньше, тем быстрей', 
							'default' => '30'
						),				
			'sneg_image' => array(
							'type' => 'radio', 
							'name' => t('Файл изображения снежинки', __FILE__), 
							'values' => $images, 
							'description' => '', 
							'default' => 'snow01.png',
							'delimer' => ' &nbsp;&nbsp;&nbsp;&nbsp;',
						),
		 
						
			),
		'Настройки плагина Snow (снег)', // титул
		'Укажите необходимые опции.'   // инфо
	);

}

# функции плагина
function snow_head($arg = array())
{
	
	$options = mso_get_option('plugin_snow', 'plugins', array() ); // получаем опции
	
	if (isset($options['hlopya']) and $options['hlopya']) // хлопья
	{
		echo '<script type="text/javascript" src="' . getinfo('plugins_url') . 'snow/js/snow-storm.js"></script>' . NR;
	}
	
	if (isset($options['sneg']) and $options['sneg']) // хлопья
	{
		if (!isset($options['sneg_kolvo'])) $options['sneg_kolvo'] = 10;
		if ((int) $options['sneg_kolvo'] < 0) $options['sneg_kolvo'] = 10;
		
		if (!isset($options['sneg_speed'])) $options['sneg_speed'] = 30;
		if ((int) $options['sneg_speed'] < 0) $options['sneg_speed'] = 30;
		
		if (!isset($options['sneg_image'])) $options['sneg_image'] = 'snow01.png';
		
		echo '<script type="text/javascript">
			var image="' . getinfo('plugins_url') . 'snow/images/' . $options['sneg_image'] . '";
			var no = ' . $options['sneg_kolvo'] . '; 
			var speed = ' . $options['sneg_speed'] . ';
		</script>';
		echo '<script type="text/javascript" src="' . getinfo('plugins_url') . 'snow/js/wp-effects.js"></script>' . NR;
	}
	
	return $arg;
}

# end file