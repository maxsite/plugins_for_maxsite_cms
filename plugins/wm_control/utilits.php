<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<h1><?= t('Управление Web Money', __FILE__) ?></h1>
<p class="info"><?= t('Плагин позволяет управлять Web Money через веб интерфейс.', __FILE__) ?></p>

<script type="text/javascript">
function open_click(str)
{
if (document.getElementById(str).style.display == ""){
	document.getElementById(str).style.display="none";
}else if(document.getElementById(str).style.display == "none"){
	document.getElementById(str).style.display="";
}
}
</script>

<?
$CI = & get_instance();
//Загрузка библиотеки для работы с WM
$CI->load->library('wm');

//Если в config.php указан WMID - делаем на него ссылку. Иначе используется WMID в wm.php
$CI->wm->Global_WMID = $config_wm['WMID'];

//Определяем пути
/*$CI->wm->Path_Folder = $config_wm['path_folder'];
$CI->wm->Path_Signer = $config_wm['path_signer'];
$CI->wm->Path_System = $config_wm['path_system'];
*/

//Проверка работы WMSignera
if ( $post = mso_check_post(array('f_session_id', 'check_wmsigner')) ){
	$stirng_signer = $CI->wm->check_wmsigner();
	if (strlen($stirng_signer) > 10){
		echo '<div class="update">' . t('Работает!', __FILE__) . '<br>'.$stirng_signer.' </div>';
	}
}

//X1. Выписка чека к оплате.
if ( $post = mso_check_post(array('f_session_id', 'enter_check')) ){	switch ($post['valute']){		case 'WMZ':	$purse = $config_wm['WMZ'];
					break;		case 'WME':	$purse = $config_wm['WME'];
					break;
		case 'WMR':	$purse = $config_wm['WMR'];
					break;
		case 'WMU':	$purse = $config_wm['WMU'];
					break;
		case 'WMB':	$purse = $config_wm['WMB'];
					break;
		case 'WMY':	$purse = $config_wm['WMY'];
					break;
		case 'WMG':	$purse = $config_wm['WMG'];
					break;
		default:	$purse = $config_wm['WMR'];
					break;
	}
	$orderid = '';
	$wmid = '';
	if(is_numeric($post['orderid'])) $orderid = $post['orderid'];
	if(is_numeric($post['customerwmid'])) $wmid = $post['customerwmid'];
	$amount = str_replace(',', '.', $post['amount']);
	$desc = iconv('utf-8', 'windows-1251', $post['desc']);
	$address = '';
	$period = 0;
	$expiration = 0;
	$arr_result = $CI->wm->WMXML1 ($orderid,$wmid,$purse,$amount,$desc,$address,$period,$expiration);
	$data = array(
		'orderid' => $arr_result['orderid'],
		'wminvid' => $arr_result['wminvid'],
		'customerwmid' => $arr_result['customerwmid'],
		'storepurse' => $arr_result['storepurse'],
		'amount' => $arr_result['amount'],
		'desc_' => $arr_result['desk'],
		'state' => $arr_result['state'],
		'time' => time(),
		'ip' => ip2long($CI->input->ip_address())
	);
	if($CI->db->insert('wm_check_trade', $data) and $arr_result['retval'] == 0){		echo '<div class="update">' . t('Счет выписан!', __FILE__) . '<br>'.t('Номер счета в WM: ', __FILE__).$arr_result['wminvid'].' </div>';	}else{		echo '<div class="error">' . t('Счет не выписан!', __FILE__) . '<br>'.t('Причина: ', __FILE__).$arr_result['retdesc'].' </div>';
	}
	//print_r($arr_result);
}

//X2. Перевод стедств.
if ( $post = mso_check_post(array('f_session_id', 'give_money')) ){
	$tranid = '';
	$arr_ = $post['rpurse'][0];
	switch ($arr_){
		case 'Z':	$purse = $config_wm['WMZ'];
					break;
		case 'E':	$purse = $config_wm['WME'];
					break;
		case 'R':	$purse = $config_wm['WMR'];
					break;
		case 'U':	$purse = $config_wm['WMU'];
					break;
		case 'B':	$purse = $config_wm['WMB'];
					break;
		case 'Y':	$purse = $config_wm['WMY'];
					break;
		case 'G':	$purse = $config_wm['WMG'];
					break;
		default:	$purse = $config_wm['WMR'];
					break;
	}
	if(is_numeric($post['orderid'])) $tranid = $post['orderid'];
	$rpurse = $post['rpurse'];
	$amount = str_replace(',', '.', $post['amount']);
	$desc = iconv('utf-8', 'windows-1251', $post['desc']);
	$address = '';
	$period = 0;
	$pcode = '';
	$expiration = 0;
	$wminvid = 0;
	$arr_result = $CI->wm->WMXML2 ($tranid,$purse,$rpurse,$amount,$period,$pcode,$desc,$wminvid);
	$data = array(
		'trunid' => $arr_result['tranid'],
		'opertype' => $arr_result['opertype'],
		'period' => $arr_result['period'],
		'wminvid' => $arr_result['wminvid'],
		'pursesrc' => $arr_result['pursesrc'],
		'pursedest' => $arr_result['pursedest'],
		'amount' => $arr_result['amount'],
		'comiss' => $arr_result['comiss'],
		'desc_' => $arr_result['desc'],
		'time' => time(),
		'ip' => ip2long($CI->input->ip_address())
	);
	if($CI->db->insert('wm_give_money', $data) and $arr_result['retval'] == 0){
		echo '<div class="update">' . t('Перевод выполнен!', __FILE__) . '<br>'.t('Номер перевода в WM: ', __FILE__).$arr_result['wminvid'].' </div>';
	}else{
		echo '<div class="error">' . t('Перевод не выполнен!', __FILE__) . '<br>'.t('Причина: ', __FILE__).$arr_result['retdesc'].' </div>';
	}
	//print_r($arr_result);
}

//X3. Просмотр истори по кошельку.
if ( $post = mso_check_post(array('f_session_id', 'info_purse')) ){
	$wmtranid = $post['wmtranid'];
	$purse = $post['purse'];
	$tranid = '';
	$wminvid = $post['wminvid'];
	$orderid = $post['orderid'];
	$datestart="20100201 00:00:00";
	$datefinish="20100401 00:00:00";
	$r = $CI->wm->WMXML3 ($purse,$wmtranid,$tranid,$wminvid,$orderid,$datestart,$datefinish);
	echo mso_load_jquery();
	echo mso_load_jquery('jquery.tablesorter.js');
	echo '<script type="text/javascript">
	            $(function() {
	               $("table.tablesorter th").animate({opacity: 0.7});
	               $("table.tablesorter th").hover(function(){ $(this).animate({opacity: 1}); }, function(){ $(this).animate({opacity: 0.7}); });
	               $("#trans").tablesorter();
	            });
	            </script>';
	echo t('Результат (0 - успешно) - ', __FILE__).$r['retval']."<br>";
	echo t('Расшифровка - ', __FILE__).$r['retdesc']."<br>";
	echo t('Количество - ', __FILE__).$r['cnt']."<br>";
	echo '<table class="page tablesorter" border="0" width="99%" id="trans"><thead><tr>
	<th>wmtranid</th>
	<th>tranid</th>
	<th>wminvid</th>
	<th>orderid</th>
	<th>'.t('тип', __FILE__).'</th>
	<th>'.t('кошелёк корр-та', __FILE__).'</th>
	<th>'.t('wmid корр-та', __FILE__).'</th>
	<th>'.t('сумма', __FILE__).'</th>
	<th>'.t('коммисия', __FILE__).'</th>
	<th>'.t('остаток', __FILE__).'</th>
	<th>'.t('протекция', __FILE__).'</th>
	<th>'.t('примечание', __FILE__).'</th>
	<th>'.t('дата', __FILE__).'</th></thead>
	</tr>';
	while(list($key,$val)=each($r['operations'])) {
		echo "
		<tr>
		<td>$key</td>
		<td>".$val['tranid']."</th>
		<td>".$val['wminvid']."</td>
		<td>".$val['orderid']."</td>
		<td>".$val['type']."</td>
		<td>".$val['corrpurse']."</td>
		<td>".$val['corrwmid']."</td>
		<td>".$val['amount']."</td>
		<td>".$val['comiss']."</td>
		<td>".$val['rest']."</td>
		<td>".$val['protection']."</td>
		<td>".iconv('WINDOWS-1251', 'UTF-8', $val['desc'])."</td>
		<td>".$val['datecrt']."</td>
		</tr>";
	}
	echo "</table>";
}

//X4. Просмотр состояния счета.
if ( $post = mso_check_post(array('f_session_id', 'history_purse')) ){
	$purse = $post['purse'];
	$wminvid = $post['wminvid'];
	$orderid = $post['orderid'];
	$datestart="20100201 00:00:00";
	$datefinish="20100401 00:00:00";
	$arr_result = $CI->wm->WMXML4 ($purse,$wminvid,$orderid,$datestart,$datefinish);
	//print_r($arr_result);
	echo t('Результат (0 - успешно) - ', __FILE__).$arr_result['retval']."<br>";
	echo t('Расшифровка - ', __FILE__).$arr_result['retdesc']."<br>";
	echo t('Количество - ', __FILE__).$arr_result['cnt']."<br>";
	while(list($key,$val)=each($arr_result['invoices'])) {
		echo t('* У счёта ', __FILE__).$key.t(' состояние оплаты ', __FILE__).$val."<br>";
	}
}

//X6. Отправка почты по внутренней почте.
if ( $post = mso_check_post(array('f_session_id', 'send_message')) ){
	$wmid = $post['receiverwmid'];
	$msg = $post['msgtext'];
	$subj = $post['msgsubj'];
	$arr_result = $CI->wm->WMXML6 ($wmid,$msg,$subj);
	//print_r($arr_result);
	echo t('Результат (0 - успешно) - ', __FILE__).$arr_result['retval']."<br>";
	echo t('Расшифровка - ', __FILE__).$arr_result['retdesc']."<br>";
	echo t('Дата и время - ', __FILE__).$arr_result['date']."<br>";
}

//X8. Проверка на существование WMID и кошелька и их соответствия.
if ( $post = mso_check_post(array('f_session_id', 'check_wmid')) ){
	$wmid = $post['wmid'];
	$purse = $post['purse'];
	$r = $CI->wm->WMXML8 ($wmid,$purse);
	//print_r($r);
	if($wmid!="" && $purse=="") {
	  if($r['retval']==1) echo 'WMID '.$wmid.t(' существует', __FILE__);
	  if($r['retval']==0) echo 'WMID '.$wmid.t(' НЕ существует', __FILE__);
	}
	elseif($wmid=="" && $purse!="") {
	  if($r['retval']==1) echo 'Кошелек '.$purse.t(' существует и принадлежит WMID ', __FILE__).$r['wmid'];
	  if($r['retval']==0) echo 'Кошелек '.$purse.t(' НЕ существует', __FILE__);
	}
	elseif($wmid!="" && $purse!="") {
	  if($r['retval']==1 && $r['wmid']!="" && $r['purse']!="")
	    echo 'ДА, кошелек '.$purse.t(' принадлежит WMID ', __FILE__).$wmid;
	  if($r['retval']==1 && $r['wmid']!="" && $r['purse']=="")
	    echo 'НЕТ, кошелек '.$purse.t(' НЕ принадлежит WMID ', __FILE__).$wmid;
	  if($r['retval']==0) echo 'WMID '.$wmid.t(' не существует. Проверка невозможна.', __FILE__);
	}
}

//X9. Проверка баланса на кошельке.
if (mso_check_post(array('f_session_id', 'balance_purses')) ){
	$arr_result = $CI->wm->WMXML9();
	//print_r($arr_result);
	echo t('Результат (0 - успешно) - ', __FILE__).$arr_result['retval']."<br>";
	echo t('Расшифровка - ', __FILE__).$arr_result['retdesc']."<br>";
	while(list($key,$val)=each($arr_result['purses'])) {
		echo t('* На кошельке ', __FILE__).$key." ".$val." WM<br>";
	}
}

//X11. Проверка аттестата и отзывов по нем.
if ( $post = mso_check_post(array('f_session_id', 'info_attestat')) ){
	$wmid = $post['wmid'];
	$arr_result = $CI->wm->WMXML11 ($wmid);
	//print_r($arr_result);
	echo t('Код аттестата - ', __FILE__).$arr_result['att']."<br>";
	echo t('Флаг отзыва (1 - отозван) - ', __FILE__).$arr_result['recalled']."<br>";
	echo t('Результат (0 - успешно) - ', __FILE__).$arr_result['retval']."<br>";
	echo t('Расшифровка - ', __FILE__).$arr_result['retdesc']."<br>";

}
?>
<p><strong><a href=# onClick=open_click('ch1')><?= t('Интерфейс X1. Выписка счета', __FILE__)?></a></strong></p>
<div id=ch1 style = "display:none; background-color:#A5CBD3; width:370px; border: 1px solid #34626B">
<?
$form = '<table border=0>';
$form .= '<tr><td width = 150>'.t('Номер счета в магазине ', __FILE__).'</td>';
$form .= '<td width = 200><input name="orderid" type="input"></td></tr>';

$form .= '<tr><td>'.t('WMID (кому выписывается)', __FILE__).'</td>';
$form .= '<td><input name="customerwmid" type="input"></td></tr>';

$form .= '<tr><td>'.t('Валюта', __FILE__).'</td>';
$form .= '<td><select name="valute">
   <option value="WMZ" selected>WMZ</option>
   <option value="WME">WME</option>
   <option value="WMR">WMR</option>
   <option value="WMU">WMU</option>
   <option value="WMB">WMB</option>
   <option value="WMY">WMY</option>
   <option value="WMG">WMG</option>
   </select></td></tr>';

$form .= '<tr><td>'.t('Сумма ', __FILE__).'</td>';
$form .= '<td><input name="amount" type="input"></td></tr>';

$form .= '<tr><td>'.t('Описание ', __FILE__).'</td>';
$form .= '<td><input name="desc" type="input"></td></tr>';

$form .= '<tr><td colspan=2><input type="submit" name="enter_check" value="' . t('Выписать счет', __FILE__) . '"></td></tr>';

$form .= '</table>';

echo '<form action="" method="post">' . mso_form_session('f_session_id');
echo $form;
echo '</form>';
?>
</div>

<p><strong><a href=# onClick=open_click('ch2')><?= t('Интерфейс X2. Перевод средств', __FILE__)?></a></strong></p>
<div id=ch2 style = "display:none; background-color:#A5CBD3; width:370px; border: 1px solid #34626B">
<?
$form = '<table border=0>';

$form .= '<tr><td width = 150>'.t('Номер счета в магазине ', __FILE__).'</td>';
$form .= '<td width = 200><input name="orderid" type="input"></td></tr>';

$form .= '<tr><td>'.t('Номер кошелька получателя', __FILE__).'</td>';
$form .= '<td><input name="rpurse" type="input"></td></tr>';

$form .= '<tr><td>'.t('Сумма ', __FILE__).'</td>';
$form .= '<td><input name="amount" type="input"></td></tr>';

$form .= '<tr><td>'.t('Описание ', __FILE__).'</td>';
$form .= '<td><input name="desc" type="input"></td></tr>';

$form .= '<tr><td colspan=2><input type="submit" name="give_money" value="' . t('Перевести', __FILE__) . '"></td></tr>';

$form .= '</table>';

echo '<form action="" method="post">' . mso_form_session('f_session_id');
echo $form;
echo '</form>';
?>
</div>

<p><strong><a href=# onClick=open_click('ch3')><?= t('Интерфейс X3. История операций', __FILE__)?></a></strong></p>
<div id=ch3 style = "display:none; background-color:#A5CBD3; width:370px; border: 1px solid #34626B">
<?
$form = '<table border=0>';

$form .= '<tr><td width = 150>'.t('Кошелек для проверок', __FILE__).'</td>';
$form .= '<td width = 200><input name="purse" type="input"></td></tr>';

$form .= '<tr><td>'.t('Номер счета в магазине (не обязательно)', __FILE__).'</td>';
$form .= '<td><input name="orderid" type="input"></td></tr>';

$form .= '<tr><td>'.t('Номер счета в Web Money (не обязательно)', __FILE__).'</td>';
$form .= '<td><input name="wminvid" type="input"></td></tr>';

$form .= '<tr><td>'.t('Номер операции в Web Money (не обязательно)', __FILE__).'</td>';
$form .= '<td><input name="wmtranid" type="input"></td></tr>';

$form .= '<tr><td colspan=2><input type="submit" name="info_purse" value="' . t('Проверить', __FILE__) . '"></td></tr>';

$form .= '</table>';

echo '<form action="" method="post">' . mso_form_session('f_session_id');
echo $form;
echo '</form>';
?>
</div>


<p><strong><a href=# onClick=open_click('ch4')><?= t('Интерфейс X4. История выписаных счетов', __FILE__)?></a></strong></p>
<div id=ch4 style = "display:none; background-color:#A5CBD3; width:370px; border: 1px solid #34626B">
<?
$form = '<table border=0>';

$form .= '<tr><td width = 150>'.t('Кошелек для проверок', __FILE__).'</td>';
$form .= '<td width = 200><input name="purse" type="input"></td></tr>';

$form .= '<tr><td>'.t('Номер счета в Web Money (не обязательно)', __FILE__).'</td>';
$form .= '<td><input name="wminvid" type="input"></td></tr>';

$form .= '<tr><td>'.t('Номер счета в магазине (не обязательно)', __FILE__).'</td>';
$form .= '<td><input name="orderid" type="input"></td></tr>';

$form .= '<tr><td colspan=2><input type="submit" name="history_purse" value="' . t('Проверить', __FILE__) . '"></td></tr>';

$form .= '</table>';

echo '<form action="" method="post">' . mso_form_session('f_session_id');
echo $form;
echo '</form>';
?>
</div>

<p><strong><a href=# onClick=open_click('ch6')><?= t('Интерфейс X6. Отправка сообщения по WM-почте', __FILE__)?></a></strong></p>
<div id=ch6 style = "display:none; background-color:#A5CBD3; width:370px; border: 1px solid #34626B">
<?
$form = '<table border=0>';

$form .= '<tr><td width = 150>'.t('WMID получателя', __FILE__).'</td>';
$form .= '<td width = 200><input name="receiverwmid" type="input"></td></tr>';

$form .= '<tr><td>'.t('Тема сообщения', __FILE__).'</td>';
$form .= '<td><input name="msgsubj" type="input"></td></tr>';

$form .= '<tr><td>'.t('Текст сообщения', __FILE__).'</td>';
$form .= '<td><input name="msgtext" type="input"></td></tr>';

$form .= '<tr><td colspan=2><input type="submit" name="send_message" value="' . t('Отправить', __FILE__) . '"></td></tr>';

$form .= '</table>';

echo '<form action="" method="post">' . mso_form_session('f_session_id');
echo $form;
echo '</form>';
?>
</div>

<p><strong><a href=# onClick=open_click('ch8')><?= t('Интерфейс X8. Проверка принадлежности и существования кошельков и WMID', __FILE__)?></a></strong></p>
<div id=ch8 style = "display:none; background-color:#A5CBD3; width:370px; border: 1px solid #34626B">
<?
$form = '<table border=0>';

$form .= '<tr><td width = 150>'.t('WMID', __FILE__).'</td>';
$form .= '<td width = 200><input name="wmid" type="input"></td></tr>';

$form .= '<tr><td>'.t('Кошелек', __FILE__).'</td>';
$form .= '<td><input name="purse" type="input"></td></tr>';

$form .= '<tr><td colspan=2><input type="submit" name="check_wmid" value="' . t('Проверить', __FILE__) . '"></td></tr>';

$form .= '</table>';

echo '<form action="" method="post">' . mso_form_session('f_session_id');
echo $form;
echo '</form>';
?>
</div>


<p><strong><a href=# onClick=open_click('ch9')><?= t('Интерфейс X9. Получение баланса на кошельках', __FILE__)?></a></strong></p>
<div id=ch9 style = "display:none; background-color:#A5CBD3; width:370px; border: 1px solid #34626B">
<?
echo '<form action="" method="post">' . mso_form_session('f_session_id');
echo '<input type="submit" name="balance_purses" value="' . t('Получить', __FILE__) . '">';
echo '</form>';
?>
</div>

<p><strong><a href=# onClick=open_click('ch11')><?= t('Интерфейс X11. Получение информации по аттестату по WMID', __FILE__)?></a></strong></p>
<div id=ch11 style = "display:none; background-color:#A5CBD3; width:370px; border: 1px solid #34626B">
<?
$form = '<p>'.t('WMID', __FILE__);
$form .= '<input name="wmid" type="input"></p>';

echo '<form action="" method="post">' . mso_form_session('f_session_id');
echo $form;
echo '<input type="submit" name="info_attestat" value="' . t('Получить', __FILE__) . '">';
echo '</form>';
?>
</div>


<?
echo '<form action="" method="post">' . mso_form_session('f_session_id');
echo '<input type="submit" name="check_wmsigner" value="' . t('Проверить работу WMSigner-a', __FILE__) . '" style="margin: 25px 0 5px 0;">';
echo '</form>';
?>