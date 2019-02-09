<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

  // получаем комментарии
  // $pag_no
  // $comuser_id
  // $user_id
  // $page_id
  // $pag_count
  // $limit
  // $sort
  // $comments_array
 $r=array();
  $r['tags_comusers'] = '<a><p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
  $r['tags_users'] = '<a><p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';

	$CI = & get_instance();

	$CI->db->select('SQL_CALC_FOUND_ROWS comments_id, comments_users_id, comments_comusers_id, comments_author_name, comments_date, comments_content, comments_approved, comments_author_ip, users.users_nik, comusers.comusers_nik, page.page_title, page.page_slug', false);
	$CI->db->from('comments');
	$CI->db->join('users', 'users.users_id = comments.comments_users_id', 'left');
	$CI->db->join('comusers', 'comusers.comusers_id = comments.comments_comusers_id', 'left');
	
	 $CI->db->join('page', 'page.page_id = comments.comments_page_id', 'left');
	
	$CI->db->where('comments.comments_approved', 1);
	
   // вручную делаем этот where, потому что придурочный CodeIgniter его неверно экранирует
	$CI->db->where($CI->db->dbprefix . 'page.page_id', $CI->db->dbprefix . 'comments.comments_page_id', false);
	$CI->db->where('page.page_status', 'publish');
   
    if ($page_id) $CI->db->where('page.page_id', $page_id);

	$CI->db->order_by('comments_date', $sort);

	$CI->db->limit($limit, $pag_no * $limit - $limit ); // не более $limit
	
	$query = $CI->db->get();

	if (!$pag_count) 
	{
	   $paged = mso_sql_found_rows($limit); // определим общее кол-во для пагинации
	   $pag_count = $paged['maxcount'];
	}   
	
	
	
	
	if ($query->num_rows() > 0)
	{
		$comments = $query->result_array();
		foreach ($comments as $comment)
		{
			$commentator = 3; // комментатор: 1-комюзер 2-автор 3-аноним
			

			if ($comment['comusers_id']) // это комюзер
			{
				if ($comment['comusers_nik']) $comment['comments_author_name'] = $comment['comusers_nik'];
				else $comment['comments_author_name'] = $r['add_author_name'] . ' ' . $comment['comusers_id'];
				$comment['comments_url'] = '<a href="' . getinfo('siteurl') . 'users/' . $comment['comusers_id'] . '">'
						. $comment['comments_author_name'] . '</a>';
				
				// есть адрес страницы
				if ($comment['comusers_url'])
				{
					// зачистка XSS
					$comments[$key]['comusers_url'] = mso_xss_clean($comment['comusers_url'], '');
				}
				
				// зачистка XSS комюзер имя
				if ($comment['comusers_nik'])
				{
					$comments[$key]['comusers_nik'] = mso_xss_clean($comment['comusers_nik']);
				}
				
				$commentator = 1;

				if (isset($all_comusers[$comment['comusers_id']]))
					$comments[$key]['comusers_count_comments'] = $all_comusers[$comment['comusers_id']];

			}
			elseif ($comment['users_id']) // это автор
			{
				if ($comment['users_url'])
						$comment['comments_url'] = '<a href="' . $comment['users_url'] . '">' . $comment['users_nik'] . '</a>';
					else $comment['comments_url'] = $comment['users_nik'];
				$commentator = 2;
			}
			else // просто аноним
			{
				if (!$comment['comments_author_name']) $comment['comments_author_name'] = $r['anonim_no_name'];
				if ($r['anonim_twitter']) // разрешено проверять это твиттер-логин?
				{
					
					if (strpos($comment['comments_author_name'], '@') === 0) // первый символ @
					{	
						$lt = mso_slug( substr($comment['comments_author_name'], 1) ); // вычленим @
						
						$lt = mso_xss_clean($lt, 'Error', $lt, true); // зачистка XSS
						
						$comment['comments_url'] = '<a href="http://twitter.com/' . $lt . '" rel="nofollow">@' . $lt . '</a>';
					}
					else $comment['comments_url'] = $comment['comments_author_name'] . $r['anonim_title']; 
				}
				else
				{
					$comment['comments_url'] = $comment['comments_author_name'] . $r['anonim_title']; 
				}
			}




			$comments_content = $comment['comments_content'];

			// защитим pre
			$t = $comments_content;
			$t = str_replace('&lt;/pre>', '</pre>', $t); // проставим pre - исправление ошибки CodeIgniter
			
			$t = preg_replace_callback('!<pre>(.*?)</pre>!is', 'mso_clean_html_do', $t);

			$t = strip_tags($t, $r['tags_comusers']);
			
			$t = mso_xss_clean($t);

			$t = str_replace('[html_base64]', '<pre>[html_base64]', $t); // проставим pre
			$t = str_replace('[/html_base64]', '[/html_base64]</pre>', $t);
			
			// обратная замена
			$t = preg_replace_callback('!\[html_base64\](.*?)\[\/html_base64\]!is', 'mso_clean_html_posle', $t);
			
			$comments_content = $t; // сохраним как текст комментария
			
			$comments_content = mso_hook('comments_content', $comments_content);
			
			$comments_content = str_replace("\n", "<br>", $comments_content);
	
			$comments_content = str_replace('<p>', '&lt;p&gt;', $comments_content);
			$comments_content = str_replace('</p>', '&lt;/p&gt;', $comments_content);
			$comments_content = str_replace('<P>', '&lt;P&gt;', $comments_content);
			$comments_content = str_replace('</P>', '&lt;/P&gt;', $comments_content);
			
			
			if (mso_hook_present('comments_content_custom'))
			{
				$comments_content = mso_hook('comments_content_custom', $comments_content);
			}
			else
			{
				$comments_content = mso_auto_tag($comments_content, true);
				$comments_content = mso_hook('content_balance_tags', $comments_content);
			}
			
			$comments_content = mso_hook('comments_content_out', $comments_content);
			/*
			
			  $darr = explode(" ", $comment['comments_date']);
			  $date = explode("-", $darr[0]);
			  $time = explode(":", $darr[1]);
			  $date = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0] );
			  */
			  $comment['comments_content'] = $comments_content;
			  
			  
			  $comments_array[] = $comment;
			  
			
		}
		
		
	}

?>