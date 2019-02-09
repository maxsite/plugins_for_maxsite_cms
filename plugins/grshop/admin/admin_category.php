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
	$nametbldb = 'grsh_cat';	// имя таблицы в базе данных
	$out='';

// блок редактирования категории
if ($post = mso_check_post(array('f_session_id', 'toadd')) or mso_segment(4)=='edit')
	{

	mso_checkreferer();
	$id_cat = '0';
	$name_cat = '';
	$id_parent_cat = '';
	$slug_cat = '';
	$descr_cat = '';
	$public_status_cat = '';
	$menu_order_cat = '';
	$shapka=t('Новая категория товаров', 'admin');
	if ($id_cat=mso_segment(5))
		{
		$CI->load->database(); 	
		$CI->db->where('id_cat', $id_cat);
		$query = $CI->db->get($nametbldb);
			foreach ($query->result_array() as $row)
			{
			$name_cat = $row['name_cat'];
			$id_parent_cat = $row['id_parent_cat'];
			$slug_cat = $row['slug_cat'];
			$descr_cat = $row['descr_cat'];
			$public_status_cat = $row['public_status_cat'];
			$menu_order_cat = $row['menu_order_cat'];
			} 
		$shapka=t('Редактирование товарной категории ID: ', 'admin').$id_cat;
		}

	$checket = FALSE;  if ($public_status_cat == 1) {$checket = TRUE;};

	$out.=
	'<h1 class="content">'.$shapka.'</h1><br />'.
	form_open($plugin_url .'/category').
	mso_form_session('f_session_id').
	form_hidden('id_cat', $id_cat).NR.
	'<table style="width: 99%; border: none; line-height: 1.4em;"><tr><td style="vertical-align: top; padding: 0 10px 0 0;">

	<div class="block_page"><h3>'.t('Название', 'admin').'</h3>'.form_input('name_cat', $name_cat).NR.'</div>
	<div class="block_page"><h3>'.t('Описание', 'admin').'</h3>'.form_textarea($data = array('name'=>'descr_cat', 'value'=>$descr_cat,'rows'=>'5')).NR.'</div>
	<div class="block_page"><h3>'.t('Опубликовано', 'admin').'</h3>'.form_checkbox('public_status_cat', '1', $checket).NR.'</div>
	<div class="block_page"><h3>'.t('Короткая ссылка', 'admin').'</h3>'.form_input('slug_cat', $slug_cat).NR.'</div>
	<div class="block_page"><h3>'.t('Родительская категория', 'admin').'</h3>'.form_input('parent_cat', $id_parent_cat).NR.'</div>
	<div class="block_page"><h3>'.t('Порядок в списке', 'admin').'</h3>'.form_input('menu_order_cat', $menu_order_cat).NR.'</div>'.

	form_submit('addcat', t('Сохранить', 'admin') ).

	'</td><td style="vertical-align: top; width: 250px;">
	<p class="info">'.
	t('Акции, действующие на категорию', 'admin').'
	<div class="block_page"><h3>'.t('Акции', 'admin').'</h3>'.
	many_check($data = array('name'=>'act', 'crit'=>'cat', 'id_crit'=>$id_cat)).NR.'</div>'.
	'</td></tr></table>'.
	form_close();
	echo $out;
	return;	
	}

// сохранение данных одной категории после редактирования или добавления
if ($post = mso_check_post(array('f_session_id', 'addcat')))
	{
	mso_checkreferer();

	$public_status_cat='0';
	if (isset ($post['public_status_cat'])) {$public_status_cat='1';};
	$id_cat=$post['id_cat'];
	$newcat = array(
			'name_cat' => $post['name_cat'] ,
 			'id_parent_cat' =>$post['parent_cat'] ,
			'slug_cat' => $post['slug_cat'],
			'descr_cat' => $post['descr_cat'],
			'public_status_cat' => $public_status_cat,
			'menu_order_cat' => $post['menu_order_cat'],
		            );
	if ($id_cat==0) 		//----если новая категория-------
			{
			$res=$CI->db->insert($nametbldb, $newcat);
			if ($res != 0) 
				{
				$id_cat = $CI->db->insert_id();  //---- теперь знаем id новой категории
				};
			}
	else		{
			$CI->db->where('id_cat', $id_cat);
			$res=$CI->db->update($nametbldb, $newcat );
			};

	if ($res != 0) 
		{
		echo '<div class="update">'.t('изменения сохранены', 'admin').'</div>';
		};

	//---тут обработака чеков----
	reseiv_many_check ($post);
	};

// удаление выбранной категории
if (mso_segment(4)=='del')
	{
	if ($id_cat=mso_segment(5))
		{
		$out .= t('Удаление категории '.$id_cat);
		mso_checkreferer();
		$query = $CI->db->delete('grsh_cat', array('id_cat' => $id_cat));
		$CI->db->delete('grsh_catact', array('id_cat' => $id_cat)); 
		};
	};

// очистить таблицу
if ($post = mso_check_post(array('f_session_id', 'delall')))
	{
	$query = $CI->db->delete($nametbldb);
	//$CI->db->where('id_cat !=', '0');	//----удаляем всё из таблицы соответствия, кроме записей о вседейств. акциях----
	$query = $CI->db->delete('grsh_catact'); 	
	};

//--------- вывод таблицы категорий ----------------------
$query = $CI->db->get($nametbldb);
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

$CI->db->order_by("name_cat", "asc"); 
$query = $CI->db->get($nametbldb, $pagination['limit'], $pagination['$offset']);

$tbl[1][1]='id'; 
$tbl[1][2]=t('название','admin'); 
$tbl[1][3]=t('ссылка', 'admin');
$tbl[1][4]=t('видимость', 'admin');
$tbl[1][5]=t('родитель', 'admin');
$tbl[1][6]=t('редактировать', 'admin');
$tbl[1][7]=t('удалить', 'admin');

$i=1;
foreach ($query->result_array() as $row)
	{
		$i=$i+1;
		$tbl[$i][1]=$row['id_cat']; 
		$tbl[$i][2]='<a href="'.$plugin_url.'/category/edit/'.$row['id_cat'].'">'.$row['name_cat'].'</a>';
		$tbl[$i][3]=$row['slug_cat'];
		$tbl[$i][4]=$row['public_status_cat'];
		$tbl[$i][5]=$row['id_parent_cat'];
		$tbl[$i][6]='<a href="'.$plugin_url.'/category/edit/'.$row['id_cat'].'">'.t('редактировать', 'admin').'</a>';		
		$tbl[$i][7]='<a href="'.$plugin_url.'/category/del/'.$row['id_cat'].'">'.t('удалить', 'admin').'</a>';	
	};

$out.=	'<h1 class="content">'.t('Категории товаров', 'admin').'</h1>'.
	form_open($plugin_url .'/category').mso_form_session('f_session_id').
	form_submit('toadd', t('добавить категорию', 'admin') ).
	form_submit('delall', t('удалить всё', 'admin') ).
	form_close().
	buildtable($tbl);    // ф-ция отрисовки таблицы из библиотеки
	echo $out;
mso_hook('pagination', $pagination);
?>