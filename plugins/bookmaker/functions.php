<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
/*
в файле Функции для работы с закладками
*/

// определим - добавлена ли закладка пользователем $comuser_id для сущности типа $e_t_id с $e_id
function bookmaker_added($comuser_id , $e_id , $e_t_id)
{
  // закладка добавлена, если есть у комюзера метаполе с именем bookmarks meta_slug = $e_t_id и значением '$e_id'
	$CI = & get_instance();

	$CI->db->select('meta_id');
	$CI->db->where( array ( 'meta_key' => 'bookmarks', 'meta_id_obj' => $comuser_id, 'meta_table' => 'comusers', 'meta_slug' => $e_t_id , 'meta_value' => $e_id) );
	$query = $CI->db->get('meta');

	if ($query->num_rows() > 0)
	{
	  $query->result_array();
		foreach ($query->result_array() as $row)	 ; 
		return $row['meta_id'];
	}	
	else return false;  
}


function bookmaker_edit($comuser_id , $e_id , $e_t_id , $action)
{
	$CI = & get_instance();
  
  // проверим - добавлена ли такая запись
  $meta_id = bookmaker_added($comuser_id , $e_id , $e_t_id);

  if ($action)
  {
    // если добавить закладку
    if (!$meta_id) // если нет такой записи
    {
      $data = array(
        'meta_key' => 'bookmarks',
        'meta_id_obj' => $comuser_id,
        'meta_table' => 'comusers',
        'meta_value' => $e_id,
        'meta_desc' => '',
        'meta_menu_order' => 0,
        'meta_slug' => $e_t_id
       );

			$res = $CI->db->insert('meta', $data);
			$CI->db->cache_delete_all();
			return false;  // нет ошибки
    }
    else return 'Уже добавлена в закладки.';
  }
  else
  {
     // удалим из закладок
     if ($meta_id)
     {
				$CI->db->where( array ('meta_id' => $meta_id) );
				$CI->db->delete('meta');
			  return false;  // нет ошибки
     }
     else return 'Нет в закладках.';
  }

}



?>