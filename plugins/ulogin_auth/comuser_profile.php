<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// формирование страниц личного кабинета залогиненного пользователя, показываемые только ему
$CI = & get_instance();

  // личный кабинет может формировать плагин profile
  $profile_plugin_optoins = mso_get_option('profile', 'plugins', array());
	$profile_plugin_optoins['title'] = isset($profile_plugin_optoins['title']) ? $profile_plugin_optoins['title'] : 'Личный кабинет';
  // получим страницы личного кабинета плагина profile
  // если не получилось, будут только одна страница
  $profile_plugin_optoins['pages'] = isset($profile_plugin_optoins['pages']) ? $profile_plugin_optoins['pages'] : false;
  $optoins['comuser_profile_title'] = isset($optoins['comuser_profile_title']) ? $optoins['comuser_profile_title'] : 'Социализация';
  
  // остальные опции личного кабинета
  $profile_plugin_optoins['exit'] = isset($profile_plugin_optoins['exit']) ? $profile_plugin_optoins['exit'] : 'Выход';
  $profile_plugin_optoins['hello'] = isset($profile_plugin_optoins['hello']) ? $profile_plugin_optoins['hello'] : 'Привет';  
  $profile_plugin_optoins['to_profile'] = isset($profile_plugin_optoins['to_profile']) ? $profile_plugin_optoins['to_profile'] : 'Профиль';  
  $profile_plugin_optoins['profiles_slug'] = isset($profile_plugin_optoins['profiles_slug']) ? $profile_plugin_optoins['profiles_slug'] : 'users';  
 
	// все доступные провайдеры
	if (!isset($options['providers_set'])) $options['providers_set'] = 'vkontakte,odnoklassniki,mailru,facebook,twitter';
	if (!isset($options['providers_hidden'])) $options['providers_hidden'] = 'google,yandex,livejournal,youtube';
  $all_providers = $options['providers_set'] . ',' . $options['providers_hidden'];
  $all_providers1 = explode("," , $all_providers);
  $all_providers = array();
  foreach ($all_providers1 as $cur_prov) $all_providers[] = trim($cur_prov);
    
   if ($profile_plugin_optoins['pages'] and isset($profile_plugin_optoins['pages'][mso_segment(2)]))
      mso_head_meta('title', $profile_plugin_optoins['title'] . ' >> ' . $profile_plugin_optoins['pages'][mso_segment(2)] ); 
   else mso_head_meta('title', $optoins['comuser_profile_title']);
   

  // Начало вывода_______________________________________________________________
  require(getinfo('template_dir') . 'main-start.php');

  echo NR . '<div class="type type_users_form">' . NR;

   extract ($comuser);

  /*
   эти поля есть, если вход при помощи провайдера:
   provider_url
   provider_key
   provider_user_id
   provider_user_url
   provider_user_avatar
   provider_user_nik
   
   поля комюзера:
   comusers_id
   comusers_password
   comusers_email
   comusers_nik
   comusers_url
   comusers_avatar_url
   comusers_last_visit
  */

  // добавим метаполя присоединенных аккаунтов
  
	$CI->db->select('meta_key, meta_value');
	$CI->db->from('meta');
	$CI->db->where('meta_table', 'comusers');
	$CI->db->where('meta_id_obj', $comusers_id);
	$query = $CI->db->get();
	if ($query->num_rows() > 0)
	{
		   // переделаем полученный массив в key = value
			 foreach ($query->result_array() as $val)
			 {
			     $prov_key = str_replace('_profile','',$val['meta_key']);
			     if (in_array($prov_key , $all_providers))
				        $comusers_providers[$prov_key] = $val['meta_value'];
				        
			  }
	}

 // обработаем данные
 require ($plugin_dir . 'functions.php');
 
 // присоединение аккаунта
 if( !empty($_POST['token']) )
 { 
	 $s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
   $connect_profile = json_decode($s, true);
   $connect_profile = get_fields($connect_profile);  
   $err = add_provider_to_comuser($comusers_id , $connect_profile['provider_key'] , $connect_profile['provider_user_id'] , $connect_profile['provider_user_url']);
   if ($err)
   {
	    echo '<div class="comment-error">' . $err . '</div>';
   }
   else
   {
     $new_provider_key = $connect_profile['provider_key'];
     $new_provider_user_nik = $connect_profile['provider_user_nik'];
     $new_provider_user_avatar = $connect_profile['provider_user_avatar'];
     // добавим в массив
     $comusers_providers[$new_provider_key] = $connect_profile['provider_user_url'];
   }
 }
 
 // отключение аккаунта
 if ( $post = mso_check_post(array('f_session_id', 'f_submit_disconnect')) ) 
 {
	   mso_checkreferer();
	   $key = mso_array_get_key($post['f_submit_disconnect']); 
	   if (in_array($key , $all_providers))
	   {
	      // проверим, чтобы не осталось ничего
	      if ( (count($comusers_providers) < 2) and (!mso_valid_email() or uogin_false_email($comusers_email)) )
	        echo '<div class="comment-error">' . t('Чтобы отсоединить все аккаунты, введите корректный email', 'plugins'). '</div>';
	      else
	      {
	         add_provider_to_comuser($comusers_id , $key);// не передаем значения, что значит - только удаляем
	         //  удалим из массива
	         unset($comusers_providers[$key]);
	      }   
	   }  
	   else echo '<div class="comment-error">' . t('Ошибка идентефикации провайдера', 'plugins') . ': ' .$key . '</div>';;
 }


 // изменение email
 if ( $post = mso_check_post(array('f_session_id', 'f_submit_comusers_email', 'f_comusers_email')) ) 
 {
	   mso_checkreferer();
	   $err = ulogin_email_modify($comusers_id , $post['f_comusers_email']);
	   if ($err)
	       echo '<div class="comment-error">' . $err . '</div>';
	   else
	      $comusers_email = $post['f_comusers_email'];
 }
 
 
 // изменение ника
 if ( $post = mso_check_post(array('f_session_id', 'f_submit_provider_user_nik', 'f_provider_user_nik')) ) 
 {
	   mso_checkreferer();
  	 $CI->db->where('comusers_id', $comusers_id);
	   $res = ($CI->db->update('comusers', array('comusers_nik'=>$post['f_provider_user_nik']))) ? '1' : '0'; 
	   if ($res)
	      $comusers_nik = $post['f_provider_user_nik'];
 } 
 
 if ( $post = mso_check_post(array('f_session_id', 'f_submit_new_provider_user_nik', 'f_new_provider_user_nik')) ) 
 {
	   mso_checkreferer();
  	 $CI->db->where('comusers_id', $comusers_id);
	   $res = ($CI->db->update('comusers', array('comusers_nik'=>$post['f_new_provider_user_nik']))) ? '1' : '0'; 
	   if ($res)
	      $comusers_nik = $post['f_new_provider_user_nik'];
 } 
  
 // изменение аватара 
 if ( $post = mso_check_post(array('f_session_id', 'f_submit_provider_user_avatar', 'f_provider_user_avatar')) ) 
 {
	   mso_checkreferer();
  	 $CI->db->where('comusers_id', $comusers_id);
	   $res = ($CI->db->update('comusers', array('comusers_avatar_url'=>$post['f_provider_user_avatar']))) ? '1' : '0'; 
	   if ($res)
	      $comusers_avatar = $post['f_provider_user_avatar'];
 } 
 
 if ( $post = mso_check_post(array('f_session_id', 'f_submit_new_provider_user_avatar', 'f_new_provider_user_avatar')) ) 
 {
	   mso_checkreferer();
  	 $CI->db->where('comusers_id', $comusers_id);
	   $res = ($CI->db->update('comusers', array('comusers_avatar_url'=>$post['f_new_provider_user_avatar']))) ? '1' : '0'; 
	   if ($res)
	      $comusers_avatar = $post['f_new_provider_user_avatar'];
 }
  
  
  
 if ($profile_plugin_optoins['pages'])
 {
  // выведем меню страниц личного кабинета
  echo '<div class="profile_menu">' . NR;
  
  $url = getinfo('siteurl') . $options['profile_slug'] . '/';
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

 
	if (!$comusers_nik) $login_name = ''; 
	else $login_name = $comusers_nik;
	$link_login = $profile_plugin_optoins['hello'] . ' ' . $login_name . ': <a href="'. getinfo('siteurl') . $profile_plugin_optoins['profiles_slug'] . '/' . $comusers_id . '">' .    $profile_plugin_optoins['to_profile'] . '</a> <a href="'. getinfo('siteurl') . 'logout">' . $profile_plugin_optoins['exit'] . '</a>';
  
  
  echo '<div class="profile_title">' . NR;
  if ($profile_plugin_optoins['pages'] and isset($profile_plugin_optoins['pages'][mso_segment(2)])) echo $profile_plugin_optoins['title'] . ' >> ' . $profile_plugin_optoins['pages'][mso_segment(2)];
  else echo $profile_plugin_optoins['title'] . ' >> ' . $optoins['comuser_profile_title'];
  echo '<span class="right">' . $link_login . '</span>' . NR;
  echo '</div>' . NR;

  // -----------------------------------------------
  



	$CI->load->helper('form');
	echo '<form action="" method="post" class="comusers-form">' . mso_form_session('f_session_id');
	echo '<input type="hidden" value="' . $comusers_email . '" name="f_comusers_email">';
	echo '<input type="hidden" value="' . $comusers_password . '" name="f_comusers_password">';

  echo '<div class="profile_block">';
  if (isset($provider_key))
  {
    echo t('Вход с помощью: ') . '<img src="'.$plugin_url.'img/'.$provider_key.'.png"> ';
    echo $provider_user_url ? '<a href="'.$provider_user_url.'">'.$provider_user_url.'</a>' : $provider_key;
  }
  else echo 'Вход произведен при помощи логина (email) и пароля.';  
  echo '</div>';

  // -----------------------------------------------

  // блок email
  echo '<div class="profile_block">';
  echo '<h3>'. t('Изменение Email'). '</h3>';
   // если 
  if (ulogin_false_email($comusers_email)) 
  {
    echo 'Email не установлен!';
    $val = '';
  }
  else $val = $comusers_email; 
  echo '<p><strong>'. t('Email'). ':</strong> <input type="text" name="f_comusers_email" value="'.$val.'"></p>  
  <p><input type="submit" name="f_submit_comusers_email" class="submit" value="' .  t('Установить') . '"></p>';
  echo '</div>';

  // -----------------------------------------------

  // блок аватара, ника и сайта
  $out = '';
  // аккаунт при помощи которого вошли
  if (isset($provider_user_nik) and ($provider_user_nik != $comusers_nik)) 
  {
    $out .= '<p><strong>'. t('Ник аккаунта') . ' ' . $provider_key . ':</strong> <input type="text" name="f_provider_user_nik" value="'.$provider_user_nik.'">
          <input type="submit" name="f_submit_provider_user_nik" class="submit" value="' .  t('Установить текущим') . '"></p>';    
  }
  if (isset($provider_user_avatar) and ($provider_user_avatar != $comusers_avatar_url)) 
  {
    $out .= '<p><strong>' . t('Аватар аккаунта') . ' ' . $provider_key . ':</strong> <img src="'.$provider_user_avatar.'">
    <input type="submit" name="f_submit_provider_user_avatar" class="submit" value="' .  t('Установить текущим') . '"> 
    <input type="hidden" name="f_provider_user_avatar" value="'.$provider_user_avatar.'"></p>';    
  }  
  
  // вновь присоединенный аккаунт
  if (isset($new_provider_user_nik) and ($new_provider_user_nik != $comusers_nik)) 
  {
    $out .= '<p><strong>' . t('Ник аккаунта') . ' ' . $new_provider_key . ':</strong> <input type="text" name="f_new_provider_user_nik" value="'.$new_provider_user_nik.'">
          <input type="submit" name="f_submit_new_provider_user_nik" class="submit" value="' .  t('Установить текущим') . '"></p>';    
  }
  if (isset($new_provider_user_avatar) and ($new_provider_user_avatar != $comusers_avatar_url)) 
  {
    $out .= '<p><strong>' . t('Аватар аккаунта') . ' ' . $new_provider_key . ':</strong> <img src="'.$provider_user_avatar.'">
    <input type="submit" name="f_submit_new_provider_user_avatar" class="submit" value="' .  t('Установить текущим') . '"> 
    <input type="hidden" name="f_new_provider_user_avatar" value="'.$new_provider_user_avatar.'"></p>';    
  }      
  if ($out) 
    echo '<div class="profile_block">' . '<h3>'. t('Управление ником и аватаром'). '</h3><p><strong>' . t('Текущий аватар') . ':</strong> <img src="'.$comusers_avatar_url . '">  <strong>' . t('Текущий ник') . ':</strong> ' . $comusers_nik . '</p>' . $out . '</div>';
 
  // -----------------------------------------------
  
  // присоединенные аккаунты
  $providers_connect = array();
  $out = '';
  if (isset($comusers_providers))
  {
    foreach ($comusers_providers as $key=>$val)
    {
      // выводим присоединенный профиль
      $out .= '<p><img src="'.$plugin_url.'img/'.$key.'.png"> ';
      $out .= $val ? '<a href="'.$val.'">'.$val.'</a>' : $provider_key;
      $out .= '<input type="submit" name="f_submit_disconnect['.$key.']" class="submit" value="' .  t('Отключить') . '"></p>'; 
      $providers_connect[] = $key; // массив присоединенных аккаунтов
    }
  }
  if ($out) echo '<div class="profile_block">' . '<h3>'. t('Присоединенные аккаунты'). '</h3>' . $out . '</div>';
  
  // -----------------------------------------------
  
  
  // присоединение аккаунтов
  // получим неприсоединенные аккаунты
  $providers_new = implode("," , array_diff($all_providers , $providers_connect));
  if ($providers_new)  
  {
		echo '<script src="http://ulogin.ru/js/ulogin.js"></script>';
    echo '<div class="profile_block">' . '<h3>'. t('Присоединение аккаунтов'). '</h3>';
    echo '
       <div id="uLogin"x-ulogin-params="display=panel;optional=first_name,last_name,photo,nickname,email;providers='.$providers_new.';redirect_uri='
        . urlencode( getinfo('siteurl') . mso_current_url() ) . '"></div></div>';
  }
     
  echo '</form>';
  
  echo NR . '</div><!-- class="type type_users_form" -->' . NR;

  require(getinfo('template_dir') . 'main-end.php');

  $error = false;



 
?>