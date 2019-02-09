<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// обработчик аякс запросов
// получает и возвращает события

$return = array(
		'error_code' => 1,
		'end' => 1,
		'error_description' => 'Неверные данные',
		'resp' => '0',
		'last_date' => 0,
);

// u_id - id пользователя или 0
// l_d - дата с которой начинать вывод
// o_id ключ элемента, события, если только его и нужно получать
// all - флаг = 1, если получать события по всем элементам
// sbj - ключ адресата события

if ( ($post = mso_check_post(array('type' , 'u_id' , 'l_d' , 'o_id' , 'all' , 'sbj'))))
{
  $out = '';
  $site_url = getinfo('siteurl');
	 
  $options = mso_get_option('profile', 'plugins', array());
  
  if (is_numeric($post['u_id'])) $get_events_user = $post['u_id']; else $get_events_user = 0; // пользователь, события которого нужно получить
  $last_date = $post['l_d']; // дата , раньше которой нужно получать события
  if (is_numeric($post['sbj'])) $subject_id = $post['sbj']; else $subject_id = 0;
  
  if ($post['all']) $only_id = false; // получить все события
  else $only_id = $post['o_id']; // получить события по конкретному ключу в $profiles_all
  
  // получим массив всех элементов всех плагинов
	$profiles_all = profile_elements($options['profile_plugins']);
  
	$options['events_count'] = isset($options['events_count']) ? $options['events_count'] : 10; // кол-во событий к выводу
	$options['order'] = isset($options['order']) ? $options['order'] : 'desc'; // сортировка событий
	$options['no_cache'] = isset($options['no_cache']) ? $options['no_cache'] : true; // сортировка событий
  
    // получим массив элементов для вывода 
    
	  if ($post['all']) $cache_key = 'profiles_' . $get_events_user . $last_date;
	  else $cache_key = 'profiles_' . $get_events_user . $last_date . $only_id . $subject_id;
	  
	  $profiles_events = mso_get_cache($cache_key);
	  if ($options['no_cache']) $profiles_events = false; // здесь будут события к выводу

	  if ($profiles_events === false)
	  {
      if ($profiles_all and $last_date)
	     // поочереди подключим все файлы, заявленные всеми, указанными в опции, плагинами
	    { 
         if ($only_id !== false) // если мы на странице конкретного события
         {
           $key_element = $only_id;
           $profile_element = $profiles_all[$only_id];
           require( getinfo('plugins_dir') . $profile_element['plugin'] . '/profiles/' . $profile_element['filename'] . '.php' );
         }  
         else 	     
	         foreach ($profiles_all as $key_element=>$profile_element)
	         {
	           // используемая инфа $last_date $get_events_user
	           require( getinfo('plugins_dir') . $profile_element['plugin'] . '/profiles/' . $profile_element['filename'] . '.php' ); 
	           // в файле мы должны кинуть события в массив $profiles_events
	         }
	     } 
	     
	     krsort($profiles_events);   
	     mso_add_cache($cache_key, $profiles_events);
	  }   
	    
    $date = 0;
    if ($profiles_events)
    {
        $i=0;
        $img_go = getinfo('plugins_url') . 'profile/img/go.png';
        
        // подключим файл с циклом
        require(getinfo('plugins_dir') . 'profile/all_foreach.php');
         
       // если еще есть что получать  
      // if (count($profiles_events)>$options['events_count']) $return['last_date'] = $date;
       $return['last_date'] = $date;
	   $return['end'] = 0;
    }
    $return['error_code'] = 0;
    $return['resp'] = $out;	   
}

	echo json_encode($return);	


// на всякий случай заново определим ф-ю - вдруг сбросится кеш

// получим массив всех элементов всех пагигов
// $profile_plugins - массив имен плагинов, которые должны формировать события
function profile_elements($profile_plugins=array())
{
   $cache_key = 'profiles_all_elements';
   $profiles_all = mso_get_cache($cache_key);
	if (!$profiles_all)
	{
	  $profiles_all = array();
  	if ($profile_plugins)
  	 foreach ($profile_plugins as $profile_plugin)
	   {
	       // элементы к подключению должны быть заявленны в этой опции каждого плагина
	       $plugin_elements = mso_get_option($profile_plugin . '_profiles', 'plugins', array());
	      /*опция должна содержать массив элементов
	      
		     ['title'] = Сообщение в дискуссии
		     ['name'] = Сообщение
		     ['all'] = Все сообщения на форуме
		     ['title_go'] = Перейти к сообщению в дискуссии
		     ['all_link'] = forum/all-comments
		     ['img'] = getinfo('plugins_url') . dialog/img/message.png
		     ['filename'] = comments_profiles
	         
	         */      
	         
	       if ($plugin_elements)
	          foreach ($plugin_elements as $plugin_element)
	          {
	            if (file_exists(getinfo('plugins_dir') . $profile_plugin . '/profiles/' . $plugin_element['filename'] . '.php'))
	            {
	              $plugin_element['plugin'] = $profile_plugin;
	              $profiles_all[] = $plugin_element;
	            }  
	          } 
	          
     }
    mso_add_cache($cache_key, $profiles_all);
  }  
  return $profiles_all; 
}  



?>


