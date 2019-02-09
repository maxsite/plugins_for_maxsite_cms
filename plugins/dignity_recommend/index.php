<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 *
 * Alexander Schilling
 * (c) http://alexanderschilling.net
 *
 */

function dignity_recommend_autoload()
{
	mso_hook_add('admin_init', 'dignity_recommend_admin_init');
	mso_hook_add('custom_page_404', 'dignity_recommend_custom_page_404');
}

function dignity_recommend_activate($args = array())
{	
	mso_create_allow('dignity_recommend_edit', t('Админ-доступ к', 'plugins') . ' ' . t('«Рекомендовать»', __FILE__)); 

	return $args;
}

function dignity_recommend_deactivate($args = array())
{	
	mso_delete_option('plugin_dignity_recommend', 'plugins');
	return $args;
}

function dignity_recommend_uninstall($args = array())
{	
	mso_delete_option('plugin_dignity_recommend', 'plugins');
	mso_remove_allow('dignity_recommend_edit');

	return $args;
}

function dignity_recommend_admin_init($args = array()) 
{
	if ( !mso_check_allow('dignity_recommend_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'dignity_recommend';
	
	mso_admin_menu_add('plugins', $this_plugin_url, t('Рекомендовать', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'dignity_recommend_admin_page');
	
	return $args;
}

function dignity_recommend_admin_page($args = array()) 
{
	
	if ( !mso_check_allow('dignity_recommend_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Рекомендовать', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Рекомендовать', __FILE__) . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'dignity_recommend/admin.php');
}

function dignity_recommend_custom_page_404($args = false)
{
   $options = mso_get_option('plugin_dignity_recommend', 'plugins', array());
   if ( !isset($options['slug']) ) $options['slug'] = 'recommend';
   
   // Если слуг из опции (если нет берём по умолчанию)
   if ( mso_segment(1)==$options['slug'] )
   {
	// загружаем recommend.php...
	require( getinfo('plugins_dir') . 'dignity_recommend/recommend.php' ) ;

      return true;
   }

   return $args;
}

?>