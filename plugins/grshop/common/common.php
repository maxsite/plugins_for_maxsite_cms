<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
mso_cur_dir_lang('admin');

function sizetable($datatable = array())
/* принимает массив, возвращает данные о его размере 
в виде массива с тремя значениями*/
	{
	$size['row']=FALSE;	
	$size['col']=FALSE;
	$size['full']=FALSE;
	if ($datatable != FALSE)
		{
		$size['row']=count($datatable);	
		if (isset($datatable[1])) {$size['col']=count($datatable[1]);}
		elseif (isset($datatable[0])) {$size['col']=count($datatable[0]);}
		else {$size['col']=1;};
		$size['full']=$size['row']*$size['col'];
		}
	return $size;
	};

function buildtable($datatable = array())
/* построение таблицы */
	{$table = FALSE;
	if (is_array($datatable))
		{$CI = & get_instance(); 
		$CI->load->library('table'); 
		$tmpl = array ('table_open'          => '<table class="grshtable rowstyle-alt" border="1" cellpadding="4" cellspacing="0">',
			'heading_row_start'   => '<tr>',
			'heading_row_end'     => '</tr>',
			'heading_cell_start'  => '<th class="sortable" >',
			'heading_cell_end'    => '</th>',	

			'row_start'           => '<tr>',
			'row_end'             => '</tr>',
			'cell_start'          => '<td>',
			'cell_end'            => '</td>',

			'row_alt_start'       => '<tr class="alt">',
			'row_alt_end'         => '</tr>',
			'cell_alt_start'      => '<td>',
			'cell_alt_end'        => '</td>',
			'table_close'         => '</table>'
			);
		$CI->table->set_template($tmpl); 
		$table=$CI->table->generate($datatable); 
		$CI->table->clear();
		}
	return $table;
	};

function	get_title_field_db($nmfdb)
/* принимает внутренние имена, возвращает человеческие названия */
	{
	global $MSO;
	global $nmfpdb;
	require_once ($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные
	$out = $nmfdb;
	if (isset($nmfpdb[$out])) $out = $nmfpdb[$out];
	return $out;
	}

function catalink ($in = array())
/* возвращает дерево категорий со сформированными ссылками 
не кешируется. Кэшируется в виджете, выводящем оглавление каталога*/
	{
	global $MSO;
	$out = '';

	//-----------параметры вывода по умолчанию---------------------------------------------
	if (!isset($in['admin'])) $in['admin'] = FALSE;
	if (!isset($in['link'])) $in['link']='/catalog/';
	if (!isset($in['check'])) $in['check']='';
	if (!isset($in['head'])) $in['head'] = t('КАТАЛОГ', 'admin');		//----должно задaваться в опциях настроек или в виджете-------
	if (!isset($in['headstart'])) $in['headstart']='<div class="w1"><h2 class="box"><span>';
	if (!isset($in['headend'])) $in['headend']='</span></h2>';
	if (!isset($in['ulstart'])) $in['ulstart']='<ul class="is_link">';
	if (!isset($in['ulend'])) $in['ulend']='</ul>';
	if (!isset($in['ulchildstart'])) $in['ulchildstart']='<ul class="child">';
	if (!isset($in['ulchildend'])) $in['ulchildend']='</ul>';
	if (!isset($in['listart'])) $in['listart']='<li>';
	if (!isset($in['liend'])) $in['liend']='</li>';
	if (!isset($in['listartcheck'])) $in['listartcheck']='<li class="selected">';
	if (!isset($in['liendcheck'])) $in['liendcheck']='</li>';
	if (!isset($in['lichildstart'])) $in['lichildstart']='<li>';
	if (!isset($in['lichildend'])) $in['lichildend']='</li>';
	if (!isset($in['lichildstartcheck'])) $in['lichildstartcheck']='<li class="selected">';
	if (!isset($in['lichildendcheck'])) $in['lichildendcheck']='</li>';
	if (!isset($in['err'])) $in['err']=t('категорий нет');
	//---------конец блока установки параметров--------------------------------

	if ($in['admin'] == TRUE)
		{
		$in['listartcheck']='<li class="admin-menu-selected">';	
		$in['lichildstartcheck']='<li class="admin-menu-selected">';
		}


	$CI = & get_instance();
	$CI->db->select('id_cat, id_parent_cat, name_cat, slug_cat, menu_order_cat');
	if ($in['admin'] != TRUE) $CI->db->where('public_status_cat', '1');
	$CI->db->order_by('id_parent_cat', 'asc');		//---------необходимость должна задваться в опциях наверно
	$CI->db->order_by('menu_order_cat', 'asc');	//---------необходимость должна задваться в опциях наверно
	$CI->db->order_by('name_cat', 'asc');		//---------необходимость должна задваться в опциях наверно
	$query = $CI->db->get('grsh_cat');

	$i = 0;
	foreach ($query->result_array() as $r)	//----------формирование массива с категориями --------------
		{
		$cat[$i]['i'] = $i;
		$cat[$i]['id_cat'] = $r['id_cat'];	
		$cat[$i]['id_parent_cat'] = $r['id_parent_cat'];
		$cat[$i]['name_cat'] = $r['name_cat'];
		$cat[$i]['slug_cat'] = $r['slug_cat'];
		$cat[$i]['menu_order_cat'] = $r['menu_order_cat'].'<br/>';
		$cat[$i]['start_id_child_cat'] = '';

		if (!isset($parent[$r['id_parent_cat']])) 
			{
			$parent[$r['id_parent_cat']] = $i;
			}

		$i++;
		};
	$par['i_start'] = 0;
	$par['$id_parent_cat'] = '0';
	
	if (isset($cat)) $out.= $in['headstart'].$in['head'].$in['headend'].catalogbuild ($in, $cat, $parent, $par);
	else {$out.= $in['headstart'].$in['head'].$in['headend'].$in['err'];};
	return $out;
	};

function catalogbuild ($in = array(), $cat = array(), $parent = array(), $par = array())
/**************** Рекурсивная функция выводящая многоуровневое дерево категорий ****************/
	{
	$out = '';
	global $level;
	if (!isset($level)) $level = -1; $level++;
	if ($level == 0) $out.= $in['ulstart']; else $out.= $in['ulchildstart'];
	$qi = count ($cat);
	for ($i = $par['i_start']; $i < $qi;  $i++)	//----------формирование массива с категориями --------------
		{
		if ($cat[$i]['id_parent_cat'] == $par['$id_parent_cat'])
			{

			$lnk = $cat[$i]['id_cat'];					//----- по умолчанию в ссылке ID категории
			if (isset($cat[$i]['slug_cat']) && $cat[$i]['slug_cat'] != '') $lnk = $cat[$i]['slug_cat'];	//--- если есть короткая ссылка, то она-

			$listart = $in['listart']; $liend = $in['liend'];						//---родитель невыделенный--
			if ($level == 0 && $in['check'] == $lnk) {$listart = $in['listartcheck']; $liend = $in['liendcheck'];};	//---родитель выделенный--
			if ($level != 0 && $in['check'] != $lnk) {$listart = $in['lichildstart']; $liend = $in['lichildend'];};	//---ребенок невыделенный---
			if ($level != 0 && $in['check'] == $lnk) {$listart = $in['lichildstartcheck']; $liend = $in['lichildendcheck'];};	//----ребенок выделенный--

			$out.= $listart;
			$out.='<a href="'.$in['link'].$lnk.'">'.$cat[$i]['name_cat'].'</a>';
			if (@$i_start = $parent[$cat[$i]['id_cat']])		//---если есть в массиве родителей, сразу и получим i первого
				{
				$chpar['i_start'] = $i_start;
				$chpar['$id_parent_cat'] = $cat[$i]['id_cat'];
				$out.=catalogbuild ($in, $cat, $parent, $chpar);	//---рекурсия!-----			
				};
			$out.= $liend;
			}
		}
	if ($level == 0) $out.= $in['ulend']; else $out.= $in['ulchildend'];	//----- какой уровень вложенности, так и выводим----
	$level--;		//--- восстанавливаем уровень вложенности входа----
	return $out;
	};

function get_array_db ($data_tip, $crit = '', $id_crit = '', $cache = FALSE)   
//---ф-ция возвращает массив данных из базы данных, что бы не обращаться в БД больше одного раза
//---при необходимости задается параметр и критерий по которому получаем данные
//--- кешируем всегда, но берем из кэша только если $cache задан явно.
	{

	$cache_key = $data_tip.$crit.$id_crit;	//-- ключ кэша в любом случае. Записываем в кэш в любом случае
	if ($cache != FALSE)		//-- возвращаем из кэша, только если нужно---
		{
		$k = mso_get_cache($cache_key);
		if ($k) return $k; // да есть в кэше ПОКА ОТКЛЮЧИЛИ!!!!!
		} 

	$CI = & get_instance();
	if  ($data_tip == 'prod')	$nametbldb = 'grsh_prod';
	if  ($data_tip == 'cat')	$nametbldb = 'grsh_cat';	
	if  ($data_tip == 'act')	$nametbldb = 'grsh_act';
	if  ($data_tip == 'ord')	
		{
		$nametbldb = 'grsh_ord';
		if ($crit != '' && $id_crit != '')
			{
			$CI->db->where($crit, $id_crit);			
			}
		}
	if  ($data_tip == 'add')	
		{
		$nametbldb = 'grsh_add';
		if ($crit = 'prod' && $id_crit != '') 
			{
			$nametbldb = 'grsh_prodadd';
			$CI->db->select('grsh_prodadd.id_add, name_add, val_prodadd');
			$CI->db->join('grsh_add', 'grsh_add.id_add = grsh_prodadd.id_add');
			$CI->db->where('id_prod', $id_crit);		
			}
		}
	$query = $CI->db->get($nametbldb);
	if ($query->num_rows() == 0) return $out = FALSE;	// если данных в таблице еще нет, то возвращаем фальс
	$fdb_ff = array_keys ($query->row_array());
	$size=sizetable($fdb_ff);
	$qfdb=$size['full'];
	$j = 0;
	foreach ($query->result_array() as $row)
		{
		for ($i=0; $i<$qfdb; $i++)
			{
			$out[$j][$fdb_ff[$i]] = $row[$fdb_ff[$i]]; 
			};
		$j++;
		}
	mso_add_cache($cache_key, $out); // сразу в кэш добавим
	return $out;
	};

function array_slug_id ($data_tip = 'cat', $in = array())
//-----ф-ция создает асс массив с ключами по слагам------------------
//-----на вход подаем тип данных и массив от ф-ции get_array_db----
	{
	$transin = FALSE;
	if  ($data_tip == 'cat')
		{
		$slug_field = 'slug_cat';
		$id_field = 'id_cat';	
		};
	foreach ($in as $row) 
		{
		if ($row[$slug_field] != '') $transin[$row[$slug_field]] = $row[$id_field];
		else {$transin[$row[$id_field]] = $row[$id_field];};
		}
	return $transin;
	};

function array_name_id ($data_tip = 'cat', $in = array())
//-----ф-ция создает асс массив с ключами по именам------------------
//-----на вход подаем тип данных и массив от ф-ции get_array_db----
	{
	$transin = FALSE;
	if  ($data_tip == 'cat')
		{
		$name_field = 'name_cat';
		$id_field = 'id_cat';	
		};
	if  ($data_tip == 'add')
		{
		$name_field = 'name_add';
		$id_field = 'id_add';	
		};
	if ($in !='')
		{
		foreach ($in as $row)
			{
			if ($row[$name_field] != '') $transin[$row[$name_field]] = $row[$id_field];
			else {$transin[$row[$id_field]] = $row[$id_field];};
			}
		};
	return $transin;
	};

function get_id_name ($name, $data_tip = 'add', $in = array())
//-----ф-ция возвращает ID по имени, если такое есть-------
//-----если такого нет, сохраняет в БД и возвращает ID-----
//-----на вход подаем тип данных и массив от ф-ции get_array_db---
	{
	$out = FALSE;
	if  ($data_tip == 'add')
		{
		$name_field = 'name_add';
		$id_field = 'id_add';
		$name_table_db = 'grsh_add';	
		};
	if  ($data_tip == 'prod')
		{
		$name_field = 'name_prod';
		$id_field = 'id_prod';
		$name_table_db = 'grsh_prod';	
		};
	$arr = array_name_id ($data_tip, $in);
	if (isset($arr[$name])) $out = $arr[$name];
	elseif ($name != '')
		{
		$CI = & get_instance();
		$CI->db->set($name_field, $name);
		$res=$CI->db->insert($name_table_db);
		if ($res!=0) $id = $CI->db->insert_id();  //---- теперь знаем id новой записи
		$in = get_array_db ($data_tip);	//---- если изменения, обновляем массив
		$out = $id;
		};
	return $out;
	};

function get_id_slug ($slug, $in = array(), $data_tip = 'cat')
//---------возвращает ID по слагу.. написано для категорий в основном-------
//---------на вход (слаг или ID), тип данных, массив от ф-ции get_array_db--
	{
	$out = FALSE;
	if  ($data_tip == 'cat')
		{
		$name_field = 'slug_cat';
		$id_field = 'id_cat';
		$name_table_db = 'grsh_cat';	
		};
	$arr = array_slug_id ($data_tip, $in);
	if (isset($arr[$slug])) $out = $arr[$slug];
	return $out;
	};

function get_cat_child ($in = array())
/* ф-ция возвращает массив дочерних категорий текущий категории */
	{
	global $MSO;
	$out = '';

	$out = 'тестовый вывод дочерних категорий текущей категории';

	return $out;
	};

function get_arr_prod ($in = array())
/* ф-ция возвращает массив данных о продуктах текущей категории, со сформированными линками,
или данные на один товар, если указать id_prod, и не указывать id_cat
должна принимать пагинацию*/
	{
	$cache = FALSE; if (isset($in['cache']))	$cache = $in['cache'];
	if ($cache != FALSE)		//-- кешируем работу ф-ции---
		{
		$cache_key = serialize($in);
		$k = mso_get_cache($cache_key);
		if ($k) return $k; // да есть в кэше
		}

	global $MSO;
	$CI = & get_instance();
	$out = FALSE;

	$id_prod = 0; if (isset($in['id_prod']))	$id_prod = $in['id_prod'];
	$id_cat = '0';	if (isset($in['id_cat']))	$id_cat = $in['id_cat'];
	$description = '0';	if (isset($in['description']))	$description = $in['description'];	
	$add = '0';	if (isset($in['add']))		$add = $in['add'];
	$link = '';		if (isset($in['link']))	$link = $in['link'];// параметр для формирования линка
	$res = FALSE;	if (isset($in['res']))		$res = $in['res'];

	if ($id_prod != 0)	$CI->db->where('grsh_prod.id_prod', $id_prod);

	if ($id_cat != '0')
		{
		//--- блок что бы товары дочерних категорий были включены ----
		$CI->db->where('id_cat', $id_cat);
		$CI->db->or_where('slug_cat', $id_cat);	
		$query = $CI->db->get('grsh_cat');
		$ccat = $query->row_array();
		$id_cat = $ccat['id_cat'];
		$namecat = $ccat['name_cat'];
		//--- тут условие что бы не отображалось название скрытых категорий---
		if ($ccat['public_status_cat'] == '0') $namecat = '';
		//--- конец условия---
		$CI->db->where('grsh_prod.public_status_prod', '1');
		$CI->db->where('grsh_catprod.id_cat', $id_cat);
		$CI->db->or_where('grsh_cat.id_parent_cat', $id_cat);
		}
	$CI->db->where('grsh_prod.public_status_prod', '1');
	if (isset($in['res']) && $in['res']!= FALSE) $CI->db->select('description_prod');
	$CI->db->select('name_prod, grsh_prod.id_prod, grsh_cat.name_cat, grsh_cat.public_status_cat, id_sklad_prod, articul_prod, cost_prod, quantity_prod, reserve_prod, photo_prod');
	$CI->db->join('grsh_prod', 'grsh_prod.id_prod = grsh_catprod.id_prod');
	$CI->db->join('grsh_cat', 'grsh_cat.id_cat = grsh_catprod.id_cat', 'right');
	$CI->db->order_by('name_prod', 'asc');
	$query = $CI->db->get('grsh_catprod');

	if ($query->num_rows() > 0)		//---если есть результат------------
		{
		foreach ($query->result_array() as $r)	//----------формирование массива с категориями --------------
			{
			//$out[$r['id_prod']]['name_cat'] = '';
			//$out[$r['id_prod']]['name_cat'] = $r['name_cat'];

			if ($r['public_status_cat'] == '1') $out[$r['id_prod']]['name_cat'] = $r['name_cat'];	//-если категория открыта для отображения--
			if ($id_cat != '0') $out[$r['id_prod']]['name_cat'] = $namecat;			//-если родительская категория--

			$out[$r['id_prod']]['name_prod'] = $r['name_prod'];
			if ($link != '') 
			$out[$r['id_prod']]['name_prod'] = '<a href="'.$link.$r['id_prod'].'">'.$r['name_prod'].'</a>';
			$out[$r['id_prod']]['articul_prod'] = $r['articul_prod'];
			$out[$r['id_prod']]['id_sklad_prod'] = $r['id_sklad_prod'];
			$out[$r['id_prod']]['cost_prod'] = $r['cost_prod'];
			if (isset($in['res']) && $in['res']!= FALSE) 
				{
				$out[$r['id_prod']]['description_prod'] = $r['description_prod'];
				$out[$r['id_prod']]['photo_prod'] = $r['photo_prod'];
				}
			if ($add != '0') $CI->db->or_where('grsh_prodadd.id_prod', $r['id_prod']);	//--для запроса хар-к товаров--
			};
		if ($add != '0') 
			{
			$CI->db->select('grsh_prodadd.id_prod, grsh_prodadd.id_add, name_add, val_prodadd, grsh_prod.name_prod');
			$CI->db->join('grsh_add', 'grsh_add.id_add = grsh_prodadd.id_add');
			$CI->db->join('grsh_prod', 'grsh_prod.id_prod = grsh_prodadd.id_prod');
			$CI->db->order_by('grsh_prod.name_prod', 'asc');	
			$query = $CI->db->get('grsh_prodadd');
			foreach ($query->result_array() as $r)	//----------формирование массива с характеристиками --------------
				{
				$out[$r['id_prod']][$r['name_add']] = $r['val_prodadd'];
				};
			}
		}
	if ($cache != FALSE) mso_add_cache($cache_key, $out); // сразу в кэш добавим
	return $out;
	};

function arr_2_buildtbl ($arr_prod_list = array(), $offset = 0, $limit = 20)
/* ф-ция преобразует массив, полученный из ф-ции get_array_db() для построения таблицы
ф-цией buildtable() */
	{
	$sdf = array_keys ($arr_prod_list);	// получаю список первых ключей
	$cftbl=0;				// инициализировали счетчик полей
	foreach ($sdf as $k=>$st)
		{
		$nfp = array_keys ($arr_prod_list[$st]);	//- получили название полей и характеристик -------
		$cnfp = count ($nfp);
		if ($cnfp > $cftbl)		//-условие на бОльшее кол-во полей--
			{
			$nf = $nfp;
			$cftbl = $cnfp;
			}
		}

	foreach ($nf as $n)		//- добавляем нулевую строку с названиями полей для вывода таблицы---			
		{
		$arthdkeyval[$n]=get_title_field_db($n);	//--ключ - имя поля, значение - заголовок для вывода--
		$artbl[0][] = $arthdkeyval[$n];		//--первая строка - заголовки для таблицы--
		}

	$cr = 1;
	$curlimit = $offset + $limit;
	foreach ($arr_prod_list as $k=>$apl)	//-преобразуем массив для таблицы---
		{
		$cc=0;
		foreach ($arthdkeyval as $kapl0=>$vapl0)
			{
			if ($cr > $offset && $cr <= $curlimit)
				{
				$artbl[$cr][$cc] = '';
				if (isset($arr_prod_list[$k][$kapl0])) $artbl[$cr][$cc] = $arr_prod_list[$k][$kapl0];
				$cc++;
				}
			}
		$cr++;
		}
	return $artbl;	
	}

function get_act_prod ($id_prod = '')
//--ф-ция возвращает данные об акцях, действующих на товар id_prod ---------
//--кэшировать нельзя, потому чтА зависит от даты времени----
	{
	$CI = & get_instance();
	$out = FALSE;
	$date_cur = strtotime(date('Y-m-d H:i:s'));

	if ($id_prod == '') //-- если не задан номер товара, выводим все акции
		{
		$CI->db->order_by('all_user_act', 'desc');
		$CI->db->order_by('end_data_act', 'asc');
		$query = $CI->db->get('grsh_act');
		if ($query->num_rows() > 0) 
			{
			foreach ($query->result_array() as $r)	//----------формирование массива с характеристиками --------------
				{
				if (strtotime($r['end_data_act']) >= $date_cur) 	// если время акции кончилось
					{
					$out[$r['id_act']]['name_act'] = $r['name_act'];
					$out[$r['id_act']]['discount_act'] = $r['discount_act'];
					$out[$r['id_act']]['description_act'] = $r['description_act'];
					$out[$r['id_act']]['public_status_act'] = $r['public_status_act'];
					$out[$r['id_act']]['other_discount_act'] = $r['other_discount_act'];
					$out[$r['id_act']]['start_data_act'] = $r['start_data_act'];
					$out[$r['id_act']]['end_data_act'] = $r['end_data_act'];
					$out[$r['id_act']]['all_user_act'] = $r['all_user_act'];
					}
				}
			return $out;
			}
		}

	$id_act = 0;	//-- текущее значение вспомогат. что бы не суммировались скидки много раз одного товара
	$onesale = 0;
	$maxdiscount = 0;

	$CI->db->where('grsh_catprod.id_prod', $id_prod);
	$CI->db->join('grsh_catact', 'grsh_catact.id_cat = grsh_catprod.id_cat');
	$CI->db->join('grsh_act', 'grsh_act.id_act = grsh_catact.id_act');
	$CI->db->join('grsh_prod', 'grsh_prod.id_prod = grsh_catprod.id_prod');
	$CI->db->order_by('grsh_act.all_user_act', 'desc');
	$CI->db->order_by('grsh_act.end_data_act', 'asc');
	$query = $CI->db->get('grsh_catprod');	
	if ($query->num_rows() > 0) 
		{
		foreach ($query->result_array() as $r)	//----------формирование массива с характеристиками --------------
			{
			if ($r['other_discount_act'] == '0' && $r['all_user_act'] == '1')
				{
				$out = FALSE;
				$curcft = 1 - (0.01 * $r['discount_act']);
				$maxdiscount = $r['discount_act'];
				$onesale = $r['id_act'];
				$startact = 1;		//-- признак записать акцию в вывод
				$endact = $r['id_act'];	//-- флаг окончания вывода--
				}
			elseif ($r['other_discount_act'] == '1' && $r['discount_act'] >= $maxdiscount && $onesale == 0 && $r['all_user_act'] == '1')
				{
				$out = FALSE;
				$maxdiscount = $r['discount_act'];
				$curcft = 1 - (0.01 * $maxdiscount);
				$startact = 1;		//-- признак записать акцию в вывод
				$endact = $r['id_act'];	//-- флаг окончания вывода--
				}
			elseif ($r['other_discount_act'] == '2' && $onesale == 0 && $r['all_user_act'] == '1')
				{
				if ($id_act != $r['id_act']) $maxdiscount = $maxdiscount + $r['discount_act'];
				$curcft = 1 - (0.01 * $maxdiscount);
				$startact = 1;	//-- признак записать акцию в вывод
				$endact = 1;	//-- флаг окончания вывода--
				}
			else
				{
				//-остались, если флаг наибольшей скидки, но скидка акции < максимальной
				$startact = 0;	//-- такую не записываем
				if ($r['all_user_act'] == '0') 	//--- а вот такую записываем---
					{
					$curcft = 1 - (0.01 * ($maxdiscount + $r['discount_act']));
					$startact = 1;	//-- признак записать акцию в вывод
					$endact = 1;	//-- флаг окончания вывода
					}
				}

			if (strtotime($r['end_data_act']) >= $date_cur && $startact == 1) 	// если время акции кончилось
				{
				$out[$r['id_act']]['name_act'] = $r['name_act'];
				$out[$r['id_act']]['discount_act'] = $r['discount_act'];
				$out[$r['id_act']]['description_act'] = $r['description_act'];
				$out[$r['id_act']]['public_status_act'] = $r['public_status_act'];
				$out[$r['id_act']]['other_discount_act'] = $r['other_discount_act'];
				$out[$r['id_act']]['start_data_act'] = $r['start_data_act'];
				$out[$r['id_act']]['end_data_act'] = $r['end_data_act'];
				$out[$r['id_act']]['all_user_act'] = $r['all_user_act'];
				$out[$r['id_act']]['kft'] = 1 - (0.01 * $r['discount_act']);
				$out[$r['id_act']]['curkft'] = $curcft;

				$startact = $endact;		//-- подн. флаг если был однодейств. акция
				$id_act = $r['id_act'];	//-- вспомогат. что бы не суммировались скидки много раз одного товара
				}
			}		
		}
	return $out;
	}

function addbasket($adp = array())
//--ф-ция добавляет данные о товаре в корзину,
//--если надо очистить корзину в поле id пишем "clear"
	{
	$out = FALSE;

	$CI = & get_instance();
	$arr_basket_prod = $CI->session->userdata('prod');	//получаем сохраненное значение

	if ($arr_basket_prod != FALSE)	$CI->session->unset_userdata('prod');	//удаляем текущее значение значение

	if (isset($adp['clear']) && $adp['clear'] != '')  return $out;	// если надо очистить корзину

	$id = $adp['id_prod'];
	$q = mso_strip($adp['q']);
	$cost = $adp['cost'];
	$cur_cost = $adp['cur_cost'];
	$name = $adp['name_prod'];
	if (isset($arr_basket_prod[$id])) 
		{
		$arr_basket_prod[$id]['qp']=$arr_basket_prod[$id]['qp']+$adp['q'];
		$arr_basket_prod[$id]['cost'] = $adp['cost'];
		$arr_basket_prod[$id]['cur_cost'] = $adp['cur_cost'];
		}
	else 
		{
		$arr_basket_prod[$id]['qp'] = $adp['q'];
		$arr_basket_prod[$id]['cost'] = $adp['cost'];
		$arr_basket_prod[$id]['cur_cost'] = $adp['cur_cost'];
		$arr_basket_prod[$id]['name'] = $adp['name_prod'];
		}
	$data_sess['prod'] = $arr_basket_prod;
	$CI->session->set_userdata($data_sess); 	// сохраняем новое значение...
	$out = TRUE;
	return $out;
	};

function get_order_number ($in, $d='')
//-- формирует уникальный номер заказа, для сообщения клиенту
//-- на вход подаем id заказа, возвращенное из БД после сохранения данных о заказе
//-- при $vec= ЛЮБОЕ ЗНАЧЕНИЕ возвращает данные по уникальному номеру это потом
	{
	$out = '';
	if ($d == '') $d = strtotime(date('Y-m-d H:i:s'));
	$out .= date('s', $d).$in.'-'.(date('d', $d)+$in).date('m', $d);
	return $out; 
	}

function save_order_2_db ($arrpb = array(), $post = array())
//-- сохраняет заявку в базу данных ---
	{
	$out = FALSE;
	$CI = & get_instance();
	$cur_data = date('Y-m-d H:i:s');
	$out['date'] = $cur_data;
	$data = array	(
			'start_data_order' => $cur_data,
			'email_order' => $post['email_order'],
			'telephon_order' => $post['telephon_order'],
			'adress_order' => $post['adress_order'],
			'person_order' => $post['person_order'],
			'description_order' => $post['description_order']
			);
	$res = $CI->db->insert('grsh_ord', $data); 
	if ($res != 0) 
		{
		$out['id_ord'] = $id_ord = $CI->db->insert_id();	// теперь знаем ID нового заказа
		foreach ($arrpb as $id_prod => $prod)
			{
			$CI->db->set('id_prod', $id_prod);
			$CI->db->set('id_ord', $id_ord);
			$CI->db->set('quantity_prodord', $prod['qp']);
			$CI->db->set('cur_cost', $prod['cur_cost']);
			$CI->db->insert('grsh_ordprod');	//---записали новые значения----
			}
		}
	return $out;
	}

function get_order($id_ord)
//---- возвращает все данные заказа в массиве двух массивов-----
//----$out['order'] - данные заказа, $out['prod'] - массив данных товаров заказа--
	{
	$out['order'] = FALSE;
	$out['prod'] = FALSE;
	$CI = & get_instance();
	$CI->db->where('grsh_ordprod.id_ord', $id_ord);
	$CI->db->join('grsh_ord', 'grsh_ord.id_ord = grsh_ordprod.id_ord');
	$CI->db->join('grsh_prod', 'grsh_prod.id_prod = grsh_ordprod.id_prod');
	$query = $CI->db->get('grsh_ordprod');
	if ($query->num_rows() > 0) 
		{
		foreach ($query->result_array() as $id_prod => $prodord)
			{
			$order['id_ord'] = $prodord['id_ord'];
			$order['num_order'] = get_order_number($prodord['id_ord'], strtotime($prodord['start_data_order']));
			$order['start_data_order'] = $prodord['start_data_order'];
			$order['status_order'] = $prodord['status_order'];
			$order['email_order'] = $prodord['email_order'];
			$order['status_pay_order'] = $prodord['status_pay_order'];
			$order['id_client_order'] = $prodord['id_client_order'];
			$order['telephon_order'] = $prodord['telephon_order'];
			$order['adress_order'] = $prodord['adress_order'];
			$order['person_order'] = $prodord['person_order'];
			$order['description_order'] = $prodord['description_order'];
			//---- далее тупо надо перечислять все нужные характеристики товара---
			$prod[$id_prod]['id_prod'] = $prodord['id_prod'];
			$prod[$id_prod]['cost_prod'] = $prodord['cost_prod'];
			$prod[$id_prod]['cur_cost'] = $prodord['cur_cost'];
			$prod[$id_prod]['quantity_prodord'] = $prodord['quantity_prodord'];
			$prod[$id_prod]['id_sklad_prod'] = $prodord['id_sklad_prod'];
			$prod[$id_prod]['articul_prod'] = $prodord['articul_prod'];
			$prod[$id_prod]['name_prod'] = $prodord['name_prod'];
			$prod[$id_prod]['quantity_prod'] = $prodord['quantity_prod'];
			$prod[$id_prod]['reserve_prod'] = $prodord['reserve_prod'];
			}
		}
	$out['order'] = $order;
	$out['prod'] = $prod;
	return $out;
	}

function get_pict($nm_pct='', $tip_pct='full')
//---- возвращает ссылку на полное или мини изображение-----
//----при этом, создавая миниатюру, если её еще нет------------
	{
	$CI = & get_instance();
	global $MSO;
	//require_once ($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные
	global $grsh;
	$puth=$MSO->config['uploads_dir'].$grsh['uploads_pict_dir'].'/';
	$puthmini=$puth.'mini/';
	if (!is_file($puthmini.$nm_pct))
		{
		$CI->load->library('image_lib');
		$size = 100;
		$r_conf = array	(
			'image_library' => 'gd2',
			'source_image' => $puth.$nm_pct,
			'new_image' => $puthmini.$nm_pct,
			'maintain_ratio' => true,
			'width' => $size,
			'height' => $size,
				);
		$CI->image_lib->initialize($r_conf );
		if (!$CI->image_lib->resize()) echo $CI->image_lib->display_errors();

		$r_conf['new_image'] = $puth.'_mso_i/'.$nm_pct;
		$CI->image_lib->initialize($r_conf );
		if (!$CI->image_lib->resize()) echo $CI->image_lib->display_errors();
		}
	$out = '/uploads/'.$grsh['uploads_pict_dir'].'/'.$nm_pct;
	if ($tip_pct!='full') $out = '/uploads/'.$grsh['uploads_pict_dir'].'/mini/'.$nm_pct;
	return $out;
	}

function get_title ($product = '', $category = '', $out = '')
//-- формирует значение для title с учетом настроек из опций
	{
	$d = ' » ';
	$dcat = $dprod = '';
	if ($category != '') $dcat = $d;
	if ($product != '') $dprod = $d;
	$out = str_replace('[category]', $category.$dcat, $out);	
	$out = str_replace('[product]', $product.$dprod, $out);
	return $out; 
	}
?>