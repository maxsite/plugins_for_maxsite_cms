<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin «Youtube_Previewer» for MaxSite CMS
 *
 * Author: (c) Илья Земсков http://vizr.ru/
 */

?>
<div class="admin-h-menu">
<?php
	# сделаем меню горизонтальное в текущей закладке
	$menu = '';
	if( mso_check_allow(basename(dirname(__FILE__)).'_edit') )
	{
		$menu .= '<a class="forms'.( mso_segment(2) == 'youtube_previewer' && mso_segment(3) == '' ? ' select' : '' ).'" href="'.getinfo('site_admin_url').basename(dirname(__FILE__)).'">Панель поиска</a> ';
	}
	if( mso_check_allow(basename(dirname(__FILE__)).'_options') )
	{
		$menu .= '<a class="options'.( mso_segment(2) == 'plugin_options' && mso_segment(3) == 'youtube_previewer' ? ' select' : '' ).'" href="'.getinfo('site_admin_url').'plugin_options/'.basename(dirname(__FILE__)).'" class="select">Настройки</a>';
	}
	echo $menu;
?>
</div>
