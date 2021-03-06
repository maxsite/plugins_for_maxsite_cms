<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function editor_openwysiwyg_autoload($args = array())
{

	mso_hook_add( 'editor_custom', 'editor_openwysiwyg'); # хук на подключение своего редактора
}

function editor_openwysiwyg($args = array()) 
{
	
	$editor_config['url'] = getinfo('plugins_url') . 'editor_openwysiwyg/';
	$editor_config['dir'] = getinfo('plugins_dir') . 'editor_openwysiwyg/';

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
	
	require($editor_config['dir'] . 'editor.php');
}


?>