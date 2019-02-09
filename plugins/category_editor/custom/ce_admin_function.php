<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

function ce_get_pages_for_cat_list($cat_id, $exclude = array(), $asc = 'ASC')
{
	$CI = & get_instance();
	$out = array();
	$pages = array();
	
	$CI->db->select('page.page_id, page_id_parent, page_title, page_status, page_menu_order, page_slug');
	
	if(!$cat_id)
	{
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id', 'left');
		$CI->db->where('category_id IS NULL');
		$CI->db->order_by('page.page_menu_order', $asc);
	}
	else
	{
		$CI->db->join('cat2obj', 'cat2obj.page_id = page.page_id');
		$CI->db->where('cat2obj.category_id', $cat_id);
		$CI->db->order_by('cat2obj.c2obj_order', $asc);
	}
	if($exclude)
	{
		$CI->db->where_not_in('page.page_id', $exclude);
	}
	
	
	$query = $CI->db->get('page');
	
	foreach($query->result_array() as $row)
	{
		$pages[] = $row['page_id'];
		$out[$row['page_id']] = $row;
		$out[$row['page_id']]['childs'] = array();
	}
	// дочерние страницы
	$childs = _ce_help_pfcl($pages, $asc);
	if($childs)
	{
		foreach($out as $key => &$val_parent)
		{
			$val_parent['childs'] = isset($childs[$key]) ? $childs[$key] : array();
		}
	}
	
	
	return $out;
}

function _ce_help_pfcl($pages_select = array(), $asc = 'ASC')
{
	if(!$pages_select) return array();
	$CI = & get_instance();
	$out = array();
	$pages = array();
	$CI->db->select('page_id, page_id_parent, page_title, page_status, page_menu_order, page_slug');
	$CI->db->where_in('page_id_parent', $pages_select);
	$CI->db->order_by('page_menu_order', $asc);
	$query = $CI->db->get('page');
	foreach($query->result_array() as $row)
	{
		$pages[] = $row['page_id'];
		$out[$row['page_id_parent']][$row['page_id']] = $row;
		$out[$row['page_id_parent']][$row['page_id']]['childs'] = array();
	}
	if(!$pages) return array();
	if($level = _ce_help_pfcl($pages, $asc))
	{
		foreach($out as &$value)
		{
			foreach($value as &$val)
			{
				$val['childs'] = isset($level[$val['page_id']]) ? $level[$val['page_id']] : array();
			}
		}
		unset($value, $val);
	}
	
	return $out;
	
	
}

function ce_child_pages_list($childs = array(), $uni = 1, $class_ul = 'is-pages', $level = 0) {
	++$level;
	$cout = '';
	if($level != 1)
	{
		$cout = '<ul class="'.$class_ul.' page-sort-connected">';
	}
	foreach($childs as $key=>$value) {
		if($value['childs']) {
			
			
			$cout .= create_item_page_start($value, $uni);
			
			
			$cout .= ce_child_pages_list($value['childs'], 'b'.$uni, 'is-pages', $level); 
		}else{
			
			$cout .= create_item_page_start($value, $uni);

			$cout .= '<ul class="is-pages page-sort-connected">'.NR;
				$cout .= '<li class="is-category-li li-empty">Нет вложенных страниц</li>';
			$cout .= '</ul>'.NR;
		}
		$cout .= '</li>'.NR;
	}
	if($level != 1)
	{
		$cout .= '</ul>'.NR;
	}
	
	return $cout;
}

function ce_change_status_page($page_id = 0)
{
	$CI = & get_instance();
	$status = '';
	$data = array();
	
	$CI->db->select('page_status');
	$CI->db->where('page_id', $page_id);
	$query = $CI->db->get('page');
	if ($query->num_rows() > 0)
	{
		$status = $query->row('page_status');
	}
	else
	{
		return false;
	}
	
	if($status == 'publish')
	{
		$data = array('page_status' => 'draft');
	}
	else
	{
		$data = array('page_status' => 'publish');
	}
	
	$CI->db->where('page_id', $page_id);
	$upd = $CI->db->update('page', $data);
	if($upd)
	{
		return $data['page_status'];
	}
	else
	{
		return false;
	}
}


function ce_change_status_category($cat_id = 0)
{
	return false;
}


function create_item_category_start($cat_id = 0, $cat_name = 'Категория', $cat_slug = 'slug')
{
	$out = '<li class="is-category-li" data-category-id="'.$cat_id.'" data-cat-slug="'.$cat_slug.'">';
	$out .= '<div class="line" ';
	$out .= 'data-obj-id = "'.$cat_id.'" ';
	$out .= 'data-site-url = "'.getinfo('site_url').'category/'.$cat_slug.'" ';
	$out .= 'data-edit-url = "'.getinfo('site_url').'admin/category_editor/edit/'.$cat_id.'" ';
	$out .= 'data-obj-type= "category" >';
	
	$out .= '<span class="e-close sp-icon" title="Открыть вложеные объекты" id="ecl_c'.$cat_id.'">+</span>';
	$out .= '<span class="e-handler sp-icon eh-category" title="Переместить категорию"></span>';
	
	$out .= '<a href="/admin/category_editor/edit/'.$cat_id.'" title="Редактировать категорию">';
	$out .= $cat_name;
	$out .= '</a>';
	
	$out .= '<div class="ce-toolbar" data-id-obj="'.$cat_id.'" data-obj-type="category">';
	$out .= '<a href="#" class="publish-switch" title="Изменить статус"></a>';
	$out .= '<a href="#" class="obj-copy" title="Копировать"></a>';
	$out .= '<a href="#" class="obj-edit" title="Редактировать"></a>';
	$out .= '<a href="#" class="obj-chpgs-update" title="Обновить список страниц"></a>';
	$out .= '<a href="#" class="obj-psort" title="Отсортировать страницы"></a>';
	$out .= '<a href="#" class="obj-delete" title="Удалить"></a>';
	$out .= '</div>';
	
	$out .= '</div> '.NR;
	return $out;
}

function create_item_page_start($args = array(), $uni = '')
{
	$page_id = isset($args['page_id']) ? $args['page_id'] : 0;
	$page_name = isset($args['page_title']) ? $args['page_title'] : 'NO TITLE';
	$page_slug = isset($args['page_slug']) ? $args['page_slug'] : '';
	$page_status = isset($args['page_status']) ? $args['page_status'] : 'draft';
	$page_parent = isset($args['page_id_parent']) ? $args['page_id_parent'] : 0;
	
	
	if($page_status != 'publish')
		$out = '<li class="is-page-li no-publish" data-page-id="'.$page_id.'" data-page-parent="'.$page_parent.'">'.NR;
	else
		$out = '<li class="is-page-li" data-page-id="'.$page_id.'" data-page-parent="'.$page_parent.'">'.NR;
		
	$out .= '<div class="line" '; 
	$out .= 'data-obj-id = "'.$page_id.'" ';
	$out .= 'data-site-url = "'.getinfo('site_url').'page/'.$page_slug.'" ';
	$out .= 'data-edit-url = "'.getinfo('site_url').'admin/page_edit/'.$page_id.'" ';
	$out .= 'data-page-parent = "'.$page_parent.'" ';
	$out .= 'data-obj-type= "page" >';
	
	$out .= '<span class="e-close sp-icon" title="Открыть вложеные объекты" id="ecl_p'.$page_id.$uni.'">+</span>';
	
	$out .= '<span class="e-handler sp-icon eh-page" title="Переместить страницу"></span>';
	
	$out .= '<a href="/admin/page_edit/'.$page_id.'" title="Редактировать страницу"  target="_blank">';
	$out .= $page_name;
	$out .= '</a>';
	
	$out .= '<div class="ce-toolbar" data-obj-id="'.$page_id.'" data-obj-type="page">'; // тулбар начало
		$out .= '<a href="#" class="publish-switch" title="Изменить статус"></a>';
		$out .= '<a href="#" class="obj-copy" title="Копировать"></a>';
		$out .= '<a href="#" class="obj-edit" title="Редактировать"></a>';
		
		$out .= '<a href="#" class="obj-delete" title="Удалить"></a>';
	$out .= '</div>';
	
	$out .= '</div> '.NR;
	
	return $out;
}

function ce_get_pages_info_full($page_id)
{
	$CI = & get_instance();
	$out = array();
	
	// данные из таблицы page
	$CI->db->where('page_id', $page_id);
	$query = $CI->db->get('page');
	if(!$query->num_rows || $query->num_rows > 1) return array();
	$table_page = $query->result_array();
	$out['table_page'] = $table_page[0];
	unset($query, $table_page);
	
	// данные из таблицы meta
	$CI->db->where('meta_id_obj', $page_id);
	$CI->db->where('meta_table', 'page');
	$query = $CI->db->get('meta');
	$out['table_meta'] = $query->result_array();
	unset($query);
	
	// данные из таблицы cat2obj
	$CI->db->where('page_id', $page_id);
	$query = $CI->db->get('cat2obj');
	$out['table_cat'] = $query->result_array();

	return $out;
}

function ce_set_pages_info_full($page_info)
{
	$CI = & get_instance();
	$out = array();
	$old_slug = $page_info['table_page']['page_slug'];
	$page_info['table_page']['page_slug'] = ce_create_new_slug_page($old_slug);
	$page_info['table_page']['page_title'] = $page_info['table_page']['page_title'] . ' КОПИЯ';
	$page_info['table_page']['page_date_publish'] = $page_info['table_page']['page_last_modified'] = date('Y-m-d H:i:s');
	$page_info['table_page']['page_status'] = 'draft';
	
	$out['page_title'] = $page_info['table_page']['page_title'];
	$out['page_slug'] = $page_info['table_page']['page_slug'];
	$out['page_status'] = $page_info['table_page']['page_status'];
	
	unset($page_info['table_page']['page_id']);
	
	$CI->db->insert('page', $page_info['table_page']);
	if(!$page_id = $CI->db->insert_id())
	{
		return false;
	}
	$out['page_id'] = $page_id;

	if($page_info['table_meta'])
	{
		foreach($page_info['table_meta'] as $val_meta)
		{
			unset($val_meta['meta_id']);
			$val_meta['meta_id_obj'] = $page_id;
			$CI->db->insert('meta', $val_meta);
		}
	}
	unset($val_meta);
	
	if($page_info['table_cat'])
	{
		foreach($page_info['table_cat'] as $val_cat)
		{
			unset($val_cat['cat2obj_id']);
			$val_cat['page_id'] = $page_id;
			$CI->db->insert('cat2obj', $val_cat);
		}
	}
	unset($val_cat);
	
	return $out;
}


function ce_get_category_info_full($category_id)
{
	$CI = & get_instance();
	$out = array();
	$CI->db->where('category_id', $category_id);
	$query = $CI->db->get('category');
	if(!$query->num_rows || $query->num_rows > 1) return array();
	$table_category = $query->result_array();
	$out['table_category'] = $table_category[0];
	unset($query, $table_category);
	
	$CI->db->where('meta_id_obj', $category_id);
	$CI->db->where('meta_table', 'category');
	$query = $CI->db->get('meta');
	$out['table_meta'] = $query->result_array();
	unset($query);
	$CI->db->where('category_id', $category_id);
	$query = $CI->db->get('cat2obj');
	$out['table_catobj'] = $query->result_array();

	return $out;
}

function ce_set_category_info_full($category_info)
{
	$CI = & get_instance();
	$out = array();
	$old_slug = $category_info['table_category']['category_slug'];
	$category_info['table_category']['category_slug'] = ce_create_new_slug_category($old_slug);
	$category_info['table_category']['category_name'] = $category_info['table_category']['category_name'] . ' КОПИЯ';
	
	
	$out['category_name'] = $category_info['table_category']['category_name'];
	$out['category_slug'] = $category_info['table_category']['category_slug'];
	
	unset($category_info['table_category']['category_id']);
	
	$CI->db->insert('category', $category_info['table_category']);
	if(!$category_id = $CI->db->insert_id())
	{
		return false;
	}
	$out['category_id'] = $category_id;
	if($category_info['table_meta'])
	{
		foreach($category_info['table_meta'] as $val_meta)
		{
			unset($val_meta['meta_id']);
			$val_meta['meta_id_obj'] = $category_id;
			$CI->db->insert('meta', $val_meta);
		}
	}
	unset($val_meta);
	
	if($category_info['table_catobj'])
	{
		foreach($category_info['table_catobj'] as $val_cat)
		{
			unset($val_cat['cat2obj_id']);
			$val_cat['category_id'] = $category_id;
			$CI->db->insert('cat2obj', $val_cat);
		}
	}
	unset($val_cat);
	
	return $out;
}

function ce_create_new_slug_page($old_slug)
{
	$new_slug = $old_slug . '-1';
	$CI = & get_instance();
	$CI->db->select('page_id');
	$CI->db->where('page_slug', $new_slug);
	$query = $CI->db->get('page');
	if($query->num_rows)
	{
		return ce_create_new_slug_page($new_slug);
	}
	else
	{
		return $new_slug;
	}
}

function ce_create_new_slug_category($old_slug)
{
	$new_slug = $old_slug . '-1';
	$CI = & get_instance();
	$CI->db->select('category_id');
	$CI->db->where('category_slug', $new_slug);
	$query = $CI->db->get('category');
	if($query->num_rows)
	{
		return ce_create_new_slug_category($new_slug);
	}
	else
	{
		return $new_slug;
	}
}

function ce_delete_cetegory($category_id = 0)
{
	$out = array('complite');
	if(!$category_id)
	{
		$out['info'] = 'Нет категории';
		$out['error'] = 'error';
		return $out;
	}
	
	$CI = & get_instance();
	$CI->db->select('category_id');
	$CI->db->where('category_id_parent', $category_id);
	$query = $CI->db->get('category');
	if($query->num_rows)
	{
		$out['info'] = 'Категория содержит дочерние категории. <br /> Сначала отвяжите их';
		$out['error'] = 'error';
		return $out;
	}
	$CI->db->where('category_id', $category_id);
	$CI->db->delete('cat2obj');
	
	$CI->db->where('category_id', $category_id);
	$CI->db->delete('category');
	
	mso_flush_cache();
	return $out;
}

function ce_delete_page($page_id = 0)
{

	$out = array('complite');
	if(!$page_id)
	{
		$out['info'] = 'Нет страницы';
		$out['error'] = 'error';
		return $out;
	}
	
	$CI = & get_instance();
	$CI->db->select('page_id');
	$CI->db->where('page_id_parent', $page_id);
	$query = $CI->db->get('page');
	if($query->num_rows)
	{
		$out['info'] = 'Страница содержит вложенные страницы. <br /> Сначала отвяжите их';
		$out['error'] = 'error';
		return $out;
	}
	$CI->db->where( array('page_id'=>$page_id) );
	$CI->db->delete('cat2obj');

	$CI->db->where( array ('meta_id_obj' => $page_id, 'meta_table' => 'page') );
	$CI->db->delete('meta');

	$CI->db->where( array('page_id'=>$page_id) );
	$CI->db->delete('page');
	
	$CI->db->where( array('comments_page_id'=>$page_id) );
	$CI->db->delete('comments');
	
	mso_flush_cache();
	return $out;
}

function ce_delete_pages_array($pages_id = array())
{
	$out = array(
		'info' => 'Страницы успешно удалены',
		'status' => 'complite'
		);
	
	if(!$pages_id)
	{
		$out['info'] = 'Нет страниц';
		$out['status'] = 'error';
		return $out;
	}
	
	$CI = & get_instance();
	$CI->db->select('page_id');
	$CI->db->where_in('page_id_parent', $pages_id);
	$query = $CI->db->get('page');
	if($query->num_rows)
	{
		$out['info'] = 'Страница содержит вложенные страницы. <br /> Сначала отвяжите их';
		$out['status'] = 'error';
		return $out;
	}
	$CI->db->where_in('page_id', $pages_id);
	$CI->db->delete('cat2obj');

	$CI->db->where('meta_table', 'page');
	$CI->db->where_in('meta_id_obj', $pages_id);
	$CI->db->delete('meta');

	$CI->db->where_in('page_id', $pages_id);
	$CI->db->delete('page');
	
	$CI->db->where_in('comments_page_id', $pages_id);
	$CI->db->delete('comments');
	
	mso_flush_cache();
	return $out;
}

function ce_delete_cats_array($cats_id = array())
{
	$out = array(
		'info' => 'Категории успешно удалены',
		'status' => 'complite'
		);
	if(!$cats_id)
	{
		$out['info'] = 'Нет категорий';
		$out['status'] = 'error';
		return $out;
	}
	
	$CI = & get_instance();
	$CI->db->select('category_id');
	$CI->db->where_in('category_id_parent', $cats_id);
	$query = $CI->db->get('category');
	if($query->num_rows)
	{
		$out['info'] = 'Категория содержит дочерние категории. <br /> Сначала отвяжите их';
		$out['status'] = 'error';
		return $out;
	}
	$CI->db->where_in('category_id', $cats_id);
	$CI->db->delete('cat2obj');

	$CI->db->where_in('category_id', $cats_id);
	$CI->db->delete('category');
	
	mso_flush_cache();
	return $out;
}


function ce_get_cnt_page_on_category($cat_id = 0)
{
	$CI = & get_instance();
	$CI->db->where('category_id', $cat_id);
	$query = $CI->db->get('cat2obj');
	return $query->num_rows;
}


function ce_del_category_on_pages($cat_id = 0, $pages_id = array())
{
	if(!$cat_id || !$pages_id) return false;
	$CI = & get_instance();
	$CI->db->where('category_id', $cat_id);
	$CI->db->where_in('page_id', $pages_id);
	$del = $CI->db->delete('cat2obj');
	return $del;
}


function ce_set_order_cats($cats_array = array(), $is_cat = 0, $parent_id = -1)
{
	if(!$cats_array) return false;
	$CI = & get_instance();
	$data = array();
	$upd = false;

	foreach($cats_array as $key => $val)
	{
		$data = array('category_menu_order' => $key + 1);
		if($parent_id != -1 and $val == $is_cat)
		{
			$data['category_id_parent'] = $parent_id;
		}
		$CI->db->where('category_id', $val);
		$upd = $CI->db->update('category', $data);
	}
	return $upd;
}

function ce_set_order_pages($pages_array = array(), $category_id = 0, $parent_id = 0)
{
	if(!$pages_array) return false;
	$CI = & get_instance();
	$data = array();
	$upd = false;
	foreach($pages_array as $key => $val)
	{
		if($category_id > 0)
		{
			$data = array('c2obj_order' => $key + 1);
			$CI->db->where('category_id', $category_id);
			$CI->db->where('page_id', $val);
			$upd = $CI->db->update('cat2obj', $data);
		}
		else
		{
			$data = array('page_menu_order' => $key + 1, 'page_id_parent' => $parent_id);
			$CI->db->where('page_id', $val);
			$upd = $CI->db->update('page', $data);
		}
		
	}
	return $upd;
}

function ce_delete_cats_page($page_id, $category_id = 0)
{
	$CI = & get_instance();
	$CI->db->where('page_id', $page_id);
	if($category_id)
	{
		$CI->db->where('category_id', $category_id);
	}
	$del = $CI->db->delete('cat2obj'); 
	return $del;
}

function ce_add_cats_page($page_id, $category_id)
{
	$CI = & get_instance();
	$data = array(
		'page_id' => $page_id,
		'category_id' => $category_id,
		'links_id' => '0',
		'c2obj_order' => '0',
		'c2obj_status' => 'publish',
		
		);
	return $CI->db->insert('cat2obj', $data); 
}

function ce_delete_parent_on_page($page_id)
{
	$CI = & get_instance();
	$CI->db->where('page_id', $page_id);
	$data = array(
		'page_id_parent' => '0',
		);
	return $CI->db->update('page', $data);
}

function ce_cat_array_mini($args = array())
{
	$CI = & get_instance();
	$CI->db->select('*');
	$query = $CI->db->get('category');
	$out = array();
	foreach($query->result_array() as $row)
	{
		$out[$row['category_id']] = $row;
	}
	return $out;
}

function ce_get_slug_id_category($par = '', $type = 'id', $i = 0)
{
	$cache_key = 'ce_id_slug_cat';
	if($cache_val = mso_get_cache($cache_key))
	{
		if($type == 'slug')
		{
			if(isset($cache_val[$par]))
			{
				return $cache_val[$par];
			}
		}
		elseif($type == 'id')
		{
			$result = array_search($par, $cache_val);
			if($result !== false)
			{
				return $result;
			}
		}
		else
		{
			return false;
		}
	}
	++$i;
	if($i > 1)
	{
		return false;
	}
	else
	{
		_ce_help_get_slug_id_category($cache_key);
		return ce_get_slug_id_category($par, $type, $i);
	}
}

function _ce_help_get_slug_id_category($cache_key)
{
	$data = array();
	$CI = & get_instance();
	$CI->db->select('category_id, category_slug');
	$CI->db->from('category');
	$CI->db->where('category_type', 'page');
	$query = $CI->db->get();
	foreach($query->result_array() as $row)
	{
		$data[$row['category_id']] = $row['category_slug'];
	}
	mso_add_cache($cache_key, $data);
}

function ce_get_page_category($page_id)
{
	$out = array();
	$CI = & get_instance();
	$CI->db->select('category_id');
	$CI->db->from('cat2obj');
	$CI->db->where( array('page_id' => $page_id, 'links_id'=> '0'));
	$query = $CI->db->get();
	if($query->num_rows)
	{
		foreach($query->result_array() as $row)
		{
			$out[] = $row['category_id'];
		}
	}
	return $out;
}

# ФУНКЦИЯ ПЕРЕДЕЛАНА
# получение всех рубрик в массиве - сразу всё с учетом вложенности
# используются рекурсивные функции с sql-запросами - РЕСУРСОЕМКАЯ!

#[15] => Array
#        (
#            [category_id] => 15
#            [category_id_parent] => 0
#            [category_type] => page
#            [category_name] => Тестовая рубрика
#            [category_desc] => 
#            [category_slug] => test
#            [category_menu_order] => 0
#            [pages_count] => 2
#            [childs] => Array
#                (
#                    [16] => Array
#                        (
#                            [category_id] => 16
#                            ...
#                          )
#                    [17] => Array
#                        (
#                            [category_id] => 17
#                            ...
#                          )

function ce_get_mso_cat_array($type = 'page', $parent_id = 0, $order = 'category_menu_order', $asc = 'asc', $child_order = 'category_menu_order', $child_asc = 'asc', $in = false, $ex = false, $in_child = false)
{
	// если неверный тип, то возвратим пустой массив
	if ( ($type != 'page') and ($type != 'links') ) return array();
	
	$parent_id = (int)$parent_id;
	
	$CI = & get_instance();
	
	$CI->db->select('category.*, COUNT(cat2obj_id) AS pages_count');
	
	$CI->db->where('category.category_type', $type);
	$CI->db->where('category.category_id_parent', $parent_id);
	
	$CI->db->join('cat2obj', 'category.category_id = cat2obj.category_id', 'left');
	
	// включить только указанные
	if ($in) $CI->db->where_in('category.category_id', $in);
	
	// исключить указанные
	if ($ex) $CI->db->where_not_in('category.category_id', $ex);
	
	if ($order == 'pages_count' ) $CI->db->order_by($order, $asc);
		else $CI->db->order_by('category.' . $order, $asc);
	
	$CI->db->group_by('category.category_id');
	
	if ($query = $CI->db->get('category')) $result = $query->result_array(); // здесь все рубрики
	else $result = array();
	
	$r = array();
	foreach ($result as $key=>$row)
	{
		$k = $row['category_id'];
		$r[$k] = $row;
		
		//$r[$k]['pages'] = array(); сами страницы нам не нужны
		
		
		if ($in_child != -1) // не включать потомков вообще
		{ 
			$ch = ce_get_get_child($type, $row['category_id'], $child_order, $child_asc, $in, $ex, $in_child);
			if ($ch) $r[$k]['childs'] = $ch;
		}
	}

	return $r;
}

# вспомогательная рекурсивная рубрика для получения всех потомков рубрики ce_get_mso_cat_array
function ce_get_get_child($type = 'page', $parent_id = 0, $order = 'category_menu_order', $asc = 'asc', $in = false, $ex = false, $in_child = false)
{
	$CI = & get_instance();
	$CI->db->select('category.*, COUNT(cat2obj_id) AS pages_count');
	$CI->db->where(array('category.category_type'=>$type, 'category.category_id_parent'=>$parent_id));
	$CI->db->join('cat2obj', 'category.category_id = cat2obj.category_id', 'left');
	
	// включить только указанные
	// если разрешено опцией для детей
	if ($in_child and $in) $CI->db->where_in('category.category_id', $in);
	
	// исключить указанные
	if ($ex) $CI->db->where_not_in('category.category_id', $ex);
	
	if ($order == 'pages_count' ) $CI->db->order_by($order, $asc);
		else $CI->db->order_by('category.' . $order, $asc);
	
	$CI->db->group_by('category.category_id');

	$query = $CI->db->get('category');
	$result = $query->result_array(); // здесь все рубрики
	
	if ($result) 
	{
		$r0 = array();
		foreach ($result as $key=>$row)
		{
			$k = $row['category_id'];
			$r0[$k] = $row;
			
			//$r0[$k]['pages'] = array();  сами страницы нам не нужны
			
			
		}
		
		$result = $r0;
		foreach ($result as $key=>$row)
		{
			$r = ce_get_get_child($type, $row['category_id'], $order, $asc, $in, $ex, $in_child);
			if ($r) $result[$key]['childs'] = $r;
		}
	}
	
	return $result;
}



# end file