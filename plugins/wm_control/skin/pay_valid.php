<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	if(($_POST['LMI_PREREQUEST'] == 1)
	and ($_POST['LMI_PAYEE_PURSE'] == 'Z887351216372')
	and ($_POST['LMI_PAYMENT_AMOUNT'] == 1)
	and ($_POST['LMI_PAYMENT_NO'] == "1")){
		$admin_email = mso_get_option('admin_email', 'general', false); // email куда приходят уведомления
		if ($admin_email){			$email_client = '';
			if (isset($_POST['email'])){				$email_client = $_POST['email'];
				$subject = 'Оптата на сайте kerzoll.org.ua';
				$text = 'Уважаемый пользователь! Вы проявили желание сделать платеж на сайте <a href="http://kerzoll.org.ua">kerzoll.org.ua</a> в размере '.$_POST['LMI_PAYMENT_AMOUNT'].' WM. Примечание! Все платежи на данном сайте в момент этой оплаты находятся в тестовом режиме! Поэтому никакие средства переведены или списаны с Ваших кошельков в пользу сайта не возсожны! Спасибо за использование нашего сервиса.';
				mso_mail($email_client, $subject, $text);
			}
			$subject = 'Оплата в Вашем сервисе.';
			$text = 'Уважаемый Админ! В Вашем сервисе была произведена оплаа через систему Мерчант на кошелек '.$_POST['LMI_PAYEE_PURSE'].' в сумме '.$_POST['LMI_PAYMENT_AMOUNT'].' WM. Эмайл Вашего покупателя - '.$email_client;
			mso_mail($admin_email, $subject, $text);
			echo "YES";
		}
	}else{
		echo "NO";
	}
?>