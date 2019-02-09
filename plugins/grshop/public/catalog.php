<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$pagination['maxcount']=1;		//инициируем начальным значением
$pagination['$offset']=0;
$pagination['type']='';
$current_paged = mso_current_paged();  // текущая страница пагинации
$par['id_cat'] = mso_segment(3);	//параметры для получения массива продуктов для этой категории
$par['link'] = getinfo('siteurl').$grsh_options['main_slug'].'/prod/';	// параметр для формирования линка ф-ции каталинк
$par['child'] = 1;
$out1 = '';
$name_cat = '';

if ($grsh_options['tip_out_prod'] == 'list')	//- если листом выводим
	{
	$pagination['limit']=$grsh_options['pag_limit_prod_list'];		//количество извлекаемых данных на одну страницу из опций
	$par['res'] = 'full';	//параметры для получения массива продуктов для этой категории
	$par['cache'] = 1;
	if ($arr_prod_list = get_arr_prod($par)) 	// ф-ция из биб. комм. возвр. массив товаров категории
		{
		//ksort($arr_prod_list);
		$size=sizetable($arr_prod_list);
		$qprod=$size['row'];
	//-----переменные пагинации----------------------------
		if ($qprod > 0)
			{
			$pagination['maxcount'] = ceil($qprod / $pagination['limit']); // всего станиц пагинации
			if ($current_paged > $pagination['maxcount']) $current_paged = $pagination['maxcount'];
			$pagination['$offset'] = $current_paged * $pagination['limit'] - $pagination['limit'];
			}
	//--- порядок и оформление списка товаров тут----------------------
		$cp=0;		// счетчик выводимых позиций
		$limit = $pagination['$offset']+$pagination['limit'];
		foreach ($arr_prod_list as $key=>$prod)
			{
			if (isset($arr_prod_list[$key]['name_cat'])) 
				{
				$name_cat = $arr_prod_list[$key]['name_cat'];
				unset ($arr_prod_list[$key]['name_cat']);
				}
			if ($cp >= $pagination['$offset'] && $cp < ($pagination['$offset']+$pagination['limit']))
				{
				//$out.= '<br>Товар №: '.$key.'<br>';	// тестовый вывод;
				$out1 .= '<div class="oneprodlist">';
				if ($grsh_options['echo_photo_prod_list'] == '1' && $prod['photo_prod']!='') 
					$out1 .= '<div class="onepictlist">
						<a href="'.$par['link'].$key.'">
						<img class="minipict" src="'.get_pict($prod['photo_prod'], 'mini').'">
						</a>
						</div><!-- end class="onepictlist" -->'.NR;

				if ($grsh_options['echo_name_prod_list'] == '1') $out1 .= '<h3>'.$prod['name_prod'].'</h3>';
				if ($grsh_options['echo_articul_prod_list'] == '1') $out1 .= t('артикул: ', 'plugins/grshop').$prod['articul_prod'].'<br>';
				if ($grsh_options['echo_cost_prod_list'] == '1') $out1 .= t('цена: ', 'plugins/grshop').$prod['cost_prod'].'<br>';
				if ($grsh_options['echo_descr_prod_list'] == '1') $out1 .= t('описание: ', 'plugins/grshop').$prod['description_prod'].'<br>';
				if ($grsh_options['echo_id_sklad_prod_list'] == '1') $out1 .= t('складской номер:', 'plugins/grshop').$prod['id_sklad_prod'].'<br>';
				//$out .=' '.$cp.'	'.'<br>';	// тестовый вывод;
				$out1 .= '</div><!-- end class="oneprodlist" -->';				
				}
			$cp++;
			};
		}
	}
if ($grsh_options['tip_out_prod'] == 'table')	//- если таблицей выводим
	{
	$pagination['limit']=$grsh_options['pag_limit_prod_table'];		//количество извлекаемых данных на одну страницу из опций
	$par['res'] = 'full';	//параметры для получения массива продуктов для этой категории
	$par['add'] = $grsh_options['echo_add_prod_table'];	//параметры для получения массива продуктов для этой категории
	$par['cache'] = 1;	//-что бы кешировался результат-
	if ($arr_prod_list = get_arr_prod($par)) 	// ф-ция из биб. комм. возвр. массив товаров категории
		{
		$size=sizetable($arr_prod_list);
		$qprod=$size['row'];
		if ($qprod > 0)
			{
			$pagination['maxcount'] = ceil($qprod / $pagination['limit']); // всего станиц пагинации
			if ($current_paged > $pagination['maxcount']) $current_paged = $pagination['maxcount'];
			$pagination['$offset'] = $current_paged * $pagination['limit'] - $pagination['limit'];
			}
		//- порядок выводимых полей тут формируем -------
		foreach ($arr_prod_list as $k=>$apl)
			{
			if (isset($apl['name_cat'])) 
				{
				$name_cat = $arr_prod_list[$k]['name_cat'];
				unset ($arr_prod_list[$k]['name_cat']);
				}
			if ($grsh_options['echo_id_sklad_prod_table'] == '0') unset ($arr_prod_list[$k]['id_sklad_prod']);
			if ($grsh_options['echo_articul_prod_table'] == '0') unset ($arr_prod_list[$k]['articul_prod']);
			if ($grsh_options['echo_name_prod_table'] == '0') unset ($arr_prod_list[$k]['name_prod']);
			if ($grsh_options['echo_descr_prod_table'] == '0') unset ($arr_prod_list[$k]['description_prod']);
			if ($grsh_options['echo_cost_prod_table'] == '0') unset ($arr_prod_list[$k]['cost_prod']);
			unset ($arr_prod_list[$k]['photo_prod']);
			}	
		$artbl = arr_2_buildtbl($arr_prod_list, $pagination['$offset'], $pagination['limit']);	// преобразуем массив для вывода
		$out1 .= buildtable($artbl);	// строим таблицу
		}
	}
$out .= '<div class="hcat"><h2>'.$name_cat.'</h2></div>';
$out .= $out1.'<br>';
$title = $name_cat;
if (isset($grsh_options['main_title']) && $grsh_options['main_title'] != '') $title = get_title('',$name_cat,$grsh_options['main_title']);
mso_head_meta('title', $title); 
?>