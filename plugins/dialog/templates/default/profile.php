<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// реактирование профайла


$edit_profile = dialog_get_profile($segment2 , $options);

if ($edit_profile)
{

   mso_head_meta('title', $options['profiles'] . ' >> ' . $edit_profile['profile_psevdonim']); 
   mso_head_meta('description', $options['desc'] ); 
   require($template_dir . 'head.php'); 

   if (!isset($edit_profile['profile_comments_on_page'])) $edit_profile['profile_comments_on_page'] = $options['comments_on_page'];
   

  // Начало вывода_______________________________________________________________
  require(getinfo('template_dir') . 'main-start.php');

  echo NR . '<div class="dialog_page">' . NR;

  require($template_dir . 'do.php');


 // обработаем данные
  if ( $post = mso_check_post(array('f_session_id', 'f_user_submit', 'f_comusers_email', 'f_comusers_password',
					'f_psevdonim', 'f_comments_on_page', 'f_podpis')) ) // это обновление формы
	{ 
      require($template_dir . 'get_new_profile_info.php');
  }



   // выясним - на какой странице профиля мы находимся
   if (mso_segment(3) == $options['comments_slug'])
   {
      $profile_link = '<a href="' . $siteurl . $options['profile_slug'] . '/' . $edit_profile['profile_user_id'] . '">' . $edit_profile['profile_psevdonim'] . '</a>' . $options['breadcrumbs_razd'] . $options['all-comments'];
      
   }
   elseif ( (mso_segment(3) == $options['subscribe_slug']) and ( ($comuser_role == 3) or ($edit_profile['profile_user_id'] == $comuser_id) ) )
   {
      $profile_link = '<a href="' . $siteurl . $options['profile_slug'] . '/' . $edit_profile['profile_user_id'] . '">' . $edit_profile['profile_psevdonim'] . '</a>' . $options['breadcrumbs_razd'] . $options['subscribers'];
      
   } 
   elseif (mso_segment(3) == $options['guds_slug'])
   {
      $profile_link = '<a href="' . $siteurl . $options['profile_slug'] . '/' . $edit_profile['profile_user_id'] . '">' . $edit_profile['profile_psevdonim'] . '</a>' . $options['breadcrumbs_razd'] . $options['guds'];
      
   }  
   elseif (mso_segment(3) == $options['send_email_slug'])
   {
      $profile_link = '<a href="' . $siteurl . $options['profile_slug'] . '/' . $edit_profile['profile_user_id'] . '">' . $edit_profile['profile_psevdonim'] . '</a>' . $options['breadcrumbs_razd'] . $options['send_email_title'];
      
   }    
   elseif ( (mso_segment(3) == $options['log_slug']) and ( ($comuser_role == 2) or ($comuser_role == 3) or ($edit_profile['profile_user_id'] == $comuser_id) ) )
   {
      $profile_link = '<a href="' . $siteurl . $options['profile_slug'] . '/' . $edit_profile['profile_user_id'] . '">' . $edit_profile['profile_psevdonim'] . '</a>' . $options['breadcrumbs_razd'] . $options['log_user'];
      
   }       
   else $profile_link = $edit_profile['profile_psevdonim'];
  
   $profiles_link = '<a href="' . $siteurl . $options['profile_slug'] . '">' . $options['profiles'] . '</a>';
  
  // хлебные крошки
  echo '<div class="breadcrumbs">' . $main_link  . $options['breadcrumbs_razd'] . $profiles_link . $options['breadcrumbs_razd'] . $profile_link . '</div>';




  //Выводим  заголовок и описание  ______________________________ 
  echo '<H1>' . $edit_profile['profile_psevdonim']  . '</H1>';

 //подключим нужный файл вывода
  if (mso_segment(3) == $options['comments_slug']) 
  {
     echo '<H3>' . $options['all-comments']  . '</H3>';
     require($template_dir . 'user-comments.php');
  }
  elseif (mso_segment(3) == $options['subscribe_slug']) 
  {
      // подписки могут просматривать хозяева профиля и администраторы
     if ( ($edit_profile['profile_user_id'] == $comuser_id) or ($comuser_role == 3))
     {
        echo '<H3>' . $options['subscribers']  . '</H3>';
        require($template_dir . 'user-subscribers.php');
     }
     else echo '<H3>' . $options['access_denided']  . '</H3>';  
  } 
  elseif (mso_segment(3) == $options['guds_slug']) 
  {
     echo '<H3>' . $options['guds']  . '</H3>';
     require($template_dir . 'user-dankes.php');
  }  
  // форма послать сообщение, если можно
  elseif ( (mso_segment(3) == $options['send_email_slug']) and ($edit_profile['profile_allow_msg'] == '1') )
  {
    require($template_dir . 'user-send_mail.php');
  
  }  
  // лог действий
  elseif (mso_segment(3) == $options['log_slug']) 
  {
      // лог могут просматривать хозяева профиля модераторы и администраторы
     if ( ($edit_profile['profile_user_id'] == $comuser_id) or ($comuser_role == 3) or ($comuser_role == 2) )
     {
        echo '<H3>' . $options['log_user']  . '</H3>';
        require($template_dir . 'user-log.php');
     }
     else echo '<H3>' . $options['access_denided']  . '</H3>';  
  }    
  else // выводим меню
  {
     echo '<div class="profile_menu">';
     // ссылка на все комменты
     echo '<p><a href="' . $siteurl . $options['profile_slug'] . '/' . $edit_profile['profile_user_id'] . '/' . $options['comments_slug']  . '">' . $options['all-comments'] . '</a>';
     
     // подписки и настройки могут просматривать хозяева профиля и администраторы
     if ( ($edit_profile['profile_user_id'] == $comuser_id) or ($comuser_role == 3))
     {
        echo ' | <a href="' . $siteurl . $options['profile_slug'] . '/' . $edit_profile['profile_user_id'] . '/' . $options['subscribe_slug']  . '">' . $options['subscribers'] . '</a>';
                echo ' | <a href="' . $siteurl . 'users' . '/' . $edit_profile['profile_user_id'] . '/edit">' . $options['profile_main'] . '</a>';
     }    
     echo ' | <a href="' . $siteurl . $options['profile_slug'] . '/' . $edit_profile['profile_user_id'] . '/' . $options['guds_slug']  . '">' . $options['guds'] . '</a>';
     
    // ссылка на все дискуссии где учавствовал профиль
    echo ' | <a href="' . $siteurl . $options['main_slug'] . '/' . $options['all-discussions_slug'] . '/member'  . '">' . $options['disc_user_comment'] . '</a>';     
    // ссылка на лог действий
    if ( ($comuser_role == 2) or ($comuser_role == 3) or ($edit_profile['profile_user_id'] == $comuser_id) )
       echo ' | <a href="' . $siteurl . $options['profile_slug'] . '/' . $edit_profile['profile_user_id'] . '/' . $options['log_slug']  . '">' . $options['log_user'] . '</a>';
    
     // если разрешено посылать сообщения
     if ( ($edit_profile['profile_allow_msg'] == '1') or ($comuser_role == 3))
     {
        echo ' | <a href="' . $siteurl . $options['profile_slug'] . '/' . $edit_profile['profile_user_id'] . '/' . $options['send_email_slug']  . '">' . $options['send_email_title'] . '</a>';
     }      
     
     echo '</p>'; 
     
     echo '</div>';
        
     require($template_dir . 'new_profile_info-form.php');

  }
  

 
  require($template_dir . 'posle.php');

   
   // конец вывода
   
   echo NR . '</div><!-- class="dialog_page" -->' . NR;

   require(getinfo('template_dir') . 'main-end.php');

   $error = false;
}
else $error = $options['out_of_profiles']; 

 
?>