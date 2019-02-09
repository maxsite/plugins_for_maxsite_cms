<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// удаляем опции
mso_delete_option('plugin_dignity_forum', 'plugins');
mso_remove_allow('dignity_forum_edit');

// получаем доступ к CI и удаляем созданые таблицы
$CI = &get_instance();
$CI->load->dbforge();
$CI->dbforge->drop_table('dignity_forum_topic');
$CI->dbforge->drop_table('dignity_forum_category');
$CI->dbforge->drop_table('dignity_forum_reply');
	
// сбрасываем кеш
mso_flush_cache();
	
// удаляем настройки виджета
mso_delete_option_mask('dignity_forum_widget_', 'plugins');

#end of file
