<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	mso_cur_dir_lang('admin');

if ( !mso_check_allow('grshop_edit') ) 
	{
	 echo 'Доступ запрещен';
	 return;
	}
	
	global $MSO;
	require_once ($MSO->config['plugins_dir'].'grshop/common/admcom.php');	// подгружаем библиотеку для админки
	require_once ($MSO->config['plugins_dir'].'grshop/common/common.php');	// подгружаем библиотеку
	$CI = & get_instance();	
	$CI->load->helper('form');	// подгружаем хелпер форм	

/* ф-ция просто повторяется во всех условных блоках кроме всех действующих */
function isss($post, $CI)
	{
	if (isset($post['status']))
		{
		$CI->db->set('status_order', $post['status']);
		$CI->db->where('id_ord', $post['id_ord']);
		$CI->db->update('grsh_ord');
		}
	}


if (mso_segment(4) == 'del')	//--если что-то делаем с конкретным заказом--
	{
	$CI->db->delete('grsh_ord', array('id_ord' => mso_segment(5)));
	$CI->db->delete('grsh_ordprod', array('id_ord' => mso_segment(5)));	
	}

$status = '';
$out = '<h1 class="content">'.t('Заказы ');
$CI->db->where('status_order !=', '4');	

if ($post = mso_check_post(array('f_session_id', 'all_orders')))
	{
	mso_checkreferer();
	$status = t(' текущие');
	$CI->db->where('status_order !=', '4');	
	}

if ($post = mso_check_post(array('f_session_id', 'step0_orders')))
	{
	mso_checkreferer();
	isss($post, $CI);
	$status = t(' поступившие');
	$CI->db->where('status_order', '0');
	}

if ($post = mso_check_post(array('f_session_id', 'step1_orders')))
	{
	mso_checkreferer();
	isss($post, $CI);
	$status = t(' подтверждённые');
	$CI->db->where('status_order', '1');
	}

if ($post = mso_check_post(array('f_session_id', 'step2_orders')))
	{
	mso_checkreferer();
	isss($post, $CI);
	$status = t(' на комплектации');
	$CI->db->where('status_order', '2');
	}

if ($post = mso_check_post(array('f_session_id', 'step3_orders')))
	{
	mso_checkreferer();
	isss($post, $CI);
	$status = t(' отгруженные');
	$CI->db->where('status_order', '3');
	}

if ($post = mso_check_post(array('f_session_id', 'step4_orders')))
	{
	mso_checkreferer();
	$query = $CI->db->get('grsh_ord');	//-- это я не знаю как по другому сбросить условие в where
	isss($post, $CI);
	$status = t(' исполненные');
	$CI->db->where('status_order', '4');
	}

//---- тут вывод таблицы заказов с параметрами сформированными в условиях выбора
//---- в зависимости от нажатой кнопы------------------------------------------------------
	$CI->db->order_by('status_order', 'asc');
	$CI->db->order_by('start_data_order', 'desc');
	//++
	$CI->db->from('grsh_ord');
	//echo _sql();				
	$query = $CI->db->get();
	//++
	//pr($query);
	//pr($CI);
	if ($query->num_rows() != 0)
		{
		$arrord[0] = array 	(
				'id_ord'=>'id', 
				'num_ord'=>t('Номер'), 
				'start_data_order'=>t('Отправлен'), 
				'status_order'=>t('статус'), 
				'email_order'=>'e-mail', 
				'telephon_order'=>t('телефон'),
				'delete_order'=>t('удалить'),
				);
		$j = 1;
		foreach ($query->result_array() as $row)
			{
			$ordernum = get_order_number($row['id_ord'], strtotime($row['start_data_order']));
			$arrord[$j]['id_ord'] = $row['id_ord'];
			$arrord[$j]['num_ord'] = '<a href="'.$plugin_url.'/ord/edit/'.$row['id_ord'].'">'.$ordernum.'</a>';
			$arrord[$j]['start_data_order'] = $row['start_data_order'];
			$arrord[$j]['status_order'] = $row['status_order'];
			$arrord[$j]['email_order'] = $row['email_order'];
			$arrord[$j]['telephon_order'] = $row['telephon_order'];
			$arrord[$j]['delete_order'] = '<a href="'.$plugin_url.'/ord/del/'.$row['id_ord'].'"><fontcolor = "red">X</fontcolor></a>';
			$j++;
			} 
		}

$out .= 	$status.'</h1><br />';
$out .= 	form_open($plugin_url.'/ord/').mso_form_session('f_session_id').
	form_submit('all_orders', t('Текущие заказы') ).
	form_submit('step0_orders', t('Поступившие') ).
	form_submit('step1_orders', t('Подтвержденные') ).
	form_submit('step2_orders', t('Комплектуются') ).
	form_submit('step3_orders', t('Отгруженные') ).
	form_submit('step4_orders', t('Исполненные') );

if (isset($arrord)) $out .= buildtable($arrord);	//--ф-ция строит таблицу из коммона----
$out .= form_close( );



if (mso_segment(4) == 'edit')	//--если что-то делаем с конкретным заказом--
	{

	$arr = get_order(mso_segment(5));	//--получили данные о заказе--
	$order = $arr['order'];
	$prodord = $arr['prod'];
	$number_status = '0';

	//$out надо инициализировать снова, что бы не выводилось все что до этого


	if ($order != FALSE)
		{
		$number_status = $order['status_order'];
		$st = array ('0' =>'поступил', '1'=>'подтвержден', '2'=>'комплектуется', '3'=>'отгружен', '4'=>'исполнен');

		$out = '<h1 class="content">'.t('Заказ').'№: '.$order['num_order'].'</h1><br />'.
		form_open($plugin_url.'/ord/').mso_form_session('f_session_id').

		'<div class="block_page"><h3>'.t('статус ', 'admin').'</h3>'.
		form_dropdown('status', $st , $order['status_order'], ' style="margin-top: 5px; width: 12em;" ').NR.form_submit('step'.$number_status.'_orders', t('Сохранить')).'</div>'.

		t('ID заказа ', 'admin').'<div class="block_page">'.''.$order['id_ord'].NR.'</div>'.
		t('Контактное лицо ', 'admin').'<div class="block_page">'.t('Контактное лицо ', 'admin').''.$order['person_order'].NR.'</div>'.
		t('Дата поступления заказа ', 'admin').'<div class="block_page">'.''.$order['start_data_order'].NR.'</div>'.
		t('e-mail заказчика ', 'admin').'<div class="block_page">'.''.$order['email_order'].NR.'</div>'.
		t('телефон заказчика ', 'admin').'<div class="block_page">'.''.$order['telephon_order'].NR.'</div>'.
		t('контактный адрес ', 'admin').'<div class="block_page">'.''.$order['adress_order'].NR.'</div>'.
		t('дополнительно ', 'admin').'<div class="block_page">'.''.$order['description_order'].NR.'</div>'.
		t('Дата поступления заказа ', 'admin').'<div class="block_page">'.''.$order['start_data_order'].NR.'</div>';
		};
	if ($prodord != FALSE) 
		{
		foreach ($prodord as $id => $prod)
			{
			$prodord[$id]['id_prod'] = '<a href="'.$plugin_url.'/product/edit/'.$prod['id_prod'].'">'.$prod['id_prod'].'</a>';
			$prodord[$id]['name_prod'] = '<a href="'.$plugin_url.'/product/edit/'.$prod['id_prod'].'">'.$prod['name_prod'].'</a>';
			}		
		$out .= buildtable(arr_2_buildtbl($prodord));
		}
	$out .= form_hidden('id_ord', $order['id_ord']).form_close( );
	}
echo $out;
?>
