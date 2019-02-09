<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

$CI = & get_instance();
if(!$CI->db->field_exists('c2obj_order', 'cat2obj'))
{
	echo '<div class="error">Для нормальной работы плагина следует внести изменение в таблицы. <a href="/admin/category_editor/_install">Перейти к установке</a> </div>';
	
	return false;
}


function child_category($childs=array(), $class_ul = 'cat-list') {
	$cout = '<ul class="'.$class_ul.' is-category cat-sort-connected">';
	foreach($childs as $key=>$value) {
		if(isset($value['childs'])) {
			$cout .= create_item_category_start($value['category_id'], $value['category_name'], $value['category_slug']);
			
			$cout .= child_category($value['childs'], 'child');
			$cout .= '<ul class="is-pages page-sort-connected">'.NR;
			if($value['pages_count'])
			{
				$cout .= '<li class="is-page-li li-empty li-go-other">';
				$cout .= '<a class="go-other-pages" href="#" data-category-id="'.$value['category_id'].'">';
				$cout .= 'Загрузить '.$value['pages_count'].' страниц</a>';
				$cout .= '</li>';	
			}
			else
			{
				$cout .= '<li class="is-page-li li-empty">Нет вложенных страниц</li>';
			}
			
			$cout .= '</ul>'.NR;
			
		}else{
			
			$cout .= create_item_category_start($value['category_id'], $value['category_name'], $value['category_slug']);
			
			
			$cout .= '<ul class="child is-category cat-sort-connected">'.NR;
				$cout .= '<li class="is-category-li li-empty">Нет вложенных категорий</li>';
			$cout .= '</ul>'.NR;
			$cout .= '<ul class="is-pages page-sort-connected">'.NR;
			if($value['pages_count'])
			{
				$cout .= '<li class="is-page-li li-empty li-go-other">';
				$cout .= '<a class="go-other-pages" href="#" data-category-id="'.$value['category_id'].'">';
				$cout .= 'Загрузить '.$value['pages_count'].' страниц</a>';
				$cout .= '</li>';
				
			}
			else
			{
				$cout .= '<li class="is-page-li li-empty">Нет вложенных страниц</li>';
			}
			$cout .= '</ul>'.NR;
		}
		$cout .= '</li>'.NR;
	}
	$cout .= '</ul>'.NR;
	return $cout;
}


echo '<div class="ce-top-panel"> ';
echo '<a href="#" id="ce_panel_closeall">Свернуть все</a> ';
echo '</div>';

$cats_array = ce_get_mso_cat_array('page', 0, 'category_menu_order', 'asc', 'category_menu_order', 'asc', false, false, false);

$list = child_category($cats_array);

$list = rtrim($list);
$list = rtrim($list, '</ul>');

$list .= '<li class="is-category-li" data-category-id="0" data-cat-slug="0">';
$list .= '<div class="line" ';
$list .= 'data-obj-id = "0" ';
$list .= 'data-obj-type= "0" >';

$list .= '<span class="e-close sp-icon" title="Открыть вложеные объекты" id="ecl_c0">+</span>';

$list .= '<a href="#" title="Редактировать категорию" onClick="return false">';
$list .= 'Без категории';
$list .= '</a>';

$list .= '<div class="ce-toolbar" data-id-obj="0" data-obj-type="category">';
	$list .= '<a href="#" class="obj-chpgs-update" title="Обновить список страниц"></a>';
	$list .= '</div>';

$list .= '</div> '.NR;

$list .= '<ul class="is-pages page-sort-connected">'.NR;
	$list .= '<li class="is-page-li li-empty">';
	$list .= '<a class="go-other-pages" href="#" data-category-id="0">';
	$list .= 'Загрузить страницы</a>';
	$list .= '</li>';
$list .= '</ul>'.NR;

$list .= '</li>'.NR;
$list .= '</ul>'.NR;

echo '<div class="list-container">';
echo $list;

echo '</div>';

# and file