<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
 // страница личного кабинета для управления присоединенными аккаунтами
 
DEFINE('KEY_PROVIDERS_VISIBLE', 'providers_visible');
$CI = & get_instance();

if (isset($options['pages'][mso_segment(2)])) $title = $options['pages'][mso_segment(2)];
else $title = '';

mso_head_meta('title', $options['title'] . ' » ' . $title); // meta title страницы 
 
 $plugin_url = getinfo('plugins_url') . 'profile/';


	// все доступные провайдеры
	if (!isset($options['providers_set'])) $options['providers_set'] = 'vkontakte,odnoklassniki,mailru,yandex,twitter';
	if (!isset($options['providers_hidden'])) $options['providers_hidden'] = 'google,facebook,livejournal,youtube';
	if (!isset($options['edit_email'])) $options['edit_email'] = false;

	$all_providers = $options['providers_set'] . ',' . $options['providers_hidden'];
  $all_providers1 = explode("," , $all_providers);
  $all_providers = array();
  foreach ($all_providers1 as $cur_prov) $all_providers[] = trim($cur_prov);
    
   

  // Начало вывода_______________________________________________________________
  require(getinfo('shared_dir') . 'main/main-start.php');

  echo NR . '<div class="type type_users_form">' . NR;

  require (getinfo('plugins_dir') . 'profile/priv_pages/menu.php' );


 //  extract ($comuser);

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

   // сформируем массив доступных для просмотра аккаунта
  $prowiders_visible ='';
/*   
  // добавим метаполя присоединенных аккаунтов
    $CI = & get_instance();  
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
				   if ($val['meta_key'] == KEY_PROVIDERS_VISIBLE)
				      $prowiders_visible = $val['meta_value'];  
			  }
	 }
*/
 
 if ($comusers_meta)
	foreach ($comusers_meta as $key=>$val)
    {
			     $prov_key = str_replace('_profile','',$key);
			     if (in_array($prov_key , $all_providers))
				        $comusers_providers[$prov_key] = $val;
				   if ($key == KEY_PROVIDERS_VISIBLE)
				      $prowiders_visible = $val; 		
	}
 

 // обработаем данные
 require (getinfo('plugins_dir') . 'profile/auth_providers/ulogin/functions.php');
 
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
	 echo '<div class="update">' . tf('Обновление выполнено!'). '</div>';
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
	      if ( (count($comusers_providers) < 2) and (!mso_valid_email($comusers_email) or ulogin_false_email($comusers_email)) )
	        echo '<div class="comment-error">' . t('Чтобы отсоединить все аккаунты, введите корректный email', 'plugins'). '</div>';
	      else
	      {
	         add_provider_to_comuser($comusers_id , $key);// не передаем функции 3 и 4 значения, что значит - только удаляем
	         //  удалим из массива
	         unset($comusers_providers[$key]);
	         
	         // изменим строку показываемых провайдеров
	         $prowiders_visible = str_replace($key , "" , $prowiders_visible);
	         if (ulogin_providers_visible($comusers_id , $prowiders_visible))
				 echo '<div class="comment-error">' . t('Ошибка!', 'plugins'). '</div>';
			 else echo '<div class="update">' . tf('Обновление выполнено!'). '</div>';
	      }   
	   }  
	   else echo '<div class="comment-error">' . t('Ошибка идентефикации провайдера', 'plugins') . ': ' .$key . '</div>';;
 }


 // изменение email
 if ( $post = mso_check_post(array('f_session_id', 'f_submit_comusers_email', 'f_comusers_email')) ) 
 {
	if (ulogin_false_email($comusers_email) or $options['edit_email'])
	{
	   mso_checkreferer();
	   $err = ulogin_email_modify($comusers_id , $post['f_comusers_email']);
	   if ($err)
	       echo '<div class="comment-error">' . $err . '</div>';
	   else
	   {	   
	      $comusers_email = $post['f_comusers_email'];
		  echo '<div class="update">' . tf('Обновление выполнено!'). '</div>';
	   }
	}
	else echo '<div class="comment-error">' . tf('Нельзя изменять email!') . '</div>';
 }
 
 // изменение настроект видимости
 if ( $post = mso_check_post(array('f_session_id', 'f_submit_visible')) ) 
 {
	   mso_checkreferer();
    $f_provider_visible = isset($post['f_provider_visible']) ? $post['f_provider_visible'] : '';
    $new_providers_visible = '';
    
    // составим строку отображаемых аккаунтов
    if ($f_provider_visible) foreach ($f_provider_visible as $key => $val)
       if ($val) 
         if ($new_providers_visible) $new_providers_visible .= ',' . $key;
         else $new_providers_visible .= $key;
         
    // откорректируем отображаемые аккаунты
    if (ulogin_providers_visible($comusers_id, $new_providers_visible))
		echo '<div class="comment-error">' . tf('Ошибка!') . '</div>';
	else echo '<div class="update">' . tf('Обновление выполнено!'). '</div>';
    
    // изменим 
    $prowiders_visible = $new_providers_visible;
 }
 
  
 // изменение ника
 if ( $post = mso_check_post(array('f_session_id', 'f_submit_provider_user_nik', 'f_provider_user_nik')) ) 
 {
	   mso_checkreferer();
  	 $CI->db->where('comusers_id', $comusers_id);
	   $res = ($CI->db->update('comusers', array('comusers_nik'=>$post['f_provider_user_nik']))) ? '1' : '0'; 
	   if ($res)
	   {
	      $comusers_nik = $post['f_provider_user_nik'];
		  echo '<div class="update">' . tf('Обновление выполнено!'). '</div>';
	   }
	   else echo '<div class="comment-error">' . tf('Ошибка!') . '</div>';
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
	   {
	      $comusers_avatar = $post['f_provider_user_avatar'];
		  echo '<div class="update">' . tf('Обновление выполнено!'). '</div>';
	   }
	   else echo '<div class="comment-error">' . tf('Ошибка!') . '</div>';		  
 } 
 
 if ( $post = mso_check_post(array('f_session_id', 'f_submit_new_provider_user_avatar', 'f_new_provider_user_avatar')) ) 
 {
	   mso_checkreferer();
  	 $CI->db->where('comusers_id', $comusers_id);
	   $res = ($CI->db->update('comusers', array('comusers_avatar_url'=>$post['f_new_provider_user_avatar']))) ? '1' : '0'; 
	   if ($res)
	   {
	      $comusers_avatar = $post['f_new_provider_user_avatar'];
		  echo '<div class="update">' . tf('Обновление выполнено!'). '</div>';
	   }
	   else echo '<div class="comment-error">' . tf('Ошибка!') . '</div>';		  
 }
  
  
  // выводим форму -----------------------------------------------

	$CI->load->helper('form');
	echo '<form action="" method="post" class="comusers-form">' . mso_form_session('f_session_id');
	echo '<input type="hidden" value="' . $comusers_email . '" name="f_comusers_email">';
	echo '<input type="hidden" value="' . $comusers_password . '" name="f_comusers_password">';

 // при помощи чего вошли 
 echo '<div class="profile_block">';
  if (isset($provider_key) and $provider_key)
  {
    echo t('Вход с помощью: ') . '<img src="'.$plugin_url.'img/'.$provider_key.'.png"> ';
    echo $provider_user_url ? '<a href="'.$provider_user_url.'">'.$provider_user_url.'</a>' : $provider_key;
  }
  else echo 'Вход произведен при помощи логина (email) и пароля.';  
  echo '</div>';

  // -----------------------------------------------

  // блок email
  // выводить блок изменения email
  if (ulogin_false_email($comusers_email) or $options['edit_email'])
  {
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
  }
  // -----------------------------------------------

  // блок аватара, ника и сайта
  $out = '';
  // аккаунт при помощи которого вошли
  if (isset($provider_user_nik) and ($provider_user_nik != $comusers_nik)) 
  {
    $out .= '<p><strong>'. t('Ник аккаунта') . ' ' . $provider_key . ':</strong> <input type="text" name="f_provider_user_nik" value="'.$provider_user_nik.'">
          <input type="submit" name="f_submit_provider_user_nik" class="submit" value="' .  t('Установить своим ником') . '"></p>';    
  }
  if (isset($provider_user_avatar) and ($provider_user_avatar != $comusers_avatar_url)) 
  {
    $out .= '<p><strong>' . t('Аватар аккаунта') . ' ' . $provider_key . ':</strong> <img src="'.$provider_user_avatar.'">
    <input type="submit" name="f_submit_provider_user_avatar" class="submit" value="' .  t('Установить своим аватаром') . '"> 
    <input type="hidden" name="f_provider_user_avatar" value="'.$provider_user_avatar.'"></p>';    
  }  
  
  // вновь присоединенный аккаунт
  if (isset($new_provider_user_nik) and ($new_provider_user_nik != $comusers_nik)) 
  {
    $out .= '<p><strong>' . t('Ник присоединенного аккаунта') . ' ' . $new_provider_key . ':</strong> <input type="text" name="f_new_provider_user_nik" value="'.$new_provider_user_nik.'">
          <input type="submit" name="f_submit_new_provider_user_nik" class="submit" value="' .  t('Установить своим ником') . '"></p>';    
  }
  if (isset($new_provider_user_avatar) and ($new_provider_user_avatar != $comusers_avatar_url)) 
  {
    $out .= '<p><strong>' . t('Аватар присоединенного аккаунта') . ' ' . $new_provider_key . ':</strong> <img src="'.$new_provider_user_avatar.'">
    <input type="submit" name="f_submit_new_provider_user_avatar" class="submit" value="' .  t('Установить своим аватаром') . '"> 
    <input type="hidden" name="f_new_provider_user_avatar" value="'.$new_provider_user_avatar.'"></p>';    
  }      
  if ($out) 
    {
		echo '<div class="profile_block">' . '<h3>'. t('Управление ником и аватаром'). '</h3>';
		if (!$comusers_nik) $comusers_nik = 'не установлен';
		if ($comusers_avatar_url) $comusers_avatar_url = '<img src="' . $comusers_avatar_url . '">';
		else $comusers_avatar_url = 'не установлен';
		echo '<p><strong>' . t('Текущий ник') . ':</strong> ' . $comusers_nik . '</p>';
		echo '<p><strong>' . t('Текущий аватар') . ':</strong> ' . $comusers_avatar_url . '</p>';
        echo $out . '</div>';
	}

 // -----------------------------------------------
  
  // присоединенные аккаунты
  $providers_connect = array();
  $out = '';
  if (isset($comusers_providers))
  {
    foreach ($comusers_providers as $key=>$val)
    {
      // чекбокс отображения
      if (strstr($prowiders_visible, $key)) $chckout = 'checked="true"';
      else $chckout = '';
      
      // выводим присоединенный профиль
      $out .= '<p>' . t('Показывать') . ': ' . '<input name="f_provider_visible['.$key.']" type="checkbox" ' . $chckout . '>
	  <img src="'.$plugin_url.'img/'.$key.'.png"> ';
      $out .= $val ? '<a href="'.$val.'">'.$val.'</a> ' : $provider_key;
      $out .= '<input type="submit" name="f_submit_disconnect['.$key.']" class="submit" value="' .  t('Отключить') . '">'; 
      
      $out .= '</p>';
      
      $providers_connect[] = $key; // массив присоединенных аккаунтов
    }
  }
  if ($out) echo '<div class="profile_block">' . '<h3>'. t('Присоединенные аккаунты'). '</h3>
  <p>На сайте можно производить авторизацию при помощи любого из присоединенных аккаунтов социальных сетей.</p>
  <p>Аккаунты, отмеченные "Показывать", будут видны посетителям сайта.</p>' . $out . 
 '<input type="submit" name="f_submit_visible" class="submit" value="' .  t('Сохранить настройки видимости') . '">
  </div>';

  // -----------------------------------------------
  
  
  // присоединение аккаунтов
  // получим неприсоединенные аккаунты
  $providers_new = implode("," , array_diff($all_providers , $providers_connect));
  if ($providers_new)  
  {
	echo '<script src="http://ulogin.ru/js/ulogin.js"></script>';
    echo '<div class="profile_block">' . '<h3>'. t('Присоединение аккаунтов'). '</h3>';
	echo '<p>Присоедените дополнительные аккаунты, чтобы иметь возможность авторизироваться любым из них.</p>';  
    echo '
       <div id="uLogin"x-ulogin-params="display=panel;optional=first_name,last_name,photo,nickname,email;providers='.$providers_new.';redirect_uri='
        . urlencode( getinfo('siteurl') . mso_current_url() ) . '"></div></div>';
  }
     
  echo '</form>';
  
  echo NR . '</div><!-- class="type type_users_form" -->' . NR;

  require(getinfo('shared_dir') . 'main/main-end.php');

  $error = false;



 
?>