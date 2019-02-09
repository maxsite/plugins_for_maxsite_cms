<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// обработчик аякс запросов смены дискуссии коммента

	$return = array(
		'error_code' => 1,
		'error_description' => 'Неверные данные',
		'resp' => '0',
	);
	
	if ( $post = mso_check_post(array('type' , 'u_id' , 'c_id' , 'd_id')) )
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

	   // получим роль пользователя
	   $CI->db->select('profile_user_role_id');
	   $CI->db->where('profile_user_id', $post['u_id']);
	   $query = $CI->db->get('dprofiles');
	   if ($query->num_rows() > 0) 
	   {
	      $profile = $query->row_array(1); 
	      if ( ($profile['profile_user_role_id'] != '2') and ($profile['profile_user_role_id'] != '3') ) $comuser_id = 0;	
	   } 
	   else $comuser_id = 0;	
	  
	  
	   if ( !$comuser_id or ($comuser_id != $post['u_id'])  )
	   {
	     $return['error_description'] = 'Ошибка сессии';
	   }
	     
	     
	   // получить форму выбора новой дискуссии
	   elseif ( ($post['type'] == 'getdisclist') )
	   {
	     //получим все дискуссии
	     // отсортируем
	     
	     $CI->db->select('discussion_id , discussion_title');
	     $CI->db->where('discussion_private', '0');
	     $CI->db->where('discussion_approved', '1');
	     
       $CI->db->order_by('discussion_date_last_active' , 'desc');
            
	     $query = $CI->db->get('ddiscussions');
	     if ($query->num_rows() > 0) 
	     {
	        $result = $query->result_array();
	        
	        // сформируем список дискуссий для выбора
	        
				  $return['error_code'] = 0;
				  $return['error_description'] = '';
				  $return['resp'] = '<p>Выберите дискуссию, в которую перенести сообщение:</p>';
          foreach ($result as $disc)	
          {
            if ($post['d_id'] != $disc['discussion_id'])
              $return['resp'] .= '<p><a href="javascript: void(0);" title="' . $disc['discussion_title'] . '" onclick="javascript:mComm('.$post['c_id'].','.$comuser_id.','.$disc['discussion_id'].');">' . $disc['discussion_title'] . '</a></p>';
          }
		    }  
	   }
	   
	   
	   // переместить коммент
	   elseif ( ($post['type'] == 'commove') /*and is_numeric($post['c_id']) and is_numeric($post['d_id'])*/ )
	   {
	     $return['error_description'] = $post['c_id'] . $post['d_id'];
	     //получим дискуссию 
	     $CI->db->select('discussion_id , discussion_title , discussion_parent_comment_id');
	     //$CI->db->where('discussion_private', '0');
	    // $CI->db->where('discussion_approved', '1');
	     $CI->db->where('discussion_id', $post['d_id']);
	     $query = $CI->db->get('ddiscussions');
	     if ($query->num_rows() > 0) 
	     {
	        $disc = $query->row_array(1);
	        if ($disc['discussion_parent_comment_id'] != $post['c_id'])
	        {
	          // получим коммент и его автора
	          $CI->db->select('comment_id , comment_discussion_id , comment_content , comment_creator_id , comusers_email , profile_psevdonim, profile_allow_info, discussion_title');
	          $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	          $CI->db->join('comusers', 'comusers.comusers_id = dcomments.comment_creator_id');
	          $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dcomments.comment_creator_id');	          
	          $CI->db->where('comment_id', $post['c_id']);
	          $query = $CI->db->get('dcomments');
	          if ($query->num_rows() > 0) 
	          {
	            $comm = $query->row_array(1);	 
	            
	            // подготовим изменения
	            $upd_data = array('comment_discussion_id'=>$post['d_id']);
	            $CI->db->where('comment_id', $post['c_id']);
	            $res = ($CI->db->update('dcomments', $upd_data)) ? '1' : '0';
	            if (!$res) $return['error_code'] = 'Ошибка изменения в БД';
	            
	            else
	            {
	               // обновим инфу о дискуссях
	               
	               require($plugin_dir . 'functions/modify_db.php');
	               require($plugin_dir . 'functions/access_db.php');
	               
	               dialog_edit_discussion(array('discussion_id' => $disc['discussion_id']) , $options);	
	               dialog_edit_discussion(array('discussion_id' => $comm['comment_discussion_id']) , $options);	
	                           
	               
	               // добавим в log
	               $ins_log_data = array(
                  'log_comment_id' => $post['c_id'],
                  'log_user_id' => $post['u_id'],
                  'log_date' => time(),
                  'log_value' => 10
	               ); 	        
			          $res = ($CI->db->insert('dlog', $ins_log_data)) ? '1' : '0';	          
	          	               
	              // информируем автора коммента о его переносе
                // если он не запретил инфосообщения
                if (mso_valid_email($comm['comusers_email']) and ($comm['profile_allow_info'] == '1') )
                {
	                 $from = mso_get_option('admin_email_server', 'general', '');
	              
	                 $subject = '[' . $options['name'] . '] ' . 'Ваше сообщение перенесено в' . ' "'. $disc['discussion_title'] . '"';

	                 $message = 'Привет, ' . $comm['profile_psevdonim'] . NR . NR . NR;
	                 $message .= 'C целью улучшения навигации на форуме ваше сообщение перенесено в другую дискуссию.' . NR;
	                 $message .= 'Прежняя дискуссия:' . ' "' . $comm['discussion_title'] . '"' . NR . NR;
	                 $message .= 'Новая дискуссия:' . ' "' . $disc['discussion_title'] . '"' . NR . NR;
	                 $message .= 'Текст комментария'  . ': ' . NR . mso_xss_clean($comm['comment_content']);
	                 $message .= NR . NR . $options['goto_new_discussion'] . ': ' . NR . getinfo('siteurl') . $options['discussion_slug'] . '/' . $post['d_id'] .  NR;   	           
				           $data = array('subscription' => true);  //А здесь для smtp_mail важно знать, чтобы запретить сохранять мыло в файл.
				 
				           $res = mso_mail($comm['comusers_email'], $subject, $message, $from, $data);                
                }	              
	              
				        $return['error_code'] = 0;
				        $return['error_description'] = '';
				        $return['resp'] = 'Коммент перенесен. <a href="'  . getinfo('siteurl') . $options['goto_slug'] . '/disc/' . $post['d_id'] . '/comm/' . $post['c_id'] . '" title="' . $disc['discussion_title'] . '">Перейти к комментарию в новом месте.</a>';
				      }  
				    } 
				    else $return['error_description'] = 'Нет коммента с таким номером.'; 
          }
          else $return['error_description'] = 'Нельзя перенести коммент в дочернюю ему дискуссию.';   
		   }  
		   else $return['error_description'] = 'Нет дискуссии с таким номером.';
		   
	   }	   

  }

	
	echo json_encode($return);	


	
?>