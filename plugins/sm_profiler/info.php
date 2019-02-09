<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('sm_profiler'),
	'description' => t('Плагин профайлер (sql-запросы и т.д.)'),
	'version' => '1.0',
	'author' => 'searchingman',
	'plugin_url' => 'http://wpcodex.ru/',
	'author_url' => 'http://wpcodex.ru/',
	'group' => 'template',
	'help' => getinfo('plugins_url') . 'sm_profiler/help.txt', # ссылка на help плагина 
	
	# ссылка на свою страницу настроек (только если используется свой admin.php!)
	# 'options_url' => getinfo('site_admin_url') . 'pluginX_', 
);

# end file