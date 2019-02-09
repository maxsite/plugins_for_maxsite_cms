<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * MaxSite CMS
 */
// $plugin_dir = getinfo('plugins_dir') . 'taggallery/'; 
 
 //опции админки
$options_admin = mso_get_option('taggallery_admin' , 'plugins', array());
require($plugin_dir . 'admin/admin_options_default.php');

//опции плагина
$options = mso_get_option('taggallery' , 'plugins', array());
require($plugin_dir . 'options_default.php');


// адрес опций шаблона
$template_setting_url = getinfo('siteurl') . 'admin/plugin_options/taggallery/templates/' . $options['template']; 



require ($plugin_dir . 'functions/access_db.php');
require ($plugin_dir . 'functions/modify_db.php');
require ($plugin_dir . 'functions/functions.php');
require ($plugin_dir . 'admin/functions/functions.php');

	$date_format = array(	'format' => 'j F Y', // 'd/m/Y H:i:s'
									'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
									'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')); 

$siteurl = getinfo('siteurl');
$admin_url =  getinfo('site_url') . 'admin/';
$plugins_url = getinfo('plugins_url');
$plugin_url = getinfo('siteurl') . 'admin/taggallery/';
$plugin_dir = getinfo('plugins_dir') . 'taggallery/';
$uploads_dir = getinfo('uploads_dir');
$uploads_url = getinfo('uploads_url');
?>
<div class="admin-h-menu">
	<a href="<?= $plugin_url ?>settings" class="select">Настройки</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>" class="select">Управление Файлами</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>gallerys" class="select">Управление галереями</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>pictures" class="select">Управление картинками</a>&nbsp;|&nbsp;	
	<a href="<?= $plugin_url ?>albums" class="select">Управление альбомами</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>import" class="select">Импорт</a>&nbsp;|&nbsp;
	<a href="<?= $plugin_url ?>watermark" class="select">Водная метка</a>&nbsp;|&nbsp;	
	<a href="<?= $plugin_url ?>collage" class="select">Коллаж</a>&nbsp;|&nbsp;		
	<a href="<?= $template_setting_url ?>" class="select" target = "blank">Опции шаблона</a>&nbsp;|&nbsp;
	
</div>
<p class="info"><?= t('TagGallery. Плагин для формирования галлерей по меткам, присвоенным картинкам', 'plugins') ?></p>
 
<?php


	$seg = mso_segment(3);
	
	if( $seg == 'gallerys' )
	{
		require(getinfo('plugins_dir') . 'taggallery/admin/edit_gallerys.php');
	}
	elseif( $seg == 'albums' )
	{
		require(getinfo('plugins_dir') . 'taggallery/admin/edit_albums.php');
	}	
	elseif( $seg == 'picture' )
	{
		require(getinfo('plugins_dir') . 'taggallery/admin/edit_picture.php');
	}		
	elseif( $seg == 'pictures' )
	{
		require(getinfo('plugins_dir') . 'taggallery/admin/pictures.php');
	}		
	elseif( $seg == 'settings' )
	{
		require(getinfo('plugins_dir') . 'taggallery/admin/settings.php');
	}		
	elseif( $seg == 'import' )
	{
		require(getinfo('plugins_dir') . 'taggallery/admin/import.php');
	}		
	elseif( $seg == 'watermark' )
	{
		require(getinfo('plugins_dir') . 'taggallery/admin/watermark.php');
	}	
	elseif( $seg == 'collage' )
	{
		require(getinfo('plugins_dir') . 'taggallery/admin/collage.php');
	}		
	else require(getinfo('plugins_dir') . 'taggallery/admin/edit_files.php');
?>