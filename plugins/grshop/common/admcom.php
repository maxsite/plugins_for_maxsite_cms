<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
require_once ($MSO->config['plugins_dir'].'grshop/common/common.php');	// подгружаем библиотеку для админки

function many_check ($in = array('name'=>'act', 'crit'=>'cat', 'id_crit'=>'0'))
//----------------------------------------------------
//---------функция создает массив чекбоксов---
//----------------------------------------------------
	{
	$CI = & get_instance();
	$out = FALSE;
	if ($in['name']=='act' && $in['crit']=='cat')	
		{
		$select1 = 'id_cat, id_act';
		$table1 = 'grsh_catact';
		$crit = 'id_cat';
		$id = 'id_act';
		$name = 'name_act';
		$select = 'id_act, name_act';
		$table = 'grsh_act';
		};
	if ($in['name']=='cat' && $in['crit']=='act')	
		{
		$select1 = 'id_cat, id_act';
		$table1 = 'grsh_catact';		
		$crit = 'id_act';
		$id = 'id_cat';
		$name = 'name_cat';
		$select = 'id_cat, name_cat';
		$table = 'grsh_cat';
		};
	if ($in['name']=='cat' && $in['crit']=='prod')	
		{
		$select1 = 'id_cat, id_prod';
		$table1 = 'grsh_catprod';		
		$crit = 'id_prod';
		$id = 'id_cat';
		$name = 'name_cat';
		$select = 'id_cat, name_cat';
		$table = 'grsh_cat';
		};

	if ($in['id_crit']>0)		//----если указан критерий---------
		{
		$CI->db->select($select1);		//---нужные поля из БД-----
		$CI->db->where($crit, $in['id_crit']);	//---все связи редактируемой категории-----
		$query = $CI->db->get($table1); 				
		}
	if (isset($query))
		{
		foreach ($query->result_array() as $row)	//-- формируем массивы отмеченных чеком позиций----
			{
			$chact[]=$row[$id];
			};
		};
	$CI->db->select($select);
	$query = $CI->db->get($table); 
	$i=0;
	foreach ($query->result_array() as $row)
		{
		$allact[$i]['id']=$row[$id];
		$allact[$i]['name']=$row[$name];
		$allact[$i]['check']='';
		if (isset($chact) && in_array($allact[$i]['id'], $chact))	//--если текущая есть в массиве отмеченных
			{
			$allact[$i]['check']=' checked="checked" ';
			};
		//if ($in['id_crit'] == 0)	//-- это на всяк случай, что бы товар сразу отмечен во всех кат.
		//	{
		//	$allact[$i]['check']=' checked="checked" ';
		//	};
		$i++;
		};
	if (isset($allact)) $out = build_check ($allact, $in);
	return $out;
	};

function build_check ($in=array(), $inpar=array())	// вспомогательная ф-ция для many_check
	{
	$q = count($in);
	$out = '<ul class="category">';
	for ($i=0; $i<$q; $i++)
		{
		$out.='<li><label><input name="ch_'.$inpar['name'].'[]" type="checkbox" '.$in[$i]['check'].' value="'.$in[$i]['id'].'">'.$in[$i]['name'].'</label></li>';
		};
	$out.='</ul>'.NR;
	$out.=	form_hidden('q_'.$inpar['name'], $q).NR.
		form_hidden('name', $inpar['name']).NR.
		form_hidden('crit', $inpar['crit']).NR;
	return $out;	
	};

function reseiv_many_check ($post)
//-------ф-ция обрабатывает форму с чеками-----------
	{
	if (!isset($post['name'])) return FALSE;

	$CI = & get_instance();
	$crit = $post['crit'];			//--- что редактируем...
	$name = $post['name'];		//--- что выводилось в мени_чеке, категории или акции или еще что...
	$id = $post['id_'.$crit];
	$q=$post['q_'.$name];		//---количество чеков всего---------------

	if ($crit == 'prod')
		{
		$field_db_crit = 'id_prod';
		$field_db_name = 'id_cat';
		$name_tbl_db = 'grsh_catprod';
		};
	if ($crit == 'act')
		{
		$field_db_crit = 'id_act';
		$field_db_name = 'id_cat';
		$name_tbl_db = 'grsh_catact';
		};
	if ($crit == 'cat')
		{
		$field_db_crit = 'id_cat';
		$field_db_name = 'id_act';
		$name_tbl_db = 'grsh_catact';
		};

	$CI->db->where($field_db_crit, $id);	//----удаляем что было для этого критерия из БД----
	$CI->db->delete($name_tbl_db);
	if (isset($post['ch_'.$name])) 			//---если отмечены чеки-------------
		{
		$ch=$post['ch_'.$name];		//---массив с номерами отмеченных чеков----------------
		$q_ch=count($ch);		//---количество отмеченных чеков--------
		for ($i=0; $i<$q_ch; $i++)  //--цикл по количеству чеков на запись в бд---
			{	
			$CI->db->set($field_db_crit, $id);
			$CI->db->set($field_db_name, $ch[$i]);
			$CI->db->insert($name_tbl_db);	//---записали новые значения----
			};
		};
	return;
	};
?>