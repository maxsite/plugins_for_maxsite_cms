<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */


function breads_check_root ($cat_id = 0) //функция для проверки, является ли эта ветка наследницей корневой с $cat_id
{
	$current_slug = mso_segment(2);
	
	if (!$current_slug || !is_type('category')) // показываем только в категориях
		return FALSE;

	$list = mso_cat_array_single();

	if (!isset($list[mso_get_cat_from_slug()]))
		return FALSE;
		
	$current = $list[mso_get_cat_from_slug()];
	$parents = $current['parents'];
	$parents_list = ($parents == 0) ? array() : explode (' ', $parents);

	$return = (in_array ($cat_id, $parents_list) || $current['category_id'] == $cat_id );
	return $return;
}

function breads_get_path () //функция возвращает массив, содержащий slug и name категорий родителей
{

	$current_slug = mso_segment(2);

	if (!$current_slug || !is_type('category') || is_type('404')) // показываем только в категориях
		return array();
		
	$list = mso_cat_array_single();
	
	if (!isset($list[mso_get_cat_from_slug()]))
		return array();

	$current = $list[mso_get_cat_from_slug()];
	$parents = $current['parents'];
	$parents_list = ($parents == 0) ? array() : explode (' ', $parents);
	$parents_list = array_reverse($parents_list);
	$return = array();
	foreach ($parents_list as $par_id)
	{
		$return[$list[$par_id]['category_slug']] = $list[$par_id]['category_name'];
	}
	
	return $return;
}


function breads_print ($array, $simple = FALSE) //функция выводит крошки, с ссылками или без, в зависимости от $simple
{
	if (count($array)==0)
		return '';

	// выводить простой текст без сыслок?
	if ($simple)
	{
		$return = implode(' > ', $array) . ' > ';
	}	
	else
	{
		$breads = array();
		foreach ($array AS $cat_slug => $cat_title)
		{
			$url = getinfo('siteurl') . $cat_slug;
			$breads[] = '<a href="' . $url . '" title="' . mso_strip($cat_title) . '">' . mso_strip($cat_title) . '</a>';
		}
		$return = implode(' > ', $breads) . ' > ';
	}
	
	return $return;
}
?>