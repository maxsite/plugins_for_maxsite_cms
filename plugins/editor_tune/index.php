<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Editor tune plugin for MaxSite CMS
 * (c) http://www.codeigniter.me/
 */

function editor_tune_autoload($args = array())
{
	mso_hook_add('init', 'editor_tune_go');
}

function editor_tune_go($args = array()) 
{
	$options = mso_get_option('editor_tune', 'plugins', array() );
	if (isset($options['content_auto_tag']) && $options['content_auto_tag'] === '0')
		mso_remove_hook('content_auto_tag', 'mso_auto_tag');

	if (isset($options['content_balance_tags']) && $options['content_balance_tags'] === '0') 
		mso_remove_hook('content_balance_tags', 'mso_balance_tags');

	if (isset($options['content_replace_chr10_br']) && $options['content_replace_chr10_br'] === '0')
		mso_hook_add('content_replace_chr10_br', 'editor_nobr_go');

	return $args;
}

function editor_nobr_go($args = array())
{
	return $args;
}

function editor_tune_uninstall($args = array()) {	
	mso_delete_option('editor_tune', 'plugins');
	return $args;
}

function editor_tune_mso_options() 
{
	mso_admin_plugin_options('editor_tune', 'plugins', 
		array(
			'content_replace_chr10_br' => array(
							'type' => 'select', 
							'name' => 'Замена /n на &lt;br /&gt;',
							'description' => '',
							'values' => '1||вкл. #0||выкл.',
							'default' => '1'
						),
			'content_auto_tag' => array(
							'type' => 'select', 
							'name' => 'Управление content_auto_tag',
							'description' => '',
							'values' => '1||вкл. #0||выкл.',
							'default' => '1'
						), 
			'content_balance_tags' => array(
							'type' => 'select', 
							'name' => 'Управление content_balance_tags',
							'description' => '',
							'values' => '1||вкл. #0||выкл.',
							'default' => '1'
						)
			),
		'Настройки визуальных и невизуальных редакторов', // титул
		'Укажите необходимые опции.'   // инфо
	);
}
?>