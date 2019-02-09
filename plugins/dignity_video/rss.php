<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_video (github)
 * License GNU GPL 2+
 */

// получаем доступ к CI
$CI = & get_instance();

// заголовки
header('Content-type: text/html; charset=utf-8');
header('Content-Type: application/rss+xml');

// кеш
$cache_key = mso_md5('rss_' . mso_current_url());
$k = mso_get_cache($cache_key);
if ($k) return print($k);
ob_start();

echo('<?xml version="1.0" encoding="utf-8"?><rss version="2.0">');

echo("<channel>
<title>" . getinfo('name_site') . ' ' . t('Видео', __FILE__) . "</title>
<link>" . getinfo('siteurl') . "</link>
<language>ru</language>
<pubDate>" . date('D, d M Y H:i:s') . "</pubDate>
<description>" . t('Лучшие видео от пользователей нашего портала.', __FILE__) . "</description>");

// берём данные из базы
$CI->db->from('dignity_video');
$CI->db->where('dignity_video_approved', '1');
$CI->db->where('dignity_video_rss', '1');
$CI->db->where('dignity_video_ontop', '1');
$CI->db->order_by('dignity_video_id', 'desc');
$CI->db->limit(7);
$query = $CI->db->get();
	
if ($query->num_rows() > 0)	
{
	$allnews = $query->result_array();
	
	foreach ($allnews as $onenews) 
	{
		echo("<item>
		<title>" . $onenews['dignity_video_title'] . "</title>
		<link>" . getinfo('site_url') . $options['slug'] . '/view/' . $onenews['dignity_video_id'] . "</link>
		<guid>" . getinfo('site_url') . $options['slug'] . '/view/' . $onenews['dignity_video_id'] . "</guid>
		<pubDate>" . mso_date_convert($format = 'D, d M Y H:i:s', $onenews['dignity_video_datecreate']) . "</pubDate>
		<description><![CDATA[" . video_cleantext($onenews['dignity_video_text']) . "]]></description>
		</item>");
	}
}

echo("</channel></rss>");

// добавляем в кеш
mso_add_cache($cache_key, ob_get_flush());

#end of file
