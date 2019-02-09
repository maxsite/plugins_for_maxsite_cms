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
	$nametbldb = 'grsh_act';	// имя таблицы в базе данных
	$out = '';

// блок редактирования акции
if ($post = mso_check_post(array('f_session_id', 'toadd')) or mso_segment(4)=='edit')
	{
	mso_checkreferer();
	$id_act = '0';
	$name_act = '';
	$description_act = '';
	$discount_act = '';
	$public_status_act = '1';	// Значение по умолчанию потом будет из опций настроек
	$other_discount_act = '1';	// значение по умолчанию потом будет из опций настроек
	$all_user_act = '0';		// признак действия акции на все категории
	$shadow_act = '0';		// признак наверно приостановки действия акции

	$shapka=t('Новая акция', 'plugins/grshop');

	$date_cur_y = date('Y');
	$date_cur_m = date('m');
	$date_cur_d = date('d');	
	$tyme_cur_h = date('H');
	$tyme_cur_m = date('i');

	$date_defend_y = 2036;
	$date_defend_m = 12;
	$date_defend_d = 31;	
	$tyme_defend_h = 23;
	$tyme_defend_m = 59;

	if ($id_act=mso_segment(5))
		{

		$CI->load->database(); 	
		$CI->db->where('id_act', $id_act);
		$query = $CI->db->get($nametbldb);
			foreach ($query->result_array() as $row)
			{
			$name_act = $row['name_act'];
			$description_act = $row['description_act'];
			$discount_act = $row['discount_act'];
			$public_status_act = $row['public_status_act'];
			$other_discount_act = $row['other_discount_act'];
			$all_user_act = $row['all_user_act'];

			$start_data_act = strtotime($row['start_data_act']);
			$end_data_act = strtotime($row['end_data_act']);

			$date_cur_y = date('Y',$start_data_act);
			$date_cur_m = date('m',$start_data_act);
			$date_cur_d = date('d',$start_data_act);	
			$tyme_cur_h = date('H',$start_data_act);
			$tyme_cur_m = date('i',$start_data_act);

			$date_defend_y = date('Y',$end_data_act);
			$date_defend_m = date('m',$end_data_act);
			$date_defend_d = date('d',$end_data_act);	
			$tyme_defend_h = date('H',$end_data_act);
			$tyme_defend_m = date('i',$end_data_act);
			}
		$shapka=t('Редактирование акции ID', 'plugins/grshop').': '.$id_act;
		}

	$chec_pubstat = FALSE;  if ($public_status_act == 1) {$chec_pubstat = TRUE;};
	$chec_all_user_act = FALSE;  if ($all_user_act == 1) {$chec_all_user_act = TRUE;};
	$chec_shadow_act = FALSE;  if ($shadow_act == 1) {$chec_shadow_act = TRUE;};

	$date_all_y = array();
	for ($i=2009; $i<2037; $i++) $date_all_y[$i] = $i;
	
	$date_all_m = array();
	for ($i=1; $i<13; $i++) $date_all_m[$i] = $i;
	
	$date_all_d = array();
	for ($i=1; $i<32; $i++) $date_all_d[$i] = $i;

	$time_all_h = array();
	for ($i=0; $i<24; $i++) $time_all_h[$i] = $i;
	
	$time_all_m = array();
	for ($i=0; $i<60; $i++) $time_all_m[$i] = $i;

	$out.= 
	'<h1 class="content">'.$shapka.'</h1><br />'.	
	form_open($plugin_url .'/actions').
	mso_form_session('f_session_id').
	form_hidden('id_act', $id_act).NR.
	'<table style="width: 99%; border: none; line-height: 1.4em;"><tr><td style="vertical-align: top; padding: 0 10px 0 0;">

	<div class="block_page"><h3>'.t('Название акции', 'plugins/grshop').'</h3>'.form_input('name_act', $name_act).NR.'</div>
	<div class="block_page"><h3>'.t('Описание', 'plugins/grshop').'</h3>'.form_textarea($data = array('name'=>'description_act', 'value'=>$description_act,'rows'=>'5')).NR.'</div>
	<div class="block_page"><h3>'.t('скидка', 'plugins/grshop').'</h3>'.form_input('discount_act', $discount_act).NR.'</div>
	<div class="block_page"><h3> '.t('опубликовано', 'plugins/grshop').' </h3>'.form_checkbox('public_status_act', '1', $chec_pubstat).NR.'</div>

	<div class="block_page"><h3>'.t('скидки других акций', 'plugins/grshop').': </h3>'.
		t('отменяются', 'plugins/grshop').' '.form_radio('other_discount_act', '0', ($other_discount_act==0)).NR.
		t('действует наибольшая', 'plugins/grshop').' '.form_radio('other_discount_act', '1', ($other_discount_act==1)).NR.
		t('суммируются', 'plugins/grshop').' '.form_radio('other_discount_act', '2', ($other_discount_act==2)).NR.'</div>
	<div class="block_page"><h3>'.t('Старт акции', 'plugins/grshop').' </h3>'.
	form_dropdown('start_date_y', $date_all_y, $date_cur_y, ' style="margin-top: 5px; width: 60px;" ').NR.
	form_dropdown('start_date_m', $date_all_m, $date_cur_m, ' style="margin-top: 5px; width: 60px;" ').' -'.NR.
	form_dropdown('start_date_d', $date_all_d, $date_cur_d, ' style="margin-top: 5px; width: 60px;" ').' '.NR.
	form_dropdown('start_time_h', $time_all_h, $tyme_cur_h, ' style="margin-top: 5px; width: 60px;" ').' :'.NR.
	form_dropdown('start_time_m', $time_all_m, $tyme_cur_m, ' style="margin-top: 5px; width: 60px;" ').NR.'</div>

	<div class="block_page"><h3>'.t('Финиш акции', 'plugins/grshop').' </h3>'.
	form_dropdown('end_date_y', $date_all_y, $date_defend_y, ' style="margin-top: 5px; width: 60px;" ').' -'.NR.
	form_dropdown('end_date_m', $date_all_m, $date_defend_m, ' style="margin-top: 5px; width: 60px;" ').' -'.NR.
	form_dropdown('end_date_d', $date_all_d, $date_defend_d, ' style="margin-top: 5px; width: 60px;" ').' '.NR.
	form_dropdown('end_time_h', $time_all_h, $tyme_defend_h, ' style="margin-top: 5px; width: 60px;" ').' :'.NR.
	form_dropdown('end_time_m', $time_all_m, $tyme_defend_m, ' style="margin-top: 5px; width: 60px;" ').NR.'</div>'.

	form_submit('addact', t('Сохранить', 'plugins/grshop') ).

	'</td><td style="vertical-align: top; width: 250px;">

	<div class="block_page"><h3> '.t('Действие акции', 'plugins/grshop').' </h3>'.
	form_checkbox('all_user_act', '0', $chec_all_user_act).' '.t('для всех покупателей', 'plugins/grshop').NR.'<br />'.
	form_checkbox('shadow_act', '1', $chec_shadow_act).' '.t('приостановить', 'plugins/grshop').NR.'
	</div>
	<p class="info">'.t('Категории, на которые действует акция', 'plugins/grshop').'</p>	
	<div class="block_page"><h3>'.t('Категории', 'plugins/grshop').'</h3>'.
	many_check ($data = array('name'=>'cat', 'crit'=>'act', 'id_crit'=>$id_act)).NR.'
	</div></td></tr></table>'.
	form_close();
	echo $out;
	return;	
	}

// сохранение данных одной акции после редактирования или добавления
if ($post = mso_check_post(array('f_session_id', 'addact')))
	{
	mso_checkreferer();
	$public_status_act='0'; if (isset ($post['public_status_act'])) {$public_status_act='1';};	//--если акция опубликована----
	$all_user_act='0'; if (isset ($post['all_user_act'])) {$all_user_act='1';};		//---действие акции для всех покупателей----
	$shadow_act='0'; if (isset($post['shadow_act'])) $shadow_act=$post['shadow_act'];	//--- признак приостановки акции----
	$id_act=$post['id_act'];
	$newact = array(
			'name_act' => $post['name_act'] ,
 			'description_act' =>$post['description_act'] ,
			'discount_act' => $post['discount_act'],
			'public_status_act' => $public_status_act,
			'other_discount_act' => $post['other_discount_act'],
			'all_user_act' => $all_user_act,
			'start_data_act' => $post['start_date_y'].'-'.$post['start_date_m'].'-'.$post['start_date_d'].' '.$post['start_time_h'].':'.$post['start_time_m'].':00',
			'end_data_act' => $post['end_date_y'].'-'.$post['end_date_m'].'-'.$post['end_date_d'].' '.$post['end_time_h'].':'.$post['end_time_m'].':59',
		            );
	if ($id_act==0) 
		{
		$res=$CI->db->insert($nametbldb, $newact);
		}
	else	{
		$CI->db->where('id_act', $id_act);
		$res=$CI->db->update($nametbldb, $newact ); 
		};


	//----обработка чеков о действии на категории-----
	if ($shadow_act == '1')
		{
		$CI->db->where('id_act', $id_act);
		$CI->db->delete('grsh_catact');	//---если приостановить, удаляем все связи-----			
		}
	else 
		{
		reseiv_many_check ($post);		//--собственно ф-ция обработки чеков------
		};
	};

// удаление выбранной акции
if (mso_segment(4)=='del')
	{
	if ($id_act=mso_segment(5))
		{
		$out.= t('Удаление акции', 'plugins/grshop').' '.$id_act;
		mso_checkreferer();
		$query = $CI->db->delete('grsh_act', array('id_act' => $id_act));
		$query = $CI->db->delete('grsh_catact', array('id_act' => $id_act)); 
		};
	};

// очистить таблицу
if ($post = mso_check_post(array('f_session_id', 'delall')))
	{
	$query = $CI->db->delete('grsh_act');
	$query = $CI->db->delete('grsh_catact');		
	};

// вывод таблицы акций
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

$CI->db->order_by("end_data_act", "asc"); 
$query = $CI->db->get($nametbldb, $pagination['limit'], $pagination['$offset']);

$tbl[1][1]='id'; 
$tbl[1][2]=t('название', 'plugins/grshop'); 
$tbl[1][3]=t('скидка', 'plugins/grshop');
$tbl[1][4]=t('публ', 'plugins/grshop');
$tbl[1][5]=t('все скидки', 'plugins/grshop');
$tbl[1][6]=t('старт', 'plugins/grshop');
$tbl[1][7]=t('финиш', 'plugins/grshop');
$tbl[1][8]=t('править', 'plugins/grshop');
$tbl[1][9]=t('убрать', 'plugins/grshop');

$i=1;
foreach ($query->result_array() as $row)
	{
		$i=$i+1;
		$tbl[$i][1]=$row['id_act']; 
		$tbl[$i][2]='<a href="'.$plugin_url.'/actions/edit/'.$row['id_act'].'">'.$row['name_act'].'</a>';
		$tbl[$i][3]=$row['discount_act'];
		$tbl[$i][4]=$row['public_status_act'];
		$tbl[$i][5]=$row['other_discount_act'];
		$tbl[$i][6]=date('Y-m-d H:i',strtotime($row['start_data_act']));
		$tbl[$i][7]=date('Y-m-d H:i',strtotime($row['end_data_act']));
		$tbl[$i][8]='<a href="'.$plugin_url.'/actions/edit/'.$row['id_act'].'">'.t('редактировать', 'plugins/grshop').'</a>';		
		$tbl[$i][9]='<a href="'.$plugin_url.'/actions/del/'.$row['id_act'].'">'.t('удалить', 'plugins/grshop').'</a>';	
	};
$out.= 	'<h1 class="content">'.t('Акции!', 'plugins/grshop').'</h1>'.
	form_open($plugin_url .'/actions').mso_form_session('f_session_id').
	form_submit('toadd', t('добавить акцию', 'plugins/grshop') ).
	form_submit('delall', t('удалить всё', 'plugins/grshop') ).
	form_close().
	buildtable($tbl);    // ф-ция отрисовки таблицы из библиотеки
echo $out;
mso_hook('pagination', $pagination);
?>