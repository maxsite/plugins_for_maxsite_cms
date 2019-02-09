<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function editor_wysibb_autoload($args = array())
{
	mso_hook_add( 'editor_custom', 'editor_wysibb'); # хук на подключение своего редактора
}

# функция выполняется при деинсталяции плагина
function editor_wysibb_uninstall($args = array())
{	
	mso_delete_option('editor_wysibb', 'plugins' ); // удалим созданные опции
	return $args;
}

function editor_wysibb($args = array()) 
{
	
	$options = mso_get_option('editor_wysibb', 'plugins', array() ); // получаем опции
	
	$editor_config['url'] = getinfo('plugins_url') . 'editor_wysibb/';
	$editor_config['dir'] = getinfo('plugins_dir') . 'editor_wysibb/';

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

	# Приведение строк с <br> в первозданный вид
	$editor_config['content'] = preg_replace('"&lt;br\s?/?&gt;"i',"\n",$editor_config['content']);
	$editor_config['content'] = preg_replace('"&lt;br&gt;"i',"\n",$editor_config['content']);


	
	require($editor_config['dir'] . 'editor-bb.php');
}



# end file