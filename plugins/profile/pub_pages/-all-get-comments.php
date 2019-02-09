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

  $r['tags_comusers'] = '<a><p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
  $r['tags_users'] = '<a><p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';

	$CI = & get_instance();

	$CI->db->select('SQL_CALC_FOUND_ROWS comments_id, comments_users_id, comments_comusers_id, comments_author_name, comments_date, comments_content, comments_approved, comments_author_ip, users.users_nik, comusers.comusers_nik, page.page_title, page.page_slug', false);
	$CI->db->from('comments');
	$CI->db->join('users', 'users.users_id = comments.comments_users_id', 'left');
	$CI->db->join('comusers', 'comusers.comusers_id = comments.comments_comusers_id', 'left');
	
	// $CI->db->join('page', 'page.page_id = comments.comments_page_id', 'left');
	
	$CI->db->where('comments.comments_approved', '1');
	
   // вручную делаем этот where, потому что придурочный CodeIgniter его неверно экранирует
	$CI->db->where($CI->db->dbprefix . 'page.page_id', $CI->db->dbprefix . 'comments.comments_page_id', false);
	$CI->db->where('page.page_status', 'publish');
   
    if ($page_id) $CI->db->where('page.page_id', $page_id);

	$CI->db->order_by('comments_date', $sort);

	$CI->db->limit($limit, $pag_no * $limit - $limit ); // не более $limit
	
	$query = $CI->db->get();

	if (!$page_count) $page_count = mso_sql_found_rows($limit); // определим общее кол-во для пагинации
	

	if ($query->num_rows() > 0)
	{
		$comments = $query->result_array();
		foreach ($comments as $comment)
		{

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
			
			
			  $darr = explode(" ", $comment['comments_date']);
			  $date = explode("-", $darr[0]);
			  $time = explode(":", $darr[1]);
			  $date = mktime($time[0], $time[1], $time[2], $date[1], $date[2], $date[0] );
			  
			  $comments[] = array($comments_content);
			  
           /* 
	         содержимое $event
	         0-Дата
	         1-Автор события
	         2-контент события
	         3-ссылка на событие 
	         4-заголовок элемента 
	         5-автор элемента 
	         6-id элемента в массиве $profiles_all
	         */
	       
	       /*  
			$profiles_events[$date] = array(
			  $date, // 0-Дата
			  $comment['comments_comusers_id'],   // 1-Пользователь (автор события)
			  $comments_content,  // 2-контент события
			  'page/' . mso_slug($comment['page_slug']) . '#comment-' . $comment['comments_id'], //  3-ссылка события			  
			  $comment['page_title'],    // 4-заголовок элемента
			  false,//$comment['page_id_autor'],      // 5-автор элемента
			  $key_element,  //  6-id вида элемента
			);
			*/
			
		}
		
		// последняя дата
		// нам нужно выбрать наименее глубокое последнее событие
		/*
		if (!$last_date) $last_date = $date;
		elseif ($date > $last_date) $last_date = $date;
		*/
	}


?>