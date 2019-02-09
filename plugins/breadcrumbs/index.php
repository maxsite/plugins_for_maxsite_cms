<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

//функция возвращает массив, содержащий slug и name либо false
function _breadcrumbs_get_path ($main) 
{
	if (is_type('home') || is_type('page_404')) // не показываем для home и 404
		return false;

	global $page;
	$out = array();
	$flag = false;
	if (is_type('page') && $page['page_slug']) {

//		if ($page['page_slug'])
		$out[$page['page_slug']] = $page['page_title'];
		
		// проверяем рубрики и родителей у страницы
		// для static приоритет - родители, для blog - рубрики
	
		$t = ($page['page_type_name'] == 'static') ? 1 : 0;
		$c = ($page['page_categories']) ? 1 : 0;
		$r = ($page['page_id_parent'] > 0) ? 1 : 0;
		
/* 		echo "кат: "; pr($c && (!$t || !$r));
		echo "пар: "; pr($r && ($t || !$c));
		echo "ост: "; pr(!$c && !$r); */
		
		if (!$c || ($r && $t)){ // обработка по парентам
			//pr($page);
			$par = $page['page_id_parent'];
			$pg = $page['page_id'];
			while ($par != 0) :
				$r = mso_page_map($pg, $par);
				if (!$r) break; // эта строчка нужна, т.к. при удалении род. страницы, page_id_parent у дочек не обнуляется в базе до обновления соответств. страниц
				$out[$r[$par]['page_slug']] = $r[$par]['page_title'];
				$pg  = $r[$par]['page_id'];
				$par = $r[$par]['page_id_parent'];
			endwhile;
		
		}else { // обработка по рубрикам

			$list = mso_get_cat_page($page['page_id']);
			if ($list) $flag = $list;
		
		}

	}
	if ($flag || (is_type('category'))){
		
		$list = mso_cat_array_single();

		if ($flag){
			$index = rand(1,count($flag))-1;
			$cur = $list[$flag[$index]];
		}else{
			$cat = mso_get_cat_from_slug();
			if(!$cat)return false;
			$cur = $list[$cat];
		}

		$cid = $cur['category_id'];

		do{
			$out['category/' . mso_get_cat_from_id($cid, 'category_slug')] = mso_get_cat_from_id($cid, 'category_name');
			$par = mso_get_cat_from_id($cid, 'category_id_parent');
			$cid = $par;
		}while ($par != 0);
		
	}
// можно создать раздел с опциями, где будут явно указываться тайтлы для неизв типов страниц
// можно для отдельных типов разбирать всю строку сегментов

// сейчас подставляется title либо mso_segment(1)
// нужны идеи	
	else
	if(!is_type('page'))
	{


//		pr($sg = mso_segment_array());
		$s = mso_segment(1);
		$t = mso_head_meta('title');
		
		$t = str_replace(getinfo('title'), '', $t);
		$t = str_replace('.', '', $t);
		$t = str_replace('—', '', $t);

		$title = $t ? $t : $s;
		$out[$s] = $title;
	}


	if (!$out) return false;
	$out[''] = $main;
	//pr($out);
	return array_reverse($out);
}

//функция выводит крошки, с ссылками или без, в зависимости от $simple
// $last - выводить последнюю крошку в виде ссылки
// $ch - разделитель
function breadcrumbs($do = '', $posle = '', $simple = false, $ch = ' &raquo; ', $last = false, $main = 'Главная') 
{
	$arr = _breadcrumbs_get_path($main);	
	if (!$arr) return '';

	// выводить простой текст без сcылок
	if ($simple){
		$out = ($arr) ? implode($ch, $arr) : '';
	}	
	else
	{
		$breads = array();
		$c = count($arr);
		foreach ($arr as $cat_slug => $cat_title)
		{
			if (--$c == 0 && !$last){
				$breads[] = htmlspecialchars($cat_title);
				continue;
			}
			$url = getinfo('siteurl') . $cat_slug;
			$breads[] = '<a href="' . $url . '" title="' . htmlspecialchars($cat_title) . '">' . htmlspecialchars($cat_title) . '</a>';
		}
		$out = implode($ch, $breads);
	}
	
	return $do . $out . $posle;
}
?>