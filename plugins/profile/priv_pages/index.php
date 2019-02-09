<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

    // управление страницами личного кабинета

	// попробуем вывести личный кабинет
	
			  
	mso_hook_add( 'head', 'profile_head');
	
	$options['title'] = isset($options['title']) ? $options['title'] : 'Личный кабинет';
	
  // получим страницы личного кабинета
  $options['pages'] = isset($options['pages']) ? $options['pages'] : array('0'=>'Основные настройки' , 'avatar' => 'Аватар' , 'logininfo' => 'Социализация' , 'commenting' => 'Комментированное' , 'files' => 'Загрузки');
  $options['exit'] = isset($options['exit']) ? $options['exit'] : 'Выход';
  $options['hello'] = isset($options['hello']) ? $options['hello'] : 'Привет';
  $options['to_profile'] = isset($options['to_profile']) ? $options['to_profile'] : 'Профиль';
  $options['upload_avatar'] = isset($options['upload_avatar']) ? $options['upload_avatar'] : true;
  mso_cur_dir_lang('templates');
  require_once( getinfo('common_dir') . 'comments.php' );  
  
	$comusers_id = $users_id = 0;
	$cur_comuser = is_login_comuser();
	if ($cur_comuser)
	{
		   // перебрасываем
		   extract($cur_comuser);
		   
		   // получаем
		   $comuser_info = mso_get_comuser($cur_comuser['comusers_id'], array('limit'=>1));
		   //$comuser_info = mso_get_comuser($comuser_info['comusers_id']);
		   extract($comuser_info[0]);  
     
		   // если активация не завершена, то вначале требуем её завершить
		   if ($comusers_activate_string != $comusers_activate_key) // нет активации
		   {
              require(getinfo('shared_dir') . 'main/main-start.php');
              $no_activation_link = getinfo('siteurl') . 'users/' . $comusers_id . '/edit';
              echo '<p><span style="color: red;">Активация не завершена.</span></p><p><a href="' . $no_activation_link  . '">Завершить</a></p>';				 
	          require(getinfo('shared_dir') . 'main/main-end.php');			  
			  return true; // выходим с true	
		  }  
			   
	}   
	// elseif (mso_segment(1) == $options['profile_user_slug']) $user_id = getinfo('users_id');
	
	// кто-то залогинен?
	if (!$comusers_id) // нет залогиненного
	{
	   require( getinfo('plugins_dir') . 'profile/priv_pages/not_login.php' ); // подключили страницу-заглушку
		 return true; // выходим с true	
	}
	
	if (!$segment2)
	{
	   require( getinfo('plugins_dir') . 'profile/priv_pages/profile.php' ); // подключили основные настройки
		 return true; // выходим с true
	}   
	elseif ($segment2 == 'avatar') 
	{
	   require( getinfo('plugins_dir') . 'profile/priv_pages/avatar.php' ); // подключили менеджер аватара
		 return true; // выходим с true
	}
	elseif ($segment2 == 'commenting') 
	{
	   require( getinfo('plugins_dir') . 'profile/priv_pages/commenting.php' ); // подключили комментируемое
		 return true; // выходим с true
	}	
	elseif ($segment2 == 'files') 
	{
	   require( getinfo('plugins_dir') . 'profile/priv_pages/uploads.php' ); // подключили комментируемое
		 return true; // выходим с true
	}		
	elseif ($segment2 == 'logininfo') 
	{
	   require( getinfo('plugins_dir') . 'profile/priv_pages/auth.php' ); // подключили управление аккаунтом
		 return true; // выходим с true
	}		



?>