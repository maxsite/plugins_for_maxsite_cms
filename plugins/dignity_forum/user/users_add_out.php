<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/*
 * (c) Alexander Schilling
 * http://alexanderschilling.net
 * https://github.com/dignityinside/dignity_forum (github)
 * License GNU GPL 2+
 */

// доступ к CodeIgniter
$CI = & get_instance();

// загружаем опции и присваиваем значения по умолчанию
$options = mso_get_option('plugin_dignity_forum', 'plugins', array());
if ( !isset($options['slug']) ) $options['slug'] = 'forum';

echo '<h2 style="padding: 3px; border-bottom: 1px solid #DDD;">' . t('Активность на форуме', __FILE__) . '</h2>';

$CI->db->from('dignity_forum_topic');
$CI->db->where('dignity_forum_topic_approved', true);
$CI->db->where('dignity_forum_topic_сomusers_id', mso_segment(2));
$forum_entry = $CI->db->count_all_results();
		
echo '<p style="padding-left:20px;">' . '<strong>' . t('Тем:', __FILE__) . '</strong> ';

if ($forum_entry > 0)
{
	echo '<a href="' . getinfo('siteurl') . $options['slug'] . '/topics/' . mso_segment(2) . '">' . $forum_entry . '</a>';
}
else
{
	echo $forum_entry;
}

echo '</p>';
	
		
$CI->db->from('dignity_forum_reply');
$CI->db->where('dignity_forum_reply_approved', '1');
$CI->db->where('dignity_forum_reply_comusers_id', mso_segment(2));
$forum_comments = $CI->db->count_all_results();
		
echo '<p style="padding-left:20px;">' . '<strong>' . t('Ответов:', __FILE__) . '</strong> ';

if ($forum_comments > 0)
{
	echo '<a href="' . getinfo('siteurl') . $options['slug'] . '/replys/' . mso_segment(2) . '">' . $forum_comments . '</a>';
}
else
{
	echo $forum_comments;
}

echo '</p>';

#end of file
