<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
	 // выводим меню элементов и ищем наш слуг

  $only_id = false; // нет идентефикатора конкретного вида событий - значит все виды событий	
  
  $title = $options['all_events_title'];
  $title_img  = '<img src="' . getinfo('plugins_url') . 'profile/img/all.png">';
  $pm_menu = '';
  $element_name = '';
  foreach ($profiles_all as $key=>$element)
  {
	  if (!$element['slug']) continue;// если слуг пуст - пропускаем
	  if ($event_slug == $element['slug'])
	  {
	    $title = $options['all_events_title'] . ' >> ' . $element['name'];
	    $element_name = $element['name'];
	    $title_img = '<img src="' . $element['img'] . '">';
	    $only_id = $key;
	    $pm_menu .= '<img class="cur_elem" src="' . $element['img'] . '" title="' . $element['name'] . '" alt="' . $element['name'] . '">';
	  }  
    else 
    {
    $pm_menu .= '<a href="' . $url . $element['slug'] . '" title="' . $element['name'] . '"><img class="link_elem" src="' . $element['img'] . '" alt="' . $element['name'] . '"></a>' . NR;
    }
  }
	
 // добавляем вначало кнопочку all - все события
  if ($only_id !== false) // если нашли конкретное событие
      $pm_menu = '<a href="' . $url . '" title="'.$options['all_events_title'].'"><img class="link_elem" src="' . getinfo('plugins_url') . 'profile/img/all.png" title="' . $options['all_events_title'] . '" alt="' . $options['all_events_title'] . '"></a>' . NR . $pm_menu;
  else // если у нас страница всех событий, тогда кнопочка all без ссылки
	   $pm_menu = '<img class="cur_elem" src="' . getinfo('plugins_url') . 'profile/img/all.png" title="' . $options['all_events_title'] . '" alt="' . $options['all_events_title'] . '">' . NR . $pm_menu; 

  $pm_menu = '<div class="menu_events">' . $pm_menu . '<span class="event_link">' . /*$title .*/ '</span></div>' . NR;
  
?>