<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// страница всех комментов юзера

  // если есть post то обработка изменения опций просмотра пользователем
    if ($post = mso_check_post(array('f_session_id', 'dialog_status_profile_submit')) )
    {
        $fn = 'get_new_profile_status.php'; 
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
           require($template_dir . $fn);
        else 
           require($template_default_dir . $fn);     
               
       // обновим профайл
       $comuser = dialog_get_login_profile($options);
    }
    
    extract ($comuser);
    $flag_comuser = false;
    
   // получаем сообщения
   $par = array(
        'count'=>$comuser['profile_comments_on_page'] ,
        'user_id' => $comuser_id ,
        'role_id' => $comuser_role,
        'sort_field' => 'comment_date_create',
        'comment_creator_id' => $edit_profile['profile_user_id'],
        );
    
   $comments = dialog_get_сomments($par , $pagination , $options); 

    
 

   
   // есть комменты -  выводим
   if ($comments)
   {
      if ($pagination) mso_hook('pagination', $pagination);
      $out = '';
      
   //   $fn = 'out-comments' . $comuser['profile_vid'] . '.php'; 
      $fn = 'out-comments-user.php';
      if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
      else 
        require($template_default_dir . $fn);   
           
      echo $out;
      if ($pagination) mso_hook('pagination', $pagination);
   }


 
?>