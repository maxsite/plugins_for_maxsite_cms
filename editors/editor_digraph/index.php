<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


function editor_digraph_autoload($args = array())
	{
	mso_hook_add('editor_custom', 'editor_digraph'); 
	mso_hook_add('admin_head', 'digraph_head');
	mso_hook_add('content_replace_chr10_br', 'digraph_br');	
	mso_hook_add('content_in', 'digraph_content_in');
	mso_hook_add('content_out','digraph_content_out');
	mso_hook_add('comments_content_custom','digraph_comments');
	}

function digraph_content_in($t)
	{
	mso_hook_add('content_auto_tag_custom', 'digraph_auto_tag');
	mso_hook_add('content_balance_tags_custom','digraph_balance_tags');
	return $t;
	}

function digraph_content_out($t)
	{
	mso_remove_hook('content_auto_tag_custom', '');
	mso_remove_hook('content_balance_tags_custom','');
	return $t;
	}

function digraph_br($t){return $t;}

function digraph_auto_tag($t){return $t;}

function digraph_balance_tags($t){return $t;}

function digraph_comments($t)
	{
	$t = mso_hook('content_auto_tag', $t);
	$t = mso_hook('content_balance_tags', $t);
	return $t;
	}

function digraph_head($args = array()) 
	{

	if ((mso_segment(2) == 'page_edit') || (mso_segment(2) == 'page_new'))
		{
		$url = getinfo('plugins_url') . 'editor_digraph/';
		echo '
	<!-- digraph css -->	
	<link type="text/css" rel="stylesheet" href="'.$url.'css/digraph.css" media="screen" />
';
		// подключение плагинов
		$path = getinfo('plugins_dir')  . 'editor_digraph/plugins/';

		chdir($path);

		foreach (glob("*.css") as $filename)
			{
			echo '	<link rel="stylesheet" href="' . $url . 'plugins/' . $filename . '" type="text/css" media="screen" />
';
			}
		echo '	<!-- end digraph css -->

';
		}
	}

function editor_digraph($args = array()) 
	{
	$editor_config['url'] = getinfo('plugins_url') . 'editor_digraph/';
	$editor_config['dir'] = getinfo('plugins_dir') . 'editor_digraph/';

	if (isset($args['content'])) $editor_config['content'] = $args['content'];
	else $editor_config['content'] = '';
		
	if (isset($args['do'])) $editor_config['do'] = $args['do'];
		else $editor_config['do'] = '';
		
	if (isset($args['posle'])) $editor_config['posle'] = $args['posle'];
		else $editor_config['posle'] = '';	
		
	if (isset($args['action'])) $editor_config['action'] = ' action="' . $args['action'] . '"';
		else $editor_config['action'] = '';
	
	if (isset($args['height']))
		$editor_config['height'] = (int) $args['height'];
	else {
		$editor_config['height'] = (int) mso_get_option('editor_height', 'general', 400);

		if ($editor_config['height'] < 100)
			$editor_config['height'] = 400;
		}

	require($editor_config['dir'] . 'digraph.php');
	}