<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

  // выведем меню страниц личного кабинета
  echo '<div class="profile_title">' . NR;
  echo $options['title'] . ' >> ' . $title;
  /* 
    // приветствие или войти
	if (!$comusers_nik) $login_name = ''; 
	else $login_name = $comusers_nik;
	$link_login = $options['hello'] . ' ' . $login_name . ': <a href="'. getinfo('siteurl') . $options['profiles_slug'] . '/' . $comusers_id . '">' . $options['to_profile'] . '</a> <a href="'. getinfo('siteurl') . 'logout">' . $options['exit'] . '</a>';
    echo '<span class="right">' . $link_login . '</span>' . NR;
  */
  echo '</div>' . NR;  
  echo '<div class="profile_menu">' . NR;
  
  $url = getinfo('siteurl') . $options['profile_slug'] . '/';
  
  $i = false;
  foreach ($options['pages'] as $slug=>$cur_title)
  {
    if ($i) echo ' | ';    
    if ($slug == '0') $slug = '';
    if ( $slug == mso_segment(2) ) echo $cur_title . NR;
    else echo '<a href="' . $url . $slug . '">' . $cur_title . '</a>' . NR;
    $i = true;
  }  
  
  echo '</div>' . NR;
  
 

    
?>