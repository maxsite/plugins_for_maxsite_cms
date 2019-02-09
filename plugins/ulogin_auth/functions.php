<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * (c) http://filimonov.com.ua
 * Функции 
 */

// функция проверяет искусственность email
function ulogin_false_email($email)
{
  if (strstr($email, '_0000.false')) return true;
  else return false;
}

// ф-я создает искусственный email
function ulogin_create_email($profile)
{
  $email1 = $profile['provider_user_key'] . '_' . $profile['provider_key'];
  $email2 = '_0000.false';
  $email = $email1 . '@' . $email2;
  
  // проверим уникальность?
  
  return $email;
}


function get_fields($profile)
/* установим поля
  provider_key - идентефикатор провайдера (возвращается сервисом ulogin в поле [network] )
  provider_url - адрес главной провайдера
  provider_user_id - идентефикатор пользователя (возвращается сервисом ulogin в поле [uid]
  provider_user_key - ключ, позволяющий восстановить ссылку на профиль пользователя (извлекается из [identity] )
  provider_user_url - ссылка на профиль пользователя (возвращается сервисом ulogin в поле [identity] )
  provider_user_avatar - (возвращается сервисом ulogin в поле [photo]  )
  provider_user_nik - ник пользователя (комбинируется из [nickname] [first_name] [last_name])
  
*/
{
  $res = array();
  if (isset($profile['network'])) $res['provider_key'] = $profile['network'];  
  else return false;
  
  if ($profile['network'] == 'vkontakte')
  {
     $res['provider_url'] = 'vk.com';
     $res['provider_user_id'] = isset($profile['uid']) ? $profile['uid'] : 0;  
     $res['provider_user_key'] = isset($profile['uid']) ? $profile['uid'] : 0;  
     $res['provider_user_url'] = isset($profile['identity']) ? $profile['identity'] : '';  
     $res['provider_user_avatar'] = isset($profile['photo']) ? $profile['photo'] : '';  
     if (isset($profile['first_name']) and isset($profile['last_name'])) 
         $res['provider_user_nik'] = $profile['first_name'] . ' ' . $profile['last_name'];  
     else $res['provider_user_nik'] = '';    
     $res['email'] = isset($profile['email']) ? $profile['email'] : ulogin_create_email($res);       
  }

  elseif ($profile['network'] == 'odnoklassniki')
  {
     $res['provider_url'] = 'odnoklassniki.ru';
     $res['provider_user_id'] = isset($profile['uid']) ? $profile['uid'] : 0;
     if (isset($profile['profile']))
     {
        $temp_data = explode('profile/' , $profile['profile']);
        $res['provider_user_key'] = $temp_data[1];
     }
     else $res['provider_user_key'] = '';
     
     $res['provider_user_url'] = isset($profile['profile']) ? $profile['profile'] : '';  
     $res['provider_user_avatar'] = isset($profile['photo']) ? $profile['photo'] : '';  
     
     if (isset($profile['first_name']))
     {
       $res['provider_user_nik'] = $profile['first_name'];
       if (isset($profile['last_name'])) $res['provider_user_nik'] .= ' ' . $profile['last_name'];  
     }    
     else $res['provider_user_nik'] = '';
         
     $res['email'] = isset($profile['email']) ? $profile['email'] : ulogin_create_email($res);       
  }
  
  else //остальные
  {
     $temp_data = false;
     if ($profile['network'] == 'mailru')
     {
        $res['provider_url'] = 'mail.ru';
        if (isset($profile['profile']))
           $temp_data = explode('mail/',$profile['profile']);
     }
     elseif ($profile['network'] == 'facebook')
     {
        $res['provider_url'] = 'facebook.com';
        if (isset($profile['profile']))
           $temp_data = explode('facebook.com/',$profile['profile']);
     }     
     elseif ($profile['network'] == 'twitter')
     {
        $res['provider_url'] = 'twitter.com';
        if (isset($profile['profile']))
           $temp_data = explode('twitter.com/',$profile['profile']);
     }      
     elseif ($profile['network'] == 'google')
     {
        $res['provider_url'] = 'google.com';
        if (isset($profile['profile']))
           $temp_data = explode('u/0/',$profile['profile']);
     }   
     elseif ($profile['network'] == 'yandex')
     {
        $res['provider_url'] = 'yandex.ru';
        if (isset($profile['profile']))
           $temp_data = explode('yandex.ru/',$profile['profile']);
     }       
     elseif ($profile['network'] == 'livejournal')
     {
        $res['provider_url'] = 'livejournal.com';
        if (isset($profile['profile']))
        {
           $temp_data2 = explode('.' , str_replace('http://' , '' , $profile['profile']));
           $temp_data[1] = $temp_data2[0];
        }   
     }       
     elseif ($profile['network'] == 'youtube')
     {
        $res['provider_url'] = 'youtube.com';
        if (isset($profile['profile']))
           $temp_data = explode('users/',$profile['profile']);
     }   
     else
     {
        $res['provider_url'] = $profile['network'];
        if (isset($profile['uid']))
           $temp_data[1] = $profile['uid'];
     }   
     
          
     if (isset($temp_data[1]))
       $res['provider_user_key'] = str_replace('/' , '' , $temp_data[1]);
     else
       $res['provider_user_key'] = '';
    
    // общее для остальных
     
     $res['provider_user_id'] = isset($profile['uid']) ? $profile['uid'] : 0;
     
     $res['provider_user_url'] = isset($profile['profile']) ? $profile['profile'] : '';  
     $res['provider_user_avatar'] = isset($profile['photo']) ? $profile['photo'] : '';  
     
     if (isset($profile['first_name']))
     {
       $res['provider_user_nik'] = $profile['first_name'];
       if (isset($profile['last_name'])) $res['provider_user_nik'] .= ' ' . $profile['last_name'];  
     }   
     elseif (isset($profile['nickname'])) $res['provider_user_nik'] = '';    
     else $res['provider_user_nik'] = ''; 
     
     $res['email'] = isset($profile['email']) ? $profile['email'] : ulogin_create_email($res);       
  }

  return $res;
}




function ulogin_comuser_auth($data)
{
  // разберемся с провайдерами
   $provider_key = isset($data['provider_key']) ? $data['provider_key'] : false;
   $provider_url = isset($data['provider_url']) ? $data['provider_url'] : '';
   $provider_user_id = isset($data['provider_user_id']) ? $data['provider_user_id'] : false;
   $provider_user_key = isset($data['provider_user_key']) ? $data['provider_user_key'] : false;
   $provider_user_url = isset($data['provider_user_url']) ? $data['provider_user_url'] : '';
   $provider_user_avatar = isset($data['provider_user_avatar']) ? $data['provider_user_avatar'] : '';
   $provider_user_nik = isset($data['provider_user_nik']) ? $data['provider_user_nik'] : '';
   

   
   if (!$provider_user_id) $provider_key = false;

   if (!isset($data['email']) and !$provider_key) return false;
      
   $email = isset($data['email']) ? $data['email'] : false;   
   
   if ($provider_key and !$email) // сконструируем email
     $email = 'no';
    
        
   $pass = isset($data['password']) ? $data['password'] : false;
   $comusers_nik = isset($data['comusers_nik']) ? $data['comusers_nik'] : '';
   $redirect = isset($data['redirect']) ? $data['redirect'] : true;
   

   $comusers_id = false; // здесь будет номер комюзера
   
   // разрешить создавать через эту функцию новых комюзеров (если такого email нет в базе)
   $allow_create_new_comuser = isset($data['allow_create_new_comuser']) ? $data['allow_create_new_comuser'] : true;
   
   $CI = & get_instance();

 if (!ulogin_false_email($email)) // если email настоящий
 {  
   // если указанный email зарегистрирован на user, то отказываем в регистрации
   $CI->db->select('users_id');
   $CI->db->where( 'users_email', $email);
   $query = $CI->db->get('users');
   if ($query->num_rows() > 0) # есть
   {
      die( tf('Данный email уже используется на сайте админом или автором.'));
   }

      
   // имя email и пароль нужно проверить, чтобы такие были в базе
   // вначале нужно проверить наличие такого email
   // если есть, то сверяем и пароль
   $CI->db->select('comusers_id, comusers_password, comusers_email, comusers_nik, comusers_url, comusers_avatar_url, comusers_last_visit');
   $CI->db->where('comusers_email', $email);
   $query = $CI->db->get('comusers');
   
   if ($query->num_rows()) // есть такой комюзер
   {
      /*
      $comuser_info = $query->row_array(1); // вся инфа о комюзере
      $comusers_id = $comuser_info['comusers_id'];
      
      if ($pass !== false) // пароль указан
      {
         // сверим пароль
         if ($comuser_info['comusers_password'] == mso_md5($pass))
         {
            // пароли равны, можно логинить
            // вынесено ниже

         }
         else
         {
           $comusers_id = false;
            // email есть но пароль ошибочный
            die(t('Переданный пароль является ошибочным для нашего сайта', 'plugins'));
         }
      }
      else
      {
         
      }*/
      
      // если у нас найдено мыло, то не можем зарегить с этим мылом
      die(t('Email, переданный социальной сетью уже зарегистрирован в системе!', 'plugins'));
   }
 } 
 
   if (!$comusers_id and $provider_key) // если входим через социальную сеть
   {
     // пытаемся получить комюзера по его id в социальной сети
     $CI->db->select('comusers.comusers_id, comusers_password, comusers_email, comusers_nik, comusers_url, comusers_avatar_url, comusers_last_visit');
      $CI->db->join('meta', 'meta.meta_id_obj = comusers.comusers_id');
      $CI->db->where('meta_key', $provider_key . '_uid');
      $CI->db->where('meta_table', 'comusers');
      $CI->db->where('meta_value', $provider_user_id);
     $query = $CI->db->get('comusers');      
    if ($query->num_rows()) // есть такой комюзер
     {
        $comuser_info = $query->row_array(1); // вся инфа о комюзере
        // может пригодится
        $comuser_info['provider_url'] = $provider_url;
        $comuser_info['provider_key'] = $provider_key;
        $comuser_info['provider_user_id'] = $provider_user_id;
        $comuser_info['provider_user_url'] = $provider_user_url;
        $comuser_info['provider_user_avatar'] = $provider_user_avatar;
        $comuser_info['provider_user_nik'] = $provider_user_nik;
        
        $comusers_id = $comuser_info['comusers_id'];
        
        
        // можно логинить - вынесено ниже    
    }
   }
   
   // логиним если найден
   if ($comusers_id) // если нашли пользователя
   {
            // сразу же обновим поле последнего входа
            $CI->db->where('comusers_id', $comuser_info['comusers_id']);
            $CI->db->update('comusers', array('comusers_last_visit'=>date('Y-m-d H:i:s')));
            
            $expire  = time() + 60 * 60 * 24 * 365; // 365 дней
            
            $name_cookies = 'maxsite_comuser';
            $value = serialize($comuser_info);
            mso_add_to_cookie($name_cookies, $value, $expire, $redirect); // в куку для всего сайта   
   }
   
   // если нет пользователя, создадим его
   
   elseif ($allow_create_new_comuser) // только если разрешено создавать новых комюзеров
   {
      
      // нет такого email, нужно регистрировать комюзера
      
      // но если запрещены регистрации, то все рубим
      if ( !mso_get_option('allow_comment_comusers', 'general', '1') )
            die(t('На сайте запрещена регистрация комюзеров...', 'plugins'));
      
      // если пароль не указан, то генерируем его случайным образом
      if ($pass === false) $pass = substr(mso_md5($email), 1, 9);
      
      $ins_data = array (
               'comusers_email' => $email,
               'comusers_password' => mso_md5($pass)
               );

      // генерируем случайный ключ активации
      $ins_data['comusers_activate_key'] = mso_md5(rand());
      $ins_data['comusers_date_registr'] = date('Y-m-d H:i:s');
      $ins_data['comusers_last_visit'] = date('Y-m-d H:i:s');
      $ins_data['comusers_ip_register'] = $_SERVER['REMOTE_ADDR'];
      $ins_data['comusers_notify'] = '1'; // сразу включаем подписку на уведомления
            
      if ($provider_key)
      {
         $ins_data['comusers_nik'] = $provider_user_nik;
         $ins_data['comusers_avatar_url'] = $provider_user_avatar;
         // $ins_data['comusers_url'] = $provider_user_url; // можно сразу установить 
      }
      
      if ($comusers_nik)
      {
         $ins_data['comusers_nik'] = $comusers_nik;
      }
      
      // Автоматическая активация новых комюзеров
      // если активация стоит автоматом, то сразу её и прописываем
      if ( mso_get_option('comusers_activate_auto', 'general', '0') )
         $ins_data['comusers_activate_string'] = $ins_data['comusers_activate_key'];

      $res = ($CI->db->insert('comusers', $ins_data)) ? '1' : '0';

      if ($res)
      {
         $comusers_id = $CI->db->insert_id(); // номер добавленной записи
         
         // нужно добавить опцию в мета «новые комментарии, где я участвую» subscribe_my_comments
         // вначале грохаем если есть такой ключ
         $CI->db->where('meta_table', 'comusers');
         $CI->db->where('meta_id_obj', $comusers_id);
         $CI->db->where('meta_key', 'subscribe_my_comments');
         $CI->db->delete('meta');
         
         // теперь добавляем как новый
         $ins_data2 = array(
               'meta_table' => 'comusers',
               'meta_id_obj' => $comusers_id,
               'meta_key' => 'subscribe_my_comments',
               'meta_value' => '1'
               );
         
         $CI->db->insert('meta', $ins_data2);
      
        // если входим через социальную сеть
        if ($provider_key) $err = add_provider_to_comuser($comusers_id , $provider_key, $provider_user_id, $provider_user_url); //добавляем метаполя
               
         // отправляем ему уведомление с кодом активации
         if (!ulogin_false_email($email))
         {
             require (getinfo('common_dir') . 'comments.php');
             mso_email_message_new_comuser($comusers_id, $ins_data, mso_get_option('comusers_activate_auto', 'general', '0'));
         }
         
         
         // после отправки можно сразу залогинить
         $comuser_info = array(
            'comusers_id' => $comusers_id,
            'comusers_password' => mso_md5($pass),
            'comusers_email' => $email,
            'comusers_nik' => $comusers_nik,
            'comusers_url' => '',
            'comusers_avatar_url' => '',
            'comusers_last_visit' => '',
         );
         
         if ($provider_key)
         {
            // может пригодится
           $comuser_info['provider_url'] = $provider_url;
           $comuser_info['provider_key'] = $provider_key;
           $comuser_info['provider_user_id'] = $provider_user_id;
           $comuser_info['provider_user_url'] = $provider_user_url;
           $comuser_info['provider_user_avatar'] = $provider_user_avatar;
           $comuser_info['provider_user_nik'] = $provider_user_nik; 
           $comuser_info['comusers_url'] = $provider_user_url;
           $comuser_info['comusers_avatar_url'] = $provider_user_avatar;
           $comuser_info['comusers_nik'] = $provider_user_nik;            
         }
         
       
         $value = serialize($comuser_info);
         
         $expire  = time() + 60 * 60 * 24 * 365; // 365 дней
         $name_cookies = 'maxsite_comuser';
         
         mso_add_to_cookie($name_cookies, $value, $expire, $redirect); // в куку для всего сайта
         
      }
      else
      {
         die(t('Произошла ошибка регистрации', 'plugins'));
      }
   }
   
   return false;
}


function add_provider_to_comuser($comusers_id , $provider_key, $provider_user_id = false, $provider_user_url= false) 
{
  // добавим соответствующие метаполя
  // вначале грохаем если есть такой ключ
  $CI = & get_instance();
  
  $CI->db->where('meta_table', 'comusers');
  $CI->db->where('meta_id_obj', $comusers_id);
  $CI->db->where('meta_key', $provider_key . '_uid');
  $CI->db->delete('meta');
           
  $CI->db->where('meta_table', 'comusers');
  $CI->db->where('meta_id_obj', $comusers_id);
  $CI->db->where('meta_key', $provider_key . '_profile');
  $CI->db->delete('meta');     
              
  if ($provider_user_id and $provider_user_url) 
  {
     // проверим, чтобы к этому аккаунту не был присоединен другой комюзер
     // пытаемся получить другого комюзера по его id в социальной сети
     $CI->db->select('comusers.comusers_id');
     $CI->db->join('meta', 'meta.meta_id_obj = comusers.comusers_id');
     $CI->db->where('meta_key', $provider_key . '_uid');
     $CI->db->where('meta_table', 'comusers');
     $CI->db->where('meta_value', $provider_user_id);
     $CI->db->where_not_in('comusers.comusers_id', array($comusers_id));
     
     $query = $CI->db->get('comusers');      
     if ($query->num_rows()) // есть такой комюзер
       return 'Аккаунт <a href=" ' . $provider_user_url . '">' . $provider_user_url . '</a><br>уже присоединен к другому пользователю.<br>Вы можете войти при помощи этого аккаунта.';
  
    // теперь добавляем как новый
    $ins_data2 = array(
               'meta_table' => 'comusers',
               'meta_id_obj' => $comusers_id,
               'meta_key' => $provider_key . '_uid',
               'meta_value' => $provider_user_id
               );
    $CI->db->insert('meta', $ins_data2);  
           
    $ins_data2 = array(
               'meta_table' => 'comusers',
               'meta_id_obj' => $comusers_id,
               'meta_key' => $provider_key . '_profile',
               'meta_value' => $provider_user_url
               );
    $CI->db->insert('meta', $ins_data2);    
  }
  
  return false;                
}

function ulogin_email_modify($comusers_id , $email)
{
  $CI = & get_instance();
  
  // проверим валидность
  if (!mso_valid_email($email))
      return  tf('Введите корректный email.');
  
  
  // проверим, чтобы такого email не было
   $CI->db->select('users_id');
   $CI->db->where( 'users_email', $email);
   $query = $CI->db->get('users');
   if ($query->num_rows() > 0) # есть
      return tf('Данный email уже используется на сайте админом или автором.');

   $CI->db->select('comusers_id');
   $CI->db->where('comusers_email', $email);
   $query = $CI->db->get('comusers');
   
   if ($query->num_rows() > 0) // есть такой комюзер
      return tf('Данный email уже используется на сайте пользователем.');
   
   // все впорядке - спасибо зарядке
   
	 $CI->db->where('comusers_id', $comusers_id);
 	 $upd_data = array ('comusers_email' => $email);
	 if ($CI->db->update('comusers', $upd_data))
	 {
	    global $MSO;
	    if (isset($MSO->data['session']['comuser']))
          $MSO->data['session']['comuser']['comusers_email'] = $email;
          
	    // изменим куку
	    $comuser_info = $MSO->data['session']['comuser'];
      $expire  = time() + 60 * 60 * 24 * 365; // 365 дней
      $name_cookies = 'maxsite_comuser';
      $value = serialize($comuser_info);
	    $curpage = mso_url_get();
		  if ( $curpage == getinfo('site_url') ) $curpage = false;      
       mso_add_to_cookie($name_cookies, $value, $expire, $curpage); // в куку для всего сайта   	    
	 }   
	 else return tf('Не удалось поменять email.');   
}

function ulogin_comuser_modify($comusers_id , $upd_data)
{
   $CI = & get_instance();
	 return ($CI->db->update('comusers', $upd_data)) ? '1' : '0'; 
}

# end file
