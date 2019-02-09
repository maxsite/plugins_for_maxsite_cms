<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
mso_cur_dir_lang('templates');

// выведем список всех действий пользователя

function profile_all_head($arg = array())
{
	echo '<script type="text/javascript" src="' . getinfo('plugins_url') . 'profile/profile.js" ></script>';
	echo '<script type="text/javascript">
			var ajax_path = "' . getinfo('ajax') . base64_encode('plugins/profile/get_events-ajax.php') . '";
		</script>';		
	
	return $arg;
}

	mso_hook_add( 'head', 'profile_all_head');
	

	$get_events_user = $comusers_id; 
	  
if (!$get_events_user and mso_get_option('page_404_http_not_found', 'templates', 1) ) header('HTTP/1.0 404 Not Found'); 

if ($get_events_user)
{
   $last_date = false; // здесь будет дата конца событий


   // все плагины, которые выводят элементы в $options['profile_plugins']
   // получим массив всех элементов всех плагинов
	 $cache_key = 'profiles_all_elements';
	 $profiles_all = mso_get_cache($cache_key);
	 
	 if (!$profiles_all)
	 {
     require (getinfo('plugins_dir') . 'profile/get_elements.php' ); // здесь получим элементы
     mso_add_cache($cache_key, $profiles_all);	 
	 }
	
 
	 // получаем меню элементов и ищем наш слуг
   $event_slug = mso_segment(4);
   $url = getinfo('siteurl') . $options['profiles_slug'] . '/' . $segment2 . '/' . $segment3 . '/';
   
  // возможно есть фильтр адресата события
  $subject = mso_segment(5);
  $subject_id = 0;
  $subject_nik = '';  
  if (is_numeric($subject))
  {
    $subject_comuser = mso_get_comuser($subject);
    if (isset($subject_comuser[0]['comusers_id'])) $subject_id = $subject_comuser[0]['comusers_id'];
    if (isset($subject_comuser[0]['comusers_nik'])) $subject_nik = $subject_comuser[0]['comusers_nik'];
  }   
   
   require (getinfo('plugins_dir') . 'profile/pub_pages/menu_events.php' ); // здесь получим меню элементов и текущий элемент
   

   mso_head_meta('title', $title); // meta title страницы
   mso_hook_add( 'head', 'profile_head');	
}


// теперь сам вывод
# начальная часть шаблона
require(getinfo('shared_dir') . 'main/main-start.php');


echo NR . '<div class="type type_users">' . NR;


// меню страниц публичного профиля
require (getinfo('plugins_dir') . 'profile/pub_pages/menu-profiles.php' );

// выводим меню событий
echo $pm_menu;
  
if ($get_events_user)
{
  $options['no_cache'] = isset($options['no_cache']) ? $options['no_cache'] : true; // сортировка событий
  // получим массив элементов для вывода на главной совокупной странице
	$cache_key = 'profiles_first_' . $get_events_user . $only_id . $subject_id;
	$profiles_events = mso_get_cache($cache_key);
	if ($options['no_cache']) $profiles_events = false; // здесь будут события к выводу
	if ($profiles_events === false)
	{
     require (getinfo('plugins_dir') . 'profile/get_events.php' ); // здесь получим события
	   mso_add_cache($cache_key, $profiles_events);
  }


 if ($profiles_events)
 {
/*
   // хлебные крошки
   $bread = '<a href="' . $url . 'all' . '" title="' . $options['all_events_title'] . '">' . $options['all_events_title'] . '</a> >> ';
   if ($subject_id) 
       $bread .= $title_img . '<a href="' . $url . 'all/' . $event_slug . '" title="' . $element_name . '">' . $element_name . '</a> >> ' . $subject_nik;
   else $bread .= $title_img . $element_name;
   echo '<H2>' . $bread . '</H2>' . NR;
 */  
   echo '<div id="events" class="events">';
   $img_go = getinfo('plugins_url') . 'profile/img/go.png';
   $out = '';
   // подключим файл с циклом вывода
   require(getinfo('plugins_dir') . 'profile/all_foreach.php');
   echo $out;
	 echo '</div>';

	if ($options['events_count'] <= count($profiles_events)) //выводить ли кнопку еще
	{
	  // установим дату последнего выведенного события
	  echo '<script type="text/javascript">
			var last_date = ' . $date . ';
		</script>';	
		
	  // данные для аякс
	  if ($only_id === false)
	  {
	    $only_id = 0; // чтобы передать в jquery
	    $all = 1; // флаг - получать все события
	  }
	  else $all = 0;	// флаг - получать только события по элементу с key = $only_id
	
	  echo '<div id="get_events_button" class="get_events"><a href="javascript: void(0);" title="Получить седующие события" onclick="javascript:getEvents('.$get_events_user.','.$only_id.','.$all.','.$subject_id.');">Еще</a></div>';
	  echo '<div class="loader" id="loader"><img src="' . getinfo('plugins_url') . 'profile/img/loader.gif' . '" alt="Идет загрузка…"></div>';
	  echo '<div id="result" class="result"></div>'; // здесь будут ошибки ajax 
  }
				 
 } 
 else echo '<p>Такие действия не совершались</p>';

}
else
{
	if ($f = mso_page_foreach('pages-not-found')) 
	{
		require($f); // подключаем кастомный вывод
	}
	else // стандартный вывод
	{
		echo '<h1>' . t('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . t('Извините, пользователь с указанным номером не найден.') . '</p>';
		echo mso_hook('page_404');
	}
}

echo NR . '</div><!-- class="type type_users" -->' . NR;

# конечная часть шаблона
require(getinfo('shared_dir') . 'main/main-end.php');


?>