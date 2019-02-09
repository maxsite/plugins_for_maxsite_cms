<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Плагин для MaxSite CMS
 * отображение посетителей блога на странице админа
 * (c) http://kerzoll.org.ua/
 */
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
	}

	$CI = & get_instance();

	$options_key = 'plugin_view_visit';
	$options = mso_get_option($options_key, 'plugins', array());

#подключаем функции, которые использует этот плагин
require(getinfo('plugins_dir') . 'view_visit/functions.php');
?>
<div class="admin-h-menu">
<?php
	# сделаем меню горизонтальное в текущей закладке

	// основной url этого плагина - жестко задается
	$plugin_url = getinfo('site_admin_url') . 'plugin_view_visit';
	$a  = mso_admin_link_segment_build($plugin_url, 'list', t('Просмотр посещений', __FILE__), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'hosts', t('Просмотр хостов', __FILE__), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'sort', t('Переходы', __FILE__), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'stat', t('Статистика', __FILE__), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'arhive', t('Архивация', __FILE__), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'edit', t('Настройка плагина', __FILE__), 'select');
	echo $a;
?>
</div>