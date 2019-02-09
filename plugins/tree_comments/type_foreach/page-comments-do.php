<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!$page_comment_allow) {
	echo '<div class="page_comments_count">' . t('Комментариев') . ': ' . count($comments) . '</div>';
	#echo '<div>';
	if (!isset($options['tc_tabs'])) $options['tc_tabs'] = '1';
	
	if (file_exists(getinfo('plugins_dir') . 'tree_comments/blocks/comments-tree.php')) 
		require(getinfo('plugins_dir') . 'tree_comments/blocks/comments-tree.php'); 
	}
?>