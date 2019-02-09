<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
// тут создаются таблицы базы данных
							
	$CI = get_instance();
	
	$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
	$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
	$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
			
	if ( !$CI->db->table_exists('dcomments')) // нет таблицы Dсomments
	{
  	$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dcomments (
		comment_id bigint(20) NOT NULL auto_increment,
		comment_discussion_id int(10) default 0,
		comment_creator_id int(10) NOT NULL default 0,
		comment_editor_id int(10) NOT NULL default 0,
		comment_deleter_id int(10) NOT NULL default 0,
		comment_date_edit int(10) default NULL,
		comment_date_create int(10) NOT NULL default 0,
		comment_date_deleted int(10) default NULL,
		comment_content text,
		comment_approved enum('1','0') NOT NULL default '0',
		comment_spam enum('1','0') NOT NULL default '0',
		comment_check enum('1','0') NOT NULL default '0',
		comment_deleted enum('1','0') NOT NULL default '0',
		comment_parent_id int(10) NOT NULL default 0,
		comment_ip varchar(100) default '',
		comment_rate int(10) NOT NULL default 0,
		comment_private enum('1','0') NOT NULL default '0',
		comment_flud enum('0','1','2') NOT NULL default '1',
		PRIMARY KEY (comment_id),
		KEY comment_discussion_id (comment_discussion_id),
		KEY comment_creator_id (comment_creator_id)
		)" . $charset_collate;
		$CI->db->query($sql);
	}	


	if ( !$CI->db->table_exists('ddiscussions')) // нет таблицы Ddiscussions
	{
  	$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "ddiscussions (
		discussion_id bigint(20) NOT NULL auto_increment,
		discussion_title varchar(100) default '',
		discussion_desc varchar(255) default '',
		discussion_creator_id int(10) NOT NULL default 0,
		discussion_category_id int(10) NOT NULL default 0,
		discussion_approved enum('1','0') NOT NULL default '0',
		discussion_closed enum('1','0') NOT NULL default '0',
		discussion_private enum('1','0') NOT NULL default '0',
		discussion_active enum('1','0') NOT NULL default '0',
		discussion_style_id int(10) NOT NULL default 0,
		discussion_ico_id int(10) NOT NULL default 0,
		discussion_remote_ip varchar(100) default '',
		discussion_spam_check enum('1','0') NOT NULL default '0',
		discussion_last_comment_id bigint(20) NOT NULL default 0,
		discussion_first_comment_id bigint(20) NOT NULL default 0,
		discussion_last_user_id bigint(20) NOT NULL default 0,
		discussion_date_last_active int(10) default NULL,
		discussion_date_create int(10) NOT NULL default 0,
		discussion_comments_count bigint(20) NOT NULL default 0,
		discussion_view_count bigint(20) NOT NULL default 0,
		discussion_order int(10) NOT NULL default 0,
		discussion_p float NOT NULL default 0,
		discussion_parent_comment_id bigint(20) NOT NULL default 0,
		PRIMARY KEY (discussion_id),
		KEY discussion_creator_id (discussion_creator_id),
		KEY discussion_category_id (discussion_category_id),
		KEY discussion_date_last_active (discussion_date_last_active),
		KEY discussion_last_user_id (discussion_last_user_id)
		)" . $charset_collate;
		$CI->db->query($sql);
	}	


	if ( !$CI->db->table_exists('dcategorys')) // нет таблицы Dcategorys
	{
  	$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dcategorys (
		category_id bigint(20) NOT NULL auto_increment,
		category_title varchar(100) default '',
		category_desc varchar(100) default '',
		category_slug varchar(100) default '',
		category_forum_id int(10) NOT NULL default 0,
		category_order int(10) NOT NULL default 0,
		PRIMARY KEY (category_id),
		KEY category_slug (category_slug)
		)" . $charset_collate;
		$CI->db->query($sql);
	}	


	if ( !$CI->db->table_exists('dforums')) // нет таблицы Dforums
	{
  	$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dforums (
		forum_id int(10) NOT NULL auto_increment,
		forum_title varchar(100) default '',
		forum_desc varchar(100) default '',
		forum_order int(10) NOT NULL default 0,
		PRIMARY KEY (forum_id)
		)" . $charset_collate;
		$CI->db->query($sql);
	}	


	if ( !$CI->db->table_exists('dprofiles')) // нет таблицы Dprofile
	{
  	$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dprofiles (
		profile_user_id int(10) NOT NULL,
		profile_style_id int(10) NOT NULL default 0,
		profile_psevdonim varchar(100) default '',
		profile_attributes varchar(100) default '',
		profile_count_visit int(10) NOT NULL default 0,
		profile_discussions_count int(10) NOT NULL default 0,
		profile_comments_count int(10) NOT NULL default 0,
		profile_date_first_visit int(10) default NULL,
		profile_date_last_active int(10) default NULL,
		profile_date_last_visit int(10) default NULL,
		profile_remote_ip varchar(100) default '',
		profile_last_discussion_id int(10) NOT NULL default 0,
		profile_last_comment_id int(10) NOT NULL default 0,
		profile_user_role_id int(10) NOT NULL default 0,
		profile_user_style_id int(10) NOT NULL default 0,
		profile_rate int(10) NOT NULL default 0,
		profile_podpis varchar(100) default '',
		profile_spam_check enum('1','0') NOT NULL default '0',
		profile_moderate enum('1','0') NOT NULL default '1',
		profile_vid varchar(100) default '1',
		profile_comments_on_page int(10) NOT NULL default 10,
		profile_comments_sort_field varchar(100) default 'comment_date_create',
		profile_key varchar(100) default '',
		profile_key_visit enum('1','0') NOT NULL default '0',
		profile_css varchar(100) default '',
		profile_dankes int(10) NOT NULL default 0,
		profile_twitter varchar(100) default '',
		profile_allow_msg enum('1','0') NOT NULL default '0',
		profile_allow_info enum('1','0') NOT NULL default '0',
		profile_allow_subscribe enum('1','0') NOT NULL default '0',
		profile_font_size int(10) NOT NULL default 12,
		profile_all_time int(10) default NULL,
		PRIMARY KEY (profile_user_id)
		)" . $charset_collate;
		$CI->db->query($sql);
	}	
          

	if ( !$CI->db->table_exists('dwatch')) // нет таблицы Dwatch
	{
  	$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dwatch (
		watch_user_id bigint(20) NOT NULL default 0,
		watch_discussion_id bigint(20) NOT NULL default 0,
		watch_count int(10) NOT NULL default 0,
		watch_comments_count int(10) NOT NULL default 0,
		watch_date int(10) default NULL,
		watch_subscribe enum('1','0') NOT NULL default '1',
		PRIMARY KEY (watch_user_id , watch_discussion_id),
		KEY watch_discussion_id (watch_discussion_id),
		KEY watch_user_id (watch_user_id)
		)" . $charset_collate;
		$CI->db->query($sql);
	}	



	if ( !$CI->db->table_exists('dvotes')) // нет таблицы Dvotes
	{
  	$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dvotes (
		vote_comment_id bigint(20) NOT NULL default 0,
		vote_user_id int(10) NOT NULL default 0,
		vote_autor_id int(10) NOT NULL default 0,
		vote_date int(10) default NULL,
		vote enum('1','0') NOT NULL default '0',
		PRIMARY KEY (vote_comment_id , vote_user_id),
		KEY vote_comment_id (vote_comment_id),
		KEY vote_user_id (vote_user_id)
		)" . $charset_collate;
		$CI->db->query($sql);
	}	
	

	if ( !$CI->db->table_exists('drooms')) // нет таблицы Drooms
	{
  	$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "drooms (
		room_user_id bigint(20) NOT NULL default 0,
		room_discussion_id int(10) NOT NULL default 0,
		room_date int(10) default NULL,
		PRIMARY KEY (room_user_id , room_discussion_id),
		KEY room_user_id (room_user_id),
		KEY room_discussion_id (room_discussion_id)
		)" . $charset_collate;
		$CI->db->query($sql);
	}	
	

	if ( !$CI->db->table_exists('dlog')) // нет таблицы Dlog
	{
  	$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dlog (
		log_id bigint(20) NOT NULL auto_increment,
		log_comment_id bigint(20) NOT NULL default 0,
		log_user_id int(10) NOT NULL default 0,
		log_value int(10) NOT NULL default 0,
		log_date int(10) default NULL,
		PRIMARY KEY (log_id),
		KEY log_user_id (log_user_id)		
		)" . $charset_collate;
		$CI->db->query($sql);
	}		
	
	
	if ( !$CI->db->table_exists('dbad')) // нет таблицы Dbad
	{
  	$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dbad (
		bad_user_id bigint(10) NOT NULL default 0,
		bad_comment_id bigint(20) NOT NULL default 0,
		bad_date int(10) default NULL,
		bad_result enum('1','0') NOT NULL default '0',
		PRIMARY KEY (bad_user_id , bad_comment_id),
		KEY bad_user_id (bad_user_id),
		KEY bad_comment_id (bad_comment_id)
		)" . $charset_collate;
		$CI->db->query($sql);
	}		
		
			
	if ( !$CI->db->table_exists('dgud')) // нет таблицы Dgud
	{
  	$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dgud (
		gud_user_id int(10) NOT NULL default 0,
		gud_autor_id int(10) NOT NULL default 0,
		gud_comment_id bigint(20) NOT NULL default 0,
		gud_date int(10) default NULL,
		PRIMARY KEY (gud_user_id , gud_comment_id , gud_autor_id),
		KEY gud_user_id (gud_user_id),
		KEY gud_comment_id (gud_comment_id),
		KEY gud_autor_id (gud_autor_id)
		)" . $charset_collate;
		$CI->db->query($sql);
	}				

	if ( !$CI->db->table_exists('dperelinks')) // нет таблицы Dperelinks
	{
  	$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dperelinks (
		perelinks_parent_id bigint(20) NOT NULL default 0,
		perelinks_child_id bigint(20) NOT NULL default 0,
		perelinks_date int(10) default NULL,
		PRIMARY KEY (perelinks_parent_id , perelinks_child_id),
		KEY perelinks_parent_id (perelinks_parent_id),
		KEY perelinks_child_id (perelinks_child_id)
		)" . $charset_collate;
		$CI->db->query($sql);
	}	
													  
	if ( !$CI->db->table_exists('dmeta')) // нет таблицы dmeta
	{
  	$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "dmeta (
	    meta_id bigint(20) NOT NULL auto_increment,
	    meta_key varchar(255) default NULL,
    	meta_id_obj bigint(20) NOT NULL default '0',
	    meta_table varchar(255) default '',
	    meta_value longtext,
	    meta_desc longtext,
	    meta_menu_order bigint(20) NOT NULL default '0',
	    meta_slug varchar(255) default NULL,
	    PRIMARY KEY (meta_id),
	    KEY meta_key (meta_key),
	    KEY meta_table (meta_table),
	    KEY meta_id_obj (meta_id_obj),
	    KEY meta_value (meta_value(256))
		)" . $charset_collate;
		$CI->db->query($sql);
	}		
	
	
														  
?>