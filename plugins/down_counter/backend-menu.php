<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin «Down Counter» for MaxSite CMS
 * 
 * Author: (c) Илья Земсков (ака Профессор)
 * Plugin URL: http://maxhub.ru/page/plugin-down-counter
 */
?>
<div class="admin-h-menu">
<?php
	# сделаем меню горизонтальное в текущей закладке
	$menu  = '<a class="backend" href="'.getinfo('site_admin_url').basename(dirname(__FILE__)).'">Статистика</a>';
	$menu .= '<a class="edit" href="'.getinfo('site_admin_url').basename(dirname(__FILE__)).'/edit">Правка данных</a>';
	$menu .= '<a class="options" href="'.getinfo('site_admin_url').'plugin_options/'.basename(dirname(__FILE__)).'" class="select">Настройки</a>';
	$menu .= '<a class="help" href="http://maxhub.ru/page/plugin-down-counter" target="_blank">Помощь</a>';
	echo $menu;
?>
</div>
