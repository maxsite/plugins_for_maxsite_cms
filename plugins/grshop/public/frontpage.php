<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');
//$CI = & get_instance();	 // получаем доступ к CodeIgniter
require_once ($MSO->config['plugins_dir'].'grshop/common/common.php');	//подгружаем библиотеку c ф-циями вывода
require_once ($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные
$grsh_options = mso_get_option($grsh['main_key_options'], 'plugins', array()); // получение опций


//mso_redirect($grsh_options['main_slug'].'/cat/8');

//-- в работе дальше пока еще.... надо править стили....

/*	//--вывод списка акций... пока отключил....
$arract = get_act_prod();	// получаем массив с данными об акциях этого товара
if (is_array($arract))
	{
	$out .= '<div class="fpactbox">'.NR;
	$out .= '<h4>'.t('Действующие акции', 'plugins/grshop').'</h4>';
	foreach ($arract as $act)
		{
		$stlact = 'fpact';		//-- это для обозначения класса в CSS
		$stlnmact = 'fpnmact';	//-- это для обозначения класса в CSS
		if (isset($act['all_user_act']) && $act['all_user_act'] == '1') 
			{
			$stlact = 'fpalluact';		//-- это для обозначения класса в CSS
			$stlnmact = 'fpnmalluact';	//-- это для обозначения класса в CSS
			}

		$out .=  	'<div class="'.$stlact.'">
			<div class="'.$stlnmact.'">'.$act['name_act'].'</div><!-- class="'.$stlnmact.'" -->
			<div class="fpdescription">'.$act['description_act'].'</div><!-- class="fpdescription" -->
			<div class="discount">'.$act['discount_act'].' % </div><!-- class="discount" -->'.NR.'
			</div><!-- end class="'.$stlact.'" -->'.NR;
		}
	$out .= '</div><!-- end class="actbox" -->'.NR;	
	}

*/

/*
$par['id_cat'] = $grsh_options['id_fp_cat'];
$par['add'] = 1;
$par['res'] = 'full';
//$par['cache'] = 1;

if ($arr_prod = get_arr_prod($par)) 	// ф-ция из биб. комм. возвр. массив товаров категории
	{
	$i=1;
	foreach ($arr_prod as $prod)
		{
		if (isset($prod['photo_prod']) && $prod['photo_prod']!='') $out .= '<div class="pict">'.$prod['photo_prod'].'</div><!-- end class="pict" -->'.NR;
		$out .= '<div class="fpprodbox'.$i.'">'.NR;
		$nf_prod = array_keys($prod);
		foreach ($nf_prod as $k=>$nf)
			{
			if ($nf == 'name_cat')	{ }
			elseif ($nf == 'photo_prod')	{ }
			elseif ($nf == 'name_prod') 	{$out .= '<div class="nameprod"><h2>'.''.$prod[$nf].'</h2></div>'.NR;}
			elseif ($nf == 'cost_prod')	{
						$generalcost = $prod[$nf];
						$out .= '<h4>'.t('Цена', 'plugins/grshop').'</h4>'; 
						if ($prod[$nf] != '0') $out .= '<div class="cost">'.$prod[$nf].' '.$grsh_options['money'].'</div><!-- end class="cost" -->';
						else {$out .= t('уточните у менджера', 'plugins/grshop');};
						$out .= '<br>'.NR;
						}
			elseif ($nf == 'description_prod')	{ if ($prod[$nf] != '') $out .= '<div class="descr">'.t('Описание:', 'plugins/grshop').' '.$prod[$nf].'</div><!-- end class="descr" -->'.NR;}
			elseif ($nf == 'articul_prod')	{ if ($prod[$nf] != '') $out .= '<div class="par">'.t('Артикул:', 'plugins/grshop').' '.$prod[$nf].'</div><!-- end class="par" -->'.NR;}
			elseif ($nf == 'id_sklad_prod')	{ if ($prod[$nf] != '0') $out .= '<div class="par">'.t('Складской номер:', 'plugins/grshop').' '.$prod[$nf].'</div><!-- end class="par" -->'.NR;}
			else { if ($prod[$nf] != '') $out .= '<div class="par">'.get_title_field_db($nf).' : '.$prod[$nf].'</div><!-- end class="par" -->'.NR;}
			}
		$out .= '</div><!-- end class="fpprodbox'.$i.'" -->'.NR;
		$i++;
		};
	}
*/

$pagination['maxcount']=1;		//инициируем начальным значением
$pagination['$offset']=0;
$pagination['type']='';
$current_paged = mso_current_paged();  // текущая страница пагинации
$par['id_cat'] = $grsh_options['id_fp_cat'];	//параметры для получения массива продуктов для этой категории
$par['link'] = getinfo('siteurl').$grsh_options['main_slug'].'/prod/';	// параметр для формирования линка ф-ции каталинк
$par['child'] = 1;
$out1 = '';
//$out = '';
$name_cat = '';


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
				$name_cat = '<div class="hcat"><h2>'.$arr_prod_list[$key]['name_cat'].'</h2></div>';
				unset ($arr_prod_list[$key]['name_cat']);
				}
			if ($cp >= $pagination['$offset'] && $cp < ($pagination['$offset']+$pagination['limit']))
				{

				//$out.= '<br>Товар №: '.$key.'<br>';	// тестовый вывод;
				$out .= '<div class="oneprodlist">';
				if ($grsh_options['echo_photo_prod_list'] == '1' && $prod['photo_prod']!='') 
					$out .= '<div class="onepictlist">
						<a href="'.$par['link'].$key.'">
						<img class="minipict" src="'.get_pict($prod['photo_prod'], 'mini').'">
						</a>
						</div><!-- end class="onepictlist" -->'.NR;
				if ($grsh_options['echo_name_prod_list'] == '1') $out .= '<h3>'.$prod['name_prod'].'</h3>';
				if ($grsh_options['echo_articul_prod_list'] == '1') $out .= 'артикул:'.' '.$prod['articul_prod'].'<br>';
				if ($grsh_options['echo_cost_prod_list'] == '1') $out .= 'цена:'.' '.$prod['cost_prod'].'<br>';
				if ($grsh_options['echo_descr_prod_list'] == '1') $out .= 'описание:'.' '.$prod['description_prod'].'<br>';
				if ($grsh_options['echo_id_sklad_prod_list'] == '1') $out .= 'складской номер:'.$prod['id_sklad_prod'].'<br>';
				//$out .=' '.$cp.'	'.'<br>';	// тестовый вывод;
				$out .= '</div><!-- end class="oneprodlist" -->'.NR;				
				}
			$cp++;
			};
		}


//$out = '';
//$pagination = false;

$title = t('GrShop', 'plugins/grshop');
if (isset($grsh_options['main_title']) && $grsh_options['main_title'] != '') $title = get_title('','',$grsh_options['main_title']);
mso_head_meta('title', $title); 

?>