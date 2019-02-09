<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// этот файл для плагина profile, откуда будет подключаться
// добавим в массив $profiles_events события-сообщения форума
//$get_events_user - здесь моет быть указан конкретный пользователь по которому получаем события

  $dialog_options = mso_get_option('dialog' , 'plugins', array());
  if (!isset($dialog_options['goto_slug'])) $dialog_options['goto_slug'] = 'goto';
 
 // pr ($dialog_options);

  require_once( getinfo('plugins_dir') . 'dialog/functions/functions.php' );
  
	$CI = & get_instance();
	
  $CI->db->select('comment_content, comment_id, comment_discussion_id, comment_creator_id, comment_date_create, discussion_title, discussion_creator_id, comusers_nik');
  
	$CI->db->where('comment_approved', '1');
	$CI->db->where('comment_deleted', '0');
	
	$CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	$CI->db->join('comusers', 'comusers.comusers_id = dcomments.comment_creator_id');
	$CI->db->join('dprofiles', 'dprofiles.profile_user_id = dcomments.comment_creator_id');
	$CI->db->where('dprofiles.profile_spam_check', '0');
	
	$CI->db->where('discussion_approved', '1');
	$CI->db->where('discussion_private', '0');
	
	if($last_date)
	  $CI->db->where('comment_date_create <' , $last_date );

  if ($get_events_user)
	   $CI->db->where('comment_creator_id' , $get_events_user);
	   	
	$CI->db->order_by('comment_date_create', $options['order']);
	$CI->db->limit($options['events_count']);	

	$query = $CI->db->get('dcomments');

	if ($query->num_rows() > 0)
	{
		$comments = $query->result_array();
		$dialog_options['what_coment_out'] = 'profile'; // чтобы ф-я знала, что выводим коммент через плагин profile

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
	         
			$profiles_events[$comment['comment_date_create']] = array(
			  $comment['comment_date_create'], // 0-Дата
			  $comment['comment_creator_id'],   // 1-Пользователь (автор события)
			  $comment['comment_content'],  // 2-контент события
			  $dialog_options['goto_slug'] . '/disc/' .$comment['comment_discussion_id'] . '/comm/' . $comment['comment_id'], //  3-ссылка события			  
			  $comment['discussion_title'],    // 4-заголовок элемента
			  false,//$comment['discussion_creator_id'],      // 5-автор элемента
			  $key_element,  //  6-id вида элемента
			);
		}
	}


?>