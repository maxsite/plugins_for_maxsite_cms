<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

mso_cur_dir_lang('templates');
$CI = & get_instance();	 // получаем доступ к CodeIgniter
$CI->load->helper('form');	// подгружаем хелпер форм
require_once ($MSO->config['plugins_dir'].'grshop/common/common.php');	//подгружаем библиотеку c ф-циями вывода
require_once ($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные

$arract = get_act_prod(mso_segment(3));	// получаем массив с данными об акциях этого товара

$generalcost = 0;				// результирующая цена на товар
$onesale = 0;				// признак отмены скидок всех акций кроме одной
$minkft = 1;				// для отбора максимальной скидки (минимальный коэффициент)
$sumdiscount = 0;				// результирующая скидка по всем общедоступным акциям
$maxdiscount = 0;				// наибольшая скидка по всем общедоступным акциям
$actioncost = 0;				// инициируем цену текущей акции

$par['id_prod'] = mso_segment(3);
$par['add'] = 1;
$par['res'] = 'full';
$par['cache'] = 1;
$arr_prod = get_arr_prod($par);	// получаем данные о товаре

$out .= '<div class="prod">'.NR;
$out .= form_open(getinfo('siteurl').$grsh_options['main_slug'].'/prod/'.$par['id_prod']).mso_form_session('f_session_id');
foreach ($arr_prod as $prod)
	{
	if (isset($prod['name_cat']) && $prod['name_cat'] != '') $out .= '<div class="nmcat"><h3>'.$prod['name_cat'].'</h3></div><!-- end class="nmcat" -->'.NR;
	if (isset($prod['photo_prod'])	&& $prod['photo_prod'] != '') 
		{
		$out .= '
		<div class="prodpictbox">
		<a class="lightbox" href="/uploads/'.$grsh['uploads_pict_dir'].'/'.$prod['photo_prod'].'" target="_blank">
		<img class="prodpict" src="/uploads/'.$grsh['uploads_pict_dir'].'/'.$prod['photo_prod'].'"><!-- end class="prodpic" -->
		</a>
		</div><!-- end class="prodpictbox" -->
		'.NR;
		}
	$out .= '<div class="prodbox">'.NR;
	$nf_prod = array_keys($prod);
	foreach ($nf_prod as $k=>$nf)
		{
		if ($nf == 'name_cat')	{ }
		elseif ($nf == 'photo_prod')	{ }
		elseif ($nf == 'name_prod') 	{
					$nameprod = $prod[$nf]; 
					$out .= '<div class="nameprod"><h2>'.''.$prod[$nf].'</h2></div>'.NR;
					}
		elseif ($nf == 'cost_prod')	{
					$generalcost = $prod[$nf];
					$out .= '<h4>Цена.</h4>'; 
					if ($prod[$nf] != '0') $out .= '<div class="cost">'.$prod[$nf].' '.$grsh_options['money'].'</div><!-- end class="cost" -->';
					else {$out .= t('уточните у менджера', 'plugins/grshop');};
					$out .= '<br>'.NR;
					}
		elseif ($nf == 'description_prod')	{ if ($prod[$nf] != '') $out .= '<div class="descr">'.t('Описание:', 'plugins/grshop').' '.$prod[$nf].'</div><!-- end class="descr" -->'.NR;}
		elseif ($nf == 'articul_prod')	{ if ($prod[$nf] != '') $out .= '<div class="par">'.t('Артикул:', 'plugins/grshop').' '.$prod[$nf].'</div><!-- end class="par" -->'.NR;}
		elseif ($nf == 'id_sklad_prod')	{ if ($prod[$nf] != '0') $out .= '<div class="par">'.t('Складской номер:', 'plugins/grshop').' '.$prod[$nf].'</div><!-- end class="par" -->'.NR;}
		else { if ($prod[$nf] != '') $out .= '<div class="par">'.get_title_field_db($nf).' : '.$prod[$nf].'</div><!-- end class="par" -->'.NR;}
		}
	$out .= '</div><!-- end class="prodbox" -->'.NR;	
	}

if (is_array($arract))
	{
	$out .= '<div class="actbox">'.NR;
	$out .= '<h4>'.t('Действующие акции', 'plugins/grshop').'</h4>';
	foreach ($arract as $act)
		{
		$stlact = 'act';		//-- это для обозначения класса в CSS
		$stlnmact = 'nmact';	//-- это для обозначения класса в CSS
		$actioncost = $prod['cost_prod']*$act['curkft'];
		$tutcost = $prod['cost_prod'] * $act['curkft'];
		if (isset($act['all_user_act']) && $act['all_user_act'] == '1') 
			{
			$stlact = 'alluact';		//-- это для обозначения класса в CSS
			$stlnmact = 'nmalluact';	//-- это для обозначения класса в CSS
			$generalcost = $actioncost;	//-- цена для добавления в корзинку
			}

		$out .=  	'<div class="'.$stlact.'">
			<div class="'.$stlnmact.'">'.$act['name_act'].'</div><!-- class="'.$stlnmact.'" -->
			<div class="discount">'.$act['discount_act'].' % </div><!-- class="discount" -->'.NR.'
			<div class="actcost">'.t('цена c учетом акции:', 'plugins/grshop').' '.$actioncost.' '.$grsh_options['money'].'</div><!-- class="actcost" -->'.NR.'
			</div><!-- end class="'.$stlact.'" -->'.NR;
		}
	$out .= '</div><!-- end class="actbox" -->'.NR;	
	}
//$available = $row['quantity_prod'] - $row['reserve_prod'];
//$out['available'] = '0'; if ($available > 0) $out['available'] = $available;
//$out .= '<a href="'.getinfo('siteurl').$grsh_options['main_slug'].'/addbasket/'.mso_segment(3).'">'.t('в корзину', 'plugins/grshop').'</a><br>';



if ($grsh_options['mode'] == 'shop') 
	{
	$out .= form_hidden('cost', $prod['cost_prod']).NR;
	$out .= form_hidden('cur_cost', $generalcost).NR;
	$out .= form_hidden('id_prod', mso_segment(3)).NR;
	$out .= form_hidden('name_prod', $prod['name_prod']).NR;

	$out .= '<div class="addbsk">';
	$out .= form_submit('addbasket', t('добавить в корзину', 'plugins/grshop') );
	$out .= ' '.t('в количестве').' '.form_input($data = array('name'=>'q', 'value'=>'1', 'size'=>'2', 'maxlength'   => '5'));
	$out .= '</div><!-- class="addbsk" -->';	
	}

$out .= form_close().NR;
$out .= '</div><!-- end class="prod" -->';


$title = $nameprod.' » '.$prod['name_cat'];
if (isset($grsh_options['main_title']) && $grsh_options['main_title'] != '') $title = get_title($nameprod,$prod['name_cat'],$grsh_options['main_title']);
mso_head_meta('title', $title);

//--- тут вывод не нужен, потому что вывод записываем в переменную $out
//--- которая выводится в файле catalog.php, откуда вызываются эти странички
?>