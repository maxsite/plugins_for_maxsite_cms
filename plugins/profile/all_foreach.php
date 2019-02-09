<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// тело цикла вывода событий в переменную $out 
// подключается как из all.php , comusers_all.php, так из get_events-agax.php

   // нужно получить имена комюзеров
   $profiles_array = profile_get_names();

   if ($get_events_user) $url = getinfo('siteurl') . $options['profiles_slug'] . '/' . mso_segment(2) . '/' . mso_segment(3) . '/';
   else $url = getinfo('siteurl') . $options['profiles_slug'] . '/' . mso_segment(2) . '/';

   $i = 0; // чтобы не вывести больше заданного
   foreach ($profiles_events as $date=>$event)
   {
           /* 
           содержимое $profiles_all, настраиваемое в опциях подключаемого элемента
           на примере события-комментарий
		     ['title'] = Сообщение в дискуссии
		     ['name'] = Сообщение
		     ['all'] = Все сообщения на форуме
		     ['title_go'] = Перейти к сообщению в дискуссии
		     ['all_link'] = forum/all-comments
		     ['img'] = getinfo('plugins_url') . dialog/img/message.png
		     ['filename'] = comments_profiles
		     
	         содержимое $event
	         0-Дата
	         1-Автор события
	         2-контент события
	         3-ссылка на событие 
	         4-заголовок элемента 
	         5-автор элемента 
	         6-id элемента в массиве $profiles_all
	         */
         
          
           $out .= '<div class="event">';
         
	       $out .= '<div class="event_title">';
	       
	    // иконку-ссылку выведем только если страница смешанная (all) а не конкретного события
	  //  if ($only_id === false) // если не на странице конкретного события
	    {
	         // выведем, если надо, ссылку на специальную страницу всех событий, если заданно
           if ($profiles_all[$event[6]]['all_link']) 
             $out .= '<a href="' . $site_url . $profiles_all[$event[6]]['all_link'] . '" title="' . $profiles_all[$event[6]]['all'] . '"><img src="' . $profiles_all[$event[6]]['img'] . '"></a>';
           elseif($event[1] and ( mso_segment(2) != $event[1])) // или , если есть автор, ссылку на автоматически сгенерированную страницу всех событий автора
             $out .= '<a href="' . getinfo('siteurl') . $options['profiles_slug'] . '/' . $event[1] . '/' . mso_segment(2) . '/' . $profiles_all[$event[6]]['slug'] . '" title="' . $profiles_all[$event[6]]['all'] . ' пользователя ' . $profiles_array[$event[1]] . '"><img src="' . $profiles_all[$event[6]]['img'] . '"></a>';
           else
             $out .= '<a href="' . $url . $profiles_all[$event[6]]['slug'] . '" title="' . $profiles_all[$event[6]]['all'] . '"><img src="' . $profiles_all[$event[6]]['img'] . '"></a>';

        } 
         // дата события
         $out .= '<span class="event_date">' . profile_date($event[0]) . '</span>';   

         // если выводим не для конкретного пользователя
         // и есть информация о пользователе (автор события)
         if (!$get_events_user and $event[1])
         {
            $out .= '<span class="event_nik"><a href="' . $site_url . $options['profiles_slug'] . '/' . $event[1] . '" title="Смотреть профиль пользователя">' . $profiles_array[$event[1]] . '</a></span>';
         }   
         
         // если есть заголовок события
         if ($profiles_all[$event[6]]['title'])         
           $out .= '<span class="event_title">' . $profiles_all[$event[6]]['title'] . '</span>';

         // если есть заголовок элемента, которого касается событие
         if ($event[4] )          
           $out .= '<span class="event_element">' . $event[4] . '</span>';
         
         // если есть информация о пользователе, которого касается событие (автор элемента)
         if ($event[5])
         {
            $out .= '<span class="event_nik"><a href="' . $site_url . $options['profiles_slug'] . '/' . $event[5] . '" title="Смотреть профиль пользователя">' . $profiles_array[$event[5]] . '</a></span>';
         }            
         
         // если есть ссылка на событие
         if ($event[3])  
         {
           $out .= '<span class="event_link"><a href="' . $site_url . $event[3] . '" title="' . $profiles_all[$event[6]]['title_go'] . '"><img src="' . $img_go . '" alt="' . $profiles_all[$event[6]]['title_go'] . '"></a></span>';
         }
         
         $out .= '</div>';//конец заголовка
         
         // если есть какой-нибудь контент еще
         if ($event[2]) $out .= '<div class="event_content">' . $event[2] . '</div>'; 
         
         $out .= '</div>';// конец события

	       $i++;
	       if ($i==$options['events_count']) break;
  } 


// получим имена комюзеров для вывода
function profile_get_names()
{
	$cache_key = 'profile_names';
	$profile_names = mso_get_cache($cache_key);
	if (!$profile_names)
	{
	  $CI = & get_instance();
	  $CI->db->select('comusers_id , comusers_nik');
	  $query = $CI->db->get('comusers');
	  if ($query->num_rows() > 0)
	  {
	     $profile_names = array();
	     $results = $query->result_array(); // данные комюзера
	     foreach ($results as $result)
	        $profile_names[$result['comusers_id']] = $result['comusers_nik'];
	    
	     mso_add_cache($cache_key, $profile_names);
	  }
	  else return array();	  
	}
	return $profile_names;
}


# получение даты
// файл foreach может подключаться по ajax тогда mso_page_date будет недоступна
function profile_date($date = 0, $format = array())
{
	if (!$date) return '';
  
  $date = date('Y-m-d H:i:s' , $date);
  
	if (!$format)
    $format = array(	'format' => 'j F Y в H:i:s', // 'j F Y в H:i:s'
											'days' => t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье'),
											'month' => t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря'));
  

		if (isset($format['format'])) $df = $format['format'];
			else $df = 'D, j F Y г.';

		if (isset($format['days'])) $dd = $format['days'];
			else $dd = t('Понедельник Вторник Среда Четверг Пятница Суббота Воскресенье');

		if (isset($format['month'])) $dm = $format['month'];
			else $dm = t('января февраля марта апреля мая июня июля августа сентября октября ноября декабря');

	// учитываем смещение времени time_zone
	$out = mso_date_convert($df, $date, true, $dd, $dm);

	return $out;
}
?>