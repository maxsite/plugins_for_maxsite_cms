<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// формирование страниц личного кабинета залогиненного пользователя, показываемые только ему

  // личный кабинет может формировать плагин profile
  $profile_plugin_optoins = mso_get_option('profile', 'plugins', array());
  $profile_plugin_optoins['title'] = isset($profile_plugin_optoins['title']) ? $profile_plugin_optoins['title'] : 'Личный кабинет';
  // получим страницы личного кабинета плагина profile
  // если не получилось, будут только страницы форума
  
  $profile_plugin_optoins['pages'] = isset($profile_plugin_optoins['pages']) ? $profile_plugin_optoins['pages'] : $options['pages_profile'];


  // остальные опции линого кабинета
  $profile_plugin_optoins['exit'] = isset($profile_plugin_optoins['exit']) ? $profile_plugin_optoins['exit'] : 'Выход';
  $profile_plugin_optoins['hello'] = isset($profile_plugin_optoins['hello']) ? $profile_plugin_optoins['hello'] : 'Привет';  
  $profile_plugin_optoins['to_profile'] = isset($profile_plugin_optoins['to_profile']) ? $profile_plugin_optoins['to_profile'] : 'Профиль';  
  
  $fn = 'head.php'; 
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
       require($template_dir . $fn);
  else 
       require($template_default_dir . $fn); 
         
  if (!isset($profile_plugin_optoins['pages'][$segment2])) $profile_plugin_optoins['pages'][$segment2] = $profile_plugin_optoins['title'];
     mso_head_meta('title', $profile_plugin_optoins['title'] . ' >> ' . $profile_plugin_optoins['pages'][$segment2] ); 

  $edit_profile = $comuser;



  // Начало вывода_______________________________________________________________
  require(getinfo('shared_dir') . 'main/main-start.php');
  echo NR . '<div class="type type_users_form">' . NR;

 // обработаем данные
  if ( $post = mso_check_post(array('f_session_id', 'f_user_submit', 'f_comusers_email', 'f_comusers_password',
					'f_psevdonim', 'f_comments_on_page', 'f_podpis')) ) // это обновление формы
  { 
      $fn = 'get_new_profile_info.php'; 
      if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
      else 
        require($template_default_dir . $fn); 
              
      $edit_profile = dialog_get_login_profile($options);
  }

  echo '<div class="profile_title">' . NR;
  echo $profile_plugin_optoins['title'] . ' >> ' . $profile_plugin_optoins['pages'][$segment2];
    /*
  // приветствие или войти
	if (!$comuser['comusers_nik']) $login_name = ''; 
	else $login_name = $comuser['comusers_nik'];
	$link_login = $profile_plugin_optoins['hello'] . ' ' . $login_name . ': <a href="'. getinfo('siteurl') . $profile_plugin_optoins['profiles_slug'] . '/' . $edit_profile['comusers_id'] . '">' . $profile_plugin_optoins['to_profile'] . '</a> <a href="'. getinfo('siteurl') . 'logout">' . $profile_plugin_optoins['exit'] . '</a>';
  
 $to_forum  = ' <a href="' . $siteurl . $options['main_slug'] . '">' . $options['to_forum'] . '</a>';
  echo '<span class="right">' . $link_login . $to_forum . '</span>' . NR;
  */
  echo '</div>' . NR;
  
  // выведем меню страниц личного кабинета
  echo '<div class="profile_menu">' . NR;
  
  $url = getinfo('siteurl') . $options['comuser_profile_slug'] . '/';
  $i = false;
  foreach ($profile_plugin_optoins['pages'] as $slug=>$title)
  {
    if ($i) echo ' | ';    
    if ($slug == '0') $slug = '';
    if ( $slug == mso_segment(2) ) echo $title . NR;
    else echo '<a href="' . $url . $slug . '">' . $title . '</a>' . NR;
    $i = true;
  }  
  
  echo '</div>' . NR;
  

 //подключим нужный файл вывода
 if (mso_segment(2) == $options['settings_slug']) 
 {
  $fn = 'new_profile_info-form.php'; 
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
       require($template_dir . $fn);
  else 
       require($template_default_dir . $fn);   
 }  
 elseif (mso_segment(2) == $options['settings_subscribe_slug'])
 {
  $fn = 'user-subscribers.php'; 
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
       require($template_dir . $fn);
  else 
       require($template_default_dir . $fn);    
 }
 elseif (mso_segment(2) == $options['log_slug'])
 {
  $fn = 'user-log.php'; 
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
       require($template_dir . $fn);
  else 
       require($template_default_dir . $fn);     
 } 
 else return $args;
  
  
   echo NR . '</div><!-- class="type type_users_form" -->' . NR;

   require(getinfo('shared_dir') . 'main/main-end.php');

   $error = false;



 
?>