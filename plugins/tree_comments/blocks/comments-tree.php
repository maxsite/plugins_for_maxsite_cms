<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

	if (!$options['tc_tabs']) echo '<h3 class="comments">' . t('Комментариев') . ': ' . count($comments) . '</h3>';
	
	#if ($page_comment_allow) 
	echo '<div class="tabs-box tabs-visible" style="display: block;">';

	$tree_comments_first_level = 'tree-comments-level-0';
	global $tree_comments_child_list;
	$tree_comments_child_list = 'tree-comments-list-childs';
	$comments_parent_id = 0;
	global $comms;
	$comms	= $comments;

	$parents = array();
	foreach ( $comments as $comment ) {
		// определим корневые узлы
		if ( $comment['comments_parent_id'] == 0 ) $parents[] = $comment;
	}
	
	$out = '<ul class="' . $tree_comments_first_level . '">';
	$out .=  build_tree( $parents, 0  ); 
	$out .= '</ul>';	

	echo $out;
	
	function  build_tree($parents, $parent_id){
	$options = mso_get_option('tree_comments', 'plugins', array() ); // получаем опции
	

	
		global $comms;
		global $tree_comments_child_list;
		$tree = '';
		foreach ( $parents as $parent ) {
			extract($parent);
			if ($users_id) $class = ' class="users"';
			elseif ($comusers_id) $class = ' class="comusers"';
			else $class = ' class="anonim"';
			
			if (!isset($options['tc_comment_date'])) $options['tc_comment_date'] = 'j F Y в H:i:s';
			$comments_date = mso_page_date($comments_date, 
									array(	'format' => $options['tc_comment_date'], // получаем формат даты
											'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
											'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря')), 
									'', '' , false);	
		
			$data = array( 	'users_email' => $users_email,
							'comusers_email' => $comusers_email,
							'users_avatar_url' => $users_avatar_url,
							'comusers_avatar_url' => $comusers_avatar_url 
                       	);
						
		$avatar_url = '';
		if ($comusers_avatar_url) $avatar_url = $comusers_avatar_url;
		elseif ($users_avatar_url) $avatar_url = $users_avatar_url;
		#else $avatar_url = $comusers_avatar_url;
		
		if (!$avatar_url) 
		{ // аватарки нет, попробуем получить из gravatara
			
			if ($users_email) $grav_email = $users_email;
			elseif ($comusers_email) $grav_email = $comusers_email;
			else $grav_email = '';
			
			if ($grav_email)
			{
				if ($gravatar_type = mso_get_option('gravatar_type', 'templates', ''))
					$d = '&amp;d=' . urlencode($gravatar_type);
				else 
					$d = '';
				
				$avatar_url = "//www.gravatar.com/avatar.php?gravatar_id=" 
						. md5($grav_email)
						. "&amp;size="
						. mso_get_option('gravatar_size', 'templates', '')
						. $d;
			}
		}
		
		if ($avatar_url) $avatar_url = '<img src="' . $avatar_url . '" width="'. mso_get_option('gravatar_size', 'templates', '') .'" height="'. mso_get_option('gravatar_size', 'templates', '') .'" alt="" title="" style="float: left; margin: 5px 15px 10px 0;" class="gravatar">';
		else $avatar_url = '<img src="//www.gravatar.com/avatar/19611d8a175e2e02905c608e04674349?size=' . mso_get_option('gravatar_size', 'templates', '') . '&d=mm" width="'. mso_get_option('gravatar_size', 'templates', '') .'" height="'. mso_get_option('gravatar_size', 'templates', '') .'" alt="" title="" style="float: left; margin: 5px 15px 10px 0;" class="gravatar">'; 

			$tree .= '<li style="clear: both;"' . $class . '><div class="tree-comment">';
			$tree .= '<div class="comment-info tree-comment">';
				$tree .= '&nbsp;<span class="tree-comment-author">' . $comments_url . '</span>';
				// опциональная ссылка на комментарий
				if (!isset($options['tc_comment_link'])) $options['tc_comment_link'] = 'date';
				//if (!isset($options['tc_comuser_link'])) $options['tc_comuser_link'] = '1';
				$comment_info = '';
				if ($comusers_url and mso_get_option('allow_comment_comuser_url', 'general', 0)) $tree .= $comment_info .= ' <a href="' . $comusers_url . '" rel="nofollow" target="_blank" class="outlink"><img src="' . getinfo('template_url') . 'images/outlink.png" width="16" height="16" alt="link" title="' . tf('Сайт комментатора') . '"></a>';
				else $tree .= '&nbsp;|';
				if ($options['tc_comment_link'] == 'date') $tree .= ' <span class="tree-comment-date"><a href="/page/' . $page_slug . '#comment-' . $comments_id . '" name="comment-' . $comments_id . '">' . $comments_date . '</a></span>';
				else $tree .= '&nbsp;<span class="tree-comment-date">' . $comments_date . '</span>';
				if ($options['tc_comment_link'] == 'text') $tree .= '&nbsp;<span class="tree-comment-meta"><a href="/page/' . $page_slug . '#comment-' . $comments_id . '" name="comment-' . $comments_id . '">(ссылка)</a></span>';
				
				
				if (is_login()) 
				{
					$edit_link = getinfo('siteurl') . 'admin/comments/edit/';
					$tree .= ' | ';
					$tree .= '<span class="tree-comment-edit"><a href="' . $edit_link . $comments_id . '">edit</a></span>';
					if (!isset($options['tc_comment_ip'])) $options['tc_comment_ip'] = '1';
					if ($options['tc_comment_ip']) $tree .= '<span class="tree-comment-ip">'. $comments_author_ip .'</span>';
				}	
				if (!$comments_approved) {
					$tree .= ' | ';
					$tree .= '<span class="tree-comment-moderate">Ожидает модерации</span>';			
				}	
	
			$tree .= '</div>';

			$tree .= '<div class="comments_content tree-comment-data">';
				$tree .= $avatar_url;
				$tree .= mso_comments_content($comments_content);	
			$tree .= '</div>';
			
			$tree .= '<div class="break"></div>';
			$tree .= '<div class="comment-reply" id="comment-reply-' . $comments_id . '">';
			$tree .= '<a class="comment-form-button" id="comment-form-button-' . $comments_id . '" type="button" name="comment-form-button-' . $comments_id . '" onclick="show_comment_form(' . $comments_id . ', ' . $page_id . ')">Ответить</a>';
			$tree .= '<div class="comment-form-comment" id="comment-form-comment-' . $comments_id . '"></div>';
			$tree .= '</div>';
			
			$tree .= '</div><!--div class="comments-end"-->';
			/**/
				$childs =array();
				foreach ( $comms as $comm ) {
					if ( $comm['comments_parent_id'] == $parent['comments_id']) { $childs[] = $comm; }
				}
				if ( isset( $childs ) && ( count($childs) > 0 ) )
				{			
					$tree .= '<ul class="' . $tree_comments_child_list . '">';
					$tree .= build_tree ( $childs, $parent['comments_id'] );
					$tree .= '</ul>';
				}
			/**/
			$tree .= '</li>';	
		}
		return $tree;			

    }
	#echo '</div>';
	
	#if ($page_comment_allow) {
		echo '<div class="break"></div>' . mso_get_val('leave_a_comment_start', '<h3 class="comments">') . mso_get_option('leave_a_comment', 'templates', t('Оставьте комментарий!')). mso_get_val('leave_a_comment_end', '</h3>');
		
		if (!isset($options['tc_form'])) $options['tc_form'] = '0';
		if ($options['tc_form']) { 
			#echo '<div class="break"></div>' . mso_get_val('leave_a_comment_start', '<h3 class="comments">') . mso_get_option('leave_a_comment', 'templates', t('Оставьте комментарий!')). mso_get_val('leave_a_comment_end', '</h3>');
				if (file_exists(getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comment-form-old.php')) 
					require(getinfo('plugins_dir') . 'tree_comments/type_foreach/page-comment-form-old.php'); 
				else echo "File FORM-old not found";
		}
		else {
			$fn1 = getinfo('template_dir') . 'type/page-comment-form.php';
			$fn2 = getinfo('shared_dir') . 'type/page/units/page-comment-form.php';
			$fn3 = getinfo('templates_dir') . 'default/type/page-comment-form.php';
			
			if ( file_exists($fn1) ) require($fn1);
			elseif (file_exists($fn2)) require($fn2);
			else if (file_exists($fn3)) require($fn3);
		}
	#}
	#else echo '<div class="page_comments_count">Дальнейшее комментирование отключено.</div>';
	if ($options['tc_tabs']) echo '</div><!-- div tabs-box end -->';
?>
