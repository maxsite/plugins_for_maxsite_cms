<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * admin tag plugin
 * Author: (c) Tux
 * Plugin URL: http://6log.ru/admin_tag 
 */

mso_cur_dir_lang(__FILE__);

# функция автоподключения плагина
function admin_meta_autoload($args = array())
{	
	mso_hook_add( 'admin_init', 'admin_meta_init');
	mso_create_allow( 'admin_meta', t('Админ-доступ к редактированию тегов') );
}


# функция выполняется при указаном хуке admin_init
function admin_meta_init($args = array()) 
{
	if ( mso_check_allow('admin_meta') ) 
	{
		$this_plugin_url = 'meta'; // url и hook

		# добавляем свой пункт в меню админки
		# первый параметр - группа в меню
		# второй - это действие/адрес в url - http://сайт/admin/demo
		#			можно использовать добавочный, например demo/edit = http://сайт/admin/demo/edit
		# Третий - название ссылки	
		# четвертый номер по порядку
		
		mso_admin_menu_add('options', $this_plugin_url, t('Теги/Метки'), 6);

		# прописываем для указаного admin_url_ + $this_plugin_url - (он будет в url) 
		# связанную функцию именно она будет вызываться, когда 
		# будет идти обращение по адресу http://сайт/admin/_null
		mso_admin_url_hook ($this_plugin_url, 'admin_meta');
	}
	return $args;
}


# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_meta($args = array()) 
{
	# выносим админские функции отдельно в файл
	global $MSO;
	if ( !mso_check_allow('admin_meta') ) 
	{
		echo t('Доступ запрещен');
		return $args;
	}
	
	// хук на заголовок в админке
	// функцию создаем динамически
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . t("Настройка Тегов"); ' );
	mso_hook_add_dinamic( 'admin_title', ' return t("Настройка Тегов") . " - " . $args; ' );

	require($MSO->config['admin_plugins_dir'] . 'admin_meta/admin.php');
}

?>