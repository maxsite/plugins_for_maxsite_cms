<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * (c) http://uncleeugene.net
 * Плагин написан на обломках родного Maxsite'овского "Redirect". 
 * Большая часть кода принадлежит автору оного Redirect'а :)
 */


# функция автоподключения плагина
function maintenance_autoload($args = array())
{
	mso_hook_add( 'admin_init', 'maintenance_admin_init'); # хук на админку
	mso_hook_add( 'init', 'maintenance_init'); # хук на init
}

# функция выполняется при активации (вкл) плагина
function maintenance_activate($args = array())
{	
	mso_create_allow('maintenance_edit', t('Админ-доступ к плагину редиректов'));
	return $args;
}

# функция выполняется при деинстяляции плагина
function maintenance_uninstall($args = array())
{
	mso_remove_allow('maintenance_edit'); // удалим созданные разрешения
	mso_delete_option('maintenance', 'plugins' ); // удалим созданные опции
	return $args;
}

# цепляемся к хуку init
function maintenance_init($args = array())
{
	// ловим текущий url
	$current_url = mso_current_url(true);

	// получаем опции
	// в опциях: 	maint_url - адрес странцы "Закрыто на обслуживание",
	//		redirect_url - адрес страницы, на которую редиректится главная.

	$options = mso_get_option('maintenance', 'plugins', array());

	if (isset($options['maint_url']) && $options['maint_url'])                         	// Если есть url для обслуживания,
	{
		$maint_url = $options['maint_url'];

	 	if (!strpos($current_url, '/admin') && !strpos($current_url, '/login') && !strpos($current_url, '/logout') && !strpos($current_url, $maint_url))       // и если текущий url не нацелен на админку и на maint_url
				mso_redirect($maint_url, true);                                 // - редиректимся на maint_url			
		return $args;                                                                   // И завершаемся.
	}
	if (isset($options['redirect_url']) && $options['redirect_url'])                        // Если maint_url нет, а redirect_url есть
	{
		$redirect_url = $options['redirect_url'];

		if ($current_url == getinfo('siteurl'))                                         // Если текущий url ведёт на главную,
				mso_redirect($redirect_url, true);                              // то редиректимся на redirect_url
	}			
	return $args;
}

# функция выполняется при хуке admin_init
function maintenance_admin_init($args = array())
{
	if ( !mso_check_allow('maintenance_edit') )
	{
		return $args;
	}

	$this_plugin_url = 'maintenance'; // url и hook

	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки

	mso_admin_menu_add('plugins', $this_plugin_url, t('Обслуживание'));

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url)
	# связанную функцию именно она будет вызываться, когда
	# будет идти обращение по адресу http://сайт/admin/redirect
	mso_admin_url_hook ($this_plugin_url, 'maintenance_admin_page');

	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function maintenance_admin_page($args = array())
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('maintenance_edit') )
	{
		echo t('Доступ запрещен');
		return $args;
	}

	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Обслуживание') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Обслуживание') . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'maintenance/admin.php');
}

# end file
