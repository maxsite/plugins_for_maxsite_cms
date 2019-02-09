<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


function taggallery_view_count_first($prefix = '' , $unique = false, $name_cookies = 'maxsite-cms-taggallery', $expire = 2592000)
{
		global $_COOKIE, $_SESSION;

		if ( !mso_get_option('page_view_enable', 'templates', '1') AND !$unique) return true; //если нет такой опции или не пришло в функцию, то выходим
		if ( !$unique ) $unique = mso_get_option('page_view_enable', 'templates', '1');

		$slug = mso_segment(2);
		if ($prefix) $picture_slug = str_replace($prefix , "" , $slug);
		else $picture_slug = $slug;
		$all_slug = array();

		if( $unique == 0 ) return false; // не вести подсчет
		elseif ($unique == 1) //с помощью куки
		{
			if (isset($_COOKIE[$name_cookies]))	$all_slug = explode('|', $_COOKIE[$name_cookies]); // значения текущего кука
			if ( in_array($slug, $all_slug) ) return false; // уже есть текущий урл - не увеличиваем счетчик
		}
		elseif ($unique == 2) //с помощью сессии
		{
			session_start();
			if (isset($_SESSION[$name_cookies]))	 $all_slug = explode('|', $_SESSION[$name_cookies]); // значения текущей сессии
			if ( in_array($slug, $all_slug) ) return false; // уже есть текущий урл - не увеличиваем счетчик
		}

		// нужно увеличить счетчик
		$all_slug[] = $slug; // добавляем текущий slug
		$all_slug = array_unique($all_slug); // удалим дубли на всякий пожарный
		$all_slug = implode('|', $all_slug); // соединяем обратно в строку
		$expire = time() + $expire;

		if ($unique == 1) @setcookie($name_cookies, $all_slug, $expire); // записали в кук
		elseif ($unique == 2) $_SESSION[$name_cookies]=$all_slug; // записали в сессию

		// получим текущее значение page_view_count
		// и увеличиваем значение на 1
		$CI = get_instance();
		$CI->db->select('picture_view_count');
		
		$CI->db->where('picture_slug', $picture_slug);
		$CI->db->limit(1);
		$query = $CI->db->get('pictures');
		

		if ($query->num_rows() > 0)
		{
			 $pages = $query->row_array();
			 $page_view_count = $pages['picture_view_count'] + 1;
		   $CI->db->where('picture_slug', $picture_slug);
			 $CI->db->update('pictures', array('picture_view_count'=>$page_view_count));
		   $CI->db->cache_delete('pictures', $picture_slug);

			return true;
		}	
	} 
	
	
	
?>