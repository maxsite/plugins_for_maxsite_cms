<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */



$CI = & get_instance();


if($post = mso_check_post(array('install')))
{
	$CI->load->dbforge();
	$tab_prefix = $CI->db->dbprefix;
	$query = "ALTER TABLE `{$tab_prefix}cat2obj`  ADD `c2obj_order` INT(8) NOT NULL DEFAULT '0' AFTER `links_id`,  ADD `c2obj_status` ENUM('publish','draft') NOT NULL DEFAULT 'publish' AFTER `c2obj_order`,  ADD INDEX (`c2obj_order`)";
	$CI->db->query($query);
}

	
if(!$CI->db->field_exists('c2obj_order', 'cat2obj'))
{
	echo '<div class="error">Плагин не установлен</div>';
	
	echo '<form action="#" method="post"> ';
	echo '<input type="submit" name="install" value="Установить"/> ';
	echo '</form>';
}
else
{
	echo '<div><p>Плагин установлен. <a href="/admin/category_editor/">Прейти на главную</a></p></div>';
}
	
	
	
	
	
	
	
	
	
	

