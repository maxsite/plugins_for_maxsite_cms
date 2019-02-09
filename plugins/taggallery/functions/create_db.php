<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
// тут создаются таблицы базы данных
							
	$CI = get_instance();
	
	if ( !$CI->db->table_exists('pictures')) // нет таблицы pictures
	{
	
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "pictures (
		picture_id bigint(20) NOT NULL auto_increment,
		picture_title varchar(255) default '',
		picture_slug varchar(255) default '',
		picture_content longtext,
		picture_desc varchar(255) default '',
		picture_exif varchar(255) default '',
    picture_source_id bigint(20) default 0,
		picture_dir varchar(255) NOT NULL default '',
		picture_file varchar(255) NOT NULL default '',
    picture_full_size_url varchar(255),		

		picture_url varchar(255),
		picture_mini_url varchar(255),
		picture_date datetime,
		picture_date_file datetime,
		picture_date_photo datetime,
		picture_view_count bigint(20) default 0,
		picture_rate_plus bigint(20) default 0,
		picture_rate_minus bigint(20) default 0,
		picture_rate_count bigint(20) default 0,
		picture_height bigint(20) default 0,
		picture_position bigint(20) default 0,
		picture_width bigint(20) default 0,
		
		PRIMARY KEY (picture_id),
		KEY picture_slug (picture_slug)
		)" . $charset_collate;
		$CI->db->query($sql);
	}	



		// таблица альбомов 
	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'gallerys` (
								`gallery_id` bigint(20) NOT NULL AUTO_INCREMENT,
								`gallery_title` varchar(255),
								`gallery_slug` varchar(255),
								`gallery_name` varchar(255),
								`gallery_desc` varchar(255),
								`gallery_content` longtext,
								`gallery_date` datetime,
								`gallery_thumb_id` bigint(20) NOT NULL default 0,
								PRIMARY KEY (`gallery_id`),
								KEY `gallery_slug` (`gallery_slug`),
								KEY `gallery_name` (`gallery_name`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');				
	
	
	// таблица альбомов 
	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'albums` (
								`album_id` bigint(20) NOT NULL AUTO_INCREMENT,
								`album_title` varchar(255),
								`album_slug` varchar(255),
								`album_desc` varchar(255),
								`album_thumb` varchar(255),
								`album_date` datetime NOT NULL,
								`album_parent_id` bigint(20) NOT NULL default 0,
								PRIMARY KEY (`album_id`),
								KEY `album_slug` (`album_slug`)							
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');				
	


	// таблица принадлежности галерей картинкам 
	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'picgal` (
	              `picgal_id` bigint(20) NOT NULL AUTO_INCREMENT,
								`picgal_picture_id` bigint(20) NOT NULL default 0,
								`picgal_gallery_id` bigint(20) NOT NULL default 0,
								PRIMARY KEY (`picgal_id`),
								KEY `picgal_picture_id` (`picgal_picture_id`),
								KEY `picgal_gallery_id` (`picgal_gallery_id`)							
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');

							
	// таблица присвоения галерей альбомам
	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'galalb` (
	              `galalb_id` bigint(20) NOT NULL AUTO_INCREMENT,
								`galalb_gallery_id` bigint(20) NOT NULL default 0,
								`galalb_album_id` bigint(20) NOT NULL default 0,
								PRIMARY KEY (`galalb_id`),
								KEY `galalb_gallery_id` (`galalb_gallery_id`),
								KEY `galalb_album_id` (`galalb_album_id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');


	// таблица источников
	$query = $CI->db->query('CREATE TABLE IF NOT EXISTS `' . $CI->db->dbprefix . 'source` (
								`source_id` bigint(20) NOT NULL AUTO_INCREMENT,
								`source_name` text CHARACTER SET utf8 NOT NULL,
								`source_dir` text CHARACTER SET utf8 NOT NULL,
								`source_type` bigint(20) NOT NULL,
								`source_link` text CHARACTER SET utf8 NOT NULL,
								PRIMARY KEY (`source_id`)
							) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1');			
												  
?>