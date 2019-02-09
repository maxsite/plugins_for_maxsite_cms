<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * Plugin Name: Special Text Box
 * Authors: Tux(http://6log.ru), minimus(http://blogovod.co.cc/)
 * Plugin URL: http://6log.ru/special-text-boxes
 */
 
$plugin_url = getinfo('siteurl') . 'admin/specialbox/';
?>
<div class="admin-h-menu">
	<a href="<?= $plugin_url ?>" class="select">Основные настройки</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>editor" class="select">Редактор</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>manage" class="select">Управление</a>
</div>
<?php

	$seg = mso_segment(3);
	
	if( empty($seg) )
	{
		require(getinfo('plugins_dir') . 'specialbox/options.php');
	}
	else if( $seg == 'editor' )
	{
		require(getinfo('plugins_dir') . 'specialbox/editor.php');
	}
	else if( $seg == 'manage' )
	{
		require(getinfo('plugins_dir') . 'specialbox/manage.php');
	}	
?>