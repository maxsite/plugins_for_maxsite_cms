<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * RGBlog
 * (c) http://rgblog.ru/
 */

	//$CI = & get_instance();
	
	$options_key = 'plugin_wm_merchant';
	
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		
		$options = array();

		$options['prefix'] = $post['f_prefix'];
		$options['image'] = $post['f_image'];
		$options['style'] = $post['f_style'];
		$options['purse'] = $post['f_purse'];
		$options['secret'] = $post['f_secret'];
		$options['result'] = $post['f_result'];
		$options['mail_title'] = $post['f_mail_title'];
		$options['mail_do'] = $post['f_mail_do'];
		$options['mail_posle'] = $post['f_mail_posle'];
	
		//pr($options);
		
		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}
	
?>
<div class="admin-h-menu">
<?php
	# сделаем меню горизонтальное в текущей закладке
	
	// основной url этого плагина - жестко задается
	$plugin_url = getinfo('site_admin_url') . 'wm_merchant';
	$a  = mso_admin_link_segment_build($plugin_url, '', t('Настройка', __FILE__), 'select') . ' | ';
	$a .= mso_admin_link_segment_build($plugin_url, 'products', t('Товары', __FILE__), 'select');
	$a .= mso_admin_link_segment_build($plugin_url, 'orders', t('Покупки', __FILE__), 'select');
	echo $a;
?>
</div>

<h1><?= t('Настройка плагина', __FILE__) ?></h1>
<p class="info"><?= t('Плагин позволяет организовать продажы на сайте по средствам WebMoney Merchant.', __FILE__) ?></p>

<?php
		$options = mso_get_option($options_key, 'plugins', array());
		$options['prefix'] = isset($options['prefix']) ? $options['prefix'] : 'pay';
		$options['image'] = isset($options['image']) ? $options['image'] : '';
		$options['style'] = isset($options['style']) ? $options['style'] : '';
		$options['purse'] = isset($options['purse']) ? $options['purse'] : '';
		$options['result'] = isset($options['result']) ? $options['result'] : 'result';
		$options['secret'] = isset($options['secret']) ? $options['secret'] : '';
		$options['mail_title'] = isset($options['mail_title']) ? $options['mail_title'] : '';
		$options['mail_do'] = isset($options['mail_do']) ? $options['mail_do'] : '';
		$options['mail_posle'] = isset($options['mail_posle']) ? $options['mail_posle'] : '';
		
		$form = '<h2>Основные настройки</h2>';
		$form .= '<p><strong>' . t('Префикс ссылки для платежей:', __FILE__) . '</strong> ' . ' <input name="f_prefix" type="text" value="' . $options['prefix'] . '">';
		$form .= '<br /><em>Вы можете изменить префикс ссылки, который виден при переходе на страницу оплаты.</em></p>';
		$form .= '<p><strong>' . t('Изображение кнопки "Купить":', __FILE__) . '</strong> ' . ' <input name="f_image" type="text" value="' . $options['image'] . '">';
		$form .= '<br /><em>Вы можете использовать вместо ссылки "Купить" изображение. Для этого введите адрес изображения.</em></p>';
		$form .= '<p><strong>' . t('Стиль кнопки "Купить":', __FILE__) . '</strong> ' . ' <input name="f_style" type="text" value="' . $options['style'] . '">';
		$form .= '<br /><em>Вы можете использовать свои стили, чтобы изменить внешний вид кнопки (ссылки).</em></p>';
		$form .= '<p><strong>' . t('Номер кошелька:', __FILE__) . '</strong> ' . ' <input name="f_purse" type="text" value="' . $options['purse'] . '">';
		$form .= '<br /><em>Введите Ваш номер рублевого кошелька в система WebMoney.</em></p>';
		$form .= '<p><strong>' . t('Секретый код мерчанта:', __FILE__) . '</strong> ' . ' <input name="f_secret" type="text" value="' . $options['secret'] . '">';
		$form .= '<br /><em>Введите Ваш секретный код, который Вы указали в настройках кошелька на сервисе WebMoney.</em></p>';
		$form .= '<p><strong>' . t('Префикс страницы Result URL:', __FILE__) . '</strong> ' . ' <input name="f_result" type="text" value="' . $options['result'] . '">';
		$form .= '<br /><em>Введите префикс страницы обрабатываеющей результаты оплаты, который указывается в настройках торгового кошелька.</em></p>';
		$form .= '<hr style="margin: 10px 0;"><h2>Письмо с товаром</h2><p><strong>' . t('Заголовок письма:', __FILE__) . '</strong> ' . ' <input name="f_mail_title" type="text" value="' . $options['mail_title'] . '">';
		$form .= '<br /><em>Заголовок письма, которое получит покупатель после оплаты товара.</em></p>';
		$form .= '<p><strong>' . t('Шаблон шапки письма:', __FILE__) . '</strong> ' . ' <textarea name="f_mail_do" rows="4">' . $options['mail_do'] . '</textarea>';
		$form .= '<br /><em>Шаблон шапки, который будет вставлен во все письма перед текстом.</em></p>';
		$form .= '<p><strong>' . t('Шаблон подвала письма:', __FILE__) . '</strong> ' . ' <textarea name="f_mail_posle" rows="4">' . $options['mail_posle'] . '</textarea>';
		$form .= '<br /><em>Шаблон шапки, который будет вставлен во все письма перед текстом.</em></p>';
		
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', __FILE__) . '" style="margin: 25px 0 5px 0;">';
		echo '</form>';

?>