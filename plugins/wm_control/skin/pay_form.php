<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

	$MSO->config['template'] = $old_template;
	$MSO->config['templates_dir'] = $old_path_templates;

	$desc = iconv('UTF-8', 'CP1251', 'тестовый платеж' );

	require(getinfo('template_dir') . 'main-start.php');
?>
	<form id=pay name=pay method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp">
	<p>пример платежа через сервис Web Merchant Interface</p>
	<p>заплатить 1 WMZ...</p>
	<p>Форма для оплаты через сервис Мерчант</p>

	<p>
		<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="1.0">
		<input type="hidden" name="LMI_PAYMENT_DESC" value="<?= $desc ?>">
		<input type="hidden" name="LMI_PAYMENT_NO" value="1">
		<input type="hidden" name="LMI_PAYEE_PURSE" value="Z887351216372">
		<input type="hidden" name="LMI_SIM_MODE" value="0">
		<p><strong><div class="t250">Ваш эмайл </strong><input type="text" name="email"></div>
		<font color='red'>Внимение!</font> Наш сервер не работает с ящиками на mail.ru, list.ru</p>
	</p>
	<p>
		<input type="submit" value="Оплатить">
	</p>
	</form>

	<form method="POST" action="">
	<p>Форма для оплаты через выписку чека WebMoney</p>
	<p>
		<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="1.0">
		<p><strong><div class="t250">Ваш эмайл </strong><input type="text" name="email"></div></p>
		<p><strong><div class="t250">Ваш WMID </strong><input type="text" name="wmid"></div></p>
	</p>
	<p>
		<input type="submit" value="Оплатить">
	</p>
	</form>
<?	require(getinfo('template_dir') . 'main-end.php');

?>