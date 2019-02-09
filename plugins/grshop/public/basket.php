<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

$CI = & get_instance();
$CI->load->helper('form');	// подгружаем хелпер форм

if ($post = mso_check_post(array('f_session_id', 'basclear')))
	{
	mso_checkreferer();
	$addp['clear'] = 'clear';
	addbasket($addp);	// очистка корзины
	mso_redirect($grsh_options['main_slug']);
	};



if ($post = mso_check_post(array('f_session_id', 'resum')))		// пересчитать сумму
	{
	mso_checkreferer();
	$fqp = $post['qp'];
	$arr_basket_prod = $CI->session->userdata('prod');	//получаем сохраненное значение
		foreach ($fqp as $id_prod => $qp)
			{
			$arr_basket_prod[$id_prod]['qp'] = mso_strip($qp);
			}
	$data_sess['prod'] = $arr_basket_prod;
	$CI->session->set_userdata($data_sess); 	// сохраняем новое значение...	
	};

$arr_basket_prod = $CI->session->userdata('prod');	//получаем сохраненное значение
if ($arr_basket_prod) 
	{
	$sum_main = 0;
	$bas_prod[0] = array('name' => t('Наименование', 'plugins/grshop'), 'cost' => t('Цена за единицу*', 'plugins/grshop'), 'qp' => t('Количество', 'plugins/grshop'), 'sum' => t('Цена', 'plugins/grshop')); //-массив для вывода
	$i=1;
	foreach ($arr_basket_prod as $id_prod => $idp)
		{
		$sum_prod = $idp['cur_cost']*$idp['qp'];
		$bas_prod[$i]['name'] = $idp['name'];
		//$bas_prod[$i]['cost'] = $idp['cost'];
		$bas_prod[$i]['cur_cost'] = $idp['cur_cost'];
		$bas_prod[$i]['qp'] = form_input($data=array('name'=>'qp['.$id_prod.']', 'value'=>$idp['qp'], 'size'=>'2', 'maxlength'=>'5')).NR;
		$bas_prod[$i]['sum'] = $sum_prod;
		//$out.= $idp['name'].' : '.$idp['qp'].t('шт.', 'plugins/grshop')<br>';
		//$out.= $idp['cost'].' X '.$idp['qp'].' = '.$sum_prod.'<br><br>';
		$sum_main = $sum_main + $sum_prod;
		$i++;
		}
	$out .= form_open(getinfo('siteurl').$grsh_options['main_slug'].'/bas/').mso_form_session('f_session_id');
	$out .= '<h3>'.t('Содержимое Вашей корзины:', 'plugins/grshop').'</h3><br/>';
	$out .= buildtable($bas_prod);
	$out .= '<h3>'.t('ИТОГО:', 'plugins/grshop').' '.$sum_main.' '.$grsh_options['money'].'</h3><br>';
	$out .= '<br>'.form_submit('predorder', t('Оформить заказ', 'plugins/grshop'));
	$out .= ' '.form_submit('resum', t('Пересчитать', 'plugins/grshop'));

	$out .= ' '.form_submit('basclear', t('Очистить корзину', 'plugins/grshop')).form_close( );
	}

if (mso_segment(3) == 'fo' || $post = mso_check_post(array('f_session_id', 'predorder')))
	{
	$out = '';		// очищаем вывод
	$out .= form_open(getinfo('siteurl').$grsh_options['main_slug'].'/bas/').mso_form_session('f_session_id');
	$out .= '<h3>'.t('Ваша заявка:', 'plugins/grshop').'</h3><br/>';
	if (mso_segment(3) == 'fo')	$out .= '<h3>'.t('Пожалуйста, заполняйте внимательнее.', 'plugins/grshop').'</h3>';
	$out .= t('Введите пожалуйста свои данные для согласования с Вами условий покупки', 'plugins/grshop').'<br><br>'.
	t('e-mail', 'plugins/grshop').': '.form_input('email_order', '').NR.'<br><br>'.
	t('телефон', 'plugins/grshop').': '.form_input('telephon_order', '').NR.'<br><br>'.
	t('адрес', 'plugins/grshop').': '.form_input('adress_order', '').NR.'<br><br>'.
	t('Контактное лицо', 'plugins/grshop').': '.form_input($data = array('name' => 'person_order', 'value'=>'', 'size' => '50')).NR.'<br><br>'.
	t('дополнительно', 'plugins/grshop').': '.'<br> '.form_textarea($data = array('name'=>'description_order', 'value'=>'','rows'=>'5', 'cols'=>'50')).NR.'<br><br>';
	$out .= form_submit('order', t('Отправить заказ', 'plugins/grshop')).form_close( );	
	}

if ($post = mso_check_post(array('f_session_id', 'order')))
	{
	$out = '';		// очищаем вывод
	$useremail = mso_valid_email($post['email_order']);
	if ((mso_valid_email($post['email_order']) == '') && ($post['telephon_order'] ==''))	mso_redirect($grsh_options['main_slug'].'/bas/fo');


	$arr_basket_prod = $CI->session->userdata('prod');	//получаем все что в корзине
	$arr = save_order_2_db($arr_basket_prod, $post);	//сохранили в db ф-ей из коммона, получили id заказа
	$id_ord = $arr['id_ord'];
	$num_ord = get_order_number ($id_ord, strtotime($arr['date']));	//-ф-ция из коммона

	$outpost = $grsh_options['email_notice'];

	$out .= '<br><h3>'.t('Ваша заявка принята к исполнению', 'plugins/grshop').'</h3>'.NR.NR;
	$out .= '<h3>'.t('Номер Вашей заявки: ', 'plugins/grshop').$num_ord.NR.NR.'</h3>';
	$sum_main = 0;

	$checklist = '';
	if ($arr_basket_prod) 
		{
		foreach ($arr_basket_prod as $id_prod => $idp)
			{
			$sum_prod = $idp['cur_cost']*$idp['qp'];
			$checklist .= $idp['name'].' : '.$idp['qp'].' шт.'.NR.'<br>';
			$checklist .= $idp['cur_cost'].' X '.$idp['qp'].' = '.$sum_prod.' '.$grsh_options['money'].NR;
			$sum_main = $sum_main + $sum_prod;
			}
		}

	$outpost = str_replace('[urlname]', '<a href="'.getinfo('siteurl').'">'.getinfo('siteurl').'</a>', $outpost);
	$outpost = str_replace('[email]', $post['email_order'], $outpost);
	$outpost = str_replace('[tel]', $post['telephon_order'], $outpost);
	$outpost = str_replace('[adress]', $post['adress_order'], $outpost);
	$outpost = str_replace('[person]', $post['person_order'], $outpost);
	$outpost = str_replace('[description]', $post['description_order'], $outpost);
	$outpost = str_replace('[num_ord]', $num_ord, $outpost);
	$outpost = str_replace('[checklist]', $checklist, $outpost);
	$outpost = str_replace('[price]', $sum_main.' '.$grsh_options['money'], $outpost);

	//$admin_email = mso_get_option('admin_email_server', 'general', 'admin@site.com'); //-- такая фигня в mso_mail автоматом add-ается
	//$from = mso_get_option('admin_email', 'general', false); // админский email
	//$email = $post['email_order'].'; '.$grsh_options['email'];

	$email = $post['email_order'];
	$res = mso_mail($email, $subject = t('Ваша Заявка N: ', 'plugins/grshop').$num_ord, $message = strip_tags($outpost), $from = false);
	$email = $grsh_options['email'];
	$res = mso_mail($email, $subject = t('Ваша Заявка N: ', 'plugins/grshop').$num_ord, $message = strip_tags($outpost), $from = false);

	//$errmail = '';
	//if (!$res) $errmail = $CI->email->print_debugger();	// если не ушло, получим причину
	addbasket($addp = array('clear' => 'clear'));	// очистка корзины
	//$out .= '<br>'.$errmail;
	}

$title = t('Оформление заказа', 'plugins/grshop');
if (isset($grsh_options['main_title']) && $grsh_options['main_title'] != '') $title = get_title('',$title,$grsh_options['main_title']);
mso_head_meta('title', $title); 

//--- тут вывод не нужен, потому что вывод записываем в переменную $out
//--- которая выводится в файле catalog.php, откуда вызываются эти странички
?>