<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function youtube_embed_autoload()
{
	mso_hook_add( 'admin_init', 'youtube_embed_admin_init'); // хук на админку
	mso_hook_add( 'content', 'youtube_embed_content');
	}

function youtube_embed_uninstall($args = array())
{	
	mso_delete_option('youtube_embed', 'plugins'); // удалим созданные опции плагина
	mso_remove_allow('youtube_embed_edit'); // удалим созданные разрешения
	return $args;
}

function youtube_embed_admin_init($args = array()) 
{
	if ( mso_check_allow('youtube_embed_edit') ) {
	$this_plugin_url = 'plugin_options/youtube_embed'; // url и hook
	mso_admin_menu_add('plugins', $this_plugin_url, t('Youtube Embed', __FILE__));
	}
	return $args;
}

function youtube_embed_mso_options() 
{
	mso_admin_plugin_options('youtube_embed', 'plugins', 
		array(
			'ye_width' => array(
						'type' => 'text', 
						'name' => 'Ширина плеера:', 
						'description' => 'указать в пикселях или процентах',
						'default' => '520'
					),
			'ye_height' => array(
						'type' => 'text', 
						'name' => 'Высота плеера:', 
						'description' => 'указать в пикселях или процентах',
						'default' => '315'
					),	
			'ye_center' => array(
						'type' => 'checkbox', 
						'name' => 'Центровать плеер?', 
						'description' => 'Если нету, то нужно добавить в стили p.center {text-align: center;}',
						'default' => '1'
					),
			),
		'Настройки Youtube Embed',
		'Выберите необходимые опции.'
	);
}


function youtube_embed_content_callback($matches)
{	
	
	$options = mso_get_option('youtube_embed', 'plugins', array());
	if (!isset($options['ye_width'])) $options['ye_width'] = '520';
	if (!isset($options['ye_height'])) $options['ye_height'] = '315';
	if (!isset($options['ye_center'])) $options['ye_center'] = 'date';
	
	$u = $matches[1];
	#$chk = strpos($u, "/embed/");
	#if ($chk === false) { 
		$n = strpos($u,"v=")+2;
		$url = substr($u,$n,11);
	#}
	#else {
	#	$url = substr($u,6,11);
	#}
	
	if ($options['ye_center']) $out = '<p class="center">'; else $out = '<p>';
	$out .= '<iframe width="'. $options['ye_width'] .'" height="'. $options['ye_height'] .'" src="http://www.youtube.com/embed/' . $url . '?wmode=Opaque&amp;wmode=transparent" frameborder="0" allowfullscreen></iframe></p>';
		
	return $out;
}

function youtube_embed_content($text = '')
{
	$pattern = '|\[youtube=http://www.youtube.com/watch(.*?)\]|ui';
	
	$text = preg_replace_callback($pattern, 'youtube_embed_content_callback' , $text);

	return $text;
}