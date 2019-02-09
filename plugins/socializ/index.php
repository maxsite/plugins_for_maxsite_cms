<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
# функция автоподключения плагина
function socializ_autoload($args = array())
{
	if ( is_type('page') )
	{
		mso_hook_add( 'head', 'socializ_head');
		mso_hook_add( 'content_end', 'socializ_content_end');
	}
}
# функция выполняется при деактивации (выкл) плагина
function socializ_deactivate($args = array())
{
	mso_delete_option('plugin_socializ', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function socializ_uninstall($args = array())
{
	mso_delete_option('plugin_socializ', 'plugins'); // удалим созданные опции
	return $args;
}
# функция отрабатывающая миниопции плагина
function socializ_mso_options() 
{
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_socializ', 'plugins', 
		array(
			'socializ_switch' => array(
				'type' => 'select', 
				'name' => 'Вид панели', 
				'description' => 'Выберите вид плавающей панели.',
				'values' => '1||Все иконки отображаются всегда # 2||Укороченная панель с переключателем',
				'default' => '2'
				),
			'socializ_css' => array(
				'type' => 'select', 
				'name' => 'Оформление', 
				'description' => 'Выберите оформление панели.',
				'values' => '1||Прозрачный белый # 2||Прозрачный черный',
				'default' => '1'
				),
			'socializ_space1' => array(
				'type' => 'text', 
				'name' => 'Отступ 1', 
				'description' => 'Расстояние от начала страницы до плавающей панели', 
				'default' => '140'
				),
			'socializ_space2' => array(
				'type' => 'text', 
				'name' => 'Отступ 2', 
				'description' => 'Отступ от левой границы страницы до плавающей панели', 
				'default' => '-80'
				),
			),
		'Настройки плавающей панели', // титул
		'Укажите необходимые опции.'   // инфо
	);
}
# подключаем в заголовок стили и js
function socializ_head($args = array()) 
{
	$options = mso_get_option('plugin_socializ', 'plugins', array() ); // получаем опции
	if (!isset($options['socializ_switch'])) $options['socializ_switch'] = 2;
	if (!isset($options['socializ_css'])) $options['socializ_css'] = 1;
	if (!isset($options['socializ_space1'])) $options['socializ_space1'] = 140;
	if (!isset($options['socializ_space2'])) $options['socializ_space2'] = -80;
	echo mso_load_jquery();
	echo "<script type='text/javascript'>
	var m1 = " . $options['socializ_space1'] . ";
	var m2 = 20;
	var f = '" . getinfo('plugins_url') . "socializ/i/';</script>";
	echo '<script type="text/javascript" src="' . getinfo('plugins_url') . 'socializ/socializ_' . $options['socializ_switch'] . '.js"></script>';
	echo '<link rel="stylesheet" href="' . getinfo('plugins_url') . 'socializ/socializ_' . $options['socializ_css'] . '.css" type="text/css" media="screen">';
	echo '<style type="text/css"> #socializ { margin-left: ' . $options['socializ_space2'] . 'px; }</style>';
	return $args;
}
# функции плагина
function socializ_content_end($args = array())
{
	global $page;
	echo '<script type="text/javascript">socializ(encodeURIComponent("' . getinfo('siteurl') . mso_current_url() . '"),encodeURIComponent("' . $page['page_title'] . '"))</script>';
	return $args;
}
?>