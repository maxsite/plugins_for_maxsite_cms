<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// формирование страниц личного кабинета залогиненного пользователя, показываемые только ему

  // личный кабинет может формировать плагин profile
  $profile_plugin_optoins = mso_get_option('profile', 'plugins', array());
  $profile_plugin_optoins = false;
	$profile_plugin_optoins['title'] = isset($profile_plugin_optoins['title']) ? $profile_plugin_optoins['title'] : 'Личный кабинет';
  // получим страницы личного кабинета плагина profile
  // если не получилось, будут только страницы плагина bookmaker
  $profile_plugin_optoins['pages'] = isset($profile_plugin_optoins['pages']) ? $profile_plugin_optoins['pages'] : array(
     'bookmaker' => 'Избранное',
   );

  // остальные опции линого кабинета
  $profile_plugin_optoins['exit'] = isset($profile_plugin_optoins['exit']) ? $profile_plugin_optoins['exit'] : 'Выход';
  $profile_plugin_optoins['hello'] = isset($profile_plugin_optoins['hello']) ? $profile_plugin_optoins['hello'] : 'Привет';  
  $profile_plugin_optoins['to_profile'] = isset($profile_plugin_optoins['to_profile']) ? $profile_plugin_optoins['to_profile'] : 'Профиль';  
  
/*
  $fn = 'head.php'; 
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
       require($template_dir . $fn);
*/
         
 //  mso_head_meta('title', $profile_plugin_optoins['title'] . ' >> ' . $profile_plugin_optoins['pages'][$segment3] ); 

   $comuser = is_login_comuser();


  // Начало вывода_______________________________________________________________
  require(getinfo('template_dir') . 'main-start.php');

  echo NR . '<div class="type type_users_form">' . NR;

  $url = getinfo('siteurl') . $options['comuser_profile_slug'] . '/';

  // выведем меню страниц личного кабинета, если кол-во разделов больше одного
  if ( count($profile_plugin_optoins['pages']) >1)
  {
    echo '<div class="profile_menu">' . NR;
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
  }
  else
    echo '<H1>' . $profile_plugin_optoins['pages'][$segment2] . '</H1>';
  
  // теперь выведем страницы закладок по типам материалов
  echo '<div class="profile_menu">' . NR;
  $i = false;
  foreach ($options['elements'] as $slug=>$title)
  {
      if ($i) echo ' | '; else $i = true;    
      if ($slug == '0') $slug = '';
      if ( $slug == $segment3 ) echo $title . NR;
      else echo '<a href="' . $url . $segment2 . '/' . $slug . '">' . $title . '</a>' . NR;
  }  
  echo '</div>' . NR;

/*
	if (!$comuser['comusers_nik']) $login_name = ''; 
	else $login_name = $comuser['comusers_nik'];
	$link_login = $profile_plugin_optoins['hello'] . ' ' . $login_name . ': <a href="'. getinfo('siteurl') . $profile_plugin_optoins['profiles_slug'] . '/' . $comuser['comusers_id'] . '">' . $profile_plugin_optoins['to_profile'] . '</a> <a href="'. getinfo('siteurl') . 'logout">' . $profile_plugin_optoins['exit'] . '</a>';

 
  
  echo '<div class="profile_title">' . NR;
 // echo $profile_plugin_optoins['title'] . ' >> ' . $profile_plugin_optoins['pages'][$segment3];
  echo '<span class="right">' . $link_login . '</span>' . NR;
  echo '</div>' . NR;
*/

 //подключим нужный файл вывода
 if (in_array($segment3 , $element_slugs))
    require($plugin_dir . 'out/' . $segment3 . '.php');
   
  
  echo NR . '</div><!-- class="type type_users_form" -->' . NR;

  require(getinfo('template_dir') . 'main-end.php');

   $error = false;



 
?>