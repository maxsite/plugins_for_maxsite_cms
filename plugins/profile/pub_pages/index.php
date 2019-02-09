<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// управление публичными страницами профиля пользователя

  mso_hook_add( 'head', 'profile_head');

 $options['all_events_title'] = isset($options['all_events_title']) ? $options['all_events_title'] : 'Все действия';
 $options['profiles_title'] = isset($options['profiles_title']) ? $options['profiles_title'] : 'Пользователи';
  // массив страниц плагина профайл
 $options['pages_profiles'] = isset($options['pages_profiles']) ? $options['pages_profiles'] : array('0'=>'Информация' , 'all' => $options['all_events_title'] , 'comments' => 'Комментарии' , 'files' => 'Загрузки');
 $options['pages_main'] = isset($options['pages_main']) ? $options['pages_main'] : array('0'=>'Все пользователи' , 'all' => 'Все действия');	  


 if (!$segment2) // нужно вывести список всех пользователей
	  {
	     require( getinfo('plugins_dir') . 'profile/pub_pages/all_profiles.php' ); // подключили вывод всех пользователей
	     return true;
	  }
	  
 if ( $segment2 == 'all' )// нужно вывести список всех действий вообще всех пользователей
	  {
	     require( getinfo('plugins_dir') . 'profile/pub_pages/all.php' ); // подключили страницу всех действий
	     return true; 
	  }		  
	  
 if ( $segment2 == 'files' )// нужно вывести список всех фото
	  {
	     require( getinfo('plugins_dir') . 'profile/pub_pages/all-uploads.php' ); // подключили страницу всех фото
	     return true; 
	  }		 	  

	  

    
  // может указан комюзер
  if (is_numeric($segment2))
  {
      require_once( getinfo('common_dir') . 'comments.php' ); 
      $find_comuser = mso_get_comuser($segment2);

      if (!isset($find_comuser[0])) 
	  {
	     require( getinfo('plugins_dir') . 'profile/pub_pages/out_of_comuser.php' ); // подключили страницу отсутствия пользователя
	     return true; 
	  }	 
	  else $comuser_info = $find_comuser[0];
	  
      extract ($comuser_info);
	  
	  if (!mso_segment(3))// нужно вывести сводную информацию о пользователе
	  {
	     require( getinfo('plugins_dir') . 'profile/pub_pages/profile_user_info.php' ); // подключили публичную страницу пользователя
	     return true; 
	  }	 

	  if (mso_segment(3)=='all')// нужно вывести список всех действий пользователя
	  {
	     require( getinfo('plugins_dir') . 'profile/pub_pages/profile_all.php' ); // подключили публичную страницу всех действий пользователя
	     return true; 
	  }	
	  
	  if (mso_segment(3)=='comments')// нужно вывести список всех комментариев пользователя
	  {
	     require( getinfo('plugins_dir') . 'profile/pub_pages/profile_comments.php' ); // подключили страницу комментариев пользователя
	     return true; 
	  }		
	    
	  if (mso_segment(3)=='files')// нужно вывести список всех загрузок пользователя
	  {
	     require( getinfo('plugins_dir') . 'profile/pub_pages/uploads.php' ); // подключили страницу комментариев пользователя
	     return true; 
	  }		
	  	   
  }  
  

 
?>