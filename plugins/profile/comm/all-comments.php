<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// вывод всех событий по всем поключаемым элементам

function profile_all_head($arg = array())
{
	echo '<script type="text/javascript" src="' . getinfo('plugins_url') . 'profile/comm/gcomm.js" ></script>';
	echo '<script type="text/javascript">
			var ajax_path = "' . getinfo('ajax') . base64_encode('plugins/profile/comm/get_comments-ajax.php') . '";
			var page_id = 0;
			var comuser_id = 0;
			var user_id = 0;
			var pag_no = 1;
			var sort = 0;
			var limit = 20;
			var pag_c = 0;
		</script>';		
	
	return $arg;
}

	mso_hook_add( 'head', 'profile_all_head');



// возможно есть фильтр страницы
  $page_id = 0;
  $comuser_id = 0;
  $user_id = 0;
  $sort = 0;
  
	

    mso_head_meta('title', 'comm'); // meta title страницы
	mso_hook_add( 'head', 'profile_head');
  
  // начало вывода 
  
  require(getinfo('shared_dir') . 'main/main-start.php');
  echo NR . '<div class="type profiles">' . NR;

 
   echo '<div id="events" class="events">';
   echo '</div>';
	
	
   echo '<div id="get_events_button" class="get_events"><a id="get_events_button" href="javascript: void(0);" title="Получить седующие комментарии" onclick="javascript:getEvents('.$page_id.','.$user_id.','.$comuser_id.','.$sort.');">Еще</a></div>';
   echo '<div class="loader" id="loader"><img src="' . getinfo('plugins_url') . 'profile/img/loader.gif' . '" alt="Идет загрузка…"></div>';
   echo '<div id="result" class="result"></div>'; 
			 

  
  echo NR . '</div><!-- class="type profiles" -->' . NR;
  require(getinfo('shared_dir') . 'main/main-end.php');
	
	

?>