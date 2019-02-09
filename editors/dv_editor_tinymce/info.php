<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$info = array(
	'name' => t('dv_editor_tinymce', __FILE__),
	'description' => t('ВИЗУАЛЬНЫЙ РЕДАКТОР на базе TinyMCE v3.5.6(2012-07-26)', __FILE__),
	'version' => '0.6',
	'author' => 'Damir V',
	'plugin_url' => 'http://dvint.ru/',
	'author_url' => 'http://dvint.ru/',
	'group' => 'template',
	'help' => getinfo('plugins_url') . 'dv_editor_tinymce/help.txt', # ссылка на help плагина

	# ссылка на свою страницу настроек (только если используется свой admin.php!)
	# 'options_url' => getinfo('site_admin_url') . 'pluginX_',
);

# end file