<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('Блок подписки'),
	'description' => t('Блок для создания подписки в контенте'),
	'version' => '1.0',
	'author' => 'Redacktor',
	'plugin_url' => 'http://www.redacktor.ru/',
	'author_url' => 'http://www.redacktor.ru/',
	'group' => 'template',
	'help' => getinfo('plugins_url') . 'block/help.txt', 
	
	# ссылка на свою страницу настроек (только если используется свой admin.php!)
	# 'options_url' => getinfo('site_admin_url') . 'pluginX_', 
);

# end file