<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
# Функции которые выполняют роль подсчета количества прочтения записи
# первая функция, проверяет из сессии значение массива с текущим url
# если номера не совпадают, то функция устанавливает значение прочтений больше на 1
# если совпадают, значит запись уже была прочитана с этого компа
# если нужно убрать уникальность и учитывать все хиты, то $unique = false
# начения хранятся в виде url1|url2|url2|url3
# url - второй сегмент
# время жизни 3 дня: 60 секунд * 60 минут * 24 часа * 3 дня = 259200

function ra_view_count_first($unique = true, $name_cookies = 'maxsite-cms', $expire = 259200)
{
	
	if ( !mso_get_option('page_view_enable', 'templates', '1') ) return true;
	session_start();
	
	global $_SESSION;

	if (isset($_SESSION['ra_view_count']))	$all_slug = $_SESSION['ra_view_count']; // значения текущего кука
		else $all_slug = ''; // нет такой куки вообще

	$slug = mso_segment(2);

	$all_slug = explode('|', $all_slug); // разделим в массив

	if ( $unique )
		if ( in_array($slug, $all_slug) ) return false; // уже есть текущий урл - не увеличиваем счетчик

	// нужно увеличить счетчик
	$all_slug[] = $slug; // добавляем текущий id
	$all_slug = array_unique($all_slug); // удалим дубли на всякий пожарный
	$all_slug = implode('|', $all_slug); // соединяем обратно в строку
	$expire = time() + $expire;
	$_SESSION['ra_view_count']=$all_slug; // записали в сессию
	
	// получим текущее значение page_view_count
	// и увеличиваем значение на 1
	$CI = & get_instance();
	$CI->db->select('page_view_count');
	$CI->db->where('page_slug', $slug);
	$CI->db->limit(1);
	$query = $CI->db->get('page');

	if ($query->num_rows() > 0)
	{
		$pages = $query->row_array();
		$page_view_count = $pages['page_view_count'] + 1;

		$CI->db->where('page_slug', $slug);
		$CI->db->update('page', array('page_view_count'=>$page_view_count));
		$CI->db->cache_delete('page', $slug);

		return true;
	}
}
?>