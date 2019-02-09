<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_blogs (github)
 * License GNU GPL 2+
 */

// доступ к CodeIgniter
	$CI = & get_instance();

	// загружаем опции
	$options = mso_get_option('plugin_dignity_blogs', 'plugins', array());
	if ( !isset($options['slug']) ) $options['slug'] = 'blogs';

	// выводим заголовок
	echo '<h2 style="padding: 3px; border-bottom: 1px solid #DDD;">' . t('Активность в блогах', __FILE__) . '</h2>';

	// подсчитываем количество статей комюзера
    $CI->db->from('dignity_blogs');
    $CI->db->where('dignity_blogs_approved', '1');
    $CI->db->where('dignity_blogs_comuser_id', mso_segment(2));
    $blogs_entry = $CI->db->count_all_results();

    // если больше одной, то выводим ссылку на блог
    if ($blogs_entry >= 1)
    {
    	$entry_url = '<a href="' . getinfo('site_url') . $options['slug'] . '/blog/' . mso_segment(2) . '">' . $blogs_entry . '</a>';
    }
    else
    {
    	$entry_url = $blogs_entry;
    }

    // выводим заголовок
    echo '<p style="padding-left:20px;">' . '<strong>' . t('Публикаций:', __FILE__) . '</strong> ' . $entry_url . '</p>';
      
    // подсчитываем количество комментарий комюзера
    $CI->db->from('dignity_blogs_comments');
	$CI->db->where('dignity_blogs_comments_approved', '1');
	$CI->db->where('dignity_blogs_comments_comuser_id', mso_segment(2));
	$blogs_comments = $CI->db->count_all_results();
    echo '<p style="padding-left:20px;">' . '<strong>' . t('Комментарий:', __FILE__) . '</strong> ' . $blogs_comments . '</p>';

#end of file
