<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
# функция автоподключения плагина
function dv_elfinder_autoload()
{
mso_create_allow('dv_elfinder',  t('Админ-доступ к менеджеру файлов (плагин)', 'admin'));
mso_hook_add( 'admin_init', 'dv_elfinder_admin_init');
mso_hook_add( 'admin_head', 'admin_dv_elfinder_head');
}

# функция выполняется при указаном хуке admin_init
function dv_elfinder_admin_init($args = array()) 
{	

	if ( !mso_check_allow('dv_elfinder') ) 
	{
		return $args;
	}

	$this_plugin_url = 'dv_elfinder'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, '' . t('elFinder', 'admin') . '');
	mso_admin_url_hook ($this_plugin_url, 'dv_elfinder_admin');
	return $args;
}
function admin_dv_elfinder_head($arg = array()){	
		if (mso_segment(2) !== 'dv_elfinder') {
		return $arg;   
		}
		else {
		require_once ('elfinder/elfinder.php');
		exit;
		}
}
# end file