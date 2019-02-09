<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$info = array(
	'name' => t('Ecwid', __FILE__),
	'description' => t('Плагин позволяет организовать свой интернет магазин с помощью сервиса Ecwid.', __FILE__),
	'version' => '1.5',
	'author' => t('Александр Шиллинг', __FILE__),
	'plugin_url' => 'http://alexanderschilling.net/',
	'author_url' => 'http://alexanderschilling.net/',
	'options_url' => getinfo('site_admin_url') . 'ecwid',
	'group' => 'template'
);

# end file
