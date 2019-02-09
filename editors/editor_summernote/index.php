<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function editor_summernote_autoload($args = array())
{
	mso_hook_add( 'editor_custom', 'editor_summernote'); # хук на подключение своего редактора
}

function editor_summernote_content_in($t) {
mso_hook_add('content_auto_tag_custom', 'editor_summernote_auto_tag');
mso_hook_add('content_balance_tags_custom','editor_summernote_balance_tags');
return $t;
}

function editor_summernote_content_out($t) {
mso_remove_hook('content_auto_tag_custom', '');
mso_remove_hook('content_balance_tags_custom','');
return $t;
}

function editor_summernote_br($t){return $t;}

function editor_summernote_auto_tag($t){return $t;}

function editor_summernote_balance_tags($t){return $t;}

function editor_summernote_comments($t) {
$t = mso_hook('content_auto_tag', $t);
$t = mso_hook('content_balance_tags', $t);
return $t;
}


function editor_summernote($args = array())
{
	
	$editor_config['url'] = getinfo('plugins_url') . 'editor_summernote/';
	$editor_config['dir'] = getinfo('plugins_dir') . 'editor_summernote/';


	if (isset($args['content'])) $editor_config['content'] = $args['content'];
	else $editor_config['content'] = '';
		
	if (isset($args['do'])) $editor_config['do'] = $args['do'];
		else $editor_config['do'] = '';
		
	if (isset($args['do_script'])) $editor_config['do_script'] = $args['do_script'];
		else $editor_config['do_script'] = '';
	
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