<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// публичные страницы профиля
// блок-добавка к главной странице профиля раздела данных на форуме
// для плагина profile
// этот файл подключается из плагина profile

    require_once( getinfo('plugins_dir') . 'dialog/functions/functions.php' );

    $CI = get_instance();
	  $CI->db->select('dprofiles.*');
	  $CI->db->where('profile_user_id', $comusers_id);
	  $query = $CI->db->get('dprofiles');
	  if ($query->num_rows() > 0) 
	  {
	     $profile = $query->row_array(1);
	     
	     $register_date = _dialog_date('j F Y' , $profile['profile_date_first_visit']);
 	  
 	     if ($profile['profile_user_role_id'] == 1) $profile_role = 'Пользователь';
 	     elseif ($profile['profile_user_role_id'] == 2) $profile_role = 'Модератор';
 	     elseif ($profile['profile_user_role_id'] == 3) $profile_role = 'Администратор';
 	     else $profile_role = '';	     
	     
 	     $profiles_blocks .= '<H3>На форуме с ' . $register_date . '</H3>';
 	     $profiles_blocks .= '<p>' . $profile_role . '</p>';
 	     $profiles_blocks .= '<p>' . 'Сообщений на форуме: ' . $profile['profile_comments_count'] . '</p>';
 	     
		 if ($profile['profile_date_last_active']) 
		 {
		    $active_date = _dialog_date('j F Y' , $profile['profile_date_last_active']);
			$profiles_blocks .= '<p>' . 'Последняя активность: ' . $active_date . '</p>';
         }

         if ($profile['profile_dankes']) $profiles_blocks .= '<p>' . 'Получено благодарностей: ' . $profile['profile_dankes'] . '</p>';
		 if ($profile['profile_rate']) $profiles_blocks .= '<p>' . 'Рейтинг на форуме: ' . $profile['profile_rate'] . '</p>';
 	     
       // Если есть залогиненный комюзер - выведем все действия этого профайла относительно комюзера	
       if ($comuser_login = is_login_comuser()) $login_id = $comuser_login['comusers_id'];
       else $login_id = 0; 
       
 	     if ($comusers_id and ($login_id != $comusers_id))
 	     {
 	       // Что делал этот пользователь вам
 	       $res = get_activ($login_id , $comusers_id);
 	       if ($res['sub'] or $res['you'])
 	       {
 	        $profiles_blocks .= '<H3>Ваши и этого пользователя действия по отношению друг к другу</H3>';
 	        
 	        $profiles_blocks .= '<table><tr><td>';
 	        
 	        $profiles_blocks .= '<p>Ваши действия к пользователю</p>';
 	        if ($res['sub'])
 	        {
 	          foreach ($res['sub'] as $cur)
 	           if ($cur['count']) 
 	           {
 	              $url = getinfo('siteurl') . $options['profiles_slug'] . '/' . $login_id . '/all/' . $cur['slug'] . '/' . $comusers_id;
 	              $profiles_blocks .= '<p><a href="' . $url . '" title="'.$cur['all'].'"><img src="' . $cur['img'] . '">' . $cur['name'] . ' (' . $cur['count'] . ')</a></p>';
 	           }  
 	        }    
 	        else $profiles_blocks .= 'Не совершались'; 
 	        
          $profiles_blocks .= '</td><td>';
          
 	        $profiles_blocks .= '<p>Действия пользователя к Вам</p>';
 	        if ($res['you'])
 	        {
 	          foreach ($res['you'] as $cur)
 	           if ($cur['count']) 
 	           {
 	              $url = getinfo('siteurl') . $options['profiles_slug'] . '/' . $comusers_id . '/all/' . $cur['slug'] . '/' . $login_id;
 	              $profiles_blocks .= '<p><a href="' . $url . '" title="'.$cur['all'].'"><img src="' . $cur['img'] . '">' . $cur['name'] . ' (' . $cur['count'] . ')</a></p>';
 	           } 
 	        }   
 	        else $profiles_blocks .= 'Не совершались'; 
 	           
 	        $profiles_blocks .= '</td></tr></table>';   
 	       }      
 	     }
    }
    else $profiles_blocks .= 'Форум не посещал.';
    
function get_activ($login_id , $comusers_id)
{
    $cache_key = 'get_activ' . $login_id . '_' . $comusers_id;
    $res = mso_get_cache($cache_key);
    if ($res) return $res;
    
    $res = array();
    $res['you'] = array();
    $res['sub'] = array();
    
    $all_el = mso_get_option('dialog_profiles', 'plugins', array());
    
    /*
       0 Сообщения
	   1 Благодарил
	   2 Получал блогадарности	
	   3 Положительно оцененные комментарии	
	   4 Положительно оценивал комментарии
	   5 Отрицательно оцененные комментарии
	   6 Отрицательно оценивал комментарии
	   7 Перенесенные сообщения	
	   8 ответил вам
	   */
	   
	   if (!isset($all_el[8])) return $res;
	   
	  $CI = & get_instance();
	
	
	  // Получим Благодарил вас
	  $CI->db->select('dgud.*'); 
	  $CI->db->where('gud_user_id', $comusers_id);
	  $CI->db->where('gud_autor_id', $login_id);
	  $query = $CI->db->get('dgud');
	  if ($query->num_rows())
	  {
	    $all_el[2]['count'] = $query->num_rows();
	    $res['you'][] = $all_el[2];
	  }  
	  
	  // Получим Благодарили вы 
	  $CI->db->select('dgud.*'); 
	  $CI->db->where('gud_user_id', $login_id);
	  $CI->db->where('gud_autor_id', $comusers_id);
	  $query = $CI->db->get('dgud');
	  if ($query->num_rows())
	  {
	    $all_el[1]['count'] = $query->num_rows();
	    $res['sub'][] = $all_el[1];
	  }  
	  	  
	  // Получим (+) вас
	  $CI->db->select('dvotes.*'); 
	  $CI->db->where('vote_user_id', $comusers_id);
	  $CI->db->where('vote_autor_id', $login_id);
	  $CI->db->where('vote', '1');
	  $query = $CI->db->get('dvotes');
	  if ($query->num_rows())
	  {
	    $all_el[3]['count'] = $query->num_rows();
	    $res['sub'][] = $all_el[3];
	  }  
	  
	  // Получим (+) вы
	  $CI->db->select('dvotes.*'); 
	  $CI->db->where('vote_user_id', $login_id);
	  $CI->db->where('vote_autor_id', $comusers_id);
	  $CI->db->where('vote', '1');
	  $query = $CI->db->get('dvotes');
	  if ($query->num_rows())
	  {
	    $all_el[4]['count'] = $query->num_rows();
	    $res['sub'][] = $all_el[4];
	  }    	  	  
	  
	  
	  // Получим (-) вас
	  $CI->db->select('dvotes.*'); 
	  $CI->db->where('vote_user_id', $comusers_id);
	  $CI->db->where('vote_autor_id', $login_id);
	  $CI->db->where('vote', '0');
	  $query = $CI->db->get('dvotes');
	  if ($query->num_rows())
	  {
	    $all_el[6]['count'] = $query->num_rows();
	    $res['you'][] = $all_el[6];
	  }   

	  	  
	  // Получим (-) вы
	  $CI->db->select('dvotes.*'); 
	  $CI->db->where('vote_user_id', $login_id);
	  $CI->db->where('vote_autor_id', $comusers_id);
	  $CI->db->where('vote', '0');
	  $query = $CI->db->get('dvotes');
	  if ($query->num_rows())
	  {
	    $all_el[5]['count'] = $query->num_rows();
	    $res['sub'][] = $all_el[5];
	  }  
	  
	  // вы ответили
	    $questions = array();
	    $questions_id = array();	  
       // получим все комменты - ваши ответы (у которых не пустое поле parent_id)
	    $CI->db->select('comment_id , comment_parent_id'); 
	    $CI->db->where('comment_parent_id >' , 0 );
	    $CI->db->where('comment_creator_id', $login_id);
	    $query = $CI->db->get('dcomments');
	    if ($query->num_rows() > 0)
	    {
		    $comments = $query->result_array();
		    // массив id вопросов пользователю
		    foreach ($comments as $comment)
	           $questions_id[] = $comment['comment_parent_id'];
	    }  
	    
	    
	 if ($questions_id) 
	 {  
	    // теперь получим все вопросы пользователя , на которые вы отвечали
        $CI->db->select('comment_id');
	    $CI->db->where_in('comment_id' , $questions_id );
	    $CI->db->where('comment_approved', '1');
	    $CI->db->where('comment_deleted', '0');
	    $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	    $CI->db->where('discussion_approved', '1');
	    $CI->db->where('discussion_private', '0');
	    $CI->db->where('comment_creator_id' , $comusers_id);
	   	
	    $query = $CI->db->get('dcomments');

	    if ($query->num_rows())
	    {
	        $all_el[8]['count'] = $query->num_rows();
	        $res['sub'][] = $all_el[8];
	     } 	    
	 }   
	 

	    // пользователь ответил
	    $all_el[8]['count'] = 0;
	    $questions = array();
	    $questions_id = array();
	    		  
       // получим все комменты - ответы пользователя
	    $CI->db->select('comment_id , comment_parent_id'); 
	    $CI->db->where('comment_parent_id >' , 0 );
	    $CI->db->where('comment_creator_id', $comusers_id);
	    $query = $CI->db->get('dcomments');
	    if ($query->num_rows() > 0)
	    {
		    $comments = $query->result_array();
		    // массив id вопросов 
		    foreach ($comments as $comment)
	           $questions_id[] = $comment['comment_parent_id'];
	    }  
	    	 
	 if ($questions_id)   
	 {
	    // теперь получим ответы на комменты в $questions_id
        $CI->db->select('comment_id');
	    $CI->db->where_in('comment_id' , $questions_id );
	    $CI->db->where('comment_approved', '1');
	    $CI->db->where('comment_deleted', '0');
	    $CI->db->join('ddiscussions', 'ddiscussions.discussion_id = dcomments.comment_discussion_id');
	    $CI->db->where('discussion_approved', '1');
	    $CI->db->where('discussion_private', '0');
	    $CI->db->where('comment_creator_id' , $login_id);
	   	
	    $query = $CI->db->get('dcomments');

	    if ($query->num_rows())
	    {
	        $all_el[8]['count'] = $query->num_rows();
	        $res['you'][] = $all_el[8];
	     }  
	  }
	  	   

	  

	  mso_add_cache($cache_key, $res);
	  return ($res);
}


    
?>