<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function tree_comments_autoload()
{
	mso_create_allow('tree_comments_edit', t('Админ-доступ к древовидным комментариям', __FILE__)); // права доступа
	mso_hook_add( 'admin_init', 'tree_comments_admin_init'); // хук на админку
	mso_hook_add( 'type-foreach-file', 'tree_comments'); // хук для своих foreach_file
	mso_hook_add( 'head', 'tree_comments_head'); // хук на шапку
	}

function tree_comments_head($args = array()){
	$url = getinfo('plugins_url') . 'tree_comments/';
	echo '<script type="text/javascript" src="'.$url.'js/jquery.tree-comments.js"></script>',NR;
	echo '<link rel="stylesheet" href="'.$url.'css/tree-comments.css" type="text/css" media="screen">',NR;
}

function tree_comments_uninstall($args = array())
{	
	mso_delete_option('tree_comments', 'plugins'); // удалим созданные опции плагина
	mso_remove_allow('tree_comments_edit'); // удалим созданные разрешения
	return $args;
}

function tree_comments_admin_init($args = array()) 
{
	if ( mso_check_allow('tree_comments_edit') ) {
	#$this_plugin_url = 'plugin_options/tree_comments'; // url и hook
	$this_plugin_url = 'tree_comments'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('Tree comments', __FILE__));
	mso_admin_url_hook ($this_plugin_url, 'tree_comments_admin_page');
	}
	return $args;
}

function tree_comments_admin_page($args = array()) 
{
	if ( !mso_check_allow('tree_comments_admin_page') ) 
	{
		echo 'Доступ запрещен';
		return $args;
	}
	
	mso_hook_add_dinamic('mso_admin_header', ' return $args."Tree Comments"; ' );
	mso_hook_add_dinamic('admin_title', ' return "Tree Comments - ".$args; ' );
	require(getinfo('plugins_dir') . 'tree_comments/admin.php');
}

function tree_comments ($tff = false) 
{   
	if ($tff == 'page-comments') return getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comments.php';
	#if ($tff == 'page-comments-do') return getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comments-do.php';
	if ($tff == 'page-comment-form') return getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comment-form.php';
	if ($tff == 'page-comment-form-do') return getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comment-form-do.php';

	return false;
	
}