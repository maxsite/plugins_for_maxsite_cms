<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// вывод блока для управления закладками


   $get_ajax_path = getinfo('ajax') . base64_encode('plugins/bookmaker/get-ajax.php');
   $edit_ajax_path = getinfo('ajax') . base64_encode('plugins/bookmaker/edit-ajax.php');
   $b_c_id = $comuser['comusers_id'];
   $b_e_id = 0;
   $b_e_t_id = 'bp'; // meta_slug

   $button_value = 'Удалить из закладок'; 
   $button_title = 'Удалить материал из закладок';
   $button_act = 0;    
                  
  ?>
  <input type="hidden" id="b_get_ajax_path" value="<?= $get_ajax_path ?>">
  <input type="hidden" id="b_edit_ajax_path" value="<?= $edit_ajax_path ?>">
  <input type="hidden" id="b_c_id" value="<?= $b_c_id ?>">
  <input type="hidden" id="b_e_id" value="<?= $b_e_id ?>">
  <input type="hidden" id="b_e_t_id" value="<?= $b_e_t_id ?>">
<?php

// require($plugin_dir . 'functions.php');	
 
 // получим все страницы, добавленные в закладки этим пользователем
 $par = array(
   'custom_func' => 'bookmakers_get_pages',
   'comuser_id' => $b_c_id , 
   'e_t_id' => $b_e_t_id  ,
 );
 
 $added_pages = mso_get_pages($par , $pag);
 
 if ($added_pages)
   foreach ($added_pages as $cur)
   {
     bookmaker_page_title($cur['page_id'] , $cur['page_slug'] , $cur['page_title'] , $button_value , $button_title , $button_act);
   
   }


function bookmaker_page_title($id , $slug, $title , $button_value , $button_title , $button_act)
{
  echo '<p><a href="' . getinfo('site_url') . 'page/' . $slug . '">' . $title . '</a>' . 
     '<span class="bookmaker1" id="bookmaker_block' . $id . '"><input id="bookmaker_edit' . $id . '" type="button" value="'.$button_value.'" title="'.$button_title.'" onClick="editBM(' . $button_act . ' , ' . $id . ') "></span></p>';
}
 
 
function bookmakers_get_pages($r, $pag)
{
  	$CI = & get_instance();
	
	$offset = 0;

	if ($r['pagination'])
	{
		# пагинация
		# для неё нужно при том же запросе указываем общее кол-во записей и кол-во на страницу
		# сама пагинация выводится отдельным плагином
		# запрос один в один, кроме limit и юзеров
		$CI->db->select('SQL_BUFFER_RESULT ' . $CI->db->dbprefix('page') . '.`page_id`', false);
		$CI->db->from('page');
		if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);

		if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);
		
		// if ($r['date_now']) $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);

		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		$CI->db->join('meta', 'meta.meta_value = page.page_id');
	
		$CI->db->where('meta_key', 'bookmarks');
		$CI->db->where('meta_table', 'comusers');
		$CI->db->where('meta_slug', $r['e_t_id']);
		$CI->db->where('meta_id_obj', $r['comuser_id']);
		
		
		
		if ($function_add_custom_sql = $r['function_add_custom_sql']) $function_add_custom_sql();

		$query = $CI->db->get();

		$pag_row = $query->num_rows();

		if ($pag_row > 0)
		{
			$pag['maxcount'] = ceil($pag_row / $r['limit']); // всего станиц пагинации
			$pag['limit'] = $r['limit']; // записей на страницу

			$current_paged = mso_current_paged($r['pagination_next_url']);
			if ($current_paged > $pag['maxcount']) $current_paged = $pag['maxcount'];

			$offset = $current_paged * $pag['limit'] - $pag['limit'];
		}
		else
		{
			$pag = false;
		}
	}
	else
		$pag = false;
		
	if (!$r['all_fields'])
	{
	// теперь сами страницы
		if ($r['content'])
			$CI->db->select('page.page_id, page_type_name, page_slug, page_title, page_date_publish, page_status, users_nik, page_content, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, users_avatar_url, meta.meta_value AS tag_name, page.page_id_autor, users_description, users_login');
		else
			$CI->db->select('page.page_id, page_type_name, page_slug, page_title, "" AS page_content, page_date_publish, page_status, users_nik, page_view_count, page_rating, page_rating_count, page_password, page_comment_allow, page_id_parent, users_avatar_url, meta.meta_value AS tag_name, page.page_id_autor, users_description, users_login', false);
	
	}
	else
	{
		$CI->db->select('page.*, page_type.*, users.*');
	}

	$CI->db->from('page');
	if ($r['page_status']) $CI->db->where('page_status', $r['page_status']);

	//if ($r['date_now']) $CI->db->where('page_date_publish < ', 'DATE_ADD(NOW(), INTERVAL "' . $r['time_zone'] . '" HOUR_MINUTE)', false);
	if ($r['date_now'] and $r['page_id_date_now']) $CI->db->where_not_in('page.page_id', $r['page_id_date_now']);
		

	if ($r['page_id_autor']) $CI->db->where('page.page_id_autor', $r['page_id_autor']);

	$CI->db->join('users', 'users.users_id = page.page_id_autor');

		$CI->db->join('page_type', 'page_type.page_type_id = page.page_type_id');
		$CI->db->join('meta', 'meta.meta_value = page.page_id');
	
		$CI->db->where('meta_key', 'bookmarks');
		$CI->db->where('meta_table', 'comusers');
		$CI->db->where('meta_slug', $r['e_t_id']);
		$CI->db->where('meta_id_obj', $r['comuser_id']);

	$CI->db->order_by($r['order'], $r['order_asc']);
	$CI->db->group_by('page.page_id');

	if (!$r['no_limit'])
	{
		if ($pag and $offset) $CI->db->limit($r['limit'], $offset);
			else $CI->db->limit($r['limit']);
	}
	
	if ($function_add_custom_sql = $r['function_add_custom_sql']) $function_add_custom_sql();

} 
 
# end file 