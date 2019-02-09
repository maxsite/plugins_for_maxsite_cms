<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
/*
в файле Функции для работы с масивами данных (для плагина форума dialog)
*/

// возвращает профайл (если нет профайла - создает новый)
// тодо нужно из нее вынести добавление профайла в dialog_add_profile()
function dialog_get_profile($user_id = 0 , &$options) // $options нужно для дефолтных значений при создании нового профиля
{
	  $CI = & get_instance();

    $profile_user_id = 0;
    $profile = array();
    
    if (!$user_id) return false;
   
     //проверка существования такого профайла
	  $CI->db->select('dprofiles.* , comusers.*');
	  $CI->db->where('profile_user_id', $user_id);
	  $CI->db->join('comusers', 'comusers.comusers_id = dprofiles.profile_user_id');
	  $query = $CI->db->get('dprofiles');
	  if ($query->num_rows() > 0) 
	  {
	     $profile = $query->row_array(1);
	  }  
    else // создадим профайл
    {
       // сперва проверим комюзера, к которому присоединяется профайл
	     $CI->db->select('comusers_id , comusers_nik , comusers_email');
	     $CI->db->where('comusers_id', $user_id);
	     $query = $CI->db->get('comusers');
	     if ($query->num_rows() > 0) 
	     {
	        $row = $query->row_array(1);
	        $user_id = $row['comusers_id'];
	        $user_nik = $row['comusers_nik'];
	     }  
       else return false;    
    
       // если указан лимит модерации
       if (!$options['moderate']) $moderate = '0';
       else $moderate = '1'; 

       $date = time(); //текущая дата

       $profile = array(
            'profile_user_id' => $user_id,
            'profile_style_id' => 1,
            'profile_psevdonim' => $user_nik,
            'profile_attributes' => '',
            'profile_count_visit' => 0,
            'profile_discussions_count' => 0,
            'profile_comments_count' => 0,
            'profile_date_first_visit' => $date,
            'profile_date_last_active' => '',
            'profile_remote_ip' => $_SERVER['REMOTE_ADDR'],
            'profile_last_discussion_id' => 0,
            'profile_last_comment_id' => 0,            
            'profile_user_role_id' => 1,
            'profile_user_style_id' => 0,
            'profile_rate' => 0,
            'profile_podpis' => '',
            'profile_spam_check' => '0',
            'profile_allow_msg' => '1',
            'profile_allow_info' => '1',
            'profile_allow_subscribe' => '1',
            'profile_allow_msg' => '1',
            
            'profile_moderate' => '1',
            'profile_key' => mso_md5(rand()),
            'profile_key_visit' => 0,   
            'profile_vid' => '1',
            'profile_font_size' => 12,
            'profile_comments_on_page' => $options['comments_on_page'], 
            'profile_all_time' => 0,   
            'profile_dankes' => 0,   
            'profile_twitter' => '',   
            // profile_css
             ); 

		     
			$res = ($CI->db->insert('dprofiles', $profile)) ? '1' : '0';
      if (!$res) $profile = false;
      else
      {
        $profile['comusers_nik'] = $user_nik;
        $profile['comusers_id'] = $user_id;
      }
      
    }
   
   return $profile;
}




// возвращает данные залогиненного пользователя
// --- и перемещает дату последнего визита этим пользователем
function dialog_get_login_profile(&$options)
{
  $comuser = is_login_comuser();
  if ($comuser)
  {
    $comuser_id = $comuser['comusers_id'];
    
    // профиль может понадобиться
    global $comuser_profile;
    
    $comuser_profile = dialog_get_profile($comuser_id , $options);
    
    $comuser = array_merge($comuser , $comuser_profile);
    
    return $comuser;
  }
  else return false;
}

//проверяет пользователя по email и паролю
function dialog_get_comuser($par = array())
{
	$CI = & get_instance();

  $comusers_id = false;

  if (!isset($par['comment_email']) or !$par['comment_email']) return false;
  
	$CI->db->select('comusers_id, comusers_password');
	$CI->db->where('comusers_email', $par['comment_email']);
	$query = $CI->db->get('comusers');
	if ($query->num_rows()) // есть такой комюзер
	{
			$row = $query->row_array(1);

			// пароль не нужно шифровать mso_md5
			if (isset($par['comment_password_md']) and $par['comment_password_md'])
			{
					if ($row['comusers_password'] != $par['comment_password']) // пароль неверный
					  return false;
			}
			else
			{
					if ($row['comusers_password'] != mso_md5($par['comment_password'])) // пароль неверный
						return false;
			}

			$comusers_id = $row['comusers_id']; // получаем номер комюзера
	}

  return $comusers_id;
}


// возвращает номера пользователей, у которых есть доступ к приватной дискуссии
function dialog_get_id_users_on_room($discussion_id=0)
{
  $CI = & get_instance();
  $users_id = array();
  if (!$discussion_id) return $users_id;

	$CI->db->select('discussion_id , discussion_creator_id , discussion_private');
	$CI->db->where('discussion_id', $discussion_id);
	$query = $CI->db->get('ddiscussions');
	if ($query->num_rows() > 0) 
	{
	     $row = $query->row_array(1);
	     // если дискуссия не приватная - функция не имеет смысла
	     if (!$row['discussion_private']) return $users_id;
	}  
  else return $users_id;    
  
  $users_id[] = $row['discussion_creator_id'];
  
	$CI->db->select('room_user_id');
	$CI->db->where('room_discussion_id', $discussion_id);
	$query = $CI->db->get('drooms');
	if ($query->num_rows() > 0) 
	{
	   $users = $query->result_array();
	   if ($users)
	     foreach ($users as $user) $users_id[] = $user['room_user_id'];
	}   
  
  return $users_id;
}


// получить последний комментарий
function dialog_get_last_comment($par=array())
{
	 $CI = & get_instance();

   $CI->db->select('dcomments.* , ddiscussions.* , dprofiles.* , comusers.*');

	 if (isset($par['user_id']) and $par['user_id']) 
	    $CI->db->where('comment_creator_id', $par['user_id']);
	    
	 if (isset($par['discussion_id']) and $par['discussion_id']) 
	    $CI->db->where('comment_discussion_id', $par['discussion_id']);	    
	    

	  $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	  $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dcomments.comment_creator_id');
	  $CI->db->join('comusers', 'comusers.comusers_id = dcomments.comment_creator_id');
	    
	 if (!isset($par['and_deleted']) or !$par['and_deleted']) 
	    $CI->db->where('comment_deleted', '0'); 

	 if (!isset($par['and_spam_check']) or !$par['and_spam_check']) 
	    $CI->db->where('dprofiles.profile_spam_check', '0'); 
	
		   
   // проверим чтобы не учесть приватные дискуссии
	 if (!isset($par['and_private']) or !$par['and_private']) 
	    $CI->db->where('discussion_private', '0');


	 if (!isset($par['and_not_approved']) or !$par['and_not_approved']) //возвращаем только разрешенные и из разрешенных дискуссий
	 {
	    $CI->db->where('discussion_approved', '1');
   	    $CI->db->where('comment_approved', '1');
	 }
	 
	 

	  $CI->db->order_by('comment_date_create', 'desc');
	  $CI->db->limit(1);
	  	  
	  $query = $CI->db->get('dcomments');
	  
	  if ($query->num_rows() > 0) 
	  {		
	     $comment = $query->row_array(1);
    }
    else $comment = array();

		return $comment;
}

// получить последние комментари переданного массива пользователей
function dialog_get_last_comments($par=array())
{
  if (isset($par['cache_flag']))
  {
    $cache_key = 'dglcs_' .  serialize($par);
    $comusers = mso_get_cache($cache_key);
	if ($comusers) return $comusers;
  }
  
  $comusers = array();
  if (!isset($par['comusers'])) $par['comusers'] = array();
  
  foreach ($par['comusers'] as $comuser)
  {
	$par['user_id'] = $comuser['profile_user_id'];
	$comment = dialog_get_last_comment($par);
	$comusers[$par['user_id']] = $comment; 
  }
  
  if (isset($par['cache_flag']))
  {
    mso_add_cache($cache_key, $comusers); // сразу в кэш добавим
    //  mso_add_cache($cache_key_pag, $pagination); // сразу в кэш добавим
  } 	
	return $comusers;
}


// сколько комментариев?
function dialog_get_comments_count($par=array())
{
	 $CI = & get_instance();

   $CI->db->select('comment_id');

	 if (isset($par['user_id']) and $par['user_id']) 
	    $CI->db->where('comment_creator_id', $par['user_id']);
	    
	 if (isset($par['discussion_id']) and $par['discussion_id']) 
	    $CI->db->where('comment_discussion_id', $par['discussion_id']);	 
	    
	// if (!isset($par['and_deleted']) or !$par['and_deleted']) 
	    $CI->db->where('comment_deleted', '0'); 	       

	    $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');

	    
	 if (!isset($par['and_not_approved']) or !$par['and_not_approved']) //возвращаем только разрешенные и из разрешенных дискуссий
	 {
	    $CI->db->where('discussion_approved', '1');
   	    $CI->db->where('comment_approved', '1');
	 }
	  
	 if (!isset($par['and_private']) or !$par['and_private']) //возвращаем только не из приватных дискуссий
	    $CI->db->where('discussion_private', '0');
  
	  
	  $query = $CI->db->get('dcomments');
	  $comments_count = $query->num_rows();

    return $comments_count;
}




// в скольких дискуссиях учавствуют юзеры
// или сколько дискуссий
function dialog_get_discussions_count($par=array())
{
	 $CI = & get_instance();

   $CI->db->select('discussion_id');

	 if (isset($par['user_id']) and $par['user_id']) 
	    $CI->db->where('discussion_creator_id', $par['user_id']);
	    
	 if (!isset($par['and_not_approved']) or !$par['and_not_approved']) //возвращаем только разрешенные и из разрешенных дискуссий
	    $CI->db->where('discussion_approved', '1');

	 if (!isset($par['and_private']) or !$par['and_private']) //возвращаем только не  из приватных дискуссий
	    $CI->db->where('discussion_private', '0');
	  
	  $query = $CI->db->get('ddiscussions');
	  $count = $query->num_rows();

    return $count;
}


function dialog_get_private_info($comuser_id = 0)
{
  $private_info = array();
  
  return $private_info;
}



//функция получает коммент и его дискуссию
function dialog_get_comment($par=array())
{
	 $CI = & get_instance();

  $errors = array();
  $messages = array();
  
  $comment_id = 0;
  $user_id = 0;
  
  if(!isset($par['role_id'])) $par['role_id'] = 0;
  
  if (!isset($par['comment_id']) or !$par['comment_id']) return false; 
  else
  {
     //проверка существования такого комментария
	   $CI->db->select('dcomments.* , ddiscussions.discussion_private , ddiscussions.discussion_approved , ddiscussions.discussion_id , ddiscussions.discussion_title , ddiscussions.discussion_desc , ddiscussions.discussion_comments_count , dprofiles.profile_user_role_id , dprofiles.profile_psevdonim');
	   
	   if (isset($par['new_comments'])) 
		    $CI->db->where_in('comment_id', $par['new_comments']);
	   else $CI->db->where('comment_id', $par['comment_id']);
   
	   $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	   $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dcomments.comment_creator_id');
	   
	   $query = $CI->db->get('dcomments');
	   if ($query->num_rows() > 0) 
	      $row = $query->row_array(1);
       else return false;  
  }
  
  $row['allow_edit'] = false;
  
  // проверим чтобы этот пользователь мог получить доступ
  
  // администратор может все
  if ($par['role_id'] == 3) $row['allow_edit'] = true;
  else
  {
      // модератор не может просмотреть удаленные
      if ( $row['comment_deleted'] ) return false;
      
       // теперь посмотрим чтобы коммент был не из приватной дискуссии
       // модератор не может просматривать приватные дискуссии
       if ($row['discussion_private'])
       {
            // получим членов этой комнаты
            $members_id = dialog_get_id_users_on_room($row['comment_discussion_id']);
            if (!in_array($par['user_id'] , $members_id )) return false;
       }
       
       // теперь то что нельзя посетителям
       if ( ($row['comment_creator_id'] != $par['user_id']) and ($par['role_id'] != 2)) 
       {
         if (!$row['comment_approved'] or !$row['discussion_approved']) return false;
         
       }
       
       else $row['allow_edit'] = true;
  }
  
  return $row;

}


// возвращает дискуссии
// для юзера готовятся поля:
// new_comments=count и new_discussions=count
// если $par['category_id'] не передано - возвращаются невзирая на категории
// если $par['category_id']=0 - возвращаются только из 0 категории
//gd
function dialog_get_discussions($par , &$options)
{
 
  if ($options['cache_flag'])
  {
    $cache_key = 'ddisc_' . serialize($par) . '_'/* . mso_current_paged()*/;
    $out = mso_get_cache($cache_key);
   // $cache_key_pag = 'ddisc_' . serialize($par) . '_'/* . mso_current_paged()*/;
   // $pagination = mso_get_cache($cache_key_pag);
    /*if ($out and $pagination) */if ($out) return $out;
  }

  $discussions = array();
 
  if(!isset($par['role_id'])) $par['role_id'] = 0;
 
	 // если указан юзер, для которого выводим - присоединим его просмотры
	 if ($par['user_id'])
      $wathes_discussions = dialog_get_wathes_discussions_id($par['user_id']);
   else $wathes_discussions = array(); 


  // если мы хотим получить только новые дискуссии для этого пользователя
  if(isset($par['news']) and $par['news'])
  {
     // получим массив id дискуссий без новостей
     $no_news = dialog_get_id_nonews(array('user_id'=>$par['user_id']));
  }
  
   
	$CI = & get_instance();
	
	// 
	if (isset($par['sort_field']) and ($par['sort_field'] == 'watch_comments_count') ) $CI->db->select('ddiscussions.* , dprofiles.* , dwatch.*');
  else $CI->db->select('ddiscussions.* , dprofiles.*');
  
  if (!isset($par['user_id'])) $par['user_id'] = 0;
  
  
  // если мы хотим получить только новые дискуссии для этого пользователя
  if(isset($par['news']) and $par['news'] and $no_news)
     // и получим все другие чем в массиве дискуссии
	   $CI->db->where_not_in('discussion_id', $no_news);
  
   
  // если указан массив включений id
  if(isset($par['id_array']) and $par['id_array'])
	   $CI->db->where_in('discussion_id', $par['id_array']);
  
  // если получать только приватные
  if(isset($par['private']) and $par['private'])
	   $CI->db->where('discussion_private', '1');
	
  // если получать только НЕ приватные
  if(isset($par['no_private']) and $par['no_private'])
	   $CI->db->where('discussion_private', '0');	
	     
  if (isset($par['category_id']))
	   $CI->db->where('discussion_category_id', $par['category_id']);

  // если указано колличество
  if(isset($par['count']))
	   $CI->db->limit($par['count']);
	   
	   
  // присоединим автора
	$CI->db->join('dprofiles', 'dprofiles.profile_user_id = ddiscussions.discussion_creator_id');


	 // если указано спам
    if(!isset($par['and_spam_check']))
	   $CI->db->where('dprofiles.profile_spam_check', '0');
	
   //где учавствовал
   if (isset($par['sort_field']) and ($par['sort_field'] == 'watch_comments_count') )  
   {
	   $CI->db->join('dwatch', 'dwatch.watch_discussion_id = ddiscussions.discussion_id' , 'left');
	   $CI->db->where('dwatch.watch_user_id', $par['user_id']);
   }
   
  // сортировка ?
 if (isset($par['sort_field']))
 {
    if (!isset($par['sort_order'])) $par['sort_order'] = 'desc';
	  $CI->db->order_by($par['sort_field'] , $par['sort_order']);
 }
 else
 {
    $CI->db->order_by('discussion_order' , 'desc');
    $CI->db->order_by('discussion_date_last_active' , 'desc');
 }
 
	  $query = $CI->db->get('ddiscussions');
	  if ($query->num_rows() > 0) 
	  {		
	     $discussions = $query->result_array(); 
	     
	     foreach ($discussions as $key=>$discussion)
	     {
	        // статусы для этого пользователя
	        
	        $discussions[$key]['new_comments'] = array();
	        // статус новых коментариев и просмотра
	        if (isset($wathes_discussions[$discussion['discussion_id']]))
	        {
	           $discussions[$key]['watch'] = true; // просмотрена
	           if ($wathes_discussions[$discussion['discussion_id']]['watch_date'] >= $discussion['discussion_date_last_active'])
	              $discussions[$key]['news'] = false; // нет новостей
	           else
	           {
	              $discussions[$key]['news'] = true; // есть новые комменты
	              // получим массив id новых комментов, созданных после последнего просмотра
	              $discussions[$key]['new_comments'] = dialog_get_comments_id(array('discussion_id'=>$discussion['discussion_id'], 'min_date'=>$wathes_discussions[$discussion['discussion_id']]['watch_date']));
	           }
	           
	           $discussions[$key]['subscribe'] = $wathes_discussions[$discussion['discussion_id']]['watch_subscribe'];
	           $discussions[$key]['watch_date'] = $wathes_discussions[$discussion['discussion_id']]['watch_date'];
	           
	        }   
	        else
	        {
	           $discussions[$key]['watch'] = false; // не просмотрена
	           $discussions[$key]['news'] = false; // новые комменты
	        }   
	        
	        //может дискуссия просматривается автором
	        if ($discussion['discussion_creator_id'] == $par['user_id']) $discussions[$key]['autor'] = true;
	        else $discussions[$key]['autor'] = false;
	        
	        
	        // присоединим последнего комментатора в дискуссии
	        // может последний комментатор есть автор
	        if ($discussion['discussion_creator_id'] != $discussion['discussion_last_user_id'])
	        {
	            $profile = dialog_get_profile($discussion['discussion_last_user_id'] , $options);
	            if ($profile)
                 $discussions[$key]['last_user_psevdonim'] = $profile['profile_psevdonim'];
              else $discussions[$key]['last_user_psevdonim'] = 'Incognito'; // а вдруг?   
	        }	        
	        else $discussions[$key]['last_user_psevdonim'] = $discussion['profile_psevdonim'];
	        
	        // может искуссия требует моерации? и пользователь - не автор и не модератор
	        if (!$discussion['discussion_approved'] and !$discussions[$key]['autor'] and ($par['role_id'] != 3) and ($par['role_id'] != 2)) unset($discussions[$key]);
	        
	     
	        // если дискуссия приватна
	        if ($discussion['discussion_private'])
	        {
	           // не вывоим ее если нет пользователя
	           if (!$par['user_id']) unset($discussions[$key]);
	           elseif ($par['role_id'] != 3)
	           {
	              // проверим - имеет ли этот пользователь доступ
	              $members_array = dialog_get_id_users_on_room($discussion['discussion_id']);
	              if (!in_array($par['user_id'] , $members_array)) unset($discussions[$key]);
	           }
	        }
	     }
    }
    
  if ($options['cache_flag'])
  {
      mso_add_cache($cache_key, $discussions); // сразу в кэш добавим
    //  mso_add_cache($cache_key_pag, $pagination); // сразу в кэш добавим
  } 
      
  return $discussions;
}


function dialog_get_discussion($par = array(), &$options)
{
	$CI = & get_instance();
	
  
  if (!isset($par['user_id']) ) $par['user_id'] = 0;
  if (!isset($par['discussion_id']) or !$par['discussion_id']) return false;
  
	   $id = (int) $par['discussion_id'];
	   if ( (string) $par['discussion_id'] != (string) $id ) return false; // $comment_discussion_id не число
  
  $CI->db->select('ddiscussions.* , dcategorys.*');
  
	$CI->db->where('discussion_id', $par['discussion_id']); 
	
	$CI->db->join('dcategorys', 'dcategorys.category_id = ddiscussions.discussion_category_id' , 'left');
	

	
	$query = $CI->db->get('ddiscussions');
	if ($query->num_rows() > 0) 
	{
	    $row = $query->row_array(1);	
	   
	    if ($row['discussion_creator_id'] == $par['user_id']) $row['autor'] = true;
	    else $row['autor'] = false;
	    
	    // если дискуссия приватная 
	    if ($row['discussion_private'])
	    {
	       // добавим участников приватной дискуссии (пользователи в комнате)
	    
	       $row['members'] = dialog_get_id_users_on_room($row['discussion_id']);
	       if (!in_array($par['user_id'] , $row['members']) and ($par['role_id'] != 3 )) return false;
	       
	    }
	    else $row['members'] = array();
	    
	    // если дискуссия неодобрена
	    if (!$row['discussion_approved'] and ($par['role_id'] != 3 ) and ($par['role_id'] != 2 ) and !$row['autor'] )
           return false;
	    
	    // доступ 
	    if ( ($par['role_id'] == 3 ) or ($par['role_id'] == 2 ) or ($row['autor']) )
	       $row['allow_edit'] = true;
	    else
	      $row['alow_edit'] = false;
	      
	    // если есть пользователь, выясним его просмотр и подписку дискуссии
	    if ($par['user_id'])
	    {
         $CI->db->select('watch_comments_count, watch_subscribe');
	       $CI->db->where('watch_discussion_id', $par['discussion_id']);
	       $CI->db->where('watch_user_id', $par['user_id']);
	       $query = $CI->db->get('dwatch');
	       if ($query->num_rows() > 0) 
	       {
	          $watch = $query->row_array(1);	       
            $row['watch_comments_count'] = $watch['watch_comments_count'];
            $row['watch_subscribe'] = $watch['watch_subscribe'];
	       }
	       else 
	       {
            $row['watch_comments_count'] = 0;
            $row['watch_subscribe'] = false;	       
	       }
	    }  
	}
  else $row = array();
  
  return $row;
}


// получим категорию
function dialog_get_category($category_slug='')
{
	$CI = & get_instance();
	
  $CI->db->select('*');
	$CI->db->where('category_slug', $category_slug);  
	$query = $CI->db->get('dcategorys');
	if ($query->num_rows() > 0) 
	    $row = $query->row_array(1);	
  else $row = array();
  
  return $row;
}



// возвращает комментарии
// gc
function dialog_get_сomments($par , &$pagination , &$options)
{
  if (!isset($options['answers'])) $options['answers'] = false; // получать ли ответы? (потомков)
  if (!isset($options['cache_flag'])) $options['cache_flag'] = false; // кешировать?
  if (!isset($par['role_id'])) $par['role_id'] = 0;
  if (!isset($par['count'])) $par['count'] = 0;
  if (!isset($par['user_id'])) $par['user_id'] = 0;
  if (!isset($par['and_unapproved'])) $par['and_unapproved'] = false;
  if (!isset($par['and_deleted'])) $par['and_deleted'] = false;
  if (!isset($par['and_spam_check'])) $par['and_spam_check'] = false;

  
  if (!isset($par['count'])) $par['count'] = 0;
  if (!isset($par['comment_creator_id'])) $par['comment_creator_id'] = false;
  if (!isset($par['private'])) $par['private'] = false;

  if (/*isset($par['discussion_id']) and */$options['cache_flag'])
  {
    $cache_key = 'dcomm_' . serialize($par) . '_' . mso_current_paged();
    $out = mso_get_cache($cache_key);
    $cache_key_pag = 'dcomm_pag_' . serialize($par) . '_' . mso_current_paged();
    $pagination = mso_get_cache($cache_key_pag);
    if ($out /* and $pagination*/) return $out;
  }
  
  $comments = array();
  $result_comments = array();
  $CI = & get_instance();
  
  
  // построим запрос в зависимости от потребностей
  
  if ($par['count'] and !$par['comment_creator_id'] and !isset($par['discussion_id'])) 
    	$CI->db->select('SQL_CALC_FOUND_ROWS comment_id , comment_discussion_id , discussion_title, comment_content, comment_creator_id , comment_approved, comment_deleted , comment_spam, comment_parent_id, comment_check, profile_podpis , profile_rate , profile_comments_count, profile_spam_check, profile_dankes, profile_date_first_visit, comusers_avatar_url, comusers_avatar_url, comusers_email, comusers_url  , comusers_nik, comment_date_create, profile_psevdonim, profile_spam_check, profile_allow_msg, comment_flud', false);
    	
  elseif ($par['count'] and !$par['comment_creator_id'] and isset($par['discussion_id'])) 
    	$CI->db->select('SQL_CALC_FOUND_ROWS comment_id , comment_discussion_id , comment_content, comment_creator_id , comment_approved, comment_deleted , comment_spam, comment_parent_id, comment_check, profile_podpis , profile_rate , profile_comments_count, profile_spam_check, profile_dankes, profile_date_first_visit, comusers_avatar_url, comusers_avatar_url, comusers_email , comusers_nik, comusers_url , comment_date_create, profile_psevdonim, profile_spam_check, profile_allow_msg, comment_flud', false);    	
    	
  elseif (!$par['count'] and !$par['comment_creator_id']) 
      $CI->db->select('dcomments.* , comusers.* , dprofiles.* , ddiscussions.discussion_title');
      
  elseif ($par['count'] and $par['comment_creator_id']) 	$CI->db->select('SQL_CALC_FOUND_ROWS comment_id , comment_content, comment_approved, comment_deleted , comment_spam, comment_check, comment_date_create , comment_parent_id , comment_creator_id , discussion_title , comment_discussion_id, comment_flud', false);
  
  else //if (!$par['count'] and $par['comment_creator_id']) 
      $CI->db->select('dcomments* , ddiscussions.discussion_title');  
  
   
  if (isset($par['discussion_id']))
	   $CI->db->where('dcomments.comment_discussion_id', $par['discussion_id']);
	else
	{
	   $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	   if ( ($par['role_id'] != 2) and ($par['role_id'] != 3)) $CI->db->where('discussion_approved', '1');
	   if ( ($par['role_id'] != 3) and !$par['private']) $CI->db->where('discussion_private', '0');
	}   
 
 
  if ($par['count']) $CI->db->limit($par['count'], mso_current_paged() * $par['count'] - $par['count'] );

  
  // если показываем комменты конкретного пользователя
  if ($par['comment_creator_id'])
  {
	  $CI->db->where('dcomments.comment_creator_id', $par['comment_creator_id']);
  }

 // else // если не комменты конкретного автора
  
    // присоединим профайл автора
	  $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dcomments.comment_creator_id' , 'left');
	
    // присоединим автора
	  $CI->db->join('comusers', 'comusers.comusers_id = dcomments.comment_creator_id');
    
 
 	// комменты забаненного, запрещенные и удаленные комменты показываем только администраторам и модераторам
	// и авторам    
	if ( ($par['role_id'] != 2) and ($par['role_id'] != 3) )
		if ( ($par['user_id'] != $par['comment_creator_id']) or !$par['user_id'] )
		{
	        if(!$par['and_unapproved']) $CI->db->where('comment_approved', '1');
	        if(!$par['and_deleted'])$CI->db->where('comment_deleted', '0');
	        if(!$par['and_spam_check'])$CI->db->where('dprofiles.profile_spam_check', '0');				
		}


 
 
 // сортировка ?
 if (isset($par['sort_field']))
 {
    if (!isset($par['sort_order'])) $par['sort_order'] = 'desc';
	  $CI->db->order_by($par['sort_field'] , $par['sort_order']);
 }

 $query = $CI->db->get('dcomments');
	
	if ( ($query->num_rows() > 0) or isset($par['parent_comment_id'])) 
	{		
	     $comments = $query->result_array(); 
	     
	     if ($par['count']) $pagination = mso_sql_found_rows($par['count']); // определим общее кол-во записей для пагинации
	     
	     // если это последняя страница пагинации, и пользователь обычный, и не страница комментов пользоваеля, и страница дискуссии
	     if ( (mso_current_paged() == $pagination['maxcount']) and $par['user_id'] and ($par['role_id'] != 2) and ($par['role_id'] != 3) and !$par['comment_creator_id'] and isset($par['discussion_id']))
	     {
	       // получим последний коммент этого пользователя
	       $last_comment = dialog_get_last_comment(array('user_id'=>$par['user_id'] , 'discussion_id'=>$par['discussion_id'] , 'and_not_approved'=>true,'and_deleted'=>true));
	       // если последнй коммент не получен в выдаче - добавим его
	       if ($last_comment and ($comments[count($comments)-1]['comment_id'] != $last_comment['comment_id'])) $comments[] = $last_comment;
	     }
	     
	     
	     // если это первая страница пагинации и дискуссия порожденная - добавим родительский коммент
	     if (isset($par['parent_comment_id']) and $par['parent_comment_id'] and (mso_current_paged() == 1))
	     {
	        // получим исходный коммент
	        $first_comment = diaog_get_first_comment($par['parent_comment_id'], $par['role_id'] , $par['discussion_id']);
	        $first_comment['parent_for'] = true;
	        array_unshift($comments, $first_comment);
	     }
	     
	     
	     // вытащим comment_id в качестве ключа
	     // заодно прицепим все другие данные
	     $result_comments = array();
	     
	     // нам нужны id всех комментов на этой странице пагинации
	     $id_in_page = array();
	     foreach ($comments  as $comment)
	       $id_in_page[] = $comment['comment_id'];
	     
	     
	     foreach ($comments as $key=>$comment)
	     {
	        // подготовим контент к выводу
	        $options['comment_creator_id'] = $comment['comment_creator_id'];
	        dialog_comment_to_out($comments[$key]['comment_content'] , $options); 
	        
	     
	        // получим все случившиеся порождения дискуссий
	        $parend_disc_array = diaog_get_all_parent_disc();
	        
	        
	        // если этот коммент порождал дискуссию и он не исходный
	        if (isset($parend_disc_array[$comment['comment_id']]) and !isset($comment['parent_for']) )
	        {
	          // добавим массив порожденных этим комментом дискуссий
	          $comments[$key]['child_disc'] = $parend_disc_array[$comment['comment_id']];
	        }
	        else $comments[$key]['child_disc'] = false;
	        
	        
	        // есть ли роитель?
	        if ($comment['comment_parent_id'] and isset($par['discussion_id']) and $par['discussion_id'])
	        {
	          // получим родителя
	          $parent_comment = diaog_get_parent_comment($comment['comment_parent_id'], $par['role_id'] , $par['discussion_id']);
	          $options['comment_creator_id'] = $parent_comment['comment_creator_id'];
	          dialog_comment_to_out($parent_comment['comment_content'] , $options); 
	          
	          // проверим родителя
	          $comments[$key]['parent'] = $parent_comment;
	        }
	        else $comments[$key]['parent'] = false;
	        
	        
	        // может нужно получать ответы?
	        if ($options['answers'] and isset($par['discussion_id']) and $par['discussion_id'])
	        {
	          // получим массив ответов
	          $answers_comments = diaog_get_answers($comment['comment_id'], $par['role_id'] , $par['discussion_id']);
	          if ($answers_comments)
	             foreach ($answers_comments as $akey=>$answer_comments)
	             {
	                $options['comment_creator_id'] = $answer_comments['comment_creator_id'];
	                dialog_comment_to_out($answers_comments[$akey]['comment_content'] , $options);
	             }
	          $comments[$key]['answers'] = $answers_comments;	          
	        }
	        else $comments[$key]['answers'] = false; 
	        
	        
	        //может коммент просматривается автором
	        if ($comment['comment_creator_id'] == $par['user_id']) $comments[$key]['autor'] = true;
	        else $comments[$key]['autor'] = false; 
	        
	        // присоеденим спасибы, перевернув их для уобства
	        $comments[$key]['comment_danke']  = array();
	        $dankes = dialog_get_danke($comment['comment_id']);  
	        if ($dankes) 
	           foreach ($dankes as $danke) $comments[$key]['comment_danke'][$danke['gud_user_id']] = $danke;
	        
	        // присоединим голосования
	        $comments[$key]['comment_votes_plus']  = array(); // массив user_id, сказавших +
	        $comments[$key]['comment_votes_minus']  = array(); // массив user_id, сказавших -
	        $comments[$key]['comment_votes_who']  = array(); // кто что сказал
	        $votes = dialog_get_votes($comment['comment_id']);  
	        if ($votes) 
	           foreach ($votes as $vote)
	           {
	              $comments[$key]['comment_votes_who'][$vote['vote_user_id']] = $vote['vote'];
	              if ($vote['vote'] == '1') $comments[$key]['comment_votes_plus'][] = $vote['vote_user_id'];	
	              else $comments[$key]['comment_votes_minus'][] = $vote['vote_user_id'];  
	           }         
	        
	        // массив для перелинковке по цитатам (кого цитируем)
	        $perelinks_info = dialog_get_perelinks_info($comment['comment_content']);
	        // теперь осуществим перелинковку по цитатам
          // в $content перед <blockquote id="comment_id"> подставим информацию о цитируемом пользователе и комменте
	        if ($perelinks_info) 
          dialog_perelink_quotes($comment['comment_id'], $comment['comment_date_create'] , $comments[$key]['comment_content'], $options, $perelinks_info, $id_in_page);
          
          
	        // массив для перелинковке по цитатам (кто цитировал)
	        $comments[$key]['comment_perelinks'] = dialog_get_comment_perelinks_info($comment['comment_id']);
	         
	          
          $result_comments[$comment['comment_id']] = $comments[$key];
	     }
  }
   
  if (/*isset($par['discussion_id']) and */$options['cache_flag'])
  {
      mso_add_cache($cache_key, $result_comments); // сразу в кэш добавим
      mso_add_cache($cache_key_pag, $pagination); // сразу в кэш добавим
  }    
  
  return $result_comments;
  
}

// для юзера возвращается массив просмотренных дискуссий и дат просмотра
function dialog_get_wathes_discussions_id($user_id = 0)
{
	$CI = & get_instance();
  
  $CI->db->select('*'); 
	$CI->db->where('watch_user_id', $user_id);
 	$query = $CI->db->get('dwatch');
 	
	if ($query->num_rows() > 0) 
	{		
	     $watches = $query->result_array(); 
	     $watch_discussions = array();
	     
	     foreach ($watches as $watch)
            $watch_discussions[$watch['watch_discussion_id']] = $watch;  
            
       return $watch_discussions;    
   }
   else return array();
}


// функция по номеру комментария выясняет страницу пагинации в дискуссии
function dialog_get_comment_page($comment_id=0 , $comments_on_page=0 , $discussion_id=0 , $user_role =0 , $user_id = 0)
{
  
  $id = (int) $comment_id;
	if ( (string) $comment_id != (string) $id ) return false;

  $id = (int) $discussion_id;
	if ( (string) $discussion_id != (string) $id ) return false;
	
    // получим все комментарии в дискуссии для этого пользователя
	  $CI = & get_instance();
  
    $CI->db->select('comment_id, comment_creator_id, comment_approved , comment_deleted'); 
	  $CI->db->where('comment_discussion_id', $discussion_id );

 	  $query = $CI->db->get('dcomments');
	  if ($query->num_rows() > 0) 
	  {		
	     $comments = $query->result_array(); 
	     
	     // проходим массив, удаляя ненужные комменты
	     $deleted_count = 0;
	     // заодно ищем наш коммент
	     $found_id = -1;
	     foreach ($comments as $key=>$comment)
	     {
	        $delete = false;
	        //может дискуссия просматривается автором
	        if ($comment['comment_creator_id'] == $user_id) $autor = true;
	        else $autor = false;
	        
	        // может дискуссия требует модерации и пользователь - не автор и не модератор
	        if (!$comment['comment_approved'] and !$autor and ($user_role != 2) and ($user_role != 3)) $delete = true;
	        // удаленные комменты только администратор может просматривать
	        if ($comment['comment_deleted'] and ($user_role != 2) and ($user_role != 3) ) $delete = true;
	        
	        if ($delete)
	        {
	          unset($comments[$key]);
	          $deleted_count = $deleted_count + 1; 
	        }
	        
	        if ($comment['comment_id'] == $comment_id) 
	        {
	          $found_id = $key;
	          break;
	        }
	     }
    }
	  else return false;
	   
	  // если коммента в этой дискуссии нет (может он перенесен)
	  if ($found_id < 0)
	  {
	    // попробуем получить новую дискуссию коммента
	     return false;
	  } 
	  
	  
	  // корректируем
	  $found_id = $found_id + 1 - $deleted_count;
	   
	  if ( ($found_id <= $comments_on_page) or !$comments_on_page) return 1;
	
	   $pages_count = dialog_get_pages_count($found_id , $comments_on_page);
	   
	   return $pages_count;
}





// выясняет разрешенные действия с комментом
// поля с разрешениями добавляются в массив коммента
function dialog_get_allows(&$comment , $user_id =0 , $user_role_id = 0)
{
   if ($user_role_id == 3)
   {
       $comment['allow_view'] = true;
       $comment['allow_edit'] = true;
       $comment['allow_delete'] = true;
       $comment['allow_undelete'] = true;
       $comment['allow_approved'] = true;
       $comment['allow_unapproved'] = true;  
       $comment['allow_spam'] = true;
       $comment['allow_unspam'] = true;  
       return true;
   }

   if (!isset($comment['comment_creator_id'])) return false;

    // нам нужна роль создателя
    if (!isset($comment['profile_user_role_id']))
    {
      // получим роль создателя коммента
	    $CI = & get_instance();
  
      $CI->db->select('profie_user_role_id'); 
	    $CI->db->where('profie_user_id', $comment['comment_creator_id']);
 	    $query = $CI->db->get('Dprofies');
 	
	    if ($query->num_rows() > 0) 
	    {	    
	      $row = $query->row_array(1);
	      $comment['profile_user_role_id'] = $row['profile_user_role_id'];  
	    }
	    else $comment['profile_user_role_id'] = 0;  
    }
    
       $comment['allow_view'] = false;
       $comment['allow_edit'] = false;
       $comment['allow_delete'] = false;
       $comment['allow_undelete'] = false;
       $comment['allow_approved'] = false;
       $comment['allow_unapproved'] = false;  
       $comment['allow_spam'] = false;
       $comment['allow_unspam'] = false;     
    
    
    if ($comment['comment_creator_id'] == $user_id) $comment['autor'] = true;
    else $comment['autor'] = false;
    
    
    if ($comment['comment_deleted']) 
        if ($user_role_id == 2) $comment['allow_view'] = true;
    elseif (!$comment['comment_approved'])
    {
        if ($user_role_id == 2)
        {
           $comment['allow_view'] = true;
           $comment['allow_edit'] = true;
           $comment['allow_delete'] = true;
           $comment['allow_undelete'] = true;
           $comment['allow_approved'] = true;
           $comment['allow_unapproved'] = true;  
           $comment['allow_spam'] = true;
           $comment['allow_unspam'] = true;            
        }   
        elseif ($comment['autor']) $comment['allow_view'] = true;

    
          $comment['allow_view'] = true;
          $comment['allow_edit'] = true;
          $comment['allow_delete'] = true;
          $comment['allow_undelete'] = true;
          $comment['allow_approved'] = true;
          $comment['allow_unapproved'] = true;  
          $comment['allow_spam'] = true;
          $comment['allow_unspam'] = true;         
    }      
    
   
   if  ($comment['profile_user_role_id'] == 3) 
   {
       $comment['allow_edit'] = false;
       $comment['allow_delete'] = false;
       $comment['allow_undelete'] = false;
       $comment['allow_approved'] = false;
       $comment['allow_unapproved'] = false;  
       $comment['allow_spam'] = false;
       $comment['allow_unspam'] = false;  
       return true;
   }  
}


// получить список имен 
function dialog_get_names(&$options, $id_array=array())
{
	 $CI = & get_instance();
   $CI->db->select('profile_user_id , profile_psevdonim'); 
   $CI->db->where_in('profile_user_id' , $id_array); 
   
 	 $query = $CI->db->get('dprofiles');
 	
	 if ($query->num_rows() > 0) 
	 {	    
	    $row = $query->result_array();
	    return $row;    
	 }
	 else return array();     
}


// получить список профайлов
function dialog_get_profiles(&$options, $par=array())
{
	$CI = & get_instance();
  
    $CI->db->select('dprofiles.* , comusers.*'); 
	$CI->db->join('comusers', 'comusers.comusers_id = dprofiles.profile_user_id');
	 
	if (isset($par['sort_field']))
	{
	   if (!isset($par['sort_order'])) $par['sort_order'] = 'desc';
	   $CI->db->order_by($par['sort_field'] , $par['sort_order']);
	}
	 
	if (isset($par['role_id']) and ( ($par['role_id']!=2) or ($par['role_id']!=3)) )
		if (!isset($par['and_spam_check']))
	       $CI->db->where('dprofiles.profile_spam_check', '0');		

	if(isset($par['only_spam_check']) and $par['only_spam_check'])
		   $CI->db->where('dprofiles.profile_spam_check', '1');		

	if (isset($par['moderate']) and $par['moderate']) $CI->db->where('dprofiles.profile_moderate', '1');		

	 
 	$query = $CI->db->get('dprofiles');
 	
	 if ($query->num_rows() > 0) 
	 {	    
	    $row = $query->result_array();
	    return $row;    
	 }
	 else return array();     
}


// получим массив id просмотренных дискуссий без новостей
function dialog_get_id_nonews($par=array())
{
   if (!isset($par['user_id']) and !$par['user_id']) return array();
  
  // получим просмотренные дискуссии
	 $CI = & get_instance();
  
   $CI->db->select('discussion_id , discussion_date_last_active , discussion_last_user_id , watch_date');
	 
	 $CI->db->where('watch_user_id', $par['user_id']);
	 
	 $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dwatch.watch_discussion_id');
   
 	 $query = $CI->db->get('dwatch');

   // сдесь будет массив id не новых дискуссий
   $no_news_id = array();
   
	 if ($query->num_rows() > 0) 
	 {	    
	    $rows = $query->result_array();
	    foreach ($rows as $key=>$discussion)
	    {
	      // если последний пользователь - этот, или дата последней активности не больше чем дата просмотра этим ползователем
	      if ( ($discussion['discussion_last_user_id'] == $par['user_id']) or ($discussion['discussion_date_last_active'] <= $discussion['watch_date']))
	          // то эта дискуссия точно не входит в новости
	          $no_news_id[] = $discussion['discussion_id'];
	    }
	    
	    // теперь у нас есть массив неновостей
	    return $no_news_id;
	 }
	 else return array(); // все дискуссии - новости.
	    
}




// получим форумы и категории
function dialog_get_forums()
{
	 $CI = & get_instance();
   $CI->db->select('*');
 	 $query = $CI->db->get('dforums');

	 if ($query->num_rows() > 0) 
	 {	    
	    $forums = $query->result_array();
	    $forums[] = array('forum_id'=>0 , 'forum_title'=>'Неразобранное', 'forum_desc'=>'Категории не в форумах');
	    foreach ($forums as $key=>$forum)
	    {
	       $forums[$key]['categorys'] = array();
	       $CI = & get_instance();
         $CI->db->select('*');
	       $CI->db->where('category_forum_id', $forum['forum_id']);
 	       $query = $CI->db->get('dcategorys');
 	       
	       if ($query->num_rows() > 0) 
	       {	    
	           $categorys = $query->result_array();
	           foreach ($categorys as $cat)
               $forums[$key]['categorys'][] = $cat;
         }	
	    }
	    return $forums;
	    
   }
   else return array();
}


// получим форумы и категории
// подготовленные к выводу для конкретного пользователя
function dialog_get_categorys($par=array())
{
   $CI = & get_instance();
   $CI->db->select('*');
   $query = $CI->db->get('dforums');
 	 
 if (!isset($par['sort_order'])) $par['sort_order'] = 'desc';
 if (!isset($par['sort_field'])) $par['sort_field'] = FALSE;
 if (!isset($par['disc_count'])) $par['disc_count'] = 0;
 if (!isset($par['role_id'])) $par['role_id'] = 1;

	 
	 $forums = array();
	 if ($query->num_rows() > 0) 
	 {
	    $rows = $query->result_array();
	    foreach ($rows as $row)
	    {
	       $forums[$row['forum_id']] = $row;
	       $forums[$row['forum_id']]['categorys'] = array();
	    }   
	 }   
	 
   $CI->db->select('*');
 	 $query = $CI->db->get('dcategorys');

	 if ($query->num_rows() > 0) 
	 {
	    $categorys = $query->result_array();

      // получм массив Id всех неновых категорий для пользователя
      if ( isset($par['user_id']) and $par['user_id']) $id_nonews = dialog_get_id_nonews(array('user_id'=>$par['user_id'])); else $id_nonews = array();
	 	 
	    foreach ($categorys as $key=>$category)
	    {
	       if (!isset($forums[$category['category_forum_id']]))
	           $forums[$category['category_forum_id']] = array('forum_id'=>0, 'forum_title'=>'' , 'forum_desc'=>'', 'forum_slug'=>'');
	       
	       if (!isset($forums[$category['category_forum_id']]['categorys'])) $forums[$category['category_forum_id']]['categorys'] = array();    
	       
	       // вычислим активность в категории
	       
	       // получим номера и титлы всех дискуссий в этой категории
	       // посчитаем и добавим для вывода первые n
	       
	          $CI->db->select('discussion_id, discussion_comments_count, discussion_title, discussion_desc');
	          $CI->db->where('discussion_category_id', $category['category_id']);

	          $CI->db->where('discussion_private', '0');  
              $CI->db->join('dprofiles', 'dprofiles.profile_user_id = ddiscussions.discussion_creator_id');

				  
			  if ( ($par['role_id'] != 2) and ($par['role_id'] != 3) ) 
			  {
				  $CI->db->where('discussion_approved', '1');
				  $CI->db->where('dprofiles.profile_spam_check', '0');
			  }	  
			  
	          if ($par['sort_field']) 
	          { 
	             $CI->db->order_by($par['sort_field'] , $par['sort_order']);
	          }   

 	          $query = $CI->db->get('ddiscussions');
 	          
	          if ($query->num_rows() > 0) 
	          {
	            $discussions = $query->result_array();
	            $category['count'] = count($discussions);
	            $category['news'] = 0;
	            $category['comments_count'] = 0;
	            $category['disc'] = array();
	            
	            foreach ($discussions as $discussion)
	              {          
	                 $category['comments_count'] = $category['comments_count'] + $discussion['discussion_comments_count'];
	                 // если есть пользователь
                     if ( isset($par['user_id']) and $par['user_id'])
	                         if (!in_array($discussion['discussion_id'] , $id_nonews)) $category['news'] = $category['news']+1;	
	                 if (count($category['disc']) < $par['disc_count']) $category['disc'][] = $discussion;      
	              }
	          }
	          else
	          {
	            $category['news'] = 0;
	            $category['count'] = 0;
	            $category['comments_count'] = 0;
	            $category['disc'] = array();
	          }
	          
	       $forums[$category['category_forum_id']]['categorys'][$category['category_id']] = $category;
	          
	    }
	    return $forums;
   }
   else return array();
}


// получим дискуссии, на которые подписан пользователь
function dialog_get_subscribers($par=array())
{
   if (!isset($par['watch_user_id']) or !$par['watch_user_id']) return false;

	 $CI = & get_instance();
   $CI->db->select('watch_discussion_id');
	 $CI->db->where('watch_user_id', $par['watch_user_id']);
	 $CI->db->where('watch_subscribe', '1');
 	 $query = $CI->db->get('dwatch');
	 if ($query->num_rows() > 0) 
	 {
	 	$watch = $query->result_array();
	 	$arr = array();
	  foreach ($watch as $cur_watch)
	      $arr[] = $cur_watch['watch_discussion_id'];  
	  return $arr;    
   }
   else return false;
}






// проверяет, чтобы комменты не часто
// возвращает true, если пропускать
function dialog_last_activity_comment($max_delta = 0)
{
  if (!$max_delta) return true;

	global $MSO;

	// предыдущего комментария не было - это первый
	if (!isset($MSO->data['session']['last_activity_dialog'])) return true;
	
	// время в секундах между последним комментарием и текущим в секундах
	$delta = time() - $MSO->data['session']['last_activity_dialog'];
	
	return ($delta < $max_delta) ? false : true;

}



// получим по ключу массив подписок пользователя
function dialog_get_unsubscribe_array($key='')
{
	 $CI = & get_instance();
   $CI->db->select('watch_discussion_id , profile_user_id');
	 $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dwatch.watch_user_id');
	 $CI->db->where('profile_key', $key);
	 $CI->db->where('watch_subscribe', '1');
 	 $query = $CI->db->get('dwatch');
	 if ($query->num_rows() > 0) 
	    return $query->result_array();
	 else return array();   
}

// получим пописчиков на дискуссию
function dialog_get_comusers_subscribers($discussion_id = 0)
{
	  $CI = & get_instance();
	
	  $CI->db->select('dwatch.watch_user_id , comusers.comusers_email , dprofiles.profile_key , dprofiles.profile_psevdonim , dprofiles.profile_allow_subscribe , dprofiles.profile_allow_info');
	  $CI->db->where('watch_subscribe', '1');
	  $CI->db->where('watch_discussion_id', $discussion_id);
	  $CI->db->join('comusers', 'comusers.comusers_id = dwatch.watch_user_id');
	  $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dwatch.watch_user_id');
	  $query = $CI->db->get('dwatch');
	  if ($query->num_rows() > 0) 
	  {
	      $comusers = $query->result_array();
	      return $comusers;
	  }    
	  else return array();   

}


function diaog_get_first_comment($first_comment_id, $role_id , $discussion_id)
{
	  $CI = & get_instance();
	
	  $CI->db->select('dcomments.* , dprofiles.* , comusers.* , ddiscussions.*');
	  $CI->db->where('comment_id', $first_comment_id);
	  $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dcomments.comment_creator_id');
	  $CI->db->join('comusers', 'comusers.comusers_id = dcomments.comment_creator_id');
	  $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	  $query = $CI->db->get('dcomments');
	  if ($query->num_rows() > 0) 
	  {
	      $parent = $query->row_array(1);
	      return $parent;
	  }    
	  else return false;   
}


function diaog_get_parent_comment($comment_parent_id, $role_id , $discussion_id)
{
	  $CI = & get_instance();
	
	  $CI->db->select('dcomments.* , dprofiles.* , comusers.*');
	  $CI->db->where('comment_id', $comment_parent_id);
	//  $CI->db->where('comment_discussion_id', $discussion_id);
	  $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dcomments.comment_creator_id');
	  $CI->db->join('comusers', 'comusers.comusers_id = dcomments.comment_creator_id');
	  $query = $CI->db->get('dcomments');
	  if ($query->num_rows() > 0) 
	  {
	      $parent = $query->row_array(1);
	      return $parent;
	  }    
	  else return false;   
}



function diaog_get_answers($comment_id, $role_id , $discussion_id )
{
	  $CI = & get_instance();
	
	  $CI->db->select('dcomments.* , dprofiles.*');
	  $CI->db->where('comment_parent_id', $comment_id);
	  $CI->db->where('comment_discussion_id', $discussion_id);
	  $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dcomments.comment_creator_id');
	  $query = $CI->db->get('dcomments');
	  if ($query->num_rows() > 0) 
	  {
	      $answers = $query->result_array();
	      return $answers;
	  }    
	  else return false;   
}


// получим все комменты, на которые пользователь получил ответ
function diaog_get_all_answers($user_id) 
{
	  $CI = & get_instance();
	
	  $CI->db->select('dcomments.* , dprofiles.*');
	  $CI->db->where('comment_parent_id', $comment_id);
	  $CI->db->where('comment_discussion_id', $discussion_id);
	  $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dcomments.comment_creator_id');
	  $query = $CI->db->get('dcomments');
	  if ($query->num_rows() > 0) 
	  {
	      $answers = $query->result_array();
	      return $answers;
	  }    
	  else return false;   
}


// получим номера всех комментов, которые присутствуют в comment_parent_id
function diaog_get_all_questions_id($user_id) 
{
	  $CI = & get_instance();
	
	  $CI->db->select('comment_id, comment_parent_id');
	  $CI->db->where('comment_parent_id', $comment_id);

	  $CI->db->where('dcomments.comment_approved' , '1' );
	  $CI->db->where('dcomments.comment_private' , '0' );
	  $CI->db->where('dcomments.comment_deleted' , '0' );

	  $query = $CI->db->get('dcomments');
	  if ($query->num_rows() > 0) 
	  {
	      $answers = $query->result_array();
	      return $answers;
	  }    
	  else return false;   
}



function dialog_get_danke($comment_id=0)
{
	  $CI = & get_instance();
	
	  $CI->db->select('dgud.*, dprofiles.profile_psevdonim');
	  $CI->db->where('gud_comment_id', $comment_id);
	  $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dgud.gud_user_id');
	  $query = $CI->db->get('dgud');
	  if ($query->num_rows() > 0) 
	  {
	      $result = $query->result_array();
	      return $result;
	  }    
	  else return array();   
}


function dialog_get_votes($comment_id=0)
{
	  $CI = & get_instance();
	
	  $CI->db->select('*');
	  $CI->db->where('vote_comment_id', $comment_id);
	  $query = $CI->db->get('dvotes');
	  if ($query->num_rows() > 0) 
	  {
	      $result = $query->result_array();
	      return $result;
	  }    
	  else return array();   
}

// получим все благодарности, выраженные пользователю $user_id
// если нет $user_id - все благодарности
function dialog_get_dankes($user_id=0)
{
	  $CI = & get_instance();
	
	  $CI->db->select('dgud.*, dprofiles.profile_psevdonim , dcomments.comment_content , dcomments.comment_discussion_id, ddiscussions.discussion_title');
	  if ($user_id) $CI->db->where('gud_autor_id', $user_id);
	  $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dgud.gud_user_id');
	  $CI->db->join('dcomments', 'dcomments.comment_id = dgud.gud_comment_id');
	  $CI->db->join('ddiscussions', 'dcomments.comment_discussion_id = ddiscussions.discussion_id');
	  $query = $CI->db->get('dgud');
	  if ($query->num_rows() > 0) 
	  {
	      $result = $query->result_array();
	      return $result;
	  }    
	  else return array();   
}

// перелинковка ________________________________________________________


// в $content найдем все <blockquote id="comment_id"> и вернем их массив
function dialog_get_perelinks(&$content='')
{
  $perelinks = array(); 
  $arr = explode('<blockquote id="' , $content);
  if (count($arr) > 1)
   foreach ($arr as $key => $ar)
   {
     if ($key == 0) continue;
     $id = explode('">' , $ar);
     if ( (count($id) > 1) and is_numeric($id[0]) and !in_array($id , $perelinks)) $perelinks[] = $id[0];
   }
  return $perelinks;
}


// найдем в контенте все цитаты и вернем массив информаци о них 
// в виде comment_id => (comment_date_create , profile_psevdonim , comment_creator_id, comment_category_id)
function dialog_get_perelinks_info(&$content='')
{
  $info = array(); 
  $quotes_id_array = dialog_get_perelinks($content);//массив id цитируемых комментов
  if ($quotes_id_array)
  {
    // получим дополнительную информацию о цитируемых комментах
	  $CI = & get_instance();
	
	  $CI->db->select('comment_date_create , profile_psevdonim , comment_creator_id , comment_id , comment_discussion_id');
	  $CI->db->where_in('comment_id', $quotes_id_array);
	  $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dcomments.comment_creator_id');
	  $query = $CI->db->get('dcomments');
	  if ($query->num_rows() > 0) 
	  {
	      $result = $query->result_array();
	      foreach ($result as $cur)
	         $info[$cur['comment_id']] = $cur;
	  }    
  }
  return $info;
}



// найдем для коммента все его цитаты и вернем массив информаци о них 
// в виде comment_id => (comment_date_create , profile_psevdonim , comment_creator_id , comment_category_id)
function dialog_get_comment_perelinks_info($comment_id=0)
{
  $info = array(); 

    // получим дополнительную информацию о цитируемых комментах
	  $CI = & get_instance();
	
	  $CI->db->select('comment_date_create , profile_psevdonim , comment_creator_id , comment_id , comment_discussion_id');
	  $CI->db->join('dcomments', 'dcomments.comment_id = dperelinks.perelinks_child_id');
	  $CI->db->where('perelinks_parent_id', $comment_id);
	  $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dcomments.comment_creator_id');
	  $query = $CI->db->get('dperelinks');
	  if ($query->num_rows() > 0) 
	      $info = $query->result_array();
	   else return false; 
	   
  return $info;
}



// получим массив номеров комментов
// созданных после min_date
// из дискуссии discussion_id
function dialog_get_comments_id($par=array())
{
	  $CI = & get_instance();
	  $CI->db->select('comment_id');
    if (isset($par['min_date'])) $CI->db->where('comment_date_create >', $par['min_date']);
    if (isset($par['discussion_id'])) $CI->db->where('comment_discussion_id', $par['discussion_id']);
	  $query = $CI->db->get('dcomments');
	  if ($query->num_rows() > 0) 
	      $result = $query->result_array();
	  else return false; 
	   
  return $result;


}


// Получим лог действий пользователя либо над комментом 
function dialog_get_log($par=array())
{
	  $CI = & get_instance();
	  $result = array();

	  if ( !isset($par['user_id']) and !isset($par['comment_id']) ) // если весь лог форума
	      $CI->db->select('dlog.* , comment_content , profile_psevdonim , discussion_title , discussion_id');	
	        
	  elseif (!isset($par['user_id']))
	      $CI->db->select('dlog.* , profile_psevdonim');
	  elseif (!isset($par['comment_id'])) 
	      $CI->db->select('dlog.* , comment_content , discussion_title , discussion_id');   
	  else $CI->db->select('dlog.*');
	  
    if (isset($par['comment_id'])) 
       $CI->db->where('dlog.log_comment_id', $par['comment_id']);
    else  
       $CI->db->join('dcomments', 'dcomments.comment_id = dlog.log_comment_id');
    
    if (isset($par['user_id'])) 
       $CI->db->where('dlog.log_user_id', $par['user_id']);
    else  
       $CI->db->join('dprofiles', 'dprofiles.profile_user_id = dlog.log_user_id');

    if (!isset($par['comment_id'])) $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');

	  $CI->db->order_by('log_date' , 'desc');
           
	  $query = $CI->db->get('dlog');
	  
	  if ($query->num_rows() > 0) 
	      $result = $query->result_array();  
	      
	  return $result;     
}



// получить все комменты с которых пораждались дискуссии
function diaog_get_all_parent_disc()
{
    $cache_key = 'parent_disc';
    $out = mso_get_cache($cache_key);
    if ($out) return $out;
    
	  $CI = & get_instance();
	  $CI->db->select('discussion_id , discussion_parent_comment_id , discussion_title');
    $CI->db->where('discussion_parent_comment_id >', 0);
    
	  $query = $CI->db->get('ddiscussions');
	  if ($query->num_rows() > 0) 
	  {
	      $result = $query->result_array();
	      $out = array();
	      foreach ($result as $res)
	      {
	         if (!isset($out[$res['discussion_parent_comment_id']])) $out[$res['discussion_parent_comment_id']] = array();
	         $out[$res['discussion_parent_comment_id']][] = $res;
	      }
	  }    
	  else $out = false; 
	   
	  mso_add_cache($cache_key, $out); 
	   
    return $out;

}


# получаем все метки дискуссии
function dialog_get_tags($par=array())
{
  if (!isset($par['discussion_id'])) return array();

	$discussion_id = (int) $par['discussion_id'];
	if (!$discussion_id) return array();

	$CI = & get_instance();

	$CI->db->select('meta_value');
	$CI->db->where( array ( 'meta_key' => 'tags', 'meta_id_obj' => $discussion_id, 'meta_table' => 'ddiscussions' ) );
	$CI->db->group_by('meta_value');
	$query = $CI->db->get('dmeta');

	if ($query->num_rows() > 0)
	{
		$tags = array();
		foreach ($query->result_array() as $row)
			$tags[] = $row['meta_value'];

		return $tags;
	}
	else return array();
}


# получаем все метки в массиве
function dialog_get_all_tags($options = array())
{
	$CI = & get_instance();

	$CI->db->select('meta_value, COUNT(meta_value) AS meta_count');
	$CI->db->where( array (	'meta_key' => 'tags', 'meta_table' => 'ddiscussions' ) );
	$CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dmeta.meta_id_obj' );

	$CI->db->where( 'discussion_approved', '1'); // только разрешенные
	$CI->db->where( 'discussion_private', '0'); // только публичные

	$CI->db->group_by('meta_value');
	$query = $CI->db->get('dmeta');

	// переделаем к виду [метка] = кол-во
	if ($query->num_rows() > 0)
	{
		$tags = array();
		foreach ($query->result_array() as $row)
			$tags[$row['meta_value']] = $row['meta_count'];

		return $tags;
	}
	else return array();
}


function dialog_get_comment_discussion_id($comment_id = 0) 
{
	$CI = & get_instance();
	$CI->db->select('comment_discussion_id');
	$CI->db->where( 'comment_id', $comment_id); 
	$query = $CI->db->get('dcomments');
	if ($query->num_rows() > 0)	
	{
	  $result = $query->row_array(1);
	  return $result['comment_discussion_id'];
	}
  else return 0;
}

?>