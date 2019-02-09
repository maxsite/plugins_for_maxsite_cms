<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// получаем доступ к CI
$CI = & get_instance();

// заголовки
header('Content-type: text/html; charset=utf-8');
header('Content-Type: application/xml');

// кеш
$cache_key = mso_md5('forum_xml_' . mso_current_url());
$k = mso_get_cache($cache_key);
if ($k) return print($k);
ob_start();

echo('<?xml version="1.0" encoding="utf-8"?>
     <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');

     	echo("<url>
	<loc>" . getinfo('site_url') . $options['slug'] . "</loc>
	<changefreq>always</changefreq>
	<priority>0.5</priority>
	</url>");
     
	echo("<url>
	<loc>" . getinfo('site_url') . $options['slug'] . '/profile/' . "</loc>
	<changefreq>hourly</changefreq>
	<priority>0.1</priority>
	</url>");
	
	echo("<url>
	<loc>" . getinfo('site_url') . $options['slug'] . '/new/' . "</loc>
	<changefreq>hourly</changefreq>
	<priority>0.1</priority>
	</url>");
	
	echo("<url>
	<loc>" . getinfo('site_url') . $options['slug'] . '/recent/' . "</loc>
	<changefreq>hourly</changefreq>
	<priority>0.1</priority>
	</url>");

// берём данные из базы
$CI->db->from('dignity_forum_topic');
$CI->db->where('dignity_forum_topic_approved', true);
$CI->db->order_by('dignity_forum_topic_id', 'desc');
$query = $CI->db->get();

// если есть что выводить
if ($query->num_rows() > 0)	
{
	$topics = $query->result_array();
	
	foreach ($topics as $topic) 
	{
		echo("<url>
		<loc>" . getinfo('site_url') . $options['slug'] . '/view/' . $topic['dignity_forum_topic_id'] . "</loc>
		<lastmod>" . mso_date_convert($format = 'Y-m-d', $topic['dignity_forum_topic_datecreate']) . "</lastmod>
		<changefreq>always</changefreq>
		<priority>0.5</priority>
		</url>");
	}
}

echo("</urlset>");

// добавляем в кеш
mso_add_cache($cache_key, ob_get_flush());

#end of file
