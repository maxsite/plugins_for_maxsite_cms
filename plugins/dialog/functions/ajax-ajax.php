<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// обработчик аякс запросов действий над комментами

	$return = array(
		'error_code' => 1,
		'error_description' => 'Неверные данные',
		'resp' => '0',
	);
	
	if ( $post = mso_check_post(array('type' , 'u_id')) )
	{
	     // нам понаобятся опции
	     $options = mso_get_option('dialog', 'plugins', array());
       $plugin_dir = getinfo('plugins_dir') . 'dialog/';
       require($plugin_dir . 'plugin_options_default.php');
       
       $template_dir = $plugin_dir . 'templates/' . $options['template'] . '/';
       $template_default_dir = $plugin_dir . 'templates/default/';
	    
	     // подключим файл интерпритации сообщений 
       $fn = 'info_messages.php';
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
        else 
          require($template_default_dir . $fn);       
          
       // добавим сообщения в опции 
       $options = array_merge($options , $options_messages);
 	    
	     global $_COOKIE, $_SESSION;
	     global $MSO;
	     
	     $CI = & get_instance();
	       
	   // проверим - кто посылает
	   // чтобы u_id было как в сессии
	   if (isset($MSO->data['session']['comuser']) and ($comuser = $MSO->data['session']['comuser']) ) $comuser_id = $comuser['comusers_id'];
		 else $comuser_id = 0;	       

	  
	   if ($comuser_id != $post['u_id'])  
	   {
	     $return['error_description'] = 'Ошибка сессии';
	   }
	     
	
	   elseif ( ($post['type'] == 'danke') and is_numeric($post['c_id']) and is_numeric($post['u_id']) and is_numeric($post['a_id']) )
	   {

 		       
	  // проверим - сколько благодарностей у этого коммента
	       
	  $CI->db->select('gud_autor_id');
	  $CI->db->where('gud_comment_id', $post['c_id']);
	  $query = $CI->db->get('dgud');
	  if ($query->num_rows() > 0) 
	  {
	      $result = $query->result_array();
	      $count = count($result);
	  }  
	  else $count = 0;
 
	       $ins_data = array(
                'gud_comment_id' => $post['c_id'],
                'gud_user_id' => $post['u_id'],
                'gud_autor_id' => $post['a_id'],
                'gud_date' => time()
	           );  		
			  $res = ($CI->db->insert('dgud', $ins_data)) ? '1' : '0';
	

 
		    if ($res)
		    {
		    
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Спасибо';
          $return['count'] = $count + 1;	
				  
				  // обновим инфу в профайле
	        $CI->db->select('*');
	        $CI->db->where('gud_autor_id', $post['a_id']);
	        $query = $CI->db->get('dgud');
	        if ($query->num_rows() > 0) 
	        {
	          $result = $query->result_array();
	      
	          //обновляем
	          $upd_data = array('profile_dankes'=>count($result));
	          $CI->db->where('profile_user_id', $post['a_id']);
		        $res = ($CI->db->update('dprofiles', $upd_data)) ? '1' : '0';
            // if ($res)  echo '<div class="update">Роль пользователя номер ' . $f_id . ' изменена.</div>';
            // else echo '<div class="error">' .  'Ошибка изменения' . '</div>';	
   	      
	        } 

		    }  
		    else
		       $return['error_description'] = 'Ошибка БД';
		              	         

	   }


	   elseif ( ($post['type'] == 'vote_plus') and is_numeric($post['c_id']) and is_numeric($post['u_id']) and is_numeric($post['a_id']) )
	   {
	      // проверим - сколько "Хорошо" у этого коммента
	       
	      $CI->db->select('vote_user_id');
	      $CI->db->where('vote_comment_id', $post['c_id']);
	      $CI->db->where('vote', '1');
	      $query = $CI->db->get('dvotes');
	      if ($query->num_rows() > 0) 
	      {
	        $result = $query->result_array();
	        $count = count($result);
	      }  
	      else $count = 0;
 
	      $ins_data = array(
                'vote_comment_id' => $post['c_id'],
                'vote_user_id' => $post['u_id'],
                'vote_autor_id' => $post['a_id'],
                'vote' => '1',
                'vote_date' => time()
	           );  		
			  $res = ($CI->db->insert('dvotes', $ins_data)) ? '1' : '0';
	
		    if ($res)
		    {
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Хорошо';
          $return['count'] = $count + 1;	
				  
				  // обновим инфу в комменте
	        $CI->db->select('*');
	        $CI->db->where('vote_comment_id', $post['c_id']);
	        $query = $CI->db->get('dvotes');
	        if ($query->num_rows() > 0) 
	        {
	          $result = $query->result_array();
	          // считаем рейтинг
	          $rate = 0;
	          foreach ($result as $cur) 
              if ($cur['vote'] == '1') $rate = $rate+1; else $rate = $rate-1;
	          //обновляем
	          $upd_data = array('comment_rate'=>$rate);
	          $CI->db->where('comment_id', $post['c_id']);
		        $res = ($CI->db->update('dcomments', $upd_data)) ? '1' : '0';
	
	        } 

		    }  
		    else
		       $return['error_description'] = 'Ошибка БД';
		              	         

	   }
	
	   elseif ( ($post['type'] == 'vote_minus') and is_numeric($post['c_id']) and is_numeric($post['u_id']) and is_numeric($post['a_id']) )
	   {
	      // проверим - сколько "Хорошо" у этого коммента
	       
	      $CI->db->select('vote_user_id');
	      $CI->db->where('vote_comment_id', $post['c_id']);
	      $CI->db->where('vote', '1');
	      $query = $CI->db->get('dvotes');
	      if ($query->num_rows() > 0) 
	      {
	        $result = $query->result_array();
	        $count = count($result);
	      }  
	      else $count = 0;
 
	      $ins_data = array(
                'vote_comment_id' => $post['c_id'],
                'vote_user_id' => $post['u_id'],
                'vote_autor_id' => $post['a_id'],
                'vote' => '0',
                'vote_date' => time()
	           );  		
			  $res = ($CI->db->insert('dvotes', $ins_data)) ? '1' : '0';
	
		    if ($res)
		    {
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Плохо';
          $return['count'] = $count + 1;	
				  
				  // обновим инфу в комменте
	        $CI->db->select('*');
	        $CI->db->where('vote_comment_id', $post['c_id']);
	        $query = $CI->db->get('dvotes');
	        if ($query->num_rows() > 0) 
	        {
	          $result = $query->result_array();
	          // считаем рейтинг
	          $rate = 0;
	          foreach ($result as $cur) 
              if ($cur['vote'] == '1') $rate = $rate+1; else $rate = $rate-1;
	          //обновляем
	          $upd_data = array('comment_rate'=>$rate);
	          $CI->db->where('comment_id', $post['c_id']);
		        $res = ($CI->db->update('dcomments', $upd_data)) ? '1' : '0';
	
	        } 

		    }  
		    else
		       $return['error_description'] = 'Ошибка БД';
		              	         

	   }	
	
	   	
	   elseif ( ($post['type'] == 'bad') and is_numeric($post['c_id']) and is_numeric($post['u_id']) and is_numeric($post['a_id']) )
	   {
	       // проверим - сколько жалоб у этого коммента
	       $found=false;
	       $CI->db->select('*');
	       $CI->db->where('bad_comment_id', $post['c_id']);
	       $query = $CI->db->get('dbad');
	       if ($query->num_rows() > 0) 
	       {
	          $result = $query->result_array();
	          $count = count($result);
	          // может этот пользователь уже жаловался на этот коммент
	          foreach ($result as $cur_bad) 
	            if ($cur_bad['bad_user_id'] == $post['u_id']) {$found=$cur_bad; break;}
	       }  
	       else $count = 0;

         if ($found) // если нашли
         {
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Уже жаловались: ' . date('Y-m-d' , $found['bad_date']);
          $return['count'] = 'Жалоб: ' . $count;     
	        echo json_encode($return);	
	        return;
         }
         
	       $ins_data = array(
                'bad_comment_id' => $post['c_id'],
                'bad_user_id' => $post['u_id'],
                'bad_date' => time(),
                'bad_result' => '0'
	           );  		
			  $res = ($CI->db->insert('dbad', $ins_data)) ? '1' : '0';
		    if ($res)
		    {
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Отправлено';
          $return['count'] = 'Жалоб: ' . ($count + 1);	
				  
          // тут бы отослать жалобу, если накопилось	         
		    }  
		    else
		       $return['error_description'] = 'Ошибка БД';

	   }  
	   
	  
	   elseif ( ($post['type'] == 'spam') and is_numeric($post['c_id']) and is_numeric($post['u_id']) )
	   {
	      $return['error_description'] = 'DB Error';
	      
	      $upd_data = array('comment_spam'=>'1', 'comment_check'=>'1');
	      $CI->db->where('comment_id', $post['c_id']);
		    $res = ($CI->db->update('dcomments', $upd_data)) ? '1' : '0';	 
		    if ($res)  
		    {  
	         $ins_data = array(
                'log_comment_id' => $post['c_id'],
                'log_user_id' => $post['u_id'],
                'log_value' => 3, // 1-разрешить , 2-запретить, 3-спам, 4-не спам, 5-удалить, 6-восстановить
                'log_date' => time()
	           );  		
			    $res = ($CI->db->insert('dlog', $ins_data)) ? '1' : '0';
				  
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Отправлено';
        }
	   }	   

	   elseif ( ($post['type'] == 'not_spam') and is_numeric($post['c_id']) and is_numeric($post['u_id']) )
	   {
	      $return['error_description'] = 'DB Error';
	      
	      $upd_data = array('comment_spam'=>'0', 'comment_check'=>'1');
	      $CI->db->where('comment_id', $post['c_id']);
		    $res = ($CI->db->update('dcomments', $upd_data)) ? '1' : '0';	 
		    if ($res)  
		    {  
	         $ins_data = array(
                'log_comment_id' => $post['c_id'],
                'log_user_id' => $post['u_id'],
                'log_value' => 4, // 1-разрешить , 2-запретить, 3-спам, 4-не спам, 5-удалить, 6-восстановить
                'log_date' => time()
	           );  		
			    $res = ($CI->db->insert('dlog', $ins_data)) ? '1' : '0';
				  
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Отправлено';
        }
	   }	
	   
	   elseif ( ($post['type'] == 'approved') and is_numeric($post['c_id']) and is_numeric($post['u_id']) )
	   {
	      $return['error_description'] = 'DB Error';
	      
	      $upd_data = array('comment_approved'=>'1');
	      $CI->db->where('comment_id', $post['c_id']);
		    $res = ($CI->db->update('dcomments', $upd_data)) ? '1' : '0';	 
		    if ($res)  
		    {  
	         $ins_data = array(
                'log_comment_id' => $post['c_id'],
                'log_user_id' => $post['u_id'],
                'log_value' => 1, // 1-разрешить , 2-запретить, 3-спам, 4-не спам, 5-удалить, 6-восстановить
                'log_date' => time()
	           );  		
			    $res = ($CI->db->insert('dlog', $ins_data)) ? '1' : '0';
				  
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Отправлено';
        }
	   }
	   
	   elseif ( ($post['type'] == 'not_approved') and is_numeric($post['c_id']) and is_numeric($post['u_id']) )
	   {
	      $return['error_description'] = 'DB Error';
	      
	      $upd_data = array('comment_approved'=>'0');
	      $CI->db->where('comment_id', $post['c_id']);
		    $res = ($CI->db->update('dcomments', $upd_data)) ? '1' : '0';	 
		    if ($res)  
		    {  
	         $ins_data = array(
                'log_comment_id' => $post['c_id'],
                'log_user_id' => $post['u_id'],
                'log_value' => 2, // 1-разрешить , 2-запретить, 3-спам, 4-не спам, 5-удалить, 6-восстановить
                'log_date' => time()
	           );  		
			    $res = ($CI->db->insert('dlog', $ins_data)) ? '1' : '0';
				  
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Отправлено';
        }
	   }	 
	   
	   elseif ( ($post['type'] == 'deleted') and is_numeric($post['c_id']) and is_numeric($post['u_id']) )
	   {
	      $return['error_description'] = 'DB Error';
	      
	      $upd_data = array('comment_deleted'=>'1');
	      $CI->db->where('comment_id', $post['c_id']);
		    $res = ($CI->db->update('dcomments', $upd_data)) ? '1' : '0';	 
		    if ($res)  
		    {  
	         $ins_data = array(
                'log_comment_id' => $post['c_id'],
                'log_user_id' => $post['u_id'],
                'log_value' => 5, // 1-разрешить , 2-запретить, 3-спам, 4-не спам, 5-удалить, 6-восстановить
                'log_date' => time()
	           );  		
			    $res = ($CI->db->insert('dlog', $ins_data)) ? '1' : '0';
				  
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Отправлено';
        }
	   }		   
	   elseif ( ($post['type'] == 'ban') and is_numeric($post['c_id']) and is_numeric($post['u_id']) and is_numeric($post['a_id']))
	   {
	      $return['error_description'] = 'DB Error';
	      
	      $upd_data = array('profile_spam_check'=>'1','profile_moderate'=>'1');
	      $CI->db->where('profile_user_id', $post['a_id']);
		    $res = ($CI->db->update('dprofiles', $upd_data)) ? '1' : '0';	 
		    if ($res)  
		    {  
	         $ins_data = array(
                'log_comment_id' => $post['c_id'],
                'log_user_id' => $post['u_id'],
                'log_value' => 11, // 1-разрешить , 2-запретить, 3-спам, 4-не спам, 5-удалить, 6-восстановить
                'log_date' => time()
	           );  		
			    $res = ($CI->db->insert('dlog', $ins_data)) ? '1' : '0';
				  
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Забанен';
        }
	   }
	   elseif ( ($post['type'] == 'not_deleted') and is_numeric($post['c_id']) and is_numeric($post['u_id']) )
	   {
	      $return['error_description'] = 'DB Error';
	      
	      $upd_data = array('comment_deleted'=>'0');
	      $CI->db->where('comment_id', $post['c_id']);
		    $res = ($CI->db->update('dcomments', $upd_data)) ? '1' : '0';	 
		    if ($res)  
		    {  
	         $ins_data = array(
                'log_comment_id' => $post['c_id'],
                'log_user_id' => $post['u_id'],
                'log_value' => 6, // 1-разрешить , 2-запретить, 3-спам, 4-не спам, 5-удалить, 6-восстановить
                'log_date' => time()
	           );  		
			    $res = ($CI->db->insert('dlog', $ins_data)) ? '1' : '0';
				  
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Отправлено';
        }
	   }		
	   
	   
	   elseif ( ($post['type'] == 'flud') and is_numeric($post['c_id']) and is_numeric($post['u_id']) )
	   {
	      $return['error_description'] = 'DB Error';
	      
	      $upd_data = array('comment_flud'=>'0');
	      $CI->db->where('comment_id', $post['c_id']);
		    $res = ($CI->db->update('dcomments', $upd_data)) ? '1' : '0';	 
		    if ($res)  
		    {  
	         $ins_data = array(
                'log_comment_id' => $post['c_id'],
                'log_user_id' => $post['u_id'],
                'log_value' => 7, // 1-разрешить , 2-запретить, 3-спам, 4-не спам, 5-удалить, 6-восстановить, 7-флуд, 8-не флуд
                'log_date' => time()
	           );  		
			    $res = ($CI->db->insert('dlog', $ins_data)) ? '1' : '0';
				  
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Отправлено';
        }
	   }


	   elseif ( ($post['type'] == 'not_flud') and is_numeric($post['c_id']) and is_numeric($post['u_id']) )
	   {
	      $return['error_description'] = 'DB Error';
	      
	      $upd_data = array('comment_flud'=>'1');
	      $CI->db->where('comment_id', $post['c_id']);
		    $res = ($CI->db->update('dcomments', $upd_data)) ? '1' : '0';	 
		    if ($res)  
		    {  
	         $ins_data = array(
                'log_comment_id' => $post['c_id'],
                'log_user_id' => $post['u_id'],
                'log_value' => 8, // 1-разрешить , 2-запретить, 3-спам, 4-не спам, 5-удалить, 6-восстановить, 7-флуд, 8-не флуд
                'log_date' => time()
	           );  		
			    $res = ($CI->db->insert('dlog', $ins_data)) ? '1' : '0';
				  
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Отправлено';
        }
	   }	
	   
	   
	   elseif ( ($post['type'] == 'font') and is_numeric($post['u_id']) )
	   {
	      $return['error_description'] = 'DB Error';
	      
	      $post['size'] = (int) str_replace("px","", $post['size']);
	      
	      $upd_data = array('profile_font_size'=>$post['size']);
	      $CI->db->where('profile_user_id', $post['u_id']);
		    $res = ($CI->db->update('dprofiles', $upd_data)) ? '1' : '0';	 
		    if ($res)  
		    {  
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = 'Отправлено';
        }
	   }	
	  
	  
	  
	   
	   elseif ( ($post['type'] == 'child_disc') and is_numeric($post['c_id']) and is_numeric($post['cat_id']) )
	   {
	   
	      $return['error_description'] = 'DB Error';
      
	      $post['title'] = trim(strip_tags($post['title']));
	      if (!$post['title']) $return['error_description'] = 'Не указан заголовок дискуссии';
	      else
	      {
	        // добавим в БД новую дискуссию
        
          $errors = '';
  
          $discussion_id = 0;
          $user_id = $post['u_id']; 
          $category_id = $post['cat_id'];
  
          $date = time(); //текущая дата

          // проверка существования такой категории
	        if ($category_id)
	        {
	           $CI->db->select('category_id');
	           $CI->db->where('category_id', $category_id);
	           $query = $CI->db->get('dcategorys');
	           if ($query->num_rows() > 0) 
	           {
	              $row = $query->row_array(1);
	              $category_id = $row['category_id'];
	           }  
             else $errors = 'Ошибка номера категории: ' . $category_id;
          }
          
          // проверка коммента и дискуссии на приватность
	        $CI->db->select('comment_creator_id , comment_content , discussion_private , discussion_approved , discussion_title , comment_discussion_id , comusers.comusers_email, profile_psevdonim , profile_allow_info');
	        $CI->db->where('comment_id', $post['c_id']);
	        $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	        $CI->db->join('comusers', 'comusers.comusers_id = dcomments.comment_creator_id');
	        $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dcomments.comment_creator_id');
	        $query = $CI->db->get('dcomments');
	        if ($query->num_rows() > 0) 
	        {
	              $comment = $query->row_array(1);
	              $comment_creator_id = $comment['comment_creator_id'];
	              $discussion_private = $comment['discussion_private'];
	              $discussion_title = $comment['discussion_title'];
	        }  
          else $errors = 'Ошибка номера коммента: ' . $post['c_id'];          
			     

          //если все верно, добавляем
          if (!$errors)
	        {

	           $approved = $comment['discussion_approved']; // предположительно, только проверенные юзеры могут делать ответвления
    
	           $ins_data = array(
                'discussion_creator_id' => $user_id,
                'discussion_category_id' => $category_id,
                'discussion_date_create' => $date,
                'discussion_desc' => '',
                'discussion_approved' => $approved, // модерация
                'discussion_first_comment_id' => 0,
                'discussion_last_comment_id' => 0,
                'discussion_last_user_id' => 0,
                'discussion_active' => '1',
                'discussion_closed' => '0',
                'discussion_private' => $discussion_private, // если исходная дискуссия приватная то и новая тоже
                'discussion_title' => $post['title'],
                'discussion_date_last_active' => '',
                'discussion_comments_count' => 0,
                'discussion_remote_ip' => $_SERVER['REMOTE_ADDR'],
                'discussion_parent_comment_id' => $post['c_id']
	           );  
		         // вставим данные о дискусии
			       $res = ($CI->db->insert('ddiscussions', $ins_data)) ? '1' : '0';

		         if ($res)
		         {
		            $discussion_id = $CI->db->insert_id();
		      
	              // юзер конечно же смотрел добавленную собой дискуссию
	              $ins_data = array(
                   'watch_user_id' => $user_id,
                   'watch_date' => $date,
                   'watch_count' => 1,
                   'watch_discussion_id' => $discussion_id);        
			          $res = ($CI->db->insert('dwatch', $ins_data)) ? '1' : '0';
          
                // может стоит подписать на новую дискуссию всех, кто был подписан на родительскую

                // подписчики на исхоную дискуссию
	              $CI->db->select('dwatch.watch_user_id , comusers.comusers_email , dprofiles.profile_key , dprofiles.profile_psevdonim , dprofiles.profile_psevdonim , dprofiles.profile_allow_info');
	              $CI->db->where('watch_subscribe', '1');
	              $CI->db->where('watch_discussion_id', $discussion_id);
	              $CI->db->join('comusers', 'comusers.comusers_id = dwatch.watch_user_id');
	              $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dwatch.watch_user_id');
	              $query = $CI->db->get('dwatch');
	          
             
	              if ($query->num_rows() > 0) 
	              {
	                $comusers_subscribers = $query->result_array();
	                
                  // рассылаем подписавшимся о ответвлении в дискуссии
	                
	                $from = mso_get_option('admin_email_server', 'general', '');
	
	                $subject = '[' . $options['name'] . '] ' . $options['new_child_disc'] . ' "' . $discussion_title . '"';
	                

	                $message = $options['new_child_on'] . ' "' . $discussion_title . '"' . NR . NR;
	                $message .= $options['from_comment']  . ': ' . NR . mso_xss_clean($comment['comment_content']);
	         
	                $message .= $options['new_child_disc']  . ': ' . $post['title'];
	                $message .= NR . NR . $options['goto_new_discussion'] . ': ' .NR.getinfo('siteurl') . $options['discussion_slug'] . '/' . $discussion_id .  NR;
	                
 
	                $flag_comment_creator_subs = false;
	                // пройдемся по подписчикам исходной дискуссии
	                foreach($comusers_subscribers as $cur_comuser)
	                {
	                   //может комюзер запретил отсылать себе инфо-сообщения
	                   if ($cur_comuser['profile_allow_info'] != '1') continue;
	                   
	                   if ($cur_comuser['watch_user_id'] == $user_id) continue; // сам себе не отсылает
	                   if ($cur_comuser['watch_user_id'] == $comment_creator_id)
	                   {
	                      $flag_comment_creator_subs = true;
	                      continue; // создателю коммента другое сообщеие будет
	                   }
	                   
			               // можно отправлять
			               if (mso_valid_email($cur_comuser['comusers_email']))
			               {
                         // добавим приветствие
                         $message_cur = 'Привет, ' . $cur_comuser['profile_psevdonim'] . NR . NR . NR . $message;
 
				                 // добавим возможность отписаться
				                 $message_cur .= NR . NR . $options['unsubscribe_title'] . NR . getinfo('siteurl') . $options['unsubscribe_slug'] . '/' . $cur_comuser['profile_key'] . '/' . $comment['comment_discussion_id'] .  NR;

				                 $data = array('subscription' => true);  //А здесь для smtp_mail важно знать, чтобы запретить сохранять мыло в файл.
				 
				                 $res = mso_mail($cur_comuser['comusers_email'], $subject, $message_cur, $from, $data);

				                 if (!$res) break; // ошибка отправки почты - рубим цикл
			               }
			                
	                }	
	              
	              }                    


                // отсылаем письмо автору коммента что его коммент породил искуссию
                // отсылаем если он разрешил инфо-сообщения или если подписан на эту дискуссию
                if (mso_valid_email($comment['comusers_email']) and ( ($comment['profile_allow_info'] == '1') or $flag_comment_creator_subs) )
                {
	                 $from = mso_get_option('admin_email_server', 'general', '');
	              
	                 $subject = '[' . $options['name'] . '] ' . $options['new_child_disc_you'] . ' "'. $post['title'] . '"';

	                 $message = 'Привет, ' . $comment['profile_psevdonim'] . NR . NR . NR;
	                 $message .= $options['new_child_on'] . ' "' . $discussion_title . '"' . NR . NR;
	                 $message .= $options['from_comment']  . ': ' . NR . mso_xss_clean($comment['comment_content']);
	                 $message .= $options['new_child_disc']  . ': ' . $post['title'];
	                 $message .= NR . NR . $options['goto_new_discussion'] . ': ' . NR . getinfo('siteurl') . $options['discussion_slug'] . '/' . $discussion_id .  NR;   	           
				           $data = array('subscription' => true);  //А здесь для smtp_mail важно знать, чтобы запретить сохранять мыло в файл.
				 
				           $res = mso_mail($comment['comusers_email'], $subject, $message, $from, $data);                
                }
               
               
                // отсылаем администратору
	              $email = mso_get_option('comments_email', 'general', false); // email куда приходят уведомления
	              if (!$email) $email = mso_get_option('admin_email', 'general', false); // если не задан, отдельный email, то берём email администратора.
	                
	              $subject = '[' . $options['name'] . '] ' . $options['new_child_disc'] . ' "'. $post['title'] . '"';

	              $message = $options['new_child_on'] . ' "' . $discussion_title . '"' . NR . NR;
	              $message .= $options['from_comment']  . ': ' . NR . mso_xss_clean($comment['comment_content']) . NR . NR;   
	              $message .= $options['new_child_disc']  . ': ' . $post['title'] . NR . NR; 
	              $message .= NR . NR . $options['goto_new_discussion'] . ': ' . NR . getinfo('siteurl') . $options['discussion_slug'] . '/' . $discussion_id .  NR;                
	              $message .= 'Autor IP: ' . $_SERVER['REMOTE_ADDR'] . NR;
	              $message .= 'Referer: ' . $_SERVER['HTTP_REFERER'] . NR;
	              $message .= 'Date: ' . date('Y-m-d H:i:s') . NR;

		            $message .= $options['autor'] . ': id=' . $comment_creator_id . NR;

		          //  $message .= $options['nik'] . ': ' . $comuser['profile_psevdonim'] . ', email: ' . $comuser['comusers_email'] . NR;
		            $message .= $options['profile']  . ': ' . getinfo('siteurl') . $options['profile_slug'] . '/' . $user_id . NR;	              
	              
				        $data = array('subscription' => true);  //А здесь для smtp_mail важно знать, чтобы запретить сохранять мыло в файл.
			
				        $res = mso_mail($email, $subject, $message, false, $data);   //А зная о комментарии, он сможет сотворить некоторые бонусы.           
                 
                // конец писем 
               
	    				  $return['error_code'] = 0;
				        $return['error_description'] = '';
				        $new_disc_url = getinfo('siteurl') . $options['discussion_slug'] . '/' . $discussion_id;
				        $return['resp'] = $options['create_child_disc'] . ': <a href="' . $new_disc_url .'" title="' . $options['goto_new_discussion'] . '">' . $post['title'] . '</a>';  
	           }
	           else $errors = 'Ошибка добавления в БД';
	           
	        }
          else $return['error_description'] = $errors;
         
        }
	   	}   
  }

     	   
  	
	if (!$return['error_code'])
	{
	  // сбрасываем кеш
	  mso_flush_cache();
	  $CI->db->cache_delete_all();	  
	}
	
	echo json_encode($return);	


	
?>