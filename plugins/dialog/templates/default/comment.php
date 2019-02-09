<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
// страница комментария

// сперва получим комментарий
 $comment = dialog_get_comment(array('comment_id'=>$segment2 , 'role_id'=>$comuser_role , 'user_id'=>$comuser_id));
 if ($comment)
 {
 	
 	extract($comment);
  
   mso_head_meta('title', $comment['discussion_title']); 
   mso_head_meta('description', $comment['discussion_desc']); 
   $flag_show_comments_js = true; // вывести в head js плагинов комментариев 

   $fn = 'head.php'; 
   if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
     require($template_dir . $fn);
   else 
     require($template_default_dir . $fn);
   
  // Начало вывода_______________________________________________________________
  require(getinfo('shared_dir') . 'main/main-start.php');
  echo NR . '<div class="dialog_page">' . NR;    

  $fn = 'do.php'; 
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
     require($template_dir . $fn);
  else 
     require($template_default_dir . $fn);

    // обработаем, если был запрос на изменение комментария
    if ($post = mso_check_post(array('comments_session', 'dialog_submit', 'comments_content')) )
    {
       require ($template_dir . 'get_new_comment.php'); 
       // обновим комментарий
       $comment = dialog_get_comment(array('comment_id'=>$segment2 , 'role_id'=>$comuser_role , 'user_id'=>$comuser_id));
    }   

    // обработаем, если были запросы на изменение статуса
    if ($post = mso_check_post(array('comments_session', 'dialog_status_submit')) )
    {
         require ($template_dir . 'get_new_comment_status.php'); 
       // обновим комментарий
       $comment = dialog_get_comment(array('comment_id'=>$segment2 , 'role_id'=>$comuser_role , 'user_id'=>$comuser_id));          
    }

  extract($comment);

	$comment_status = array();
	if ($comment_deleted)	$comment_status[] = $options['comment_deleted'];
	if (!$comment_approved)	$comment_status[] = $options['not_approved'];
	if ($comment_spam)	$comment_status[] = $options['spam'];
	if (!$comment_check)	$comment_status[] = $options['not_spam_check'];
  if ($comment_status) echo '<span class="red">' . implode("<br />" , $comment_status) . '</span>';
  
   if (!isset($comuser['profile_comments_on_page']))
        $comuser['profile_comments_on_page'] = $options['comments_on_page'];
        

  // хлебные крошки
  
  $discussion_link = '<a href="' . $siteurl . $options['discussion_slug'] .'/' . $comment_discussion_id . '">' . $discussion_title . '</a>';
  $comment_link = '<a href="' . $siteurl . $options['goto_slug'] . dialog_get_url($comment_discussion_id , $comment_id) . '" title ="' . $options['comment_page'] . '">' . $options['comment_number'] . $comment_id . '</a>';
  echo'<div class="breadcrumbs">' . $main_link  . $options['breadcrumbs_razd'] . $discussion_link . $options['breadcrumbs_razd'] . $comment_link . '</div>';
  
  // выводим инфу о комменте
  echo '<p>' . $options['autor'] . dialog_profile_link($comment_creator_id, $profile_psevdonim , $options['profile_slug'], $siteurl , $options['profile']) . '</p>';
  echo '<p>' . $options['date_create'] . _dialog_date('j F Y в H:i:s' , $comment_date_create) . '</p>';  
  
  
  echo '<div class="single_comment">';
  
		// имеет ли пользователь доступ к редактированию коммента
		if ($allow_edit)
		{
 		    // готовим данные для формы
       $form_title = $options['title_edit_comment_form'];
       $form_desc = $options['desc_edit_comment_form'];
       $new_discussion_flag = false;
       $free_discussion_flag = false;
       $comment_comment_content = $comment_content;
       $edit_comment_id = $comment_id;// сигнал о том что редактируем комментарий
       
       // в зависимости от роли добавим дополнительные кнопки
       $comment_form_add = '';
       // возможности только администратора
       if ($comuser_role == 3)
       {
          if ($comment_deleted) 
             $comment_form_add .= '<input name="dialog_status_submit[undelete]" type="submit" value="' . $options['form_undelete'] . '" class="comments_submit undelete">';
       }
       
       // возможности модератора и администратора
       if (($comuser_role == 3) or ($comuser_role == 2) )
       {
          if (!$comment_deleted) 
             $comment_form_add .= '<input name="dialog_status_submit[delete]" type="submit" value="' . $options['form_delete'] . '" class="comments_submit delete">';        
          if ($comment_approved) 
             $comment_form_add .= '<input name="dialog_status_submit[unapproved]" type="submit" value="' . $options['form_unapproved'] . '" class="comments_submit unapproved">';     
          else $comment_form_add .= '<input name="dialog_status_submit[approved]" type="submit" value="' . $options['form_approved'] . '" class="comments_submit approved">';   
          
          if (!$comment_check)
          {
            $comment_form_add .= '<input name="dialog_status_submit[unspam]" type="submit" value="' . $options['form_unspam'] . '" class="comments_submit unspam">';     
            $comment_form_add .= '<input name="dialog_status_submit[spam]" type="submit" value="' . $options['form_spam'] . '" class="comments_submit spam">';             
          }
          else
          {   
            if ($comment_spam ) 
                $comment_form_add .= '<input name="dialog_status_submit[unspam]" type="submit" value="' . $options['form_unspam'] . '" class="comments_submit unspam">';     
            if (!$comment_spam ) $comment_form_add .= '<input name="dialog_status_submit[spam]" type="submit" value="' . $options['form_spam'] . '" class="comments_submit spam">';     
          }         
       }
       
       
       if ($comment_form_add) $comment_form_add = $options['edit_status'] . $comment_form_add;
       
       // выводим форму
       $fn = 'new_comment-form.php'; 
       if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
           require($template_dir . $fn);
       else 
           require($template_default_dir . $fn);
		}
		else
		{
		   // просто выводим комментарий
       dialog_comment_to_out($comment_content, $options);	   
		   echo '<p>' . $comment_content . '</p>';
		}   
		
    echo NR . '</div>' . NR;
    
    echo  $options['comment_page'] . ': ' . $comment_link;
    
    
    
    // теперь выведем лог действий с комментом
    $log = dialog_get_log(array('comment_id'=>$comment_id));
    if ($log)
    {
      echo NR . '<div class="log_comment">';
      echo  '<H3>' . $options['log_comment'] . '</H3>';
      
      // подключим вывод из массива $log 
     $fn = 'out_log.php'; 
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
       require($template_dir . $fn);
     else 
       require($template_default_dir . $fn);   
          
     echo '</div>' . NR;
    }
    
    
    $fn = 'posle.php'; 
    if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
       require($template_dir . $fn);
    else 
       require($template_default_dir . $fn); 
    
    // конец вывода
    echo NR . '</div><!-- class="dialog_page" -->' . NR;
    require(getinfo('shared_dir') . 'main/main-end.php');		
    
    $error = false;
 }
 else $error = $options['out_of_comment'];



?>