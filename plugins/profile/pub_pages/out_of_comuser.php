<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

  // нет такого пользователя

 header('HTTP/1.0 404 Not Found'); 

 require(getinfo('shared_dir') . 'main/main-start.php');
 echo NR . '<div class="type type_users">' . NR;

 echo '<p>Нет такого пользователя</p>';
 echo '<a href="' . getinfo('siteurl') . $options['profiles_slug'] . '">' . $options['profiles_title'] . '</a>'; 

 echo NR . '</div><!-- class="type type_users_form" -->' . NR;
 require(getinfo('shared_dir') . 'main/main-end.php');


?>