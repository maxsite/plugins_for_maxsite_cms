<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

# функция автоподключения плагина
function picture_cleaner_autoload($args = array())
{
	mso_create_allow('picture_cleaner', t('Админ-доступ к Picture Cleaner', 'plugins'));
	mso_hook_add( 'admin_init', 'picture_cleaner_admin_init'); # хук на админку
}

# функция выполняется при указаном хуке admin_init
function picture_cleaner_admin_init($args = array()) 
{
	if ( !mso_check_allow('picture_cleaner_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'plugin_picture_cleaner'; // url и hook
	
	# добавляем свой пункт в меню админки
	# первый параметр - группа в меню
	# второй - это действие/адрес в url - http://сайт/admin/demo
	#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
	# Третий - название ссылки	
	
	mso_admin_menu_add('plugins', $this_plugin_url, 'Picture Cleaner');

	# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
	# связанную функцию именно она будет вызываться, когда 
	# будет идти обращение по адресу http://сайт/admin/_null
	mso_admin_url_hook ($this_plugin_url, 'picture_cleaner_admin_page');
	
	return $args;
}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function picture_cleaner_admin_page($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	if ( !mso_check_allow('picture_cleaner_admin_page') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "Picture Cleaner "; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "Picture Cleaner - " . $args; ' );

	require($MSO->config['plugins_dir'] . 'picture_cleaner/admin.php');
}


?>