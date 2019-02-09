<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


# функция автоподключения плагина
function corner_autoload()
{
	mso_hook_add('head', 'corner_head');
	
	$options = mso_get_option('plugin_corner', 'plugins', array() ); // получаем опции
	if (isset($options['admin']) and $options['admin']) mso_hook_add('admin_head', 'corner_head');
}

# функция выполняется при деинсталяции плагина
function corner_uninstall($args = array())
{	
	mso_delete_option('plugin_corner', 'plugins'); // удалим созданные опции
	return $args;
}

function corner_mso_options() {
# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_corner', 'plugins', 
		array(
		
			'pic_big' => array(
							'type' => 'text', 
							'name' => t('Путь', __FILE__), 
							'description' => 'Укажите путь к большой картинке 500х500', 
							'default' => ''
						),

			'pic_small' => array(
							'type' => 'text', 
							'name' => t('Путь', __FILE__), 
							'description' => 'Укажите путь к маленькой картинке 75х75', 
							'default' => ''
						),		

			'aut_url' => array(
							'type' => 'text', 
							'name' => t('Ссылка', __FILE__), 
							'description' => 'Укажите ссылку на страничку', 
							'default' => ''
						),	 
						
			),
		'Настройки плагина corner (Волшебный уголок)', // титул
		'Укажите необходимые опции.'   // инфо
	);
}
# функции плагина
function corner_head($arg = array())
{
	
	$options = mso_get_option('plugin_corner', 'plugins', array() ); // получаем опции
	
		echo '<script type="text/javascript">
			var adurlmc = escape("' . $options['aut_url'] . '");
			var smallimagemc = escape("' . $options['pic_small'] . '");
			var bigimagemc = escape("' . $options['pic_big'] . '");
			var smallpathmc = "'. getinfo('plugins_url') . 'corner/small.swf";
			var bigpathmc = "'. getinfo('plugins_url') . 'corner/large.swf";
		</script>';

		echo '<script type="text/javascript" src="' . getinfo('plugins_url') . 'corner/peel.js"></script>' . NR;
	
	return $arg;
}

# end file