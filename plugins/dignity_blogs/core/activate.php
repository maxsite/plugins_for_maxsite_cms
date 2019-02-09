<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_blogs (github)
 * License GNU GPL 2+
 */

mso_create_allow('dignity_blogs_edit', t('Админ-доступ к', 'plugins') . ' ' . t('«Блоги»', __FILE__));

// поулчаем доступ к CI
$CI = & get_instance();	

$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate .  ' ENGINE=MyISAM;';	

// создаём табилицу для записей
if ( !$CI->db->table_exists('dignity_blogs'))
{
	
	$sql = "
	CREATE TABLE " . $CI->db->dbprefix . "dignity_blogs (
	dignity_blogs_id bigint(20) NOT NULL auto_increment,
	dignity_blogs_title varchar(100) NOT NULL default '',
	dignity_blogs_keywords longtext NOT NULL default '',
	dignity_blogs_description longtext NOT NULL default '',
	dignity_blogs_cuttext longtext NOT NULL default '',
	dignity_blogs_text longtext NOT NULL default '',
	dignity_blogs_datecreate datetime NOT NULL default '0000-00-00 00:00:00',
	dignity_blogs_dateupdate datetime NOT NULL default '0000-00-00 00:00:00',
	dignity_blogs_approved varchar(1) NOT NULL default '',
	dignity_blogs_comments varchar(1) NOT NULL default '',
	dignity_blogs_rss varchar(1) NOT NULL default '',
	dignity_blogs_ontop varchar(1) NOT NULL default '',
	dignity_blogs_views bigint(20) NOT NULL default '0',
	dignity_blogs_comuser_id bigint(20) NOT NULL default '0',
	dignity_blogs_category bigint(20) NOT NULL default '0',
	PRIMARY KEY (dignity_blogs_id)
	)" . $charset_collate;
		
	$CI->db->query($sql);
}
	
// создаём табилицу для комментарий
if ( !$CI->db->table_exists('dignity_blogs_comments'))
{
		
	$sql = "
	CREATE TABLE " . $CI->db->dbprefix . "dignity_blogs_comments (
	dignity_blogs_comments_id bigint(20) NOT NULL auto_increment,
	dignity_blogs_comments_text longtext NOT NULL default '',
	dignity_blogs_comments_thema_id bigint(20) NOT NULL default '0',
	dignity_blogs_comments_datecreate datetime NOT NULL default '0000-00-00 00:00:00',
	dignity_blogs_comments_dateupdate datetime NOT NULL default '0000-00-00 00:00:00',
	dignity_blogs_comments_approved varchar(1) NOT NULL default '',
	dignity_blogs_comments_comuser_id bigint(20) NOT NULL default '0',
	PRIMARY KEY (dignity_blogs_comments_id)
	)" . $charset_collate;
		
	$CI->db->query($sql);
}
	
// создаём табилицу категорий
if ( !$CI->db->table_exists('dignity_blogs_category'))
{
		
	$sql = "
	CREATE TABLE " . $CI->db->dbprefix . "dignity_blogs_category (
	dignity_blogs_category_id bigint(20) NOT NULL auto_increment,
	dignity_blogs_category_name longtext NOT NULL default '',
	dignity_blogs_category_description longtext NOT NULL default '',
	dignity_blogs_category_position bigint(20) NOT NULL default '0',
	dignity_blogs_category_parent_id bigint(20) NOT NULL default '0',
	PRIMARY KEY (dignity_blogs_category_id)
	)" . $charset_collate;
		
	$CI->db->query($sql);
}

// создаём табилицу для тэгов (на будущее)
if ( !$CI->db->table_exists('dignity_blogs_tags_entrys'))
{
	
	$sql = "
	CREATE TABLE " . $CI->db->dbprefix . "dignity_blogs_tags_entrys (
	dignity_blogs_tags_entrys_id bigint(20) NOT NULL auto_increment,
	dignity_blogs_tags_entrys_entryid bigint(20) NOT NULL default '0',
	dignity_blogs_tags_entrys_tagid bigint(20) NOT NULL default '0',
	PRIMARY KEY (dignity_blogs_tags_entrys_id)
	)" . $charset_collate;
		
	$CI->db->query($sql);
}
	
// создаём табилицу для тэгов (на будущее)
if ( !$CI->db->table_exists('dignity_blogs_tags'))
{
		
	$sql = "
	CREATE TABLE " . $CI->db->dbprefix . "dignity_blogs_tags (
	dignity_blogs_tags_id bigint(20) NOT NULL auto_increment,
	dignity_blogs_tags_tag varchar(200) NOT NULL default '',
	PRIMARY KEY (dignity_blogs_tags_id)
	)" . $charset_collate;
		
	$CI->db->query($sql);
}

#end of file
