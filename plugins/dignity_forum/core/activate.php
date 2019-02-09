<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

mso_create_allow('dignity_forum_edit', t('Админ-доступ к', 'plugins') . ' ' . t('«Форум»', __FILE__));

// поулчаем доступ к CI
$CI = & get_instance();	

$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate .  ' ENGINE=MyISAM;';	

// создаём табилицу тем
if ( !$CI->db->table_exists('dignity_forum_topic'))
{
	
	$sql = "
	CREATE TABLE " . $CI->db->dbprefix . "dignity_forum_topic (
	dignity_forum_topic_id bigint(20) NOT NULL auto_increment,
	dignity_forum_topic_subject varchar(255) NOT NULL default '',
	dignity_forum_topic_text longtext NOT NULL default '',
	dignity_forum_topic_category int(10) NOT NULL default '0',
	dignity_forum_topic_datecreate datetime NOT NULL default '0000-00-00 00:00:00',
	dignity_forum_topic_dateupdate datetime NOT NULL default '0000-00-00 00:00:00',
	dignity_forum_topic_approved varchar(1) NOT NULL default '0',
	dignity_forum_topic_сomusers_id bigint(20) NOT NULL default '0',
	dignity_forum_topic_users_id bigint(20) NOT NULL default '0',
	dignity_forum_topic_views bigint(20) NOT NULL default '0',
	dignity_forum_topic_closed varchar(1) NOT NULL default '0',
	dignity_forum_topic_ontop varchar(1) NOT NULL default '0',
	dignity_forum_topic_onlycomusers varchar(1) NOT NULL default '0',
	dignity_forum_topic_onlyusers varchar(1) NOT NULL default '0',
	dignity_forum_topic_rating bigint(20) NOT NULL default '0',
	dignity_forum_topic_img longtext NOT NULL default '',
	PRIMARY KEY (dignity_forum_topic_id)
	)" . $charset_collate;
		
	$CI->db->query($sql);
}
	
// создаём табилицу для разделов
if ( !$CI->db->table_exists('dignity_forum_category'))
{
		
	$sql = "
	CREATE TABLE " . $CI->db->dbprefix . "dignity_forum_category (
	dignity_forum_category_id bigint(20) NOT NULL auto_increment,
	dignity_forum_category_name varchar(255) default '',
	dignity_forum_category_description varchar(255) default '',
	dignity_forum_category_order bigint(20) NOT NULL default '0',
	dignity_forum_category_onlycomusers varchar(1) NOT NULL default '0',
	dignity_forum_category_onlyusers varchar(1) NOT NULL default '0',
	dignity_forum_category_parent_id bigint(20) NOT NULL default '0',
	dignity_forum_category_img longtext NOT NULL default '',
	PRIMARY KEY (dignity_forum_category_id)
	)" . $charset_collate;
	$CI->db->query($sql);
}
	
// создаём табилицу для ответов
if ( !$CI->db->table_exists('dignity_forum_reply'))
{
	
	$sql = "
	CREATE TABLE " . $CI->db->dbprefix . "dignity_forum_reply (
	dignity_forum_reply_id bigint(20) NOT NULL auto_increment,
	dignity_forum_reply_text longtext default '',
	dignity_forum_reply_datecreate datetime NOT NULL default '0000-00-00 00:00:00',
	dignity_forum_reply_dateupdate datetime NOT NULL default '0000-00-00 00:00:00',
	dignity_forum_reply_topic_id bigint(20) NOT NULL default '0',
	dignity_forum_reply_comusers_id bigint(20) NOT NULL default '0',
	dignity_forum_reply_users_id bigint(20) NOT NULL default '0',
	dignity_forum_reply_approved varchar(1) NOT NULL default '0',
	dignity_forum_reply_onlycomusers varchar(1) NOT NULL default '0',
	dignity_forum_reply_onlyusers varchar(1) NOT NULL default '0',
	dignity_forum_reply_rating bigint(20) NOT NULL default '0',
	dignity_forum_reply_parent_id bigint(20) NOT NULL default '0',
	PRIMARY KEY (dignity_forum_reply_id)
	)" . $charset_collate;
	$CI->db->query($sql);
}

#end of file
