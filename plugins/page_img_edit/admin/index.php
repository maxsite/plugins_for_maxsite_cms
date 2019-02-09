<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * MaxSite CMS
 */

$siteurl = getinfo('siteurl');
$admin_url =  getinfo('site_url') . 'admin/';
$plugins_url = getinfo('plugins_url');
$plugin_url = getinfo('siteurl') . 'admin/page_img_edit/';
$plugin_dir = getinfo('plugins_dir') . 'page_img_edit/';

?>
<div class="admin-h-menu">
	<a href="<?= $plugin_url ?>" class="select">Управление</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>settings" class="select">Орции</a>&nbsp;|&nbsp;
</div>
<?php
	$seg = mso_segment(3);
	if( $seg == 'settings' )
	{
		require($plugin_dir . 'admin/settings.php');
	}
	else require($plugin_dir . 'admin/admin.php');
?>