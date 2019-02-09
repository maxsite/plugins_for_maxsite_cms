<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


# функция автоподключения плагина
function admin_struct_autoload($args = array())
{	
	mso_create_allow('admin_struct',  t('Админ-доступ к менеджеру файлов (плагин)', 'admin'));
	mso_hook_add( 'admin_init', 'admin_struct_admin_init');
	mso_hook_add( 'admin_head', 'admin_struct_admin_head');
}

# функция выполняется при указаном хуке admin_init
function admin_struct_admin_init($args = array()) 
{	
	if ( !mso_check_allow('admin_struct') ) 
	{
		return $args;
	}

	$this_plugin_url = 'admin_struct'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, '' . t('Дерево сайта', 'admin') . '');
	mso_admin_url_hook ($this_plugin_url, 'admin_struct_admin');
	
	return $args;
}

function admin_struct_admin_head($args = array()) 
{
echo '<link href="' . getinfo('plugins_url') . 'admin_struct/tree_style.css" rel="stylesheet" type="text/css" media="screen">';
echo ' <script type="text/javascript" src="'.getinfo('plugins_url').'admin_struct/lib/jquery.treeview.js"></script>';	
echo '<link href="'.getinfo('plugins_url').'admin_struct/lib/jquery.treeview.css" rel="stylesheet" type="text/css" media="screen">';	
echo ' <script type="text/javascript" src="'.getinfo('plugins_url').'admin_struct/lib/jquery.cookie.js"></script>';
echo " <script type=\"text/javascript\">
var path_ajax = '" . getinfo('ajax') . base64_encode('plugins/admin_struct/admin_struct-ajax.php') . "';
</script>";
echo '<script type="text/javascript" src="'.getinfo('plugins_url').'admin_struct/lib/my_scr.js"></script>';



	return $args;
}


# функция вызываемая при хуке, указанном в mso_admin_url_hook
function admin_struct_admin($args = array()) 
{
	if ( !mso_check_allow('admin_struct') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	# выносим админские функции отдельно в файл
	global $MSO;
	
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Загрузки. Файлы. Галереи', 'admin') . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Загрузки. Файлы. Галереи', 'admin') . ' - " . $args; ' );
	
	require($MSO->config['plugins_dir'] . 'admin_struct/admin.php');
}
# end file