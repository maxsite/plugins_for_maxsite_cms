<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// этот файл для плагина profile, откуда будет подключаться
// добавим в массив $profiles_events события-благодарности
//$get_events_user - здеесь моет быть указан конкретный пользователь

  $dialog_options = mso_get_option('dialog' , 'plugins', array());

  require_once( getinfo('plugins_dir') . 'dialog/functions/functions.php' );
  
    // получим все благодарности, выраженные пользователем
	  $CI = & get_instance();
	
	  $CI->db->select('dgud.*, comusers.comusers_nik , dcomments.comment_content , dcomments.comment_creator_id , dcomments.comment_id , dcomments.comment_discussion_id'); // , ddiscussions.discussion_title
	  
	  if ($get_events_user) $CI->db->where('gud_user_id', $get_events_user);
	  
	  // возможно есть фильтр по субъекту, которому благодарность
	  if ($subject_id) $CI->db->where('gud_autor_id', $subject_id);
	  	  
	  if($last_date)
	    $CI->db->where('gud_date <' , $last_date );	  
	  
	  $CI->db->join('dcomments', 'dcomments.comment_id = dgud.gud_comment_id');
	 // $CI->db->join('ddiscussions', 'dcomments.comment_discussion_id = ddiscussions.discussion_id');
	  $CI->db->join('comusers', 'comusers.comusers_id = dgud.gud_user_id');
	  
	  $CI->db->order_by('gud_date', $options['order']);
	  $CI->db->limit($options['events_count']);		 
	   	  
	  $query = $CI->db->get('dgud');

	if ($query->num_rows() > 0)
	{
		$comments = $query->result_array();
		foreach ($comments as $comment)
		{
			//pr($comment);
      $dialog_options['comment_creator_id'] = $comment['comment_creator_id'];
			dialog_comment_to_out($comment['comment_content'] , $dialog_options);

				
						/* шпаргалка
	         0-Дата
	         1-Автор события
	         2-контент события
	         3-ссылка на событие 
	         4-заголовок элемента 
	         5-автор элемента 
	         6-id элемента в массиве $profiles_all
	         */
	         
			$profiles_events[$comment['gud_date']] = array(
			  $comment['gud_date'], // 0-Дата
			  $comment['gud_user_id'],   // 1-Пользователь (автор события)
			  $comment['comment_content'],  // 2-контент события
			  $dialog_options['goto_slug'] . '/disc/' .$comment['comment_discussion_id'] . '/comm/' . $comment['comment_id'], //  3-ссылка события			  
			  false, //$comment['discussion_title'],    // 4-заголовок элемента
			 $comment['gud_autor_id'],      // 5-автор элемента
			  $key_element,  //  6-id вида элемента
			);
		}
	}


?>