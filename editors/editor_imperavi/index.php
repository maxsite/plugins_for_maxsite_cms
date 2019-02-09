<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function editor_imperavi_autoload($args = array())
{
    	mso_create_allow('editor_imperavi', t('Админ-доступ к настройкам Редактора Imperavi', __FILE__));
	mso_hook_add( 'editor_custom', 'editor_imperavi'); # хук на подключение своего редактора
        mso_hook_add('content_replace_chr10_br', 'editor_imperavi_br');	
        mso_hook_add( 'admin_init', 'editor_imperavi_admin_init');
        mso_hook_add('custom_page_404', 'editor_imperavi_custom_page_404');
}

function editor_imperavi_br($t){return $t;}


function editor_imperavi_admin_init($args = array())
{
	if ( mso_check_allow('editor_imperavi_edit') )
	{
		mso_admin_menu_add('plugins', 'editor_imperavi', t('Редактор Imperavi', __FILE__));
		mso_admin_url_hook ('editor_imperavi', 'editor_imperavi_admin_page');
	}
	return $args;
}

function editor_imperavi_admin_page($args = array())
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('editor_imperavi') )
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('imperavi', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('imperavi', __FILE__) . ' - " . $args; ' );
	require(getinfo('plugins_dir') . 'editor_imperavi/admin.php');
}

function editor_imperavi($args = array())
{
	$options = mso_get_option('editor_imperavi', 'plugins', array());
	$editor_config['url'] = getinfo('plugins_url') . 'editor_imperavi/';
	$editor_config['dir'] = getinfo('plugins_dir') . 'editor_imperavi/';


	if (isset($args['content'])) $editor_config['content'] = $args['content'];
	else $editor_config['content'] = '';
		
	if (isset($args['do'])) $editor_config['do'] = $args['do'];
		else $editor_config['do'] = '';
		
	if (isset($args['posle'])) $editor_config['posle'] = $args['posle'];
		else $editor_config['posle'] = '';	
		
	if (isset($args['action'])) $editor_config['action'] = ' action="' . $args['action'] . '"';
		else $editor_config['action'] = '';
	
	if (isset($args['height'])) $editor_config['height'] = (int) $args['height'];
	else 
	{
		$editor_config['height'] = (int) mso_get_option('editor_height', 'general', 400);
		if ($editor_config['height'] < 100) $editor_config['height'] = 400;
	}
        
        if(!isset($options['init']) OR $options['init']=='') $options['init']='$(document).ready(function(){$(\'#f_content\').redactor({imageUpload: \'/editor_imperavi_uploader\', imageGetJson: false });});';

	require($editor_config['dir'] . 'editor.php');
}

function editor_imperavi_custom_page_404($args = false)
{
	if ( mso_segment(1)== 'editor_imperavi_uploader' ) 
	{
		require( getinfo('plugins_dir') . 'editor_imperavi/uploader.php' ); // подключили свой файл вывода
		return true; // выходим с true
	}

	return $args;
}


?>