<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function top_menu_autoload()
{
	if (is_login() or is_login_comuser())  
	{
		mso_hook_add('body_start', 'top_menu_out');
		mso_hook_add('head', 'top_menu_head');
	}
}

# функция выполняется при активации (вкл) плагина
function top_menu_activate($args = array())
{	
	mso_create_allow('top_menu_edit', t('Админ-доступ к настройкам Top menu', 'plugins') . ' ' . t('top_menu', __FILE__));
	return $args;
}

# функция выполняется при деактивации (выкл) плагина
function top_menu_deactivate($args = array())
{	
	mso_delete_option('plugin_top_menu', 'plugins'); // удалим созданные опции
	return $args;
}

# функция выполняется при деинсталяции плагина
function top_menu_uninstall($args = array())
{	
	mso_delete_option('plugin_top_menu', 'plugins'); // удалим созданные опции
	mso_remove_allow('top_menu_edit'); // удалим созданные разрешения
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function top_menu_mso_options() 
{
	if ( !mso_check_allow('top_menu_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return;
	}
	
	# ключ, тип, ключи массива
	mso_admin_plugin_options('plugin_top_menu', 'plugins', 
		array(

			'menu_admin' => array(
							'type' => 'textarea', 
							'name' => t('Меню админа/автора', __FILE__), 
							'description' => t('Укажите пункты меню админа', __FILE__), 
							'default' => 
'admin/home | Информация
admin/page_new | Создать запись
admin/page | Список записей
admin/cat | Рубрики
admin/plugins | Плагины
admin/files | Загрузки
[
# | Помощь
http://max-3000.com/page/faq | ЧАВО для новичков
http://max-3000.com/help | Центр помощи
http://forum.max-3000.com/ | Форум поддержки
]
logout | Выход
'
			
						),			
			'menu_comuser' => array(
							'type' => 'textarea', 
							'name' => t('Меню комюзера', __FILE__), 
							'description' => t('Укажите пункты меню комюзера', __FILE__), 
							'default' => 
'users/[comusers_id] | Своя страница
[
# | Помощь
http://max-3000.com/page/faq | ЧАВО для новичков
http://max-3000.com/help | Центр помощи
http://forum.max-3000.com/ | Форум поддержки
]
logout | Выход
'
						),
			),
		'Настройки плагина Top menu', // титул
		'Задайте пункты меню.'   // инфо
	);
}


function top_menu_head($arg = array())
{
	if (file_exists(getinfo('template_dir') . 'top-menu.css')) // шаблон/top-menu.css
		$url =  getinfo('template_url') . 'top-menu.css';
	elseif (file_exists(getinfo('template_dir') . 'css/top-menu.css')) // шаблон/css/top-menu.css
		$url =  getinfo('template_url') . 'css/top-menu.css' ;
	else
		$url = getinfo('plugins_url') . 'top_menu/top-menu.css'; // плагин/top-menu.css
	
	echo NR . '	<link rel="stylesheet" href="' . $url . '" type="text/css" media="screen">';
		
	return $arg;
}

function top_menu_out($arg = array())
{
	$options = mso_get_option('plugin_top_menu', 'plugins', '');
	
	if (is_login())
		$menu = (isset($options['menu_admin']) and $options['menu_admin']) ? $options['menu_admin'] : 
'admin/home | Информация
admin/page_new | Создать запись
admin/page | Список записей
admin/cat | Рубрики
admin/plugins | Плагины
admin/files | Загрузки
[
# | Помощь
http://max-3000.com/page/faq | ЧАВО для новичков
http://max-3000.com/help | Центр помощи
http://forum.max-3000.com/ | Форум поддержки
]
logout | Выход
'; 

	elseif (is_login_comuser())
	{
		$menu = (isset($options['menu_comuser']) and $options['menu_comuser']) ? $options['menu_comuser'] : 
'users/[comusers_id] | Своя страница
[
# | Помощь
http://max-3000.com/page/faq | ЧАВО для новичков
http://max-3000.com/help | Центр помощи
http://forum.max-3000.com/ | Форум поддержки
]
logout | Выход
';

		$comuser = is_login_comuser();
		$menu = str_replace('[comusers_id]', $comuser['comusers_id'], $menu);
	}
	else $menu = false;

	if ($menu)
	{
		$menu = mso_menu_build($menu, 'selected', false);
		echo '<ul class="dropdown">' . $menu . '</ul><br clear="all">';
	}
	
	return $arg;
}


#end file
