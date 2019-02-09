<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function validator_autoload($args = array())
{
	mso_create_allow('validator_edit', 'Админ-доступ к настройкам плагина «Валидатор»');
	mso_hook_add( 'admin_init', 'validator_admin_init');
	
	$options = mso_get_option('plugin_validator', 'plugins', array());
	if ( !isset($options['content_at']) ) $options['content_at'] = false;
	if ( !isset($options['content_bt']) ) $options['content_bt'] = false;
	if ( !isset($options['noindex']) ) $options['noindex'] = false;
	
	if ($options['content_at'] == true) mso_remove_hook('content_auto_tag', 'mso_auto_tag');
	if ($options['content_bt'] == true) mso_remove_hook('content_balance_tags', 'mso_balance_tags');
	if ($options['noindex'] == true) mso_hook_add('content_out', 'validator_content');
}

function validator_uninstall($args = array())
{	
	mso_delete_option('plugin_validator', 'plugins');
	return $args;
}

function validator_admin_init($args = array()) 
{
	if ( !mso_check_allow('validator_edit') ) 
	{
		return $args;
	}
	
	$this_plugin_url = 'plugin_validator';
	mso_admin_menu_add('plugins', $this_plugin_url, 'Валидатор');
	mso_admin_url_hook($this_plugin_url, 'validator_admin_page');
	return $args;
}

function validator_admin_page($args = array()) 
{
	global $MSO;
	if ( !mso_check_allow('validator_admin_page') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	mso_hook_add_dinamic('mso_admin_header', ' return $args."Валидатор"; ' );
	mso_hook_add_dinamic('admin_title', ' return "Валидатор - ".$args; ' );
	require($MSO->config['plugins_dir'].'validator/admin.php');
}

function validator_content($text = '')
{
	$options = mso_get_option('plugin_validator', 'plugins', array());
	if ( !isset($options['noindex_class']) ) $options['noindex_class'] = '';
	
	if ($options['noindex_class'] == '')
		{ 
			$class = 'style="display:none"';
		} else {
			$class = 'class="'.$options['noindex_class'].'"';
		}
		
	if (!is_feed())
	{
		$text = str_replace('<noindex>', '<span '.$class.'><![CDATA[<noindex>]]></span>', $text);
		$text = str_replace('</noindex>', '<span '.$class.'><![CDATA[</noindex>]]></span>', $text);
	}

	return $text;
}

?>