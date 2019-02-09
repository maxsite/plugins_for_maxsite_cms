<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// этот файл для плагина profile, откуда и будет подключаться
// добавим в массив $profiles_events события-переносы коммента пользователя
//$get_events_user - здеесь моет быть указан конкретный пользователь

  $dialog_options = mso_get_option('dialog' , 'plugins', array());

  require_once( getinfo('plugins_dir') . 'dialog/functions/functions.php' );
  
    // получим все переносы комментариев пользователя
	  $CI = & get_instance();
	
	  $CI->db->select('dlog.*, comusers.comusers_nik , dcomments.comment_content , dcomments.comment_creator_id , dcomments.comment_id , dcomments.comment_discussion_id , ddiscussions.discussion_title'); // , ddiscussions.discussion_title
	  
	  
	  if($last_date)
	     $CI->db->where('log_date <' , $last_date );
	  	  
	  $CI->db->where('dcomments.comment_approved' , '1' );
	  $CI->db->where('dcomments.comment_private' , '0' );
	  $CI->db->where('dcomments.comment_deleted' , '0' );

	  $CI->db->where('dlog.log_value' , 10);
	  
	  $CI->db->join('dcomments', 'dcomments.comment_id = dlog.log_comment_id');
	  $CI->db->join('ddiscussions', 'dcomments.comment_discussion_id = ddiscussions.discussion_id');
	  
	  if ($get_events_user) 
	     $CI->db->where('dcomments.comment_creator_id', $get_events_user);

    $CI->db->join('comusers', 'comusers.comusers_id = dlog.log_user_id');

	  
	  $CI->db->order_by('log_date', $options['order']);
	  $CI->db->limit($options['events_count']);		 
	   
	  $query = $CI->db->get('dlog');

	if ($query->num_rows() > 0)
	{
		$comments = $query->result_array();
		foreach ($comments as $comment)
		{
			//pr($comment);
      $dialog_options['comment_creator_id'] = $comment['comment_creator_id'];
			dialog_comment_to_out($comment['comment_content'] , $dialog_options);

						/* шпаргалка - что содержит массив
	         0-Дата
	         1-Автор события
	         2-контент события
	         3-ссылка на событие 
	         4-заголовок элемента 
	         5-автор элемента 
	         6-id элемента в массиве $profiles_all
	         */
	         
			$profiles_events[$comment['log_date']] = array(
			  $comment['log_date'], // 0-Дата
			  $comment['log_user_id'],   // 1-Пользователь (автор события)
			  $comment['comment_content'],  // 2-контент события
			  $dialog_options['goto_slug'] . '/disc/' .$comment['comment_discussion_id'] . '/comm/' . $comment['comment_id'], //  3-ссылка события			  
			  $comment['discussion_title'],    // 4-заголовок элемента
			 $comment['log_user_id'],      // 5-автор элемента
			  $key_element,  //  6-id вида элемента
			);
		}
	}


?>