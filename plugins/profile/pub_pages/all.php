<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// вывод всех событий по всем поключаемым элементам

function profile_all_head($arg = array())
{
	echo '<script type="text/javascript" src="' . getinfo('plugins_url') . 'profile/profile.js" ></script>';
	echo '<script type="text/javascript">
			var ajax_path = "' . getinfo('ajax') . base64_encode('plugins/profile/get_events-ajax.php') . '";
		</script>';		
	
	return $arg;
}

	mso_hook_add( 'head', 'profile_all_head');

// выведем все события

 $options['pages_main'] = isset($options['pages_main']) ? $options['pages_main'] : array('0'=>'Все пользователи' , 'all' => 'Все действия');	  
 $options['no_cache'] = isset($options['no_cache']) ? $options['no_cache'] : true; // сортировка событий


  // зададим необходимые переменные что получать все события 
  $get_events_user = 0;// значит получать для всех
  $profiles_array = array(); // здесь будут пользователи-участники событий
  $last_date = false; // здесь будет дата конца событий
  
  
  // все плагины, которые выводят элементы в $options['profile_plugins']
  // получим массив всех элементов всех плагинов
	$cache_key = 'profiles_all_elements';
	
	$profiles_all = mso_get_cache($cache_key); // может есть в кеше
	if (!$profiles_all)
	{
     require (getinfo('plugins_dir') . 'profile/get_elements.php' ); // здесь получим элементы
     mso_add_cache($cache_key, $profiles_all);	 
	}
 
  // возможно есть фильтр адресата события
  $subject = mso_segment(4);
  $subject_id = 0;
  $subject_nik = '';
  if (is_numeric($subject))
  {
   require_once( getinfo('common_dir') . 'comments.php' ); 

    $subject_comuser = mso_get_comuser($subject);
    if (isset($subject_comuser[0]['comusers_id'])) $subject_id = $subject_comuser[0]['comusers_id'];
    if (isset($subject_comuser[0]['comusers_nik'])) $subject_nik = $subject_comuser[0]['comusers_nik'];
  }
  
  	
	// выводим меню элементов и ищем наш слуг
  $url = getinfo('siteurl') . $options['profiles_slug'] . '/' . $segment2 . '/';
  $event_slug = $segment3; 
  

  
  require (getinfo('plugins_dir') . 'profile/pub_pages/menu_events.php' ); // здесь получим меню элементов и текущий элемент
  

  // получим массив элементов для вывода на главной совокупной странице
	$cache_key = 'profiles_first_' . $only_id; // добавляем идентефикатор если ужно выводиь конкретный вид событий
	$profiles_events = mso_get_cache($cache_key);
	if ($options['no_cache']) $profiles_events = false; // здесь будут события к выводу
	if ($profiles_events === false)
	{
     require (getinfo('plugins_dir') . 'profile/get_events.php' ); // здесь получим события
	   mso_add_cache($cache_key, $profiles_events);
  }

  mso_head_meta('title', $title); // meta title страницы
  mso_hook_add( 'head', 'profile_head');
  
  // начало вывода 
  
  require(getinfo('shared_dir') . 'main/main-start.php');
  echo NR . '<div class="type profiles">' . NR;

  require (getinfo('plugins_dir') . 'profile/menu-main.php' ); // выводим главное меню
  
  // выводим меню событий
  echo $pm_menu;
 
 if ($profiles_events)
 {
 //  echo '<H2>' . $title . $title_img  . '</H2>';
   
   echo '<div id="events" class="events">';
   $img_go = getinfo('plugins_url') . 'profile/img/go.png';
   $out = '';
   // подключим файл с циклом вывода
   require(getinfo('plugins_dir') . 'profile/all_foreach.php');
   echo $out;
	 echo '</div>';
	 
	
	if ($options['events_count'] <= count($profiles_events))
	{
	    // установим дату последнего выведенного события
	    echo '<script type="text/javascript">
			         var last_date = ' . $date . ';
		        </script>';

	   if ($only_id === false)
	   {
	     $only_id = 0;
	     $all = 1;
	   }
	   else $all = 0;
	
	   echo '<div id="get_events_button" class="get_events"><a id="get_events_button" href="javascript: void(0);" title="Получить седующие события" onclick="javascript:getEvents('.$get_events_user.','.$only_id.','.$all.','.$subject_id.');">Еще</a></div>';
	   echo '<div class="loader" id="loader"><img src="' . getinfo('plugins_url') . 'profile/img/loader.gif' . '" alt="Идет загрузка…"></div>';
	   echo '<div id="result" class="result"></div>'; 
  }
				 
 } 
 else echo '<p>Нет событий</p>';
  
  echo NR . '</div><!-- class="type profiles" -->' . NR;
	require(getinfo('shared_dir') . 'main/main-end.php');
	
	

?>