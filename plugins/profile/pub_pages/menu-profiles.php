<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

   require (getinfo('plugins_dir') . 'profile/menu-main.php' ); // выводим главное меню
 
  // выведем меню публичных страниц пользователя
  echo '<div class="profile_menu">' . NR;
  
  $url = getinfo('siteurl') . $options['profiles_slug'] . '/' . mso_segment(2) . '/';
  
  if (!$comuser_info['comusers_nik']) $comuser_info['comusers_nik'] = t('Комментатор'). ' ' . $comuser_info['comusers_id'];

    echo $comuser_info['comusers_nik'] . ': ';

  foreach ($options['pages_profiles'] as $slug=>$cur_title)
  {
    if ($slug == '0') $slug = '';
    if ( $slug == $segment3 ) echo $cur_title . ' | ' . NR;
    else echo '<a href="' . $url . $slug . '">' . $cur_title . '</a> | ' . NR;
  }  
  
  echo '</div>' . NR;


  
  echo '<div class="profile_title">' . NR;
  echo '<a href="' . getinfo('siteurl') . $options['profiles_slug'] . '">' . $options['profiles_title'] . '</a> >> ' . $comuser_info['comusers_nik'] . ' >> ' . $title;
  echo '</div>' . NR;
  
?>