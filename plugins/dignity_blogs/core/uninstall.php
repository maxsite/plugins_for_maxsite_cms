<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_blogs (github)
 * License GNU GPL 2+
 */


mso_delete_option('plugin_dignity_blogs', 'plugins');
mso_remove_allow('dignity_blogs_edit');
	
// получааем доступ к CI
$CI = &get_instance();
	
$CI->load->dbforge();
	
// удаляем таблицы
$CI->dbforge->drop_table('dignity_blogs');
$CI->dbforge->drop_table('dignity_blogs_comments');
$CI->dbforge->drop_table('dignity_blogs_category');
$CI->dbforge->drop_table('dignity_blogs_tags_entrys');
$CI->dbforge->drop_table('dignity_blogs_tags');
	
// удаляем настройки виджета
mso_delete_option_mask('dignity_blogs_category_widget_', 'plugins');
mso_delete_option_mask('dignity_blogs_new_widget_', 'plugins');

// сбрасываем кеш
mso_flush_cache();

#end of file
