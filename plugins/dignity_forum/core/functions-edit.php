<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// добавить новую тему
function add_new_topic($ins_data = array())
{
	// получаем доступ к CI
	$CI = & get_instance();

	// в зависимости от результата
	$res = ($CI->db->insert('dignity_forum_topic', $ins_data)) ? '1' : '0';

	return $res;

	// сбрасываем кеш
	mso_flush_cache();
}

// измениь тему
function edit_topic($ins_data = array(), $id = '')
{
	// получаем доступ к CI
	$CI = & get_instance();

	// добавляем данные в базу
	$CI->db->where('dignity_forum_topic_id', $id);
	
	$res = ($CI->db->update('dignity_forum_topic', $ins_data)) ? '1' : '0';

	return $res;

	// сбрасываем кеш
	mso_flush_cache();
}

// добавляем новый ответ
function add_new_reply($ins_data = array())
{
	// получаем доступ к CI
	$CI = & get_instance();

	// результат...
	$res = ($CI->db->insert('dignity_forum_reply', $ins_data)) ? '1' : '0';

	return $res;

	// сбрасываем кеш
	mso_flush_cache();
}

// изменить новый ответ
function edit_new_reply($ins_data = array(), $id)
{
	// получаем доступ к CI
	$CI = & get_instance();

	// добавляем данные в базу
	$CI->db->where('dignity_forum_reply_id', $id);
	
	$res = ($CI->db->update('dignity_forum_reply', $ins_data )) ? '1' : '0';

	return $res;

	// сбрасываем кеш
	mso_flush_cache();

}

function topic_time_update($ins_data = array(), $id)
{
	// получаем доступ к CI
	$CI = & get_instance();

	$CI->db->where('dignity_forum_topic_id', $id);
	$CI->db->update('dignity_forum_topic', $ins_data);

	// сбрасываем кеш
	mso_flush_cache();
}

#end of file
