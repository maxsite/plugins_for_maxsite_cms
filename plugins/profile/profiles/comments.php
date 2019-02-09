<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// добавим в массив $profiles_events события-комментарии
//$get_events_user - здесь моет быть указан конкретный пользователь

  $r['tags_comusers'] = '<a><p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';
	$r['tags_users'] = '<a><p><img><strong><em><i><b><u><s><font><pre><code><blockquote>';

	$CI = & get_instance();
	
	$CI->db->select('page.page_id, page.page_slug, page.page_title, page.page_id_autor, comusers_nik, comments.*');

	$CI->db->where('comments.comments_approved', '1');

	// вручную делаем этот where, потому что придурочный CodeIgniter его неверно экранирует
	$CI->db->where($CI->db->dbprefix . 'page.page_id', $CI->db->dbprefix . 'comments.comments_page_id', false);
	
	$CI->db->where('page.page_status', 'publish');

	// $CI->db->where('comments.comments_comusers_id >0');

  if ($last_date)
	   $CI->db->where('comments.comments_date <' , date('Y-m-d H:i:s' , $last_date));

  if ($get_events_user)
	   $CI->db->where('comments.comments_comusers_id' , $get_events_user);
	     
	$CI->db->join('comusers', 'comusers.comusers_id = comments.comments_comusers_id' , 'left');

	$CI->db->order_by('comments.comments_date', $options['order']);
	
	$CI->db->limit($options['events_count']);
	
	$CI->db->from('comments, page');

	$query = $CI->db->get();


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
	         
			$profiles_events[$date] = array(
			  $date, // 0-Дата
			  $comment['comments_comusers_id'],   // 1-Пользователь (автор события)
			  $comments_content,  // 2-контент события
			  'page/' . mso_slug($comment['page_slug']) . '#comment-' . $comment['comments_id'], //  3-ссылка события			  
			  $comment['page_title'],    // 4-заголовок элемента
			  false,//$comment['page_id_autor'],      // 5-автор элемента
			  $key_element,  //  6-id вида элемента
			);
		}
		
		// последняя дата
		// нам нужно выбрать наименее глубокое последнее событие
		/*
		if (!$last_date) $last_date = $date;
		elseif ($date > $last_date) $last_date = $date;
		*/
	}


?>