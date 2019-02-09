<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * MaxSite CMS
 */
 
// $plugin_dir = getinfo('plugins_dir') . 'taggallery/'; 


$siteurl = getinfo('siteurl');
$admin_url =  getinfo('site_url') . 'admin/';
$plugins_url = getinfo('plugins_url');
$plugin_url = getinfo('siteurl') . 'admin/profile/';
$plugin_dir = getinfo('plugins_dir') . 'profile/';
$uploads_dir = getinfo('uploads_dir');
$uploads_url = getinfo('uploads_url');
?>
<div class="admin-h-menu">
	<a href="<?= $plugin_url ?>" class="select">Опции</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>auth" class="select">Настройки ulogin</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>comments" class="select">Элемент comments</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>auth_stat" class="select">Статистика аккаунтов</a>&nbsp;|&nbsp;
	
</div>
 
<?php


	$seg = mso_segment(3);
	
	if( $seg == 'auth' )
	{
		require(getinfo('plugins_dir') . 'profile/admin/auth.php');
	}
	elseif( $seg == 'comments' )
	{
		require(getinfo('plugins_dir') . 'profile/admin/element_comments.php');
	}		
	elseif( $seg == 'auth_stat' )
	{
		require(getinfo('plugins_dir') . 'profile/admin/auth_stat.php');
	}		
	elseif( !$seg )
	{
		require(getinfo('plugins_dir') . 'profile/admin/settings.php');
	}	

?>