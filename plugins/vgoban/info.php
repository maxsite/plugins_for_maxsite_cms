<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

$info = array(
	'name' => t('Гобан GOSWF', __FILE__),
	'description' => t('Позволяет выводить на странице гобан GOSWF, подробнее в readme.txt', __FILE__),
	'version' => '1.0',
	'author' => 'Derian',
	'plugin_url' => 'http://derian.isgreat.org/',
	'author_url' => 'http://derian.isgreat.org/',
	'group' => 'template',
	'help' => getinfo('plugins_url') . 'flash_goban/readme.txt', # ссылка на help плагина

	# ссылка на свою страницу настроек (только если используется свой admin.php!)
	# 'options_url' => getinfo('site_admin_url') . 'pluginX_',
);

# end file