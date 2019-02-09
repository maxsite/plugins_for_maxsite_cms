<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 *
 */

function dignity_storage_autoload()
{
	mso_hook_add('admin_init', 'dignity_storage_admin_init');
	mso_hook_add('custom_page_404', 'dignity_storage_custom_page_404');
}

function dignity_storage_activate($args = array())
{	
	mso_create_allow('dignity_storage_edit', t('Админ-доступ к', 'plugins') . ' ' . t('«Хранилище»', __FILE__));

	return $args;
}

function dignity_storage_uninstall($args = array())
{	
	mso_delete_option('plugin_dignity_storage', 'plugins');
	mso_remove_allow('dignity_storage_edit');

	return $args;
}

function dignity_storage_admin_init($args = array()) 
{
	if ( !mso_check_allow('dignity_storage_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'dignity_storage';
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Хранилище', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'dignity_storage_admin_page');
	
	return $args;
}

function dignity_storage_admin_page($args = array()) 
{
	if ( !mso_check_allow('dignity_storage_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Хранилище', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Хранилище', __FILE__) . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'dignity_storage/admin.php');
}

function dignity_storage_custom_page_404($args = false)
{
	$options = mso_get_option('plugin_dignity_storage', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'storage';
	
	if ( mso_segment(1)==$options['slug'] )
	{
	
		require(getinfo('plugins_dir') . 'dignity_storage/storage.php');
		return true;
	}

   return $args;
}

#end of file