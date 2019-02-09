<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
подключим требуемые файлы плагинов чтобы поместить события в массив $profiles_events
	        используемая инфа
	        $last_date - дата с которой начинать получение событий
	        $get_events_user - номер пользователя, чьи события получать или 0
	        $only_id - ключ элемента событий в массиве $profiles_all = 0,1,2... (подключается только он) или false , если подключать все
	        $subject_id - ключ фильтра адресата события
*/
	       
  $profiles_events = array();
  
	$options['events_count'] = isset($options['events_count']) ? $options['events_count'] : 10; // кол-во событий к выводу
	$options['order'] = isset($options['order']) ? $options['order'] : 'desc'; // сортировка событий
  
  if ($only_id !== false)
  {
     $key_element = $only_id;
     $profile_element = $profiles_all[$only_id];
     require( getinfo('plugins_dir') . $profile_element['plugin'] . '/profiles/' . $profile_element['filename'] . '.php' );
  }  
  else 
	    // поочереди подключим все файлы, заявленные всеми, указанными в опции, плагинами
	    foreach ($profiles_all as $key_element=>$profile_element)
	        require( getinfo('plugins_dir') . $profile_element['plugin'] . '/profiles/' . $profile_element['filename'] . '.php' ); 
	    
	krsort($profiles_events);     
	    
  


?>


