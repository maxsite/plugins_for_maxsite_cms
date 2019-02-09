<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


// функции добавляют просмотр дикуссии 

function dialog_add_wath($par = array())
{
	 if (!isset($par['user_id'])) return 'Не указан пользователь';
	 if (!isset($par['discussion_id'])) return 'Не указана дискуссия';
	 
	 $CI = & get_instance();
	 
	   $CI->db->select('watch_count');
	   $CI->db->where('watch_discussion_id', $par['discussion_id']);
	   $CI->db->where('watch_user_id', $par['user_id']);
	   $query = $CI->db->get('dwatch');
	   if ($query->num_rows() > 0) 
	   {	 
	      $row = $query->row_array(1);
	      $upd_data = array(
             'watch_date' => time(),
             'watch_count' => $row['watch_count'] + 1
               );
               
        $CI->db->where('watch_user_id', $par['user_id']);
        $CI->db->where('watch_discussion_id', $par['discussion_id']);
			  $res = ($CI->db->update('dwatch', $upd_data)) ? '1' : '0';
			  if (!$res) return 'Не обновлен просмотр';	     
	   }
     else
     {
	      $ins_data = array(
             'watch_user_id' => $par['user_id'],
             'watch_date' => time(),
             'watch_count' => 1,
             'watch_discussion_id' => $par['discussion_id']);        
			  $res = ($CI->db->insert('dwatch', $ins_data)) ? '1' : '0';
			  if (!$res) return 'Не обновлен просмотр';
		 }  
		 
		 return false;  // нет ошибок
}



function dialog_view_count_first($discussion_id = 0 , $unique = false, $name_cookies = 'maxsite-cms-dialog', $expire = 2592000)
{
		global $_COOKIE, $_SESSION;
    $all_discussions = array(); 
		if ( !mso_get_option('page_view_enable', 'templates', '1') AND !$unique) return true; //если нет такой опции или не пришло в функцию, то выходим
		if ( !$unique ) $unique = mso_get_option('page_view_enable', 'templates', '1');

		if( $unique == 0 ) return false; // не вести подсчет
		elseif ($unique == 1) //с помощью куки
		{
			if (isset($_COOKIE[$name_cookies]))	$all_discussions = explode('|', $_COOKIE[$name_cookies]); // значения текущего кука
			if ( in_array($discussion_id, $all_discussions) ) return false; // уже есть текущий урл - не увеличиваем счетчик
		}
		elseif ($unique == 2) //с помощью сессии
		{
			session_start();
			if (isset($_SESSION[$name_cookies]))	 $all_discussions = explode('|', $_SESSION[$name_cookies]); // значения текущей сессии
			if ( in_array($discussion_id, $all_discussions) ) return false; // уже есть текущий урл - не увеличиваем счетчик
		}

		// нужно увеличить счетчик
		$all_discussions[] = $discussion_id; // добавляем текущий slug
		$all_discussions = array_unique($all_discussions); // удалим дубли на всякий пожарный
		$all_discussions = implode('|', $all_discussions); // соединяем обратно в строку
		$expire = time() + $expire;

		if ($unique == 1) @setcookie($name_cookies, $all_discussions, $expire); // записали в кук
		elseif ($unique == 2) $_SESSION[$name_cookies]=$all_discussions; // записали в сессию

		// получим текущее значение discussion_view_count
		// и увеличиваем значение на 1
		$CI = get_instance();
		$CI->db->select('discussion_view_count');
		
		$CI->db->where('discussion_id', $discussion_id);
		$CI->db->limit(1);
		$query = $CI->db->get('ddiscussions');
		
		if ($query->num_rows() > 0)
		{
			 $discussions = $query->row_array();
			 $discussion_view_count = $discussions['discussion_view_count'] + 1;
		   $CI->db->where('discussion_id', $discussion_id);
			 $CI->db->update('ddiscussions', array('discussion_view_count'=>$discussion_view_count));
		   $CI->db->cache_delete('ddiscussions', $discussion_id);
			return true;
		}
		else return false;	
	} 
	
	
	
?>