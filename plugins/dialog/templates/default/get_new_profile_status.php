<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**

изменяем атрибуты просмотра дискуссии пользователем

*/

 require ($plugin_dir . 'functions/modify_db.php');

 $errors = array();

 


		if ( !isset($post['comment_email']) or !$post['comment_email'] )
						$errors[] = 'Нужно указать Email';

		if ( !isset($post['comment_password']) or !$post['comment_password'] )
						$errors[] = 'Нужно указать пароль';



		$user['comment_email'] = mso_strip($post['comment_email']);
		$user['comment_password'] = mso_strip($post['comment_password']);

		if ( !mso_valid_email($user['comment_email']) ) $errors[] = 'Ошибочный Email';


    if (isset($post['comment_password_md'])) $user['comment_password_md'] = $post['comment_password_md'];
        
	  // проверим пользователя
	  // здесь мы сравним чтобы id было как у залогиненного (заодно проверим залогиненность)
	  $comuser_id2 = dialog_get_comuser($user);
	  
	  if (!$comuser_id2 or ($comuser_id2 != $comuser_id)) $errors[] = 'Неверные регистрационные данные';

		$status_name = mso_array_get_key($post['dialog_status_profile_submit']);
		
		if (!$errors)
		{
		   $upd_date = array ();
		
       if ($status_name == '10')
          $upd_date['profile_comments_on_page'] = 10;
       elseif ($status_name == '20')
          $upd_date['profile_comments_on_page'] = 20;
       elseif ($status_name == '30')
          $upd_date['profile_comments_on_page'] = 30;
       
       elseif ($status_name == 'vid0')
          $upd_date['profile_vid'] = '0';
       elseif ($status_name == 'vid1')
          $upd_date['profile_vid'] = '1';                     

		   // если меняем статус в Dprofies
       if ($upd_date)
       {
			     $CI = & get_instance();
			     
			     $CI->db->where('profile_user_id', $comuser_id);
			     $res = ($CI->db->update('dprofiles', $upd_date )) ? '1' : '0';
			
			     $CI->db->cache_delete_all();
			     mso_flush_cache(); // сбросим кэш
			
			     if (!$res) $errors = 'Ошибка БД при обновлении настроек просмотра';
			     else $comuser = dialog_get_profile($comuser_id , $options);
        }
        // может нужно изменить статус подпискм
        else
        {
            if ($status_name == 'subscribe')
               $upd_date['watch_subscribe'] = '1';
            elseif  ($status_name == 'unsubscribe')   
               $upd_date['watch_subscribe'] = '0';
              
               
           if ($upd_date)
           {
			         $CI = & get_instance();
			        
			         $CI->db->where('watch_user_id', $comuser_id);
			         $CI->db->where('watch_discussion_id', $discussion['discussion_id']);
			         $res = ($CI->db->update('dwatch', $upd_date )) ? '1' : '0';
			
			         $CI->db->cache_delete_all();
			         mso_flush_cache(); // сбросим кэш
			
			         if (!$res) $errors = 'Ошибка БД при обновлении подписок';
			         else $discussion['watch_subscribe'] = $upd_date['watch_subscribe'];
            }               
        }

    }

   //выведем результат обработки
   if (!is_array($errors)) $errors = array();
   
   if ($errors) 
   { 
       echo '<div class="comment-error">';
       foreach ($errors as $error) echo $error;
       echo '</div>'; 
   }      
   else 
   {
   
      if ($status_name == 'subscribe') echo '<div class="comment-ok">' . $options['subscribe-ok'] . '</div>'; 
      if ($status_name == 'unsubscribe') echo '<div class="comment-ok">' . $options['unsubscribe-ok'] . '</div>'; 
      
      if ($status_name == 'vid0') echo '<div class="comment-ok">' . $options['vid0-ok'] . '</div>'; 
      if ($status_name == 'vid1') echo '<div class="comment-ok">' . $options['vid1-ok'] . '</div>';   
      
      
      // если меняем кол-во сообщений на странице
      if (in_array($status_name , array( '10' , '20', '30')))
      {
         // сохраним пользователя примерно в том же месте что и до изменения пагинации
         
         // если флаг редиректа есть
         if (isset($flag_redirect) and $flag_redirect)
         {
           reset($comments);
           $comment0_id = key($comments);    
           if ($comment0_id)
           {
              // запишем номер первого коммента на странице, где находился пользователь до изменения
              // после изменения перенаправим на этот комент
              mso_redirect($options['goto_slug'] . '/disc/' . $discussion['discussion_id'] . '/comm/' . $comment0_id);
           }
         }
         else
         {
           // редирект на первую страницу пагинации текущего урла
           mso_redirect($options['main_slug']  . '/' . $options['all-comments_slug'] . '/' . $segment3);
         }  
         // было до редиректа
         echo '<div class="comment-ok">' . $options['pag-ok'] . '</div>'; 
      }   
   }   

   echo '<div class="break"></div>';
 
 ?>