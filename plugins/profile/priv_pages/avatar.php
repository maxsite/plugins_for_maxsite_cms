<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
//
  if (isset($options['pages'][mso_segment(2)])) $title = $options['pages'][mso_segment(2)];
  else $title = '';
  mso_head_meta('title', $options['title'] . ' » ' . $title); // meta title страницы

  require(getinfo('shared_dir') . 'main/main-start.php');
  echo NR . '<div class="type type_users_form">' . NR;

  require (getinfo('plugins_dir') . 'profile/priv_pages/menu.php' );
  require (getinfo('plugins_dir') . 'profile/functions_avatar.php' );



  # обработка отправленных данных - возвращает результат
  
  if ($post = mso_check_post(array('f_session_id', 'f_submit_clear')))
  {
  	    $CI = & get_instance();

			        $upd_date = array (	'comusers_avatar_url' =>	'');
				
			        $CI->db->where('comusers_id', $comusers_id);
			        $res = ($CI->db->update('comusers', $upd_date )) ? '1' : '0';
			        if ($res)    
			        {
                 $comusers_avatar_url = '';
                 echo '<div class="update">Аватар очищен</div>';
              } 
              else echo '<div class="error">Ошибка очистки аватара</div>';     
  }
    
  if ($post = mso_check_post(array('f_session_id', 'f_submit')))
  {
    $ok=false;
  
    // если был указан файл для загрузки
    if (isset($_FILES['f_userfile']['name']) and $_FILES['f_userfile']['name'])
    {
	    $CI = & get_instance();
	    $CI->load->helper('file_helper'); 
	
	    $allowed_types = 'gif|jpg|jpeg|png';
	
	
	    $path = getinfo('uploads_dir') . $subdir . '/' . $comusers_id . '/';
      if ( ! is_dir(getinfo('uploads_dir') . $subdir) ) @mkdir(getinfo('uploads_dir') . $subdir, 0777); // нет каталога, пробуем создать	    
	    if ( ! is_dir($path) ) @mkdir($path, 0777); // нет каталога, пробуем создать
	
	    require_once( getinfo('common_dir') . 'uploads.php' ); // функции загрузки 
	
	    // параметры для mso_upload
	    $mso_upload_ar1 = array( // конфиг CI-библиотеки upload
			  'upload_path' => $path ,
			  'allowed_types' => $allowed_types,
		  );
		
	    $mso_upload_ar2 = array( // массив прочих опций
			  //'userfile_title' => 'Аватар', // описание файла
		    //	'fn_mso_descritions' => $fn_mso_descritions, // файл для описаний
			   'userfile_resize' => (isset($options['userfile_resize'])) ? $options['userfile_resize'] : '1', // нужно ли менять размер
			   'userfile_resize_size' => (isset($options['userfile_resize_size'])) ? $options['userfile_resize_size'] : 100, // размер
		     //	'userfile_water' => (isset($options['userfile_water'])) ? $options['userfile_water'] : '0', // нужен ли водяной знак
	      	//	'userfile_water_file' => getinfo('uploads_dir') . ((isset($options['userfile_water_file'])) ? $options['userfile_water_file'] : 'watermark.png'), // файл водяного знака
		       //	'water_type' => (isset($options['water_type'])) ? $options['water_type'] : 4, // тип водяного знака
			    'userfile_mini' => (isset($options['userfile_mini'])) ? $options['userfile_mini'] : '0', // делать миниатюру?
		      //	'userfile_mini_size' => (isset($options['userfile_mini_size'])) ? $options['userfile_mini_size'] : 200, // размер миниатюры
		     //	'mini_type' => (isset($options['mini_type'])) ? $options['mini_type'] : 1, // тип миниатюры
			    'prev_size' => 100, // размер превьюхи
			    'message1' => '', // не выводить сообщение о загрузке каждого файла			
	    	);
			
	      $res = false; // результат загрузки
	      $res = mso_upload($mso_upload_ar1, 'f_userfile', $mso_upload_ar2);  
	      
	      if ($res)
	      {
	        $up_data = $CI->upload->data();
		      $up_data['file_name'] = strtolower($up_data['file_name']);
          $up_data['full_path'] = $up_data['file_path'] . $up_data['file_name'];
          @unlink($up_data['file_path'] . 'avatar.jpg');
				  rename($up_data['full_path'], $up_data['file_path'] . 'avatar.jpg');
          @unlink($up_data['file_path'] . '_mso_i/avatar.jpg');
				  rename($up_data['file_path'] . '_mso_i/' . $up_data['file_name'], $up_data['file_path'] . '_mso_i/avatar.jpg');				  
	        // выведем инфо о загрузке
	       
	        if (file_exists($up_data['file_path'] . '/avatar.jpg'))	
	        {
	          echo '<div class="update">Файл загружен</div>';
	          $f_comusers_avatar_url = getinfo('uploads_url') . $subdir . '/' . $comusers_id . '/avatar.jpg';
	          $ok = true;
	        }  
	        else
	          echo '<div class="error">Файл не загружен</div>';
        }
        else echo '<div class="error">Ошибка загрузки</div>';
    }
    else // тогда взять из поля
    {
		 	  $f_comusers_avatar_url = mso_strip($post['f_comusers_avatar_url'], false,
				  array('\\', '|', '?', '%', '*', '`'));

			  $allowed_ext = array('gif', 'jpg', 'jpeg', 'png'); // разрешенные типы
			  $ext = strtolower(str_replace('.', '', strrchr($f_comusers_avatar_url, '.'))); // расширение файла
			  if ( !in_array($ext, $allowed_ext) ) $f_comusers_avatar_url = ''; // запрещенный тип файла 
			  if ($f_comusers_avatar_url) $ok = true;
    }
    
    // теперь изменим
    if ($ok)
    {
		    mso_checkreferer();
		   // if ($MSO->data['session']['session_id'] != $post['f_session_id']) mso_redirect();
		    $f_comusers_email = trim($post['f_comusers_email']);
	    	$f_comusers_password = trim($post['f_comusers_password']);
		    if ($f_comusers_email and $f_comusers_password)
		    {
		       $CI = & get_instance();
		       $CI->db->select('*');
		       $CI->db->from('comusers');
		       $CI->db->where('comusers_activate_string=comusers_activate_key', '', false); // активация должна уже быть
		       $CI->db->where(array('comusers_id'=>$comusers_id,
							'comusers_email'=>$f_comusers_email,
							'comusers_password'=>$f_comusers_password
							));
		       $CI->db->limit(1);
		       $query = $CI->db->get();

		       if ($query->num_rows() > 0)
		       {   
			        $comuser = $query->result_array(); // данные комюзера
			        $upd_date = array (	'comusers_avatar_url' =>	$f_comusers_avatar_url);
				
			        $CI->db->where('comusers_id', $comusers_id);
			        $res = ($CI->db->update('comusers', $upd_date )) ? '1' : '0';
			        if ($res)    
			        {
                 $comusers_avatar_url = $f_comusers_avatar_url;
                 echo '<div class="update">Изменения сохранены.</div>';
              } 
              else echo '<div class="error">Ошибка обновления.</div>';
           }
           else echo '<div class="error">Ошибка пользователя.</div>';
        } 
        else echo '<div class="error">Ошибка идентефикации.</div>';
   }
   else echo '<div class="error">Не получен новый аватар.</div>';
 }

  // выводим форму
	// форма редактирования аватара
	
	$CI = & get_instance();
	$CI->load->helper('form');
	echo '<form action="" method="post" enctype="multipart/form-data" class="comusers-form">' . mso_form_session('f_session_id');
	echo '<input type="hidden" value="' . $comusers_email . '" name="f_comusers_email">';
	echo '<input type="hidden" value="' . $comusers_password . '" name="f_comusers_password">';
				
				
	echo '<div class="profile_block">';
  echo '<h3>'. t('Текущий аватар'). '</h3>';
  
  // выводим аватар
  $avatar_url = profile_avatar(array('comusers_avatar_url'=>$comusers_avatar_url, 'users_avatar_url'=>'', 'users_email'=>'', 'comusers_email'=>$comusers_email));
  $avatar_size = (int) mso_get_option('gravatar_size', 'templates', 80);
  
  echo '<table><tr><td>';
  if ($avatar_url) echo '<img src="' . $avatar_url . '" width="' . $avatar_size . '" height="'. $avatar_size . '" alt="" title="Аватар">';
  echo '</td><td>';
  if (!$comusers_avatar_url) echo '<p>Аватар не установлен</p>';
  if ($avatar_url and !$comusers_avatar_url) echo '<p>Показан аватар, найденный в gravatar.com</p>';
  echo '<p>Аватар можно не устанавливать, а указать глобально в сервисе gravatar.com, привязав к своему email. Тогда он будет показан на всех сайтах, где вы указываете свой email.</p>';
  echo '</td></tr></table>';
	echo '</div>';

	echo '<div class="profile_block">';
  echo '<h3>'. t('Установка аватара'). '</h3>';
   echo '<p>'. t('Вы можете указать адрес расположения файла аватара.'). '</p>';


				echo '<p><strong>'. t('Адрес аватара'). ':</strong> <input type="text" id="avatar_url" name="f_comusers_avatar_url" value="' . $comusers_avatar_url . '"></p>';
	echo '</div>';
					
	//загрузка аватара
	if ($options['upload_avatar'])	
	{

	 echo '<div class="profile_block">';   
   echo '<h3>'. t('Установка собственного аватара'). '</h3>';
   echo '<p>'. t('Вы можете установить аватар, загруженный со своего компьютера.'). '</p>';
     $avatar_file = getinfo('uploads_dir') . 'userfile/' . $comusers_id . '/avatar.jpg';
     
	  $avatar_upload_url = getinfo('uploads_url') . 'userfile/' . $comusers_id . '/avatar.jpg';
	  
	  if (file_exists($avatar_file)) 
	  {
	     echo 'Загруженный ранее файл аватара: <img class="gravatar" src="' . $avatar_upload_url . '">';
	    if ($avatar_upload_url != $avatar_url)
	    {
	      echo '<input type="button" id="set_avatar" class="set_avatar"  onClick="setAvatar()" value="' .  t('Установить аватаром') . '">';
         echo '<script type="text/javascript">
          function setAvatar(){
            var avatar_url = document.getElementById("avatar_url");
            avatar_url.value = \'' . $avatar_upload_url . '\';
         }	        
	       </script>';
	    }
	    else
	      echo ' - этот файл установлен аватаром.';
	   }
	   echo '</div>';

	 echo '<div class="profile_block">';   
   echo '<h3>'. t('Загрузка аватара'). '</h3>';
   echo '<p>'. t('Вы можете загрузить аватар со своего компьютера.'). '</p>';	     
			 echo '<p><strong>'. t('Укажите файл аватара'). ':</strong> <input type="file" name="f_userfile" ></p>';
	     echo '</div>';

	}			
					
	echo '<p><input type="submit" name="f_submit[' . $comusers_id . ']" value="' .  t('Сохранить новый аватар') . '">';
	if ($comusers_avatar_url) echo '<input type="submit" name="f_submit_clear" value="' .  t('Удалить аватар') . '">';
	echo '</p></form>';

  
   echo NR . '</div><!-- class="type type_users_form" -->' . NR;
	 require(getinfo('shared_dir') . 'main/main-end.php');

/*
# вывод аватарки комментатора
# на входе массив комментария из page-comments.php
function profile_avatar($comment, $img_add = 'style="float: left; margin: 5px 10px 10px 0;" class="gravatar"', $echo = false, $size = false)
{
	extract($comment);

	$avatar_url = '';
	if ($comusers_avatar_url) $avatar_url = $comusers_avatar_url;
	elseif ($users_avatar_url) $avatar_url = $users_avatar_url;
	
	if ($size === false)
		$avatar_size = (int) mso_get_option('gravatar_size', 'templates', 80);
	else
		$avatar_size = $size;
		
	if ($avatar_size < 1 or $avatar_size > 512) $avatar_size = 80;
	
	if (!$avatar_url) 
	{ 
		// аватарки нет, попробуем получить из gravatara
		if ($users_email) $grav_email = $users_email;
		elseif ($comusers_email) $grav_email = $comusers_email;
		else 
		{
			$grav_email = $comments_author_name; // имя комментатора
		}
		
		if ($gravatar_type = mso_get_option('gravatar_type', 'templates', ''))
			$d = '&amp;d=' . urlencode($gravatar_type);
		else 
			$d = '';
		
		if (!empty($_SERVER['HTTPS'])) 
		{
		   $avatar_url = "https://secure.gravatar.com/avatar.php?gravatar_id="
				. md5($grav_email)
				. "&amp;size=" . $avatar_size
				. $d;
		} 
		else 
		{
		   $avatar_url = "http://www.gravatar.com/avatar.php?gravatar_id="
				. md5($grav_email)
				. "&amp;size=" . $avatar_size
				. $d;
		}
	}
	
	if ($avatar_url) 
		$avatar_url =  '<img src="' . $avatar_url . '" width="' . $avatar_size . '" height="'. $avatar_size . '" alt="" title="" '. $img_add . '>';
	
	if ($echo) echo $avatar_url;	
		else return $avatar_url;
}

*/

?>