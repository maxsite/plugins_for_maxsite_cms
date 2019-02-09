<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('dv_elfinder'),
	'description' => t('elFinder 2.0 (rc1) <a href = "http://elfinder.org/" target="_blank">http://elfinder.org/</a>'),
	'version' => '0.1',
	'author' => 'DamirV',
	'plugin_url' => 'http://dvint.ru/',
	'author_url' => 'http://dvint.ru/',
	'group' => 'template',
	'help' => getinfo('plugins_url') . 'dv_elfinder/help.txt', # ссылка на help плагина 
	
	# ссылка на свою страницу настроек (только если используется свой admin.php!)
	# 'options_url' => getinfo('site_admin_url') . 'pluginX_', 
);

# end file