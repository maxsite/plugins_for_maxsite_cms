<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	mso_cur_dir_lang('admin');

	if ( !mso_check_allow('grshop_edit') ) 
	{
	 echo 'Доступ запрещен';
	 return;
	}

	$CI = & get_instance();	 // получаем доступ к CodeIgniter
	$CI->load->helper('form');	// подгружаем хелпер форм
	require_once ($MSO->config['plugins_dir'].'grshop/common/admcom.php');	// подгружаем библиотеку для админки
	require_once ($MSO->config['plugins_dir'].'grshop/common/common.php');	// подгружаем библиотеку
	require_once ($MSO->config['plugins_dir'].'grshop/config.php');	// подгружаем переменные
	$out='';

// блок редактирования товаров (продуктов)
if ($post = mso_check_post(array('f_session_id', 'toadd')) or mso_segment(4)=='edit' or $post = mso_check_post(array('f_session_id', 'delladd')))
	{
	mso_checkreferer();

	if (isset($post['delladd']))	//-- если удаляем добавочную характеристику---
		{
		$delladd = $post['delladd'];
		$CI = & get_instance();
		$tables = array('grsh_add', 'grsh_prodadd');
		$CI->db->where('id_add', key($delladd));
		$CI->db->delete($tables);
		}

	$id_prod = '0';	if (isset($post['id_prod'])) {$id_prod = $post['id_prod'];}
	$id_sklad_prod = '0';
	$articul_prod = '0';
	$name_prod = '';
	$cost_prod = '0';
	$public_status_prod = '';
	$photo_prod = '';
	$description_prod = '';
	$quantity_prod = '0';
	$reserve_prod = '0';
	$shapka=t('Новый товар', 'admin');
	$qadd = 0;
	$id_new_add = 0; 	if (isset($post['id_new_add'])) {$id_new_add = $post['id_new_add'];}


	if (mso_segment(5))	{$id_prod = mso_segment(5);}

	if ($id_prod != '0')
		{
		$CI->load->database(); 	
		$CI->db->where('id_prod', $id_prod);
		$query = $CI->db->get('grsh_prod');
			foreach ($query->result_array() as $row)
			{
			$id_sklad_prod = $row['id_sklad_prod'];
			$articul_prod = $row['articul_prod'];
			$name_prod = $row['name_prod'];
			$cost_prod = $row['cost_prod'];
			$public_status_prod = $row['public_status_prod'];
			$description_prod = $row['description_prod'];
			$quantity_prod = $row['quantity_prod'];
			$reserve_prod = $row['reserve_prod'];
			$photo_prod = $row['photo_prod'];
			}

		if ($id_new_add != 0 || $post['name_new_add'] != '')
			{
			$arradd =  get_array_db ('add');	// массив со всеми характеристиками
			if ($post['name_new_add'] != '') $id_new_add = get_id_name ($post['name_new_add'], 'add', $arradd);
				$CI = & get_instance();
				$CI->db->set('id_add', $id_new_add);
				$CI->db->set('id_prod', $id_prod);
				$CI->db->set('val_prodadd', $post['val_new_add']);
				$CI->db->insert('grsh_prodadd');
			}
		$arraddprodid = get_array_db ('add', 'prod', $id_prod);	// массив с характеристиками товара ID
		if ($arraddprodid != FALSE) 
			{
			$size=sizetable($arraddprodid);	// размер этого массива
			$qadd = $size['row'];	// количество характеристик товара ID
			}
		$shapka = t('Редактирование товарa ID: ', 'admin').$id_prod;
		}

	$arralladd = get_array_db ('add');		// получаем массив с ID и самими add-ами;
	if ($arralladd != FALSE) 
		{
		$arraddf[0] = t('характ.');
		foreach ($arralladd as $row) {$arraddf[$row['id_add']]=$row['name_add'];};	// преобразовали в массив для выпадающего списка формы.
		};


	$checket = FALSE;  if ($public_status_prod == 1) {$checket = TRUE;};		// флаг разрешающий отображать товар
	$photoout='';
	if ($photo_prod!='') 
		$photoout='<a class="lightbox" href="/uploads/'.$grsh['uploads_pict_dir'].'/'.$photo_prod.'" target="_blank">
		<img style="margin: 5px auto; max-width: 300px; display: block;" src="'.get_pict($photo_prod, 'full').'">
		</a>';
	
	$out.=
	'<h1 class="content">'.$shapka.'</h1><br />'.
	form_open($plugin_url.'/product').
	mso_form_session('f_session_id').
	form_hidden('id_prod', $id_prod).NR.
	'<table style="width: 99%; border: none; line-height: 1.4em;"><tr><td style="vertical-align: top; padding: 0 0px 0 0;">

		<table style="width: 99%; border: none; line-height: 1.4em;">
		<tr>
		<td style="vertical-align: top; padding: 0 0px 0 0;">
			'.$photoout.'
		</td>
		<td style="vertical-align: top; padding: 0 0px 0 0;">
			<div class="block_page">
			<h3>'.t('Название файла изображения', 'admin').'</h3>'.form_input('photo_prod', $photo_prod).NR.'
			</div>
		</td>
		<tr>
		<td style="vertical-align: top; padding: 0 0px 0 0;">
			<div class="block_page"><h3>'.t('Складской номер', 'admin').'</h3>'.form_input('id_sklad_prod', $id_sklad_prod).NR.'
			</div>
		</td>
		<td style="vertical-align: top; padding: 0 0px 0 0;">
			<div class="block_page"><h3>'.t('Артикул', 'admin').'</h3>'.form_input('articul_prod', $articul_prod).NR.'
			</div>
		</td>
		</tr>
		<tr>
		<td style="vertical-align: top; padding: 0 0px 0 0;">
			<div class="block_page"><h3>'.t('Название товара', 'admin').'</h3>'.form_input('name_prod', $name_prod).NR.'
			</div>
		</td>
		<td style="vertical-align: top; padding: 0 0px 0 0;">
			<div class="block_page"><h3>'.t('Базовая цена', 'admin').'</h3>'.form_input('cost_prod', $cost_prod).NR.'
			</div>
		</td>
		</tr>
		<tr>
		<td style="vertical-align: top; padding: 0 0px 0 0;">
			<div class="block_page"><h3>'.t('Количество', 'admin').'</h3>'.form_input('quantity_prod', $quantity_prod).NR.'
			</div>
		</td>
		<td style="vertical-align: top; padding: 0 0px 0 0;">
			<div class="block_page"><h3>'.t('Резерв', 'admin').'</h3>'.form_input('reserve_prod', $reserve_prod).NR.'
			</div>
		</td>
		</tr>
		</table>


	<div class="block_page"><h3>'.t('Описание', 'admin').'</h3>'.form_textarea($data = array('name'=>'description_prod', 'value'=>$description_prod,'rows'=>'5')).NR.'</div>

		<table style="width: 99%; border: none; line-height: 1.4em;">';

	if (isset($arraddprodid) && $arraddprodid != FALSE)
		{
		foreach ($arraddprodid as $row)
			{
			$out .=  '<tr><td>'.$row['name_add'].NR.'</td><td>'.NR;
			$out .= form_input('arr_id_add['.$row['id_add'].']', $row['val_prodadd']).NR.form_submit('delladd['.$row['id_add'].']', t('удалить', 'admin') ).'</td></tr>'.NR;
			};		
		}
	
	$out .=  '<tr><td>';
	if (isset($arraddf)) $out .= form_dropdown('id_new_add', $arraddf, '0').' или '.NR;
	else 	{$out .= t('Новая характеристика: ').NR;}
	$out .= form_input('name_new_add', '').'</td><td>'.
	form_input('val_new_add', '').NR.form_submit('toadd', t('добавить характеристику', 'admin') ).'</td></tr>
	</table>'.
	form_submit('addprod', t('Сохранить', 'admin') ).
	'</td><td style="vertical-align: top; width: 250px;">
	<div class="block_page"><h3>'.t('Отображать', 'admin').'</h3>'.form_checkbox('public_status_prod', '1', $checket).NR.'</div>
	<p class="info">'.
	t('Отметьте категории, включающие товар', 'admin').'
	<div class="block_page"><h3>'.t('Категории', 'admin').'</h3>'.
	many_check($data = array('name'=>'cat', 'crit'=>'prod', 'id_crit'=>$id_prod)).NR.'</div>'.
	'</td></tr></table>'.
	form_hidden('qadd', $qadd).NR.
	form_hidden('id_prod', $id_prod).NR.
	form_close();
	echo $out;
	return;	
	}

// сохранение данных одного товара после редактирования или добавления
if ($post = mso_check_post(array('f_session_id', 'addprod')))
	{
	mso_checkreferer();

	$public_status_prod='0';
	if (isset ($post['public_status_prod'])) {$public_status_prod='1';};
	$id_prod=$post['id_prod'];
	$newprod = array(
			'id_sklad_prod' => $post['id_sklad_prod'],
			'articul_prod' => $post['articul_prod'],
			'name_prod' => $post['name_prod'],
			'cost_prod' => $post['cost_prod'],
			'public_status_prod' => $public_status_prod,
			'description_prod' => $post['description_prod'],
			'quantity_prod' => $post['quantity_prod'],
			'reserve_prod' => $post['reserve_prod'],
			'photo_prod' => $post['photo_prod']
		            );
	if ($id_prod==0) 		//----если новый товар-------
			{
			$res=$CI->db->insert('grsh_prod', $newprod);
			if ($res != 0) 
				{
				$id_prod = $CI->db->insert_id();  //---- теперь знаем id нового товара
				};
			}
	else		{
			$CI->db->where('id_prod', $id_prod);
			$res=$CI->db->update('grsh_prod', $newprod );
			};

	if ($res != 0) 
		{
		echo '<div class="update">'.t('изменения сохранены', 'admin').'</div>';
		};
	//---обработка форм характеристик товара ------
	if (isset($post['arr_id_add']))
		{
		$arr_prodadd = $post['arr_id_add'];
		$CI->db->where('id_prod', $id_prod);
		$res=$CI->db->delete('grsh_prodadd');
		foreach ($arr_prodadd as $id => $v)
			{
			if ($v != '')
				{
				$new_add['id_add'] = $id;
				$new_add['val_prodadd'] = $v;
				$new_add['id_prod'] = $id_prod;
				$CI->db->insert('grsh_prodadd', $new_add);
				}
			}
		}

	//---тут обработака чеков---- 
	reseiv_many_check ($post);
	mso_redirect('admin/grshop/product/edit/'.$id_prod);	//--редирект на стр. отредактируемого товара
	};


// удаление выбранного товара
if (mso_segment(4)=='del')
	{
	if ($id_prod=mso_segment(5))
		{
		$out .= t('Удаление товара '.$id_prod);
		mso_checkreferer();
		$query = $CI->db->delete('grsh_prod', array('id_prod' => $id_prod));
		$query = $CI->db->delete('grsh_catprod', array('id_prod' => $id_prod));
		$query = $CI->db->delete('grsh_prodadd', array('id_prod' => $id_prod));
		};
	};

// очистить таблицу
if ($post = mso_check_post(array('f_session_id', 'delall')))
	{
	if (isset($post['id_cat']))	//-если вызов из категории то удалим товары только из этой категории
		{
		$CI->db->where('id_cat', $post['id_cat']);
		$query = $CI->db->get('grsh_catprod');
		foreach ($query->result_array() as $row)
			{
			$CI->db->or_where('id_prod', $row['id_prod']);
			}
		}
	$table = array('grsh_prod', 'grsh_prodadd', 'grsh_catprod');
	$query = $CI->db->delete($table);
	};

//--------- вывод таблицы товаров ----------------------

$nmcat = t('всех категорий');

$catarray = get_array_db('cat', '','', $cache=FALSE);
if ($catarray) 	$slug_id = array_slug_id('cat', $catarray);


if (isset($post['id_cat']))
	{
	$id_cat = $post['id_cat'];
	$CI->db->join('grsh_catprod', 'grsh_catprod.id_prod = grsh_prod.id_prod');
	$CI->db->where('id_cat', $id_cat);
	}

if (mso_segment(4) !='' && mso_segment(4) !='next' && mso_segment(4) !='del')
	{
	$id_cat = $slug_id[mso_segment(4)];
	$CI->db->join('grsh_catprod', 'grsh_catprod.id_prod = grsh_prod.id_prod');
	$CI->db->where('id_cat', $id_cat);
	}
$query = $CI->db->get('grsh_prod');
$pag_row = $query->num_rows();	// количество результатов запроса
$query->free_result();		//освобождаем память от результатов запроса

$pagination['maxcount']=1;		//инициируем начальным значением
$pagination['$offset']=0;
$pagination['limit']=20;		//количество извлекаемых данных на одну страницу будем в настройках хранить
$current_paged = mso_current_paged();  // текущая страница пагинации

if ($pag_row > 0)
	{
	$pagination['maxcount'] = ceil($pag_row / $pagination['limit']); // всего станиц пагинации
		if ($current_paged > $pagination['maxcount']) $current_paged = $pagination['maxcount'];
	$pagination['$offset'] = $current_paged * $pagination['limit'] - $pagination['limit'];
	}
else
	{
	$pagination = false;
	}

$postidcat = '';
if (mso_segment(4) !='' && mso_segment(4) !='next' && mso_segment(4) !='del')
	{
	$id_cat = $slug_id[mso_segment(4)];
	$CI->db->join('grsh_catprod', 'grsh_catprod.id_prod = grsh_prod.id_prod');
	$CI->db->where('id_cat', $id_cat);
	$postidcat = form_hidden('id_cat', $id_cat);
	}

if (isset($post['id_cat']))
	{
	$id_cat = $post['id_cat'];
	$CI->db->join('grsh_catprod', 'grsh_catprod.id_prod = grsh_prod.id_prod');
	$CI->db->where('id_cat', $id_cat);
	$postidcat = form_hidden('id_cat', $id_cat);
	}

$CI->db->order_by("grsh_prod.id_prod", "asc");
$query = $CI->db->get('grsh_prod', $pagination['limit'], $pagination['$offset']);

$tbl[1][1]='id'; 
$tbl[1][2]=t('арт.','admin'); 
$tbl[1][3]=t('имя', 'admin');
$tbl[1][4]=t('цена', 'admin');
$tbl[1][5]=t('кол-во', 'admin');
$tbl[1][6]=t('резерв', 'admin');
$tbl[1][7]=t('править', 'admin');
$tbl[1][8]=t('удалить', 'admin');

$i=1;
foreach ($query->result_array() as $row)
	{
		$i++;
		$tbl[$i][1]=$row['id_prod']; 
		$tbl[$i][2]=$row['articul_prod'];
		$tbl[$i][3]='<a href="'.$plugin_url.'/product/edit/'.$row['id_prod'].'">'.$row['name_prod'].'</a>';
		$tbl[$i][4]=$row['cost_prod'];
		$tbl[$i][5]=$row['quantity_prod'];
		$tbl[$i][6]=$row['reserve_prod'];
		$tbl[$i][7]='<a href="'.$plugin_url.'/product/edit/'.$row['id_prod'].'">'.t('редактировать', 'admin').'</a>';		
		$tbl[$i][8]='<a href="'.$plugin_url.'/product/del/'.$row['id_prod'].'">'.t('удалить', 'admin').'</a>';	
	};


if (isset($id_cat)) 
		{
		$arrnmcat = array_name_id($data_tip = 'cat', $catarray);
		$nmcat = ' категории: "'.array_search($id_cat, $arrnmcat).'"';
		}

$out.=	'<h1 class="content">'.t('Товары', 'admin').' '.$nmcat.'</h1>
	<table style="width: 99%; border: none; line-height: 1.4em;"><tr><td style="vertical-align: top; padding: 0 10px 0 0;">'.
	form_open($plugin_url .'/product').mso_form_session('f_session_id').
	form_submit('toadd', t('добавить товар', 'admin') ).
	form_submit('delall', t('удалить всё', 'admin') ).$postidcat.
	form_close().
	buildtable($tbl);    // ф-ция отрисовки таблицы из библиотеки

	echo $out;	
	$out = '';
	mso_hook('pagination', $pagination);


	$in['link'] = $plugin_url.'/product/';
	$in['admin'] = TRUE;
	$in['check'] = mso_segment(4);
$out.= 	'</td><td style="vertical-align: top; width: 250px;">'.
	catalink($in).NR.'</td></tr></table>';

	echo $out;

?>