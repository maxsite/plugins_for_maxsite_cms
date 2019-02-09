<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * MaxSite CMS
 */
// $plugin_dir = getinfo('plugins_dir') . 'dialog/'; 
 


 $options = mso_get_option('dialog', 'plugins', array());
 $plugin_dir = getinfo('plugins_dir') . 'dialog/';
 require($plugin_dir . 'plugin_options_default.php');


$siteurl = getinfo('siteurl');
$admin_url =  getinfo('site_url') . 'admin/';
$plugins_url = getinfo('plugins_url');
$plugin_url = getinfo('siteurl') . 'admin/dialog/';
$plugin_dir = getinfo('plugins_dir') . 'dialog/';

?>
<div class="admin-h-menu">
	<a href="<?= $plugin_url ?>" class="select">Настройки</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>categorys" class="select">Управление категориями</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>messages" class="select">Задание заголовков</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>settings_profile" class="select">Slug's профиля</a>&nbsp;|&nbsp;	
	<a href="<?= $plugin_url ?>profiles" class="select">Элементы профиля</a>&nbsp;|&nbsp;	
	<a href="<?= $plugin_url ?>roles" class="select">Управление ролями</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>approved" class="select">Непроверенные</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>baned" class="select">Забаненные</a>&nbsp;|&nbsp;	
	<a href="<?= $plugin_url ?>delete" class="select">Удаление</a>&nbsp;|&nbsp;	
	
</div>
<p class="info"><?= t('Настройки плагина форума', 'plugins') ?></p>
 
<?php


	$seg = mso_segment(3);
	
	if( $seg == 'categorys' )
	{
		require(getinfo('plugins_dir') . 'dialog/admin/categorys.php');
	}
	elseif( $seg == 'roles' )
	{
		require(getinfo('plugins_dir') . 'dialog/admin/roles.php');
	}	
	elseif( $seg == 'messages' )
	{
		require(getinfo('plugins_dir') . 'dialog/admin/messages.php');
	}	
	elseif( $seg == 'profiles' )
	{
		require(getinfo('plugins_dir') . 'dialog/admin/profiles.php');
	}		
	elseif( $seg == 'delete' )
	{
		require(getinfo('plugins_dir') . 'dialog/admin/delete.php');
	}		
	elseif( $seg == 'settings_profile' )
	{
		require(getinfo('plugins_dir') . 'dialog/admin/settings_profile.php');
	}		
	elseif( $seg == 'baned' )
	{
		require(getinfo('plugins_dir') . 'dialog/admin/baned.php');
	}	
	elseif( $seg == 'approved' )
	{
		require(getinfo('plugins_dir') . 'dialog/admin/approved.php');
	}
	else require(getinfo('plugins_dir') . 'dialog/admin/settings.php');
?>