<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

  require(getinfo('shared_dir') . 'main/main-start.php');
  echo NR . '<div class="type_loginform">' . NR;

  echo '<p class="header">Только зарегистрированные пользователи имеют доступ к личному кабинету.</p>';

  echo '<p><a href="'. getinfo('siteurl') . 'loginform">' . 'Войти' . '</a></p>';
  echo '<p><a href="'. getinfo('siteurl') . $options['register_slug'] . '">' . 'Зарегистрироваться' . '</a></p>';
  echo '<p><a href="'. getinfo('siteurl') . 'password-recovery">' . 'Восстановить пароль' . '</a></p>';
  

   echo NR . '</div><!-- class="type_loginform" -->' . NR;
	 require(getinfo('shared_dir') . 'main/main-end.php');

?>