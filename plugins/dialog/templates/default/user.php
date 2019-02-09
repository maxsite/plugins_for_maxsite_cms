<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// публичные страницы профиля
// главный файл

  $error = false;

  $edit_profile = dialog_get_profile($segment2 , $options);


  $profile_plugin_optoins['profiles_title'] = isset($profile_plugin_optoins['profiles_title']) ? $profile_plugin_optoins['profiles_title'] : 'Пользователи форума';
  $profile_plugin_optoins['profiles_slug'] = isset($profile_plugin_optoins['profiles_slug']) ? $profile_plugin_optoins['profiles_slug'] : 'profiles';
	
	
  // получим страницы личного кабинета
  // и если получить не удалось, то будут только страницы форума
  $profile_plugin_optoins['pages_profiles'] = isset($profile_plugin_optoins['pages_profiles']) ? $profile_plugin_optoins['pages_profiles'] : $options['pages_profiles'];
  
  $profile_plugin_optoins['exit'] = isset($profile_plugin_optoins['exit']) ? $profile_plugin_optoins['exit'] : 'Выход';
  $profile_plugin_optoins['hello'] = isset($profile_plugin_optoins['hello']) ? $profile_plugin_optoins['hello'] : 'Привет';  
  
 
  $fn = 'head.php';
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
    require($template_dir . $fn);
  else 
    require($template_default_dir . $fn); 
          
  mso_hook_add( 'head', 'profile_head');
  
  
  if ( isset($profile_plugin_optoins['pages_profiles'][mso_segment(3)]) ) 
	  $title = $profile_plugin_optoins['pages_profiles'][mso_segment(3)];
  else 
  {
	  $title = '';
  }
  
  
  mso_head_meta('title', $edit_profile['comusers_nik'] . ' >> ' . $title ); 


if ($edit_profile)
{

   mso_head_meta('description', $options['desc'] ); 

   if (!isset($edit_profile['profile_comments_on_page'])) 
	   $edit_profile['profile_comments_on_page'] = $options['comments_on_page'];
   
  // Начало вывода_______________________________________________________________
  require(getinfo('shared_dir') . 'main/main-start.php');
  echo NR . '<div class="dialog_page">' . NR;

/*  
    $fn = 'do.php'; 
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
     require($template_dir . $fn);
  else 
     require($template_default_dir . $fn);   
*/
 
  // меню профиля
  echo '<div class="profile_menu">' . NR;
  $url = getinfo('siteurl') . $options['profile_slug'] . '/' . $segment2 . '/';
  echo $edit_profile['comusers_nik'] . ': ';
  if ($profile_plugin_optoins['pages_profiles']) 
   foreach ($profile_plugin_optoins['pages_profiles'] as $slug=>$cur_title)
   {
    if ($slug == '0') $slug = '';
    if ( $slug == mso_segment(3) ) echo $cur_title . ' | ' . NR;
    else echo '<a href="' . $url . $slug . '">' . $cur_title . '</a> | ' . NR;
   }  
  echo '</div>' . NR;
  
  
  echo '<div class="profile_title">' . NR;
  echo '<a href="' . getinfo('siteurl') . $profile_plugin_optoins['profiles_slug'] . '">' . $profile_plugin_optoins['profiles_title'] . '</a> >> ' . $edit_profile['comusers_nik'];
  if ($title)  echo ' >> ' . $title;
  $to_forum  = $link_login . ' <a href="' . $siteurl . $options['main_slug'] . '">' . $options['to_forum'] . '</a>';
  
  echo '<span class="right">' . $to_forum . '</span>' . NR;  
  echo '</div>' . NR;
/*
 // обработаем данные если было редактирование настроек форума
  if ( $post = mso_check_post(array('f_session_id', 'f_user_submit', 'f_comusers_email', 'f_comusers_password',
					'f_psevdonim', 'f_comments_on_page', 'f_podpis')) ) // это обновление формы
	{ 
     $fn = 'get_new_profile_info.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);       
  }
*/

  $profiles_link = false;


 //подключим нужный файл вывода
  if (mso_segment(3) == $options['comments_slug']) // сообщения пользователя
  {
     $fn = 'user-comments.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);     
  }
  
  // ссылки на страницу подписок пользователя пока нет в меню профиля, но если ввести ссылку вручную - откроется
  elseif (mso_segment(3) == $options['subscribe_slug']) 
  {
      // подписки могут просматривать хозяева профиля и администраторы
     if ( ($edit_profile['profile_user_id'] == $comuser_id) or ($comuser_role == 3))
     {
       $fn = 'user-subscribers.php';
       if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
       else 
          require($template_default_dir . $fn);        
     }
     else echo '<H3>' . $options['access_denided']  . '</H3>';  
  } 
  elseif (mso_segment(3) == $options['guds_slug']) //благодарности пользователя
  {
       $fn = 'user-dankes.php';
       if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
       else 
          require($template_default_dir . $fn);      
  }  
  // форма послать сообщение, если можно
  elseif ( (mso_segment(3) == $options['send_email_slug']))
  {
       $fn = 'user-send_mail.php';
       if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
       else 
          require($template_default_dir . $fn);     
  }  
  // лог действий
  elseif (mso_segment(3) == $options['log_slug']) 
  {
       $fn = 'user-log.php';
       if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
       else 
          require($template_default_dir . $fn);      
  }    
  // главная страница профайлов
  elseif (!$profile_plugin and !$segment3) // если нет плагина profile
  {
    // сводная информация
       $fn = 'user-info.php';
       if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
       else 
          require($template_default_dir . $fn);      
  }    	
  else 
  {
	   // сюда вообще-то не должны попасть
	   echo 'Ничего не найденно';
  }	  
  
 /*
    $fn = 'posle.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn); 
 */  
   // конец вывода
   
   echo NR . '</div><!-- class="dialog_page" -->' . NR;
   require(getinfo('shared_dir') . 'main/main-end.php');

}
else $error = $options['out_of_profiles']; 

 
?>