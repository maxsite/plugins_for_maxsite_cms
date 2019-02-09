<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

//функция добавляет форум
function dialog_add_forum($forum=array())
{
	 $CI = & get_instance();

  $error = false;
  
  if (!isset($forum['forum_title']) or !$forum['forum_title']) return 'Не указан заголовок форума';
  if (!isset($forum['forum_order'])) $forum['forum_order'] = 0;
  if (!isset($forum['forum_desc'])) $forum['forum_desc'] = '';


	    $ins_data = array(
                'forum_title' => $forum['forum_title'],
                'forum_desc' => $forum['forum_desc'],
                'forum_order' => $forum['forum_order'],
	           );  
		  // вставим данные
			$res = ($CI->db->insert('dforums', $ins_data)) ? '1' : '0';
		  if (!$res) return 'Ошибка добавления в БД';
  mso_flush_cache();
  $CI->db->cache_delete_all();
  return $error;  
}


//функция изменяет форум
function dialog_edit_forum($forum=array())
{
	$CI = & get_instance();

  $error = false;
  
  if (!isset($forum['forum_id']) or !$forum['forum_id']) return 'Не указан Id форума';

	$upd_data = array();
	if (isset($forum['forum_title'])) $upd_data['forum_title'] = $forum['forum_title'];
	if (isset($forum['forum_desc'])) $upd_data['forum_desc'] = $forum['forum_desc'];
	if (isset($forum['forum_order'])) $upd_data['forum_order'] = $forum['forum_order'];

	if ($upd_data)
	{
	   $CI->db->where('forum_id', $forum['forum_id']);
	   $res = ($CI->db->update('dforums', $upd_data)) ? '1' : '0';
	   if (!$res) return 'Ошибка изменения форума в БД';
  }
  else return 'Нет данных для изменения';
  mso_flush_cache();
  $CI->db->cache_delete_all();  
  return $error;  
}



//функция удаляет форум
function dialog_delete_forum($forum_id = 0)
{
	$CI = & get_instance();

  $error = false;
  
  // проверим что нет у форума категорий
	$CI->db->select('category_id');
	$CI->db->where('category_forum_id', $forum_id);
	$query = $CI->db->get('dcategorys');
	if ($query->num_rows() > 0) return 'Форум содержит категории - Удалите сперва их.';

	// удалим  форум
	$CI->db->where('forum_id', $forum_id);
	$res = ($CI->db->delete('dforums')) ? '1' : '0';
	if (!$res) return 'Ошибка удаления форума из БД';
  mso_flush_cache();
  $CI->db->cache_delete_all();
  return $error;  
}


//функция добавляет категорию
function dialog_add_category($category=array())
{
	 $CI = & get_instance();

  $error = '';
  
  if (!isset($category['category_title']) or !$category['category_title']) return 'Не указан заголовок категории';
  if (!isset($category['category_slug']) or !$category['category_slug'])
            $category['category_slug'] = mso_slug($category['category_title']);
  if (!isset($category['category_desc'])) $category['category_desc'] = '';
  if (!isset($category['category_forum_id'])) $category['category_forum_id'] = 0;
  if (!isset($category['category_order'])) $category['category_order'] = 0;

  // проверка существования такого слуга
	$CI->db->select('category_id');
	$CI->db->where('category_slug', $category['category_slug']);
	$query = $CI->db->get('dcategorys');
	if ($query->num_rows() > 0) return'Категория с таким слугом существует: ' . $category['category_slug'];
  else 
  {
	    $ins_data = array(
                'category_title' => $category['category_title'],
                'category_slug' => $category['category_slug'],
                'category_desc' => $category['category_desc'],
                'category_forum_id' => $category['category_forum_id'],
                'category_order' => $category['category_order'],
	           );  
		  // вставим данные
			$res = ($CI->db->insert('dcategorys', $ins_data)) ? '1' : '0';
		  if (!$res) return 'Ошибка добавления в БД';
	}
  mso_flush_cache();
  $CI->db->cache_delete_all();	
  return $error;
}


//функция редактирует категорию
function dialog_edit_category($category=array())
{
	$CI = & get_instance();

  $error = '';
  
  if ( !isset($category['category_id']) and !$category['category_id'] ) return 'Нет Id категории';
  
  // проверка существования такой категории
	$CI->db->select('category_id');
	$CI->db->where('category_id', $category['category_id']);
	$query = $CI->db->get('dcategorys');
	if ($query->num_rows() > 0) 
  {
	    $upd_data = array();
	    if (isset($category['category_title'])) $upd_data['category_title'] = $category['category_title'];
	    if (isset($category['category_slug'])) $upd_data['category_slug'] = $category['category_slug'];
	    if (isset($category['category_desc'])) $upd_data['category_desc'] = $category['category_desc'];
	    if (isset($category['category_forum_id'])) $upd_data['category_forum_id'] = $category['category_forum_id'];
	    if (isset($category['category_order'])) $upd_data['category_order'] = $category['category_order'];
 
		  // вставим данные
		  if ($upd_data)
		  {
	      $CI->db->where('category_id', $category['category_id']);
			  $res = ($CI->db->update('dcategorys', $upd_data)) ? '1' : '0';
		    if (!$res) return 'Ошибка обновления в БД';
		  }
		  else return 'Нет данных для обновления';  
	}
	else return'Категория с таким Id не существует: ' . $category['category_id'];
  mso_flush_cache();
  $CI->db->cache_delete_all();	
  return $error;
}

//функция добавляет пользователей в комнату
function dialog_add_in_room($discussion_id , $users_id , &$options)
{
  if (!$users_id or !is_array($users_id)) return false;

  // получим пользователей, которые уже в комнате у этой дискуссии
  $in_room = dialog_get_id_users_on_room($discussion_id);
  
  // вычтем, чтобы не добавить уже добавленных
  if ($in_room) $users_id = array_diff($users_id , $in_room);
  
	if (!$users_id) return false;
	
	// дефолтные данные
	$def_data = array (
					'room_discussion_id' => $discussion_id,
					'room_date' => time(), //текущая дата
					);
  
  $CI = & get_instance();

	foreach ($users_id as $user_id)
	{
		$ins_data = $def_data;
		$ins_data['room_user_id'] = $user_id;
		$CI->db->insert('drooms', $ins_data);
	}
	
  mso_flush_cache();
  $CI->db->cache_delete_all();	

}


//функция добавляет дискуссию
function dialog_add_discussion($discussion=array() , &$options)
{
	 $CI = & get_instance();

  $errors = array();
  $messages = array();
  
  $discussion_id = 0;
  $user_id = 0; 
  $category_id = 0;
  
  
  $date = time(); //текущая дата

  if (!isset($discussion['discussion_user_id']) or !$discussion['discussion_user_id']) $errors[] = 'Не указан пользователь';

   if ($discussion['room_members']) $discussion['discussion_private'] = '1';  
   else $discussion['discussion_private'] = '0';
  
  
  if (!isset($discussion['discussion_title'])) $discussion['discussion_title'] = '';
  if (!isset($discussion['discussion_desc'])) $discussion['discussion_desc'] = '';

  // дискуссия может не принадлежать категории, но это нужно явно указать
  if (!isset($discussion['discussion_category_id'])) $errors = 'Не указана категория';

	if ($discussion['discussion_category_id'])
	{
	   $id = (int) $discussion['discussion_category_id'];
	   if ( (string) $discussion['discussion_category_id'] != (string) $id ) $id = false; // $comment_discussion_id не число
	   if (!$id) $errors[] = 'Номер категории дискуссии не число';
	   $discussion['discussion_category_id'] = $id; 
  }
  else $discussion['discussion_category_id'] = 0;
  
  if (!$errors)
  {
     $user_profile = dialog_get_profile($discussion['discussion_user_id'] , $options);
     if (!$user_profile) $errors[] = 'Ошибка доступа';
     else
     {
        $user_id = $user_profile['profile_user_id'];
        $moderate = $user_profile['profile_moderate'];     
     }

     // проверка существования такой категории
     // осуществляется если категория указана
	   if ($discussion['discussion_category_id'])
	   {
	     $CI->db->select('category_id');
	     $CI->db->where('category_id', $discussion['discussion_category_id']);
	     $query = $CI->db->get('dcategorys');
	     if ($query->num_rows() > 0) 
	     {
	        $row = $query->row_array(1);
	        $category_id = $row['category_id'];
	     }  
       else $errors[] = 'Ошибка номера категории: ' . $discussion['discussion_category_id'];
     }
  }

  //если все верно, добавляем
  if (!$errors)
	{
	    if ($moderate) $approved = '0'; else $approved = '1'; // если не модерировать то сразу одобряем
	    
	    $ins_data = array(
                'discussion_creator_id' => $user_id,
                'discussion_category_id' => $category_id,
                'discussion_date_create' => $date,
                'discussion_desc' => $discussion['discussion_desc'],
                'discussion_approved' => $approved, // модерация
                'discussion_first_comment_id' => 0,
                'discussion_last_comment_id' => 0,
                'discussion_last_user_id' => 0,
                'discussion_active' => '1',
                'discussion_closed' => '0',
                'discussion_private' => $discussion['discussion_private'],
                'discussion_title' => $discussion['discussion_title'],
                'discussion_date_last_active' => '',
                'discussion_comments_count' => 0,
                'discussion_remote_ip' => $_SERVER['REMOTE_ADDR']
	           );  
		  // вставим данные о дискусии
			$res = ($CI->db->insert('ddiscussions', $ins_data)) ? '1' : '0';
		  if ($res)
		  {
		      $discussion_id = $CI->db->insert_id();
		      
	        // юзер конечно же смотрел добавленную собой тему
	        $ins_data = array(
                'watch_user_id' => $user_id,
                'watch_date' => $date,
                'watch_count' => 1,
                'watch_discussion_id' => $discussion_id);        
			    $res = ($CI->db->insert('dwatch', $ins_data)) ? '1' : '0';
			    if (!$res) $errors[] = 'Не обновлен просмотр';

          // если дискуссия приватна, то нужно добавить члена в комнату
          if ($discussion['room_members'])
              dialog_add_in_room($discussion_id , $discussion['room_members'] , $options);

		      // обновим инфу о юзере
	        $res = dialog_edit_profile(array('profile_user_id' => $user_id) , $options);// обновляем данные пользователя    
	        if ($res['messages']) $messages = $messages + $res['messages'];
	        if ($res['errors']) $errors = $errors + $res['errors'];
	    }
	    else $errors[] = 'Ошибка добавления в БД';
	}
  mso_flush_cache();
  $CI->db->cache_delete_all();
  return array('errors'=>$errors , 'messages'=>$messages , 'discussion_id' => $discussion_id);  

}


//функция добавляет комментарий
// aco
function dialog_add_comment($comment=array(), &$options , &$comuser , &$discussion)
{

	$CI = & get_instance();

  $errors = array();
  $messages = array();
  
  $discussion_id = 0;
  $user_id = 0;
  $comment_id =0;
  $comment_parent_id =0;
  $moderate = true;

  $date = time(); //текущая дата

  if (!isset($comment['comment_content'])) $comment['comment_content'] = '';
  if (!isset($comment['comment_remote_ip'])) $comment['comment_remote_ip'] = '';

  if (!isset($comment['comment_user_id']) or !$comment['comment_user_id']) $errors[] = 'Не указан пользователь';

  if (!$comuser) $errors[] = 'Ошибка доступа';
  else
  {
        $user_id = $comuser['profile_user_id'];
		// не проверяются комменты проверенных пользователей
		// и непроверенных пользователей, имеющих более заданного кол-ва комментариев
		if ($comuser['profile_moderate'] == '1')
        {
           if ($comuser['profile_comments_count'] >= $options['moderate']) $moderate = false; 
		}		   
        else $moderate = false;	
		
  }


  if (!isset($comment['comment_discussion_id'])) $errors[] = 'Не указана дискуссия';
  else
  {
    if (!$discussion)
    {
        //получение дискуссии, если не передана
	      $CI->db->select('discussion_id , discussion_title , discussion_comments_count , discussion_closed , discussion_approved , discussion_private , discussion_creator_id');
	      $CI->db->where('discussion_id', $comment['comment_discussion_id']);
	      $query = $CI->db->get('ddiscussions');
	   
	      if ($query->num_rows() > 0) 
	     	     $discussion = $query->row_array(1);
	     	else $errors[] = 'Неудача с дискуссией.';      
    } 


     // если задан роитель
     if (isset($comment['comment_parent_id']) and $comment['comment_parent_id'])
     { 
        //проверка существования коммента - родителя
	      $CI->db->select('comment_id , comment_discussion_id');
	      $CI->db->where('comment_id', $comment['comment_parent_id']);
	      $query = $CI->db->get('dcomments');
	   
	      if ($query->num_rows() > 0) 
	      {
	     	     $comment_parent = $query->row_array(1);
	     	     $comment_parent_id = $comment['comment_parent_id'];
	     	}     
	     	else $errors[] = 'Нет такого родителя';     
     } 
     else $comment_parent_id = 0;
     

   
	   if ( $discussion and ($comment['comment_discussion_id'] == $discussion['discussion_id']) )
	   {
	     
	     // если дискуссия приватна - проверим можно ли пользователю в нее постить
	     if ($discussion['discussion_private'])
	     {
	       $users_id = dialog_get_id_users_on_room($comment['comment_discussion_id']); // получим массив пользователей, которым доступна дискуссия
	       if (!in_array($user_id , $users_id )) $errors[] = 'Нет дискуссии для вас';
	     }  
	     
	     if (!$errors)
	     {
	       if ($discussion['discussion_closed'] and ($comuser['profile_user_role_id'] != 3)) $errors[] = 'Дискуссия закрыта';
	       elseif (!$discussion['discussion_approved']) 
	       {
	          // если дискуссия не разрешена, но добавляющий коммент - администратор или модератор
	          if ($discussion['discussion_creator_id'] != $user_id) $errors[] = 'Дискуссия закрыта';
	          else $discussion_id = $discussion['discussion_id'];
	       }
	       else $discussion_id = $discussion['discussion_id'];
	     }  
	   }  
     else $errors[] = 'Ошибка номера дискуссии: ' . $comment['comment_discussion_id'];  
  }

  //если все верно, добавляем
  if ($discussion_id and $user_id and !$errors)
	{

	    $ins_data = array(
                'comment_discussion_id' => $discussion_id,
                'comment_creator_id' => $user_id,
                'comment_date_create' => $date,
                'comment_content' => $comment['comment_content'],
                'comment_spam' => '0',
                'comment_parent_id' => $comment_parent_id,
                'comment_flud' => '1',
                'comment_check' => '0',
                'comment_ip' => $comment['comment_ip']	         
	           );  

      if ($discussion['discussion_private'])
           $ins_data['comment_private'] = '1';
      

	    // если добавляет администрантор или модератор - сразу ставим что разрешено, не спам и проверено
	    if ( ($comuser['profile_user_role_id'] == 2) or ($comuser['profile_user_role_id'] == 3))
	    {
	      $ins_data['comment_approved'] = '1';
	      $ins_data['comment_spam'] = '0';
	      $ins_data['comment_check'] = '1';
	    }
	    elseif ($moderate) $ins_data['comment_approved'] = '0'; 
	    else $ins_data['comment_approved'] = '1'; // если не модерировать то сразу одобряем
	    

		  // вставим данные

			$res = ($CI->db->insert('dcomments', $ins_data)) ? '1' : '0';

		  if ($res)
		  {
		      $comment_id = $CI->db->insert_id();

       /*   // добавим ответ ??
          if ($comment_parent_id)
          {
            $res = diaog_add_answer($comment_parent , $comment_id);
          }
*/
          // найдем в комменте все <blokquote id="comment_id">
          // получим массив id цитируемых комментов
          $perelinks_array = dialog_get_perelinks($ins_data['comment_content']);
          if ($perelinks_array )
              // добавим это в таблицу перелинковки
                 dialog_add_perelinks($comment_id , $perelinks_array);
          

		      $CI->session->set_userdata('last_activity_dialog', time());
		      
					// посколько у нас идет редирект, то данные об отправленном комменте
					// сохраняем в сессии номер комментария
					if ( isset($MSO->data['session']) )
					{
							$CI->session->set_userdata(array( 'comments' =>
												array(
							 					// $CI->db->insert_id()=>$comments_page_id
							 					$comment_id
							 					)));
					}		      
		      
		      //обновим инфу о юзере
		      		      
	        $res = dialog_edit_profile(array('profile_user_id' => $user_id) , $options);// обновляем данные пользователя 
	        {  
	          if ($res['messages']) $messages = $messages + $res['messages'];
	          if ($res['errors']) $errors = $errors + $res['errors'];
	        }  
	        
	        	        
	        //обновим инфу о дискуссии
	        $res = dialog_edit_discussion(array('discussion_id' => $discussion_id) , $options);// обновляем данные дискуссии    
	        {  
	          if ($res['messages']) $messages = $messages + $res['messages'];
	          if ($res['errors']) $errors = $errors + $res['errors'];
	        }  

          // передвинем (но не добавим +1) просмотр этой дискуссии автором, который добавляет коммент
          // заодно, подпишем пользователя на эту дискуссию, если это первый коммент
          // и добавим кол-ву комментов в этой дискуссии +1
          $res = dialog_update_wath(array('user_id'=>$user_id , 'discussion_id'=>$discussion_id, 'subscribe'=>true , 'add_comment'=>true));
          // если ошибок нет, функция возвращает false
          if ($res) $errors[] = $res;
	      
		      //заключительные манипуляции
	        mso_flush_cache();
	        $CI->db->cache_delete_all();
	        
			  /*    
	    [comment_date_create]
	    [comment_creator_id]
	    [comment_ip]
	    [comment_id] => id комментария
	    [comments_content] =>  текст комментария
	    [comments_approved] =>  если 0, то отправки нет
	    [comment_discussion_id] => номер дискуссии
	    [discussion_title] => заголовок дискуссии
	    [comment_creator_id] => автор коммента (ему не отсылаем)			 
	    */  	     
	        $ins_data['discussion_title'] =  $discussion['discussion_title'];  
	        $ins_data['comment_id'] = $comment_id;
          dialog_email_message_new_comment($ins_data , $options , $comuser , $perelinks_array);
							        
	    }
	    else $errors[] = 'Ошибка добавления в БД';

	}



  return array('errors'=>$errors , 'messages'=>$messages , 'comment_id' => $comment_id, 'moderate' => $moderate);
}


//функция обновляет коммент
// eco
function dialog_edit_comment($comment=array() , &$options)
{
	 $CI = & get_instance();

  $errors = array();
  $messages = array();
  
  $comment_id = 0;
  $user_id = 0;
  
  
  if (!isset($comment['comment_id']) or !$comment['comment_id']) $errors[] = 'Не указан коммент';
  else
  {
     //проверка существования такого комментария
	   $CI->db->select('dcomments.* , ddiscussions.discussion_title , ddiscussions.discussion_approved');
	   $CI->db->where('comment_id', $comment['comment_id']);
	   $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	   $query = $CI->db->get('dcomments');
	   if ($query->num_rows() > 0) 
	   {
	      $row = $query->row_array(1);
	   }   
     else $errors[] = 'Ошибка номера комментария: ' . $comment['comment_id'];  
  }
  
  // проверим чтобы этот пользователь мог отредактировать коммент
  if (!$comment['comment_user_id']) $errors[] = 'Нужно войти чтобы редактировать';
  else
  {
      // модераторы и администраторы могут редактировать все
      if ( ($comment['role_id'] != 2) and ($comment['role_id'] != 3) )
      {
         // удаленный коммент нельзя редактировать никому, кроме администратора
         if ($row['comment_deleted'])
            if ($comment['comment_user_id'] == $row['comment_creator_id'])
                $errors[] = 'Коммент не разрешен';
            else              
                $errors[] = 'Нет коммента';
                
         // если не разрешен то может редактировать создатель
         if (!$row['comment_approved'] and ($comment['comment_user_id'] != $row['comment_creator_id']))
              $errors[] = 'Коммент не разрешен';
              
         // если дискуссия коммента приватна
         // тоже нужно что-то сделать
      }
  }
  
  
  if (!$errors)
  {
      $upd_data = array();
      $log = array();
      
      if (isset($comment['comment_content']))
      {
		    $upd_data['comment_editor_id'] = $comment['comment_user_id'];
		    $upd_data['comment_date_edit'] = date('Y-m-d h:m:s'); //текущая дата
		    $upd_data['comment_content'] = $comment['comment_content'];
		    $log[] = 9; //  1-разрешить , 2-запретить, 3-спам, 4-не спам, 5-удалить, 6-восстановить, 7-флуд, 8-не флуд, 9-edit, 10-перенесен
	    }
	     
      if (isset($comment['comment_approved']))
      {
		    $upd_data['comment_approved'] = $comment['comment_approved'];
		    if ( ($row['comment_approved'] == '1') and ($comment['comment_approved'] != '1')) $log[] = 2;//запрещаем коммент
		    elseif ( ($row['comment_approved'] != '1') and ($comment['comment_approved'] == '1')) $log[] = 1;//разрешаем коммент
	    }
	    
      if (isset($comment['comment_deleted']))
      {
		     $upd_data['comment_deleted'] = $comment['comment_deleted'];
		     if ($comment['comment_deleted'])
		     {
		        $upd_data['comment_deleter_id'] = $comment['comment_user_id'];
		        $upd_data['comment_date_deleted'] = date('Y-m-d h:m:s'); //текущая дата		    
		     }
		    if ( ($row['comment_deleted'] == '0') and ($comment['comment_deleted'] != '0')) $log[] = 5;//удаляем коммент
		    elseif ( ($row['comment_deleted'] != '0') and ($comment['comment_deleted'] == '0')) $log[] = 6;//восстанавливаем коммент		     
	    }	
	    
      if (isset($comment['comment_spam']))
      {
		     $upd_data['comment_spam'] = $comment['comment_spam'];
		     $upd_data['comment_check'] = '1' ;
		     
		    if ( ($row['comment_spam'] != '0') and ($comment['comment_spam'] == '0')) $log[] = 4;//не спам
		    elseif ( ($row['comment_spam'] == '0') and ($comment['comment_spam'] != '0')) $log[] = 3;//спам		 		     
	    }		        
	    	     
		// теперь отредактируем коммент
		if ($upd_data)
		{
		  // если редактирует простой пользователь - сделаем коммент непроверенным
		  if ( ($comment['role_id'] != 2) and ($comment['role_id'] != 3) ) $upd_data['comment_check'] = '0' ;
		
	    $CI->db->where('comment_id', $row['comment_id']);
			$res = ($CI->db->update('dcomments', $upd_data)) ? '1' : '0';
	    if ($res) 
	    {
	       // заморочка с перелинковками если редактировали контент
	       if (isset($upd_data['comment_content']))
	       {
	         // удалим все цитаты в этом комменте
	         $CI->db->where('perelinks_child_id', $row['comment_id']);
	         $CI->db->delete('dperelinks');
	         
	         // заново найдем в комменте все <blokquote id="comment_id">
           // получим массив id цитируемых комментов
           $perelinks_array = dialog_get_perelinks($upd_data['comment_content']);
           if ($perelinks_array )
              // добавим это в таблицу перелинковки
                 dialog_add_perelinks($row['comment_id'] , $perelinks_array);
          }      
	    
	        // добавим в лог
	        if ($log)
	        {
	         $ins_log_data = array(
                'log_comment_id' => $row['comment_id'],
                'log_user_id' => $comment['comment_user_id'],
                'log_date' => time()
	           ); 	        
	          foreach ($log as $cur_log)
	          {
		          $ins_log_data['log_value'] = $cur_log;
			        $res = ($CI->db->insert('dlog', $ins_log_data)) ? '1' : '0';	          
	          }
	        }
	    
	        // если комментарий сменил $comment['comment_approved'] с 0 на 1 то сменим approved на 1 и дискуссии
	        if (isset($upd_data['comment_approved']) and ($upd_data['comment_approved'] == '1') and ($row['comment_approved'] == '0'))
	        {
	           // разрешим дискуссию коммента, если она запрещена
	           if ($row['discussion_approved'] == '0')
	           {
	               $upd_data = array('discussion_approved' => '1');
	               $CI->db->where('discussion_id', $row['discussion_id']);
			           $res = ($CI->db->update('ddiscussions', $upd_data)) ? '1' : '0';	
			       } 
			      
			      // отошлем как новый коммент подписчикам этой дискуссии 
			  /*    
	    [comment_date_create]
	    [comment_creator_id]
	    [comment_ip]
	    [comment_id] => id комментария
	    [comments_content] =>  текст комментария
	    [comments_approved] =>  если 0, то отправки нет
	    [comment_discussion_id] => номер дискуссии
	    [discussion_title] => заголовок дискуссии
	    [comment_creator_id] => автор коммента (ему не отсылаем)			 
	    */     
	    
            //dialog_email_message_new_comment($par , $options , $comuser);
	        }
	         
	    
		      //обновим инфу о юзере
	        $res = dialog_edit_profile(array('profile_user_id' => $row['comment_creator_id']) , $options);// обновляем данные пользователя 
	        {  
	          if ($res['messages']) $messages = $messages + $res['messages'];
	          if ($res['errors']) $errors = $errors + $res['errors'];
	        }  
	        	        
	        //обновим инфу о дискуссии
	        $res = dialog_edit_discussion(array('discussion_id' => $row['comment_discussion_id']) , $options);// обновляем данные дискуссии    
	        {  
	          if ($res['messages']) $messages = $messages + $res['messages'];
	          if ($res['errors']) $errors = $errors + $res['errors'];
	        }  
	      
	        mso_flush_cache();
	        $CI->db->cache_delete_all();	      
	    }
	    else $errors[] = 'Ошибка обновления комментария в БД';
   }
   else $errors[] = 'Нет данных для изменения';

  }
  	    
	    	   mso_flush_cache();
	        $CI->db->cache_delete_all();	
	        
	        
  return array('errors'=>$errors , 'messages'=>$messages , 'comment_id' => $comment_id);
}



//функция обновляет информацию о дискуссии
function dialog_edit_discussion($discussion, $options)
{
	 $CI = & get_instance();

  $errors = array();
  $messages = array();
  
  $discussion_id = 0;
  $user_id = 0;
  $comment_id =0;
  
  
  
  if (!isset($discussion['discussion_id']) or !$discussion['discussion_id']) $errors[] = 'Не указана дискуссия';
  else
  {
     //проверка существования такой дискуссии
	   $CI->db->select('discussion_id, discussion_first_comment_id, discussion_date_create , discussion_private');
	   $CI->db->where('discussion_id', $discussion['discussion_id']);
	   $query = $CI->db->get('ddiscussions');
	   if ($query->num_rows() > 0) 
	   {
	     $row = $query->row_array(1);
	     $discussion_id = $row['discussion_id'];
	     $discussion_private = $row['discussion_private'];
	     
	     $discussion_first_comment_id = $row['discussion_first_comment_id'];
	   }  
     else $errors[] = 'Ошибка номера дискуссии: ' . $discussion['discussion_id'];  
  }
  
  // проверим чтобы число
  if (isset($discussion['discussion_creator_id']))
  {
	   $id = (int) $discussion['discussion_category_id'];
	   if ( (string) $discussion['discussion_category_id'] != (string) $id ) $id = false; // $comment_discussion_id не число
	   if (!$id) $errors[] = 'Номер категории дискуссии не число'; 
  }
  
   
  if ($discussion_id and !$errors)
  {
      $upd_data = array();
      
		  // пересчитаем кол-во комментов 
		  $par = array('discussion_id' => $discussion_id);
		  if ($discussion_private == 1) $par['and_private'] = true;
		  $upd_data['discussion_comments_count'] = dialog_get_comments_count($par);
		  
  if (isset($discussion['discussion_order']))
  {
   		$id = (int) $discussion['discussion_order'];
		  if ( (string) $discussion['discussion_order'] != (string) $id ) $errors[] = 'Номер сортировки не целое число'; 
		  else $upd_data['discussion_order'] = $id;
  }
		
  if (isset($discussion['discussion_style_id']))
  {
   		$id = (int) $discussion['discussion_style_id'];
		  if ( (string) $discussion['discussion_style_id'] != (string) $id ) $errors[] = 'Номер стиля не целое число'; 
		  else $upd_data['discussion_style_id'] = $id;
  }
  
		  // вычислим последний коммент, последнюю дату и последнего юзера
		  $par = array('discussion_id' => $discussion_id);
		  if ($discussion_private == 1) $par['and_private'] = true;		  
		  $last_comment = dialog_get_last_comment($par);
	    if ($last_comment) 
	    {		
	       $upd_data['discussion_date_last_active'] = $last_comment['comment_date_create']; 
		     $upd_data['discussion_last_comment_id'] = $last_comment['comment_id'];
		     $upd_data['discussion_last_user_id'] = $last_comment['comment_creator_id'];
		  }
		  else // если нет разрешенных комментов
		  {
	       $upd_data['discussion_date_last_active'] = NULL; 
		     $upd_data['discussion_last_comment_id'] = 0;
		     $upd_data['discussion_last_user_id'] = 0;
		  }
		  
		  
		  //может комментарий первый?
		  if ($upd_data['discussion_comments_count'] == 1) $upd_data['discussion_first_comment_id'] = $upd_data['discussion_last_comment_id'];


		  // пересчитаем плотность (актуальность) дискуссии
		  if (!$upd_data['discussion_comments_count']) $upd_data['discussion_p'] = 0;
		  else
		  {
		     $date = time(); //текущая дата
		     $discussion_lenght = ($date - $row['discussion_date_create']) / 86400; // сколько существует дискуссия в днях

		     if ($discussion_lenght < 1) $discussion_lenght = 1;
		     // сколько комментариев в заданном кол-ве дней
		     if ($discussion_lenght) $upd_data['discussion_p'] = $upd_data['discussion_comments_count']/$discussion_lenght;
		     else $upd_data['discussion_p'] = 0;
		  }   
		  
		  
      // данные, явно указанные
      if (isset($discussion['discussion_title'])) $upd_data['discussion_title'] = $discussion['discussion_title'];
      if (isset($discussion['discussion_desc'])) $upd_data['discussion_desc'] = $discussion['discussion_desc'];
      if (isset($discussion['discussion_creator_id'])) $upd_data['discussion_creator_id'] = $discussion['discussion_creator_id'];
      if (isset($discussion['discussion_category_id'])) $upd_data['discussion_category_id'] = $discussion['discussion_category_id'];
      if (isset($discussion['discussion_approved'])) $upd_data['discussion_approved'] = $discussion['discussion_approved'];
      if (isset($discussion['discussion_active'])) $upd_data['discussion_activeesc'] = $discussion['discussion_active'];
      if (isset($discussion['discussion_closed'])) $upd_data['discussion_closed'] = $discussion['discussion_closed'];
      if (isset($discussion['discussion_private'])) $upd_data['discussion_private'] = $discussion['discussion_private'];
      if (isset($discussion['discussion_style_id'])) $upd_data['discussion_style_id'] = $discussion['discussion_style_id'];
      if (isset($discussion['discussion_attributes'])) $upd_data['discussion_attributes'] = $discussion['discussion_attributes'];
      if (isset($discussion['discussion_remote_ip'])) $upd_data['discussion_remote_ip'] = $discussion['discussion_remote_ip'];
      if (isset($discussion['discussion_spam_check'])) $upd_data['discussion_spam_check'] = $discussion['discussion_spam_check'];
      if (isset($discussion['discussion_private'])) $upd_data['discussion_private'] = $discussion['discussion_private'];  

		  // теперь отредактируем дискуссию
	    $CI->db->where('discussion_id', $discussion_id);
			$res = ($CI->db->update('ddiscussions', $upd_data)) ? '1' : '0';
	    if (!$res) $errors[] = 'Ошибка обновления дискуссии в БД';
	    
	    // добавим членов приватной дискуссии
	    if ( ($discussion_private == '1') and isset($discussion['discussion_members']) )
	        dialog_add_in_room($discussion_id , $discussion['discussion_members'] , $options);
	    
	    // отредактируем метки дискуссии
	    if (isset($discussion['discussion_tags']))
	    {
			   // дефолтные данные
			   $def_data = array (
					  'meta_key' => 'tags',
					  'meta_id_obj' => $discussion_id,
					  'meta_table' => 'ddiscussions'
					);

			  // вначале грохнем старые, потом добавим новые
			  $CI->db->where($def_data);
			  $CI->db->delete('dmeta');

			  // получим существующие метки
			  $CI->db->select('meta_id');
			  $CI->db->where($def_data);
			  $query = $CI->db->get('dmeta');

			  if (!$query->num_rows()) // нет меток для этой страницы
			  {	// значит инсерт
				  $tags = mso_explode($discussion['discussion_tags'], false, false); // в массив - не только числа

				  foreach ($tags as $key=>$val)
				  {
					  $ins_data = $def_data;
					  $ins_data['meta_value'] = $val;
					  $CI->db->insert('dmeta', $ins_data);
					  # $CI->db->cache_delete_all();
				  }
			  }	    
	    }
	    
	    mso_flush_cache();
	    $CI->db->cache_delete_all();	
	    
  }
  return array('errors'=>$errors , 'messages'=>$messages , 'discussion_id' => $discussion_id);
}


// редактировать инфу о юзере
function dialog_edit_profile($profile=array() , &$options)
{
	 $CI = & get_instance();

  // выясним - есть ли модерация?
  // это отражено в опциях добавления

  $moderate_limit = $options['moderate'];

  $errors = array();
  $messages = array();
  $user_id = 0;
  $date = time(); //текущая дата
  
  if (!isset($profile['profile_user_id']) or !$profile['profile_user_id']) $errors[] = 'Не указан юзер';
  else 
  {
     $profile = dialog_get_profile($profile['profile_user_id'] , $options);
     if ($profile) $user_id = $profile['profile_user_id'];
     else $errors[] = 'Нет профайла';
  }
  
  // если юзер и его профайл есть
  if ($user_id)
  {
      $upd_data = array();
            
      if (isset($par['profile_new_visit']) and $par['profile_new_visit']) // ЭТО ВИЗИТ?
      {
          $upd_data['profile_count_visit'] = $profile['profile_count_visit'] + 1;
          $upd_data['profile_date_last_visit'] = $date;
      } 
      
      
      // пересчитаем discussions_count
      $upd_data['profile_discussions_count'] = dialog_get_discussions_count(array('user_id' => $user_id)); 
      
      // пересчитаем comments_count
      $upd_data['profile_comments_count'] = dialog_get_comments_count(array('user_id' => $user_id));
      
      // пересчитаем last_comment_id, last_discussion_id и date_last_active
		  $last_comment = dialog_get_last_comment(array('user_id' => $user_id));
	    if ($last_comment) 
	    {		
	       $upd_data['profile_date_last_active'] = $last_comment['comment_date_create']; 
		     $upd_data['profile_last_comment_id'] = $last_comment['comment_id'];
		     $upd_data['profile_last_discussion_id'] = $last_comment['comment_discussion_id'];
		  }
		  else // если нет разрешенных комментов
		  {
	       $upd_data['profile_date_last_active'] = ''; 
		     $upd_data['profile_last_comment_id'] = 0;
		     $upd_data['profile_last_discussion_id'] = 0;
		  }           
      
      // проверим moderate
      //модерируются только первые  $moderate_limit комментарии
      if (!$moderate_limit) $upd_data['profile_moderate'] = '0';
      elseif ($upd_data['profile_comments_count'] >= $moderate_limit) $upd_data['profile_moderate'] = '0';
      else $upd_data['profile_moderate'] = '1';
	  
	  
      // подготовим остальные данные
      $upd_data['profile_rate'] = dialog_calc_profile_rate($profile , $options);

  	  // теперь отредактируем профайл
	    $CI->db->where('profile_user_id', $user_id);
			$res = ($CI->db->update('dprofiles', $upd_data)) ? '1' : '0';
	    if (!$res) $errors = 'Ошибка обновления БД';
	    
	    mso_flush_cache();
	    $CI->db->cache_delete_all();		    
  }
  else $errors[] = 'Ошибка получения профайла';
  
  return array('errors'=>$errors , 'messages'=>$messages , 'user_id' => $user_id);
}



// подготовим новый комментарий к добавлению
function dialog_prepare_new_comment($new_comment = array() , &$options)
{
	
	$options['noword'] = mso_explode($options['noword'] , false , false);	

	// вычищаем от запрещенных тэгов
	if ($options['tags']) 
	{
			$t = $new_comment['comment_content'];
			$t = strip_tags($t, $options['tags']); // теперь оставим только разрешенные тэги
			$new_comment['comment_content'] = $t; // сохраним как текст комментария
	}
		
	// если указано рубить коммент при обнаруженной xss-атаке 
	if ($options['xss_clean_die'] and mso_xss_clean($new_comment['comment_content'], true, false) === true)
	{
		return array('comment_content' =>false , 'errors'=>'Обнаружена XSS-атака!');
	}
			
	if (!trim($new_comment['comment_content'])) 
		return array('comment_content' =>false , 'errors'=>'Ошибка, нет текста!');

	// возможно есть текст, но только из одних html - не пускаем
	if ( !trim(strip_tags(trim($new_comment['comment_content']))) )
		return array('comment_content' =>false , 'errors'=>'Ошибка, нет полезного текста!');
		
	// вычищаем текст от xss
	if ($options['xss_clean'])
	{
		$new_comment['comment_content'] =  mso_xss_clean($new_comment['comment_content']);
		// проставим pre исправление ошибки CodeIgniter
		$new_comment['comment_content'] = str_replace('&lt;/pre>', '</pre>', $new_comment['comment_content']); 
	}	
		
	$new_comment['comment_content'] = mso_hook('new_comments_content', $new_comment['comment_content']);
		
		
		/*
		// провека на спам - проверим через хук new_comments_check_spam
		$comments_check_spam = mso_hook('new_comments_check_spam',
										array(
											'comments_content' => $comments_content,
											'comments_date' => $comments_date,
											'comments_author_ip' => $comments_author_ip,
											'comments_page_id' => $comments_page_id,
											'comments_server' => $_SERVER,
											'comments_parent_id' => $comments_parent_id,
											'comments_author' => (isset($post['comments_author'])) ? $post['comments_author'] : false,
											'comments_email' => (isset($post['comments_email'])) ? $post['comments_email'] : false,
											'comusers_email' => (isset($post['comusers_email'])) ? $post['comusers_email'] : false,
											'comments_user_id' => (isset($post['comments_user_id'])) ? $post['comments_user_id'] : false,
										), false);


		if ($comments_check_spam)
		{
			if (isset($comments_check_spam['check_spam']) and $comments_check_spam['check_spam']==true)
			{
				if ( isset($comments_check_spam['message']) and $comments_check_spam['message'] )
					return '<div class="' . $args['css_error']. '">' . $comments_check_spam['message'] . '</div>';
				else
					return 'Ваш комментарий определен как спам и удален.';
			}
			else
			{
				// спам не определен, но возможно стоит moderation - принудительная модерация
				if (isset($comments_check_spam['moderation'])) $moderation = $comments_check_spam['moderation'];
			}
		}

    */
		

	// проверим есть ли уже такой комментарий
	// проверка по ip и тексту
	if ($options['check_repeat']) 
	{
		$CI = & get_instance();
		$CI->db->select('comment_id');
		 $CI->db->where(array (
		  	'comment_ip' => $new_comment['comment_ip'],
			  'comment_discussion_id' => $new_comment['comment_discussion_id'],
			  'comment_content' => $new_comment['comment_content'],
			  ));
		$query = $CI->db->get('dcomments');
		if ($query->num_rows()) // есть такой коммент
		{
			return array('comment_content' =>false , 'errors'=>'Похоже, вы уже отправили этот комментарий...');
		}
    }

	// проверим время последнего комментария чтобы не очень часто
	if (!dialog_last_activity_comment($options['delta_time'])) 
		return array('comment_content' =>false , 'errors'=>'Слишком частые комментарии.');

    // ура 
    return array('comment_content' =>$new_comment['comment_content'] , 'errors'=>false);
}


// увеличивает просмотры дискуссии пользователем
function dialog_update_wath($par = array())
{
	 if (!isset($par['user_id'])) return 'Не указан пользователь';
	 if (!isset($par['discussion_id'])) return 'Не указана дискуссия';
	 if (!isset($par['subscribe'])) $par['subscribe'] = false;
	 if (!isset($par['add_comment'])) $par['add_comment'] = false;
	 
	 $CI = & get_instance();
	 
	   $CI->db->select('*');
	   $CI->db->where('watch_discussion_id', $par['discussion_id']);
	   $CI->db->where('watch_user_id', $par['user_id']);
	   $query = $CI->db->get('dwatch');
	   if ($query->num_rows() > 0) 
	   {	 
	      $row = $query->row_array(1);
	      $upd_data = array(
             'watch_date' => date('Y-m-d h:m:s'),
             'watch_count' => $row['watch_count']+1
               );
        if ($par['add_comment']) $upd_data['watch_comments_count'] = $row['watch_comments_count']+1;       
        if ($par['subscribe'] and !$row['watch_comments_count']) $ins_data['watch_subscribe'] = '1'; 
                      
        $CI->db->where('watch_user_id', $par['user_id']);
        $CI->db->where('watch_discussion_id', $par['discussion_id']);
			  $res = ($CI->db->update('dwatch', $upd_data)) ? '1' : '0';
			  if (!$res) return 'Не обновлен просмотр';	     
	   }
     else
     {
	      $ins_data = array(
             'watch_user_id' => $par['user_id'],
             'watch_date' => time(),
             'watch_count' => 1,
             'watch_discussion_id' => $par['discussion_id']); 
        if ($par['subscribe']) $ins_data['watch_subscribe'] = '1';       
                    
			  $res = ($CI->db->insert('dwatch', $ins_data)) ? '1' : '0';
			  if (!$res) return 'Не обновлен просмотр';
		 }  
		 
		 return false;  // нет ошибок
}





	/*
	функция отправляет админу сайта уведомление о новом сообщении 
	$data: Array
	(
	    [comment_date_create]
	    [comment_creator_id]
	    [comment_ip]
	    [comment_id] => id комментария
	    [comments_content] =>  текст комментария
	    [comments_approved] =>  если 0, то отправки нет
	    [comment_discussion_id] => номер дискуссии
	    [discussion_title] => заголовок дискуссии
	    [comment_creator_id] => автор коммента (ему не отсылаем)			
  )
	*/
	
// отошлем инфу о комменте администратору и вызовем ф-ю рассылки юзерам
function dialog_email_message_new_comment($data = array() , &$options , &$comuser, &$perelinks_array)
{
	# рассылаем если разрешен комментарий всем, кто на него подписан
	if ($data['comment_approved'] == '1') dialog_email_message_new_comment_subscribe($data, $options, $comuser, $perelinks_array);
	

  // теперь администратору
	if (!$options['admin_email']) return false; // может не указано отсылать админу

	$CI = & get_instance();

	if ($data['comment_approved'] == '0') // нужно промодерировать
		$subject = '[' . $options['name'] . '] ' . '(-) ' . $options['new_comment'] . ' (' . $data['comment_id'] . ') "' . $data['discussion_title'] . '"';
	else
		$subject = '[' . $options['name'] . '] ' . $options['new_comment'] . ' (' . $data['comment_id'] . ') "' . $data['discussion_title'] . '"';

	$text = $options['new_comment_on'] . ' "' . $data['discussion_title'] . '"'. NR ;

	if ($data['comment_approved'] == '0') // нужно промодерировать
	{
		$text .= $options['comment_need_moderate'] . ': ' . NR
			. getinfo('siteurl') . $options['comment_slug'] . '/' . $data['comment_id'] . NR . NR;
	}//////

	$text .= 'Автор IP: ' . $data['comment_ip'] . NR;
	$text .= 'Referer: ' . $_SERVER['HTTP_REFERER'] . NR;
	$text .= 'Дата: ' . date('d-m-Y H:i:s' , $data['comment_date_create']) . NR;

		$text .= $options['autor'] . ': id=' . $data['comment_creator_id'] . NR;

		$text .= $options['nik'] . ': ' . $comuser['profile_psevdonim'] . ', email: ' . $comuser['comusers_email'] . NR;
		$text .= $options['profile']  . ': ' . getinfo('siteurl') . $options['profile_slug'] . '/' . $data['comment_creator_id'] . NR;

	$text .= NR . $options['text']  . ': ' .  NR . $data['comment_content'] . NR;
	
	$text .= NR . NR . $options['goto_discussion'] . NR . getinfo('siteurl') . $options['goto_slug'] . '/disc/' . $data['comment_discussion_id'] . '/comm/' . $data['comment_id'] .  NR;

	$data = array_merge($data, array('comment' => true));      //Чтобы плагин smtp_mail точно знал, что ему подсунули коммент, а не вычислял это по subject
	mso_mail($options['admin_email'], $subject, $text, false, $data);   //А зная о комментарии, он сможет сотворить некоторые бонусы.
}



# рассылаем по email уведомление о новом комментарии
function dialog_email_message_new_comment_subscribe($data , $options, &$comuser, &$perelinks_array)
{
  $r = array(
               'what_coment_out' => 'email', // указываем ф-ии подготовки контента то, что комент для отправки по почте подписчикам
               'comment_creator_id' => $data['comment_creator_id'],
               'email_tags' => $options['email_tags']
             );  
	dialog_comment_to_out($data['comment_content'], $r);// подготавливаем к выводу
	
	
	# Опция не рассылать подписку.
	if (!mso_get_option('allow_comments_subscribe', 'general', 1)) return;

	// комментарий не одобрен, не отсылаем
	if ($data['comment_approved'] == '0') return;
	
	
	// разослать нужно всем комюзерам, которые подписаны на эту дискуссию
	$CI = & get_instance();
	

	$comusers_subscribers = dialog_get_comusers_subscribers($data['comment_discussion_id']); // все подписчики на эту дискуссию
	
	$from = mso_get_option('admin_email_server', 'general', '');
	
	$subject = '[' . $options['name'] . '] ' . $options['new_message'] . ' "'. $data['discussion_title'] . '"';

	$message = $options['new_message'] . ' "' . $data['discussion_title'] . '"' . NR . NR;
	
	$message .= $options['text']  . ': ' . NR . mso_xss_clean($data['comment_content']);
	
	$message .= NR . NR . $options['goto_discussion'] . NR . getinfo('siteurl') . $options['goto_slug'] . '/disc/' . $data['comment_discussion_id'] . '/comm/' . $data['comment_id'] .  NR;

 $parent_comment = false;
	
	// если коммент - ответ, получим id автора коммента-вопроса
  if ($data['comment_parent_id'])
  {
	  $CI->db->select('*');
	  $CI->db->where('comment_id', $data['comment_parent_id']);
	  $query = $CI->db->get('dcomments');
	  if ($query->num_rows() > 0) // если есть 
	    $parent_comment = $query->row_array(1);
  }
  
  // если массив перелинковок не пуст (есть id цитируемых комментов)
  // найдем всех авторов цитируемых комментов
   $quote_comments_autors = array();   
  if ($perelinks_array)
  {
	  $CI->db->select('comment_creator_id , comment_content');
	  $CI->db->where_in('comment_id', $perelinks_array);
	  $query = $CI->db->get('dcomments');
	  if ($query->num_rows() > 0) // если есть цитируемые комменты
	  {
	    $rows = $query->result_array();
	    // переделаем, чтобы ключ - номер пользователя автора цитируемого сообщение
	    foreach ($rows as $row) $quote_comments_autors[$row['comment_creator_id']] = $row['comment_content'];
	  }  

  }  
  
 
if ($comusers_subscribers)	
	foreach($comusers_subscribers as $cur_comuser)
	{
	   if ($cur_comuser['watch_user_id'] == $data['comment_creator_id']) continue; // сам себе не отсылает
	   
	   if ($cur_comuser['profile_allow_subscribe'] != '1') // может пользователь отменил рассылку подписок вообще
	   {
	      if ($cur_comuser['profile_allow_info'] == '1') // и не отменил рассылку инфо-сообщений
	      {
             if (!(($parent_comment and ($parent_comment['comment_creator_id'] == $cur_comuser['watch_user_id'])) or ($quote_comments_autors and isset($quote_comments_autors[$cur_comuser['watch_user_id']])) ) ) continue; 
        }     
	      else continue; // может пользователь отменил рассылку подписок вообще
     }
     
	    // если емайл автора сообщения совпадает с емайлом , куда приходят уведомления - не отсылаем, потому что итак отослали
	   // пока для теста отсылаем.

			// можно отправлять
			if (mso_valid_email($cur_comuser['comusers_email']))
			{

         // добавим приветствие
         $message_cur = 'Привет, ' . $cur_comuser['profile_psevdonim'] . NR . NR;
         $subject_cur = $subject;  
    
	      // может коммент является ответом на сообщение адресата?
	      // если есть родитель и на данной итерации отсылаем автору родителя
	      if ($parent_comment and ($parent_comment['comment_creator_id'] == $cur_comuser['watch_user_id'])) 
	      {
	         // отсылаем что "Вы получили ответ"
	        	$subject_cur = '[' . $options['get_answer'] . '] ' . $options['name'] . ' "'. $data['discussion_title'] . '"';
	        	// добавляем вопрос
	        	$message_cur .= $options['question']  . ': ' . NR . mso_xss_clean($parent_comment['comment_content']) . NR . NR;

        }	
        
        
        // если в комменте есть цитаты и найдены авторы цитируемых комментов
        // если текущий подписчик вхоит в состав цитируемых
        elseif ($quote_comments_autors and isset($quote_comments_autors[$cur_comuser['watch_user_id']]))
        {
	         // отсылаем что "Вас процитировали"
	        	$subject_cur = '[' . $options['get_quote'] . '] ' . $options['name'] . ' "'. $data['discussion_title'] . '"';
	        	// добавляем вопрос
	        	$message_cur .= $options['quote_comment']  . ': ' . NR . mso_xss_clean($quote_comments_autors[$cur_comuser['watch_user_id']]) . NR . NR;     
        }
        
        
        // добавляем общую для всех часть письма
        $message_cur .= NR . $message;

				 // добавим возможность отписаться
				 $message_cur .= NR . NR . $options['unsubscribe_title'] . NR . getinfo('siteurl') . $options['unsubscribe_slug'] . '/' . $cur_comuser['profile_key'] . '/' . $data['comment_discussion_id'] .  NR;


				 $data = array_merge($data, array('subscription' => true));  //А здесь для smtp_mail важно знать, чтобы запретить сохранять мыло в файл.
				 
				 
				 $res = mso_mail($cur_comuser['comusers_email'], $subject_cur, $message_cur, $from, $data);

				if (!$res) break; // ошибка отправки почты - рубим цикл
			}
	}

}

 
// свяжем цитируемый коммент с тем, в котором он цитируется 
function dialog_add_perelinks($comment_id=0 , $perelinks_array=array())
{
  if (!$comment_id or !$perelinks_array) return false;
  $i=0;
  $CI = & get_instance();

	foreach ($perelinks_array as $parent_id)
	{
	  // выясним: есть ли такие комменты вообще
	  $CI->db->select('comment_id');
	  $CI->db->where_in('comment_id', array($parent_id , $comment_id));
	  $query = $CI->db->get('dcomments');
	  if ($query->num_rows() > 1) // если есть оба коммента
	  {
	    // перелинкуем их
		  $ins_data = array (
					'perelinks_parent_id' => $parent_id,
					'perelinks_child_id' => $comment_id,
					'perelinks_date' => time() // а нахуя?
					);
		  $CI->db->insert('dperelinks', $ins_data);
		  $i++;
	  }  
	  // else идите нахуй
  }
  return $i;
} 
 
 
// посчитаем рейтинг профиля
function dialog_calc_profile_rate(&$profile , &$options) 
{
  $rate = 0; 
  
  // общий рейтинг комментариев пользователя
  $rate_votes = 0;
  // получим все голосования за комменты этого пользователя
  $CI = & get_instance();
	$CI->db->select('*');
	$CI->db->where('vote_autor_id', $profile['profile_user_id']);
	$query = $CI->db->get('dvotes');
	if ($query->num_rows() > 0) 
	{
	   $result = $query->result_array(); 
	   foreach ($result as $cur) 
	     if ($cur['vote'] == '1') $rate_votes=$rate_votes+1; else $rate_votes=$rate_votes-1;
  }
  
  $options['rate_func'] = str_replace(array('##comments_count##' , '##dankes_count##' , '##votes##') , array($profile['profile_comments_count'], $profile['profile_dankes'], $rate_votes) , $options['rate_func']);
  eval($options['rate_func']);
  return $rate;
}



?>