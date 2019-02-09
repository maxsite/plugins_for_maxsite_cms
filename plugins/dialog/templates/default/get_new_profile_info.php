<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// обработаем данные реактирование профайла


	  $errors = false;
	
		if ( ($edit_profile['profile_user_id'] == $comuser_id) or ($comuser_role == 3) )
		{
		    # защита рефера
		    mso_checkreferer();

        $user = array(
            'comment_password_md' => 1,
            'comment_password' => trim($post['f_comusers_password']),
            'comment_email' => trim($post['f_comusers_email'])
           );
        
        $comuser_id2 = dialog_get_comuser($user);

        if (!$comuser_id2 or ($comuser_id2 != $comuser_id)) $errors = 'Неверные регистрационные данные';
        
        

		       $comments_on_page = strip_tags($post['f_comments_on_page']);
		       if (!$comments_on_page) $comments_on_page = 0;
		       $id = (int) $comments_on_page;
		       if ( (string) $comments_on_page != (string) $id ) $comments_on_page = false; 
		       if (!$comments_on_page) $errors = 'Кол-во постов не целое число';
		       if ($comments_on_page<=0) $errors = 'Кол-во постов не положительное';
		       if ($comments_on_page>50) $errors = 'Кол-во постов больше 50';
		    

		       
		            
        if (!$errors)
        {
			      $upd_date = array (
				      'profile_podpis' =>	strip_tags($post['f_podpis']),
				      'profile_psevdonim' =>	strip_tags($post['f_psevdonim']),
				      'profile_comments_on_page' =>	$comments_on_page
			    	);

		       $upd_date['profile_allow_msg']  = isset($post['f_profile_allow_msg']) ? '1' : '0';
		       $upd_date['profile_allow_info']  = isset($post['f_profile_allow_info']) ? '1' : '0';
		       $upd_date['profile_allow_subscribe']  = isset($post['f_profile_allow_subscribe']) ? '1' : '0';
		       				
			     $CI = & get_instance();
			     
			     $CI->db->where('profile_user_id', $edit_profile['profile_user_id']);
			     $res = ($CI->db->update('dprofiles', $upd_date )) ? '1' : '0';
			
			     $CI->db->cache_delete_all();
			      mso_flush_cache(); // сбросим кэш
			
			     if (!$res) $errors = 'Ошибка БД при обновлении';
			     else 
			     {
			       $edit_profile = dialog_get_profile($segment2 , $options);
			       if (!isset($edit_profile['profile_comments_on_page'])) $edit_profile['profile_comments_on_page'] = $options['comments_on_page'];
           }

       }
     }
     else $errors = 'Ошибочные регистрационные данные';
     
   if ($errors) echo '<div class="comment-error">' . $errors . '</div>'; 
   else echo '<div class="comment-ok">' . $options['edit-ok'] . '</div>';
 
    


 
?>