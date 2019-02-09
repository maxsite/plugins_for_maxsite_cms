<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

require_once(getinfo('plugins_dir').'category_editor/custom/ce_admin_function.php');

if($post = mso_check_post(array('action')))
{
	mso_checkreferer();
	
	
	if(isset($post['action']))
	{
		$category_id = $post['category_id']; 
		$page_id = $post['page_id']; 
		$categoryes_id = $post['categoryes_id'];
		$pages_id = $post['pages_id'];
		
		$custom_val_string = $post['custom_val_string']; 
		$custom_val_arr = $post['custom_val_arr'];
		
		$out['status'] = 'error';
		
		$CI = & get_instance();
		
		switch($post['action'])
		{

			case 'get_childs_items':
				
				$out = array();
	
				$pages = ce_get_pages_for_cat_list($category_id);
				if($pages)
				{
					$out['pages_list'] = ce_child_pages_list($pages, $category_id);
				}
				else
				{
					$out['pages_list'] = '<li class="is-page-li li-empty">Нет вложенных страниц</li>';
				}
				
				
				$out['info'] = '';
				$out['status'] = 'complite';
				echo json_encode($out);
				break;

			case 'change_status':
				if((!$page_id and !$category_id) || ($page_id and $category_id))
				{
					$out['info'] = 'Не указан объект';
					echo json_encode($out);
					return;
				}
	
				if($page_id)
				{
					if($new_status = ce_change_status_page($page_id))
					{
						$out['info'] = 'Статус страницы успешно установлен';
						$out['status'] = 'complite';
						$out['new_status'] = $new_status;
					}
					else
					{
						$out['info'] = 'Ошибка обновления статуса';
					}
				}

				if($category_id)
				{
					$out['info'] = 'Изменение статуса категории невозможно';
					$out['status'] = 'complite';
					$out['new_status'] = 'publish';
			
				}
				echo json_encode($out);
				break;
			case 'copy_obj':
				$out['item_li'] = '';
				if((!$page_id and !$category_id) || ($page_id and $category_id))
				{
					$out['info'] = 'Ошибка! Не указан объект';
					echo json_encode($out);
					return;
				}
				if($page_id)
				{
					$page_full = ce_get_pages_info_full($page_id);
					$res = ce_set_pages_info_full($page_full);
					
					if($res)
					{
						$out['info'] = 'Страница успешно скопирована';
						$out['status'] = 'complite';
						
						$out['item_li'] .= create_item_page_start($res, '');
						
						$out['item_li'] .= '<ul class="is-pages page-sort-connected">'.NR;
							$out['item_li'] .= '<li class="is-page-li li-empty">Нет вложенных страниц</li>';
						$out['item_li'] .= '</ul>'.NR;
						
						$out['item_li'] .= '</li>'.NR;
					}
					else
					{
						$out['info'] = 'Ошибка копирования страницы';
					}
					
				}
				if($category_id)
				{
					$category_full = ce_get_category_info_full($category_id);
					$res = ce_set_category_info_full($category_full);
					
					$cnt_pages = ce_get_cnt_page_on_category($res['category_id']);
					$out['status'] = 'complite';
					$out['info'] = 'Категория успешно скопирована';
					
					$out['item_li'] .= create_item_category_start($res['category_id'], $res['category_name'], $res['category_slug']);
					
					$out['item_li'] .= '<ul class="child is-category cat-sort-connected">'.NR;
						$out['item_li'] .= '<li class="is-category-li li-empty">Нет вложенных категорий</li>';
					$out['item_li'] .= '</ul>'.NR;
					
					$out['item_li'] .= '<ul class="is-pages page-sort-connected">'.NR;
						$out['item_li'] .= '<li class="is-page-li li-empty">';
							if($cnt_pages)
							{
								$out['item_li'] .= '<a class="go-other-pages" href="#" data-category-id="'.$res['category_id'].'">';
								$out['item_li'] .= 'Загрузить '.$cnt_pages.' страниц</a>';
							}
							else
							{
								$out['item_li'] .= 'Нет вложенных страниц';
							}
							
						$out['item_li'] .= '</li>';
					$out['item_li'] .= '</ul>'.NR;
					
					$out['item_li'] .= '</li>'.NR;
					
				}
				
				echo json_encode($out);
				break;
				
			case 'delete_obj':
				if((!$page_id and !$category_id) || ($page_id and $category_id))
				{
					$out['info'] = 'Ошибка! Не указан объект';
					echo json_encode($out);
					return;
				}
				if($page_id)
				{
					$res = ce_delete_page($page_id);
					if(isset($res['error']))
					{
						$out['status'] = 'error';
						$out['info'] = $res['info'];
					}
					else
					{
						$out['info'] = 'Страница успешно удалена';
						$out['status'] = 'complite';
					}
						
				}
				if($category_id)
				{
					$res = ce_delete_cetegory($category_id);
					if(isset($res['error']))
					{
						$out['status'] = 'error';
						$out['info'] = $res['info'];
					}
					else
					{
						$out['status'] = 'complite';
						$out['info'] = 'Категория успешно удалена';
					}
				}
				
				echo json_encode($out);
				break;

				
			case 'set_order_cats': 
				$parent_category = $custom_val_string; 
				$res = ce_set_order_cats($categoryes_id, $category_id, $parent_category);
				if($res)
				{
					$out['status'] = 'complite';
					$out['info'] = 'Категории успешно упорядочены';
				}
				else
				{
					$out['info'] = 'Ошибка обновления';
				}
				echo json_encode($out);
				break;
				
			case 'set_order_pages': 
				if($category_id == 0)
				{
					ce_delete_cats_page($page_id);
					$res = ce_set_order_pages($pages_id, 0, 0);
					$out['status'] = 'complite';
					$out['info'] = 'Страницы успешно упорядочены';
				}
				elseif($custom_val_string == -5)
				{
					if($categoryes_id['0'])
					{
						ce_delete_cats_page($page_id, $categoryes_id['0']);
					}
					$ins = ce_add_cats_page($page_id, $category_id);
					
					$res = ce_set_order_pages($pages_id, $category_id, 0);
					ce_delete_parent_on_page($page_id);
					$out['status'] = 'complite';
					$out['info'] = 'Страницы успешно упорядочены';
				}
				else
				{
					$res = ce_set_order_pages($pages_id, $category_id, $custom_val_string);
					if($categoryes_id['0'] and $custom_val_string > 0)
					{
						ce_delete_cats_page($page_id, $categoryes_id['0']);
					}
					$out['status'] = 'complite - '.$categoryes_id['0'];
					$out['info'] = 'Страницы успешно упорядочены';
				}
				
				echo json_encode($out);
				return;
				
				return $res;
				if($res)
				{
					$out['status'] = 'complite';
					$out['info'] = 'Страницы успешно упорядочены';
				}
				else
				{
					$out['info'] = 'Ошибка обновления';
				}
				echo json_encode($out);
				

			case 'change_categoryes': 
				$ins = false;
				if($custom_val_string and $custom_val_string == 'del_position')
				{
					foreach($pages_id as $pid)
					{
						$del_c = ce_delete_cats_page($pid);
					}
					unset($pid);
				}
				else
				{
					foreach($categoryes_id as $cid)
					{
						ce_del_category_on_pages($cid, $pages_id);
					}
					unset($cid);
				}
				foreach($pages_id as $pid)
				{
					foreach($categoryes_id as $cid)
					{
						$ins = ce_add_cats_page($pid, $cid);
					}
				}
				
				if($ins)
				{
					$out['status'] = 'complite';
					$out['info'] = 'Категории успешно присвоены';
				}
				else
				{
					$out['info'] = 'Ошибка присвоения категорий';
				}
				echo json_encode($out);
				
				break;
			
				
			default: return;
		}
	}
	else
	{
		return;
	}
}# end if post