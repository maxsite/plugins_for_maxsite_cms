<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

# функция автоподключения плагина
function editor_tinymce_autoload($args = array())
{
	mso_hook_add( 'editor_custom', 'editor_tinymce'); # хук на подключение своего редактора
	
	mso_hook_add('content_replace_chr10_br', 'editor_tinymce_br');	
	mso_hook_add('content_in', 'editor_tinymce_content_in');
	mso_hook_add('content_out','editor_tinymce_content_out');
	mso_hook_add('comments_content_custom','editor_tinymce_comments');
}

function editor_tinymce_content_in($t) {
mso_hook_add('content_auto_tag_custom', 'editor_tinymce_auto_tag');
mso_hook_add('content_balance_tags_custom','editor_tinymce_balance_tags');
return $t;
}

function editor_tinymce_content_out($t) {
mso_remove_hook('content_auto_tag_custom', '');
mso_remove_hook('content_balance_tags_custom','');
return $t;
}

function editor_tinymce_br($t){return $t;}

function editor_tinymce_auto_tag($t){return $t;}

function editor_tinymce_balance_tags($t){return $t;}

function editor_tinymce_comments($t) {
$t = mso_hook('content_auto_tag', $t);
$t = mso_hook('content_balance_tags', $t);
return $t;
}

function editor_tinymce($args = array()) 
{
	
	$editor_config['url'] = getinfo('plugins_url') . 'editor_tinymce/';
	$editor_config['dir'] = getinfo('plugins_dir') . 'editor_tinymce/';

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