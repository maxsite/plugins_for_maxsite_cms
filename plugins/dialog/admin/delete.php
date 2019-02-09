<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

  // опции 
  $options = mso_get_option('dialog' , 'plugins', array());
  require($plugin_dir . 'plugin_options_default.php');
  require($plugin_dir . 'functions/functions.php');
  
	global $MSO;


	$CI = & get_instance();
	$CI->load->library('table');
	$tmpl = array (
				'table_open'		  => '<table class="page tablesorter">',
				'row_alt_start'		  => '<tr class="alt">',
				'cell_alt_start'	  => '<td class="alt">',
		  );
		  
	$CI->table->set_template($tmpl); // шаблон таблицы
	$CI->table->set_heading('ID', '&bull;', t('Текст'));


  // удаление комментариев
	if ( $post = mso_check_post(array('f_session_id', 'f_delete_submit', 'f_check_comments')) )
	{
		mso_checkreferer();
		$f_check_comments = $post['f_check_comments']; // номера отмеченных
		
		// на всякий случай пройдемся по массиву и составим массив из ID
		$arr_ids = array(); // список всех где ON
		foreach ($f_check_comments as $id_com=>$val)
			if ($val) $arr_ids[] = $id_com;
		
		// убедимся что все комменты имеют статус удаленных
	  $CI->db->select('comment_id');  
	  $CI->db->where('comment_deleted', '1');
	  $CI->db->where_in('comment_id', $arr_ids);
    $query = $CI->db->get('dcomments');
		
		// если совпадает кол-во
		if ($query->num_rows() == count($arr_ids))
		{
		  $CI->db->where_in('comment_id', $arr_ids);
		  if ( $CI->db->delete('dcomments') )
		  {
			  mso_flush_cache();
			  echo '<div class="update">' . t('Удалено!') . '</div>';
		  }
		  else 
			  echo '<div class="error">' . t('Ошибка удаления') . '</div>';
		}
		else echo '<div class="error">' . t('Ошибка выбора') . '</div>';	  
	}
	
	// удаление дискуссий
	if ( $post = mso_check_post(array('f_session_id', 'f_delete_discussions_submit', 'f_check_discussions')) )
	{
		mso_checkreferer();
		$f_check_discussions = $post['f_check_discussions']; // номера отмеченных
		
		// на всякий случай пройдемся по массиву и составим массив из ID
		$arr_ids = array(); // список всех где ON
		foreach ($f_check_discussions as $id_com=>$val)
			if ($val) $arr_ids[] = $id_com;
		
		// убедимся что все дискуссии имеют статус запрещенных
	  $CI->db->select('discussion_id');  
	  $CI->db->where('discussion_approved', 0);
	  $CI->db->where_in('discussion_id', $arr_ids);
    $query = $CI->db->get('ddiscussions');		
		// если совпадает кол-во
		if ($query->num_rows() == count($arr_ids)) $err1=false;
		else $err1 = true;

    // убедимся что нет сообщений в удаляемых дискуссиях
	  $CI->db->select('comment_id');  
	  $CI->db->where_in('comment_discussion_id', $arr_ids);
    $query = $CI->db->get('dcomments');		
		if ($query->num_rows() > 0) $err2=true;
		else $err2 = false;
		 
		if (!$err1 and !$err2) 
		{
		  $CI->db->where_in('discussion_id', $arr_ids);
		  if ( $CI->db->delete('ddiscussions') )
		  {
		    $CI->db->where_in('watch_discussion_id', $arr_ids);
		    if ( $CI->db->delete('dwatch') )		  
		  
			  mso_flush_cache();
			  echo '<div class="update">' . t('Удалено!') . '</div>';
		  }
		  else 
			  echo '<div class="error">' . t('Ошибка удаления') . '</div>';
		}
		else echo '<div class="error">' . t('Ошибка выбора дискуссий') . '</div>';	  
	}
	
	
	
	echo '<h3>'. t('Удаление сообщений, имеющих статус deleted'). '</h3></p>';


  
	echo '<form  method="post" class="fform admin_comments">' . mso_form_session('f_session_id');


	// получим сообщения, имеющие статус удаленных
	$CI->db->select('comment_id , comment_discussion_id, comment_date_create , comment_content , ddiscussions.discussion_title , comusers.comusers_nik');  
	$CI->db->where('comment_deleted', 1);
	$CI->db->join('comusers', 'comusers.comusers_id = dcomments.comment_creator_id');
	$CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	
  $query = $CI->db->get('dcomments');		
	
  if ( $query->num_rows() > 0) 
	{		
	   $comments = $query->result_array(); 
	   // выводим комментарии
	    
	   foreach ($comments as $comment)
	   {

	       
			$id = $comment['comment_id'];
			
			// для вывода делаем чекбокс + hidden всех комментов для того, чтобы проверить тех,
			// которые окажутся не отмечены - их POST не передает
			$id_out = '<input type="checkbox" name="f_check_comments[' . $id . ']">' . NR;
			
			$comment_date = date('Y-m-d H:i:s' , $comment['comment_date_create']);
			
			$author = '<span class="comuser">' . $comment['comusers_nik'] . '</span>';
			
			$view_url = $MSO->config['site_url'] . $options['goto_slug'] . '/disc/' . $comment['comment_discussion_id'] . '/comm/' . $comment['comment_id'];
			$discussion_title = '<a target="_blank" href="' . $view_url . '">«' . htmlspecialchars( $comment['discussion_title'] ) . '»</a>';
			
			// определим XSS и визуально выделим такой комментарий
			$comment_content_xss_start = mso_xss_clean($comment['comment_content'], '<span style="color: red">XSS!!! ', '');
			if ($comment_content_xss_start) $comment_content_xss_end = '</span>';
				else $comment_content_xss_end = '';
			
			$comment_content = htmlspecialchars($comment['comment_content']);
			$comment_content = str_replace('&lt;p&gt;', '<br>', $comment_content);
			$comment_content = str_replace('&lt;/p&gt;', '', $comment_content);
			$comment_content = str_replace('&lt;br /&gt;', '<br>', $comment_content);
			
			if (mb_strlen($comment_content, 'UTF-8') > 300)
				$comment_content = mb_substr($comment_content, 0, 300, 'UTF-8') . ' ...';
			
			$out = $comment_content_xss_start 
					// . '<strong>' . $author . '</strong>' . $act . '<br>'
					. $comment_date. ' | ' 
					. $discussion_title 
					. $comment_content_xss_end 
					. '<p>' . $comment_content . '</p>' 
					. NR;
						
			
			$CI->table->add_row($id, $id_out, $out);	       
	       
	    }	
		
     echo '<div class="info">' . t('Выберите сообщения и нажмите удалить.', 'plugins') . '</div>';
		 echo $CI->table->generate();
		 echo '
			<p class="br">' . t('C отмеченными:') . '
			<input type="submit" name="f_delete_submit" onClick="if(confirm(\'' . t('Уверены?') . '\')) {return true;} else {return false;}" value="' . t('Удалить') . '"></p><br>
			';
	}	
	else
	{
		echo '<p>' . t('Нет удаленных сообщений') . '</p>';  
	}		
		
		
		
		
	echo '<h3>'. t('Удаление запрещенных дискуссий, которые фактически не содержат сообщений'). '</h3></p>';
	
	// получим запрещенные дискуссии
	$CI->db->select('discussion_id, discussion_date_create, discussion_title, discussion_comments_count , comusers.comusers_nik , 
	    COUNT(comment_discussion_id) as comments_count_real');  
	$CI->db->where('discussion_approved', 0);
	
	$CI->db->join('comusers', 'comusers.comusers_id = ddiscussions.discussion_creator_id');
	
	$CI->db->join('dcomments', 'ddiscussions.discussion_id = dcomments.comment_discussion_id', 'left');
	$CI->db->group_by('comment_discussion_id');
	
  $query = $CI->db->get('ddiscussions');
  if ( $query->num_rows() > 0) 
	{		
	   $discussions = $query->result_array(); 
	   // выводим дискуссии 
	   $CI->table->clear(); 
	   $CI->table->set_heading('ID', '&bull;', t('Дата'), t('Заголовок'), t('Сообщений'), t('Фактически'));
	    
    echo '<div class="info">' . t('Выберите дискуссии и нажмите удалить.', 'plugins') . '</div>';
    echo '<div class="info">' . t('Удалить можно только те дискуссии, которые не содержат сообщения.', 'plugins') . '</div>';
    
	   
	   foreach ($discussions as $discussion)
	   {
			$id = $discussion['discussion_id'];

			if ($discussion['comments_count_real']) $id_out = '';
			else $id_out = '<input type="checkbox" name="f_check_discussions[' . $id . ']">' . NR;
			
			$discussion_date = date('Y-m-d' , $discussion['discussion_date_create']);
			
			$author = '<span class="comuser">' . $discussion['comusers_nik'] . '</span>';
			
			$view_url = $MSO->config['site_url'] . $options['discussion_slug'] . '/' . $id;
			$discussion_title = '<a target="_blank" href="' . $view_url . '">«' . htmlspecialchars( $discussion['discussion_title'] ) . '»</a>';
			
			$CI->table->add_row($id, $id_out, $discussion_date , $discussion_title , $discussion['discussion_comments_count'] , $discussion['comments_count_real']);	       
     }
     echo $CI->table->generate();
		 echo '
			<p class="br">' . t('C отмеченными:') . '
			<input type="submit" name="f_delete_discussions_submit" onClick="if(confirm(\'' . t('Уверены?') . '\')) {return true;} else {return false;}" value="' . t('Удалить') . '"></p><br>
			';     
	}
	else	echo '<p>' . t('Нет запрещенных дискуссий') . '</p>';  
	
	echo '</form>';		

?>
