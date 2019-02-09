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

?>
<div class="admin-h-menu">
<?php
	# сделаем меню горизонтальное в текущей закладке

	// основной url этого плагина - жестко задается
	$plugin_url = getinfo('site_admin_url') . 'plugin_wm_control';
	$a  = mso_admin_link_segment_build($plugin_url, 'utilits', t('Утилиты', __FILE__), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'merchant', t('Настройка Мерчанта', __FILE__), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'edit', t('Настройка плагина', __FILE__), 'select');
	echo $a;
?>
</div>