<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// этот файл для плагина profile, откуда и будет подключаться

// добавим в массив $profiles_events события - ответы (пользователю отвечали)
//$get_events_user - здесь моет быть указан конкретный пользователь
//$subject_id - моет быть указан фильтр - конкретный отвечавший пользователь

  $dialog_options = mso_get_option('dialog' , 'plugins', array());

  require_once( getinfo('plugins_dir') . 'dialog/functions/functions.php' );
  
	$CI = & get_instance();
	    $questions = array();
	    $questions_id = array();
	    $questions_comuser_id = array();
	    
	  
    // получим все комменты - ответы (у которых не пустое поле parent_id)
	    $CI->db->select('comment_id , comment_parent_id'); 
	    $CI->db->where('comment_parent_id >' , 0 );

	    // отфильтруем, если нужно чтобы ответы конкретного пользователя
	    if ($get_events_user) $CI->db->where('comment_creator_id', $get_events_user);
	    $query = $CI->db->get('dcomments');
	    if ($query->num_rows() > 0)
	    {
		    $comm = $query->result_array();
		    // массив id сообщений, которые являются вопросами (на которые давались ответы)
		    foreach ($comm as $comme)
		    {	    
	        $questions_id[] = $comme['comment_parent_id'];
	      }
	    }  
	    
	 if ($questions_id) 
	 {  
	    // теперь получим все вопросы пользователя (которые содержатся в качестве parent_id других комментов)
	    $CI->db->select('comment_content, comment_id, comment_creator_id'); 
	    $CI->db->where_in('comment_id' , $questions_id );
	    $CI->db->where('comment_approved', '1');
	    $CI->db->where('comment_deleted', '0');	 
	    $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	    //$CI->db->join('comusers', 'comusers.comusers_id = dcomments.comment_creator_id');
	
	    $CI->db->where('discussion_approved', '1');
	    $CI->db->where('discussion_private', '0');	       
	    // отфильтруем, если нужно чтобы конкретного пользователя
	    if ($subject_id) $CI->db->where('comment_creator_id', $subject_id);
	    $query = $CI->db->get('dcomments');
	    if ($query->num_rows() > 0)
	    {
	      $questions_id = array();
		    $comm = $query->result_array();
		    foreach ($comm as $comme)
		    {	    
		      $dialog_options['comment_creator_id'] = $comme['comment_creator_id'];
			  dialog_comment_to_out($comme['comment_content'] , $dialog_options);

	          $questions[$comme['comment_id']] = $comme['comment_content'];
	          $questions_comuser_id[$comme['comment_id']] = $comme['comment_creator_id'];
	          $questions_id[] = $comme['comment_id'];
	       }
	    }  	    
	 }
	 
	 
	 if ($questions)   
	 {
	    
	  // теперь получим ответы на комменты в $questions_id
  $CI->db->select('comment_content, comment_id, comment_discussion_id, comment_creator_id, comment_date_create, discussion_title, discussion_creator_id, comusers_nik , comment_parent_id');
 
	$CI->db->where_in('comment_parent_id' , $questions_id );
  
	$CI->db->where('comment_approved', '1');
	$CI->db->where('comment_deleted', '0');
	
	$CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	$CI->db->join('comusers', 'comusers.comusers_id = dcomments.comment_creator_id');
	
	$CI->db->where('discussion_approved', '1');
	$CI->db->where('discussion_private', '0');
	
	if($last_date)
	  $CI->db->where('comment_date_create <' , $last_date );

  if ($subject_id)
	   $CI->db->where('comment_creator_id' , $subject_id);
	   	
	$CI->db->order_by('comment_date_create', $options['order']);
	$CI->db->limit($options['events_count']);	

	$query = $CI->db->get('dcomments');

	if ($query->num_rows() > 0)
	{
		$comm = $query->result_array();	  
		
		foreach ($comm as $comment)
		{		 
            $dialog_options['comment_creator_id'] = $comment['comment_creator_id'];
			dialog_comment_to_out($comment['comment_content'] , $dialog_options);

            if (!isset($questions_comuser_id[$comment['comment_parent_id']])) $questions_comuser_id[$comment['comment_parent_id']] = false;
		
            // вставляем вопрос
            if (isset($questions[$comment['comment_parent_id']])) 
			   $comment['comment_content'] =  '<blockquote>' . $questions[$comment['comment_parent_id']] . '</blockquote>' . $comment['comment_content'];
            

			/* шпаргалка
	         0-Дата
	         1-Автор события
	         2-контент события
	         3-ссылка на событие 
	         4-заголовок элемента 
	         5-автор элемента 
	         6-id элемента в массиве $profiles_all
	         */
	         
			$profiles_events[$comment['comment_date_create']] = array(
			  $comment['comment_date_create'], // 0-Дата
			  $comment['comment_creator_id'],   // 1-Пользователь (автор события)
			  $comment['comment_content'],  // 2-контент события
			  $dialog_options['goto_slug'] . '/disc/' .$comment['comment_discussion_id'] . '/comm/' . $comment['comment_id'], //  3-ссылка события			  
			  $comment['discussion_title'],    // 4-заголовок элемента
			  $questions_comuser_id[$comment['comment_parent_id']], //$comment['comment_creator_id']      // 5-автор элемента
			  $key_element,  //  6-id вида элемента
			  );
		}
		
  }		 
	  
  }
  
  
?>