<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
// страница комментария

// сперва получим дискуссию

$discussion = dialog_get_discussion(array('user_id' => $comuser_id , 'discussion_id' => $segment2 , 'role_id' => $comuser_role) , $options);

if ($discussion)
{
  
   mso_head_meta('title', $discussion['discussion_title']); 
   mso_head_meta('description', $discussion['discussion_desc']); 

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

    // обработаем, если были запросы на изменение дискуссии
    if ($discussion['allow_edit'] and ($post = mso_check_post(array('comments_session', 'dialog_status_submit')) ))
    {
       $fn = 'get_new_discussion.php'; 
       if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
         require($template_dir . $fn);
       else 
         require($template_default_dir . $fn);
       
       // обновим дискуссию
       $discussion = dialog_get_discussion(array('user_id' => $comuser_id , 'discussion_id' => $segment2 , 'role_id' => $comuser_role) , $options);
    }

  extract($discussion);

       

  // хлебные крошки
  if ($discussion['category_slug'])
  { 
    $category_link = '<a href="' . $siteurl . $options['main_slug'] . '/' . $discussion['category_slug'] . '">' . $discussion['category_title'] . '</a>' . $options['breadcrumbs_razd'];
  }
  else $category_link = ''; 
 
  
  $discussion_link = '<a href="' . $siteurl . $options['discussion_slug'] .'/' . $discussion_id . '">' . $discussion_title . '</a>';

  echo'<div class="breadcrumbs">' . $main_link  . $options['breadcrumbs_razd'] . $category_link . $discussion_link . $options['breadcrumbs_razd'] . $options['edit_discussion']. '</div>';

 // статусы дискуссии
     if (!$discussion['discussion_approved']) echo '<div class="yellow">' . $options['not_approved']  . '</div>';
     if ($discussion['discussion_closed']) echo '<div class="bg-gray">' . $options['discussion_closed']  . '</div>';
     if ($discussion['discussion_private']) 
     {
        $members = dialog_get_names($options, $discussion['members']);
        $memb = '';
        foreach ($members as $uid) 
          $memb .= dialog_profile_link($uid['profile_user_id'], $uid['profile_psevdonim'], $options['profile_slug'] , $siteurl, $options['profile']) . ' ';
        echo '<div class="bg-gray">' . $options['discussion_private']  . '. ' . $options['private_members'] . ': ' . $memb . '</div>';
     }   
      
   
   echo '<div class="single_comment">';
		// имеет ли пользователь доступ к редактированию
		if ($allow_edit)
		{
 		    // выводим форму
       $fn = 'new_discussion_status-form.php'; 
       if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
         require($template_dir . $fn);
       else 
         require($template_default_dir . $fn);		   
		}
		else
		{
		   // просто выводим дискуссию
		   echo '<H2>' . $discussion_title . '</H2>';
		   echo '<p>' . $discussion_desc . '</p>';
		   echo '<p>' . $options['form_discussion_tags'] . ' ' .implode(', ', dialog_get_tags(array('discussion_id'=>$discussion_id))) . '</p>';
		}   
		
    echo NR . '</div>' . NR;
    
    echo  $options['discussion_page'] . ': ' . $discussion_link;
    
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