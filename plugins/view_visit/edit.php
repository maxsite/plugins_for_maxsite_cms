<? if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Плагин для MaxSite CMS
 * отображение посетителей блога на странице админа
 * (c) http://kerzoll.org.ua/
 */
	$options_key = 'plugin_view_visit';
	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) ){		$options = array();
		if (is_numeric($post['col_pagination'])){
			$options['col_pagination'] = $post['col_pagination'];
		}
		$options['robots'] = $post['robots'];
		if (is_numeric($post['interval'])){			if ($post['interval'] < 10) $post['interval'] = 10;
			$options['interval'] = $post['interval'];
		}
		if (is_string($post['unknw_browser'])){			$options['unknw_browser'] = $post['unknw_browser'];		}
		if (is_string($post['unknw_platform'])){
			$options['unknw_platform'] = $post['unknw_platform'];
		}
		if (!isset($post['link_to_link'] )){
			$options['link_to_link'] = null;
		}else{			$options['link_to_link'] = $post['link_to_link'];		}
		if (!isset($post['ip_to_country'])){
			$options['ip_to_country'] = null;
		}else{
			$options['ip_to_country'] = $post['ip_to_country'];
		}

		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}

	if ( mso_check_post(array('f_session_id', 'cache_drop')) ){		$CI->db->cache_delete_all();
		echo '<div class="update">' . t('Кеш БД очищен!', __FILE__) . '</div>';	}

	if ( mso_check_post(array('f_session_id', 'csv2bin')) ){		require(getinfo('plugins_dir') . 'view_visit/ip_csv2bin.php');
		echo '<div class="update">' . t('Преобразование выполнено! csv файл может быть удален.', __FILE__) . '</div>';
	}


	if ($post = mso_check_post(array('f_session_id', 'check_ip')) ){
		if (!isset($post['ip'])){			$ip = t('IP не задан.', __FILE__);
		}else{			$ip = $post['ip'];		}
		require(getinfo('plugins_dir') . 'view_visit/iptocountry.php');
		$country_small = ip2country($ip);
		if ($country_small != '') $country = $country_list[$country_small];
		echo '<div class="update">' . t('Поиск завершен! Страна - ', __FILE__).$country. '</div>';
	}

	$options = mso_get_option($options_key, 'plugins', array());

	if (!isset($options['col_pagination'])) $options['col_pagination'] = 10;
	if (!isset($options['interval'])) $options['interval'] = 30;
	if (!isset($options['robots'] )) $options['robots'] = "Googlebot, Yandex";
	if (!isset($options['unknw_browser'] )) $options['unknw_browser'] = t('Неизвестный браузер!', __FILE__);
	if (!isset($options['unknw_platform'] )) $options['unknw_platform'] = t('Неизвестная платформа!', __FILE__);
	if (!isset($options['link_to_link'] )){		$options['link_to_link'] = null;
	}else{		$options['link_to_link'] = 'checked="checked"';	}
	if (!isset($options['ip_to_country'] )){
		$options['ip_to_country'] = null;
	}else{
		$options['ip_to_country'] = 'checked="checked"';
	}


?>

<h1><?= t('Настройка плагина view_visit', __FILE__) ?></h1>
<p class="info"><?= t('Плагин позволяет просматривать посещения сайта.', __FILE__) ?></p>

<?
		include(APPPATH.'config/database'.EXT);
		$cache_db = $db['default']['cache_on'];
		unset($db);

		$form = '<p><strong><u>'.t('Количество записей при пагинации', __FILE__).'</strong></u></p>';
		$form .= '<p><input name="col_pagination" type="input" value="'.$options['col_pagination'].'"></p>';

		$form .= '<p><strong><u>'.t('Интервал фиксации посещения. Не менее 10 сек.', __FILE__).'</strong></u></p>';
		$form .= '<p><input name="interval" type="input" value="'.$options['interval'].'">'. t('сек', __FILE__).'</p>';

		$form .= '<p><strong><u>'.t('Надпись для неопределенных браузеров', __FILE__).'</strong></u></p>';
		$form .= '<p><input name="unknw_browser" type="input" value="'.$options['unknw_browser'].'"></p>';

		$form .= '<p><strong><u>'.t('Надпись для неопределенных платформ', __FILE__).'</strong></u></p>';
		$form .= '<p><input name="unknw_platform" type="input" value="'.$options['unknw_platform'].'"></p>';

		$form .= '<p><strong><u>'.t('Список роботов, которые не определяются стандартным методом. Через запятую. Строгий регистр.', __FILE__).'</strong></u></p>';
		$form .= '<p><input name="robots" type="input" value="'.$options['robots'].'" size = "100"></p>';

		$form .= '<p><strong><u>'.t('Показывать ссылки как ссылки', __FILE__).'</u></strong> ';
		$form .= '<input name="link_to_link" type="checkbox" '.$options['link_to_link'].'></p>';

		if (is_file(getinfo('uploads_dir').'iptocountry/iptocountry.dat')){
			$form .= '<p><strong><u>'.t('Включить определение страны по IP', __FILE__).'</u></strong> ';
			$form .= '<input name="ip_to_country" type="checkbox" '.$options['ip_to_country'].'></p>';
		}elseif($options['ip_to_country'] != null){
			$options['ip_to_country'] = null;
			mso_add_option($options_key, $options, 'plugins');
		}

		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', __FILE__) . '" style="margin: 25px 0 5px 0;">';
		echo '</form>';

		unset($form);

		$form = '<p><strong><u>'.t('Очистка кеша БД', __FILE__).'</u></strong></p>';
		if ($cache_db == TRUE){
			$form .= '<p><input type="submit" name="cache_drop" value="' . t('Сбросить кеш БД', __FILE__) . '"></p>';
		}else{			$form .= t('Кеш БД не включен', __FILE__);		}

		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo $form;
		echo '</form>';

		unset($form);

		echo '<p><strong><u>'.t('Настройка распознания страны по IP посетителя', __FILE__).'</p></strong></u>';
		echo '<i>'.t('Перед включением данной функции необходимо загрузить csv файл с базой IP адресов (http://software77.net/geo-ip/) и преобразовать его в бинарный файл', __FILE__).'</i><br>';

		// права доступа файла
		$path = getinfo('uploads_dir').'iptocountry/';
		$fileperms = substr ( decoct ( fileperms ( $path ) ), 2, 6 );
		if ( strlen ( $fileperms ) == '3' ){ $fileperms = '0' . $fileperms; }
		echo t('Права доступа к папке', __FILE__) .' <b>'.$path.'</b>: ' . $fileperms . '<br>';

		$form = '';
		if (is_file(getinfo('uploads_dir').'iptocountry/iptocountry.dat')){
			echo t('Наличие файла iptocountry.dat в uploads/iptocounty: <b>есть</b><br>', __FILE__);
			$form .= '<p><u>'.t('Проверить IP', __FILE__).'</u></p>';
			$form .= '<p><input name="ip" type="input"></p>';
			$form .= '<p><input type="submit" name="check_ip" value="' . t('Проверить', __FILE__) . '"></p>';
		}else{
			echo t('Наличие файла iptocountry.dat в uploads/iptocountry: <b>файл с таким именем не найден!</b><br>', __FILE__);
		}

		if (is_file(getinfo('uploads_dir').'iptocountry/iptocountry.csv')){			echo t('Наличие файла iptocountry.csv в uploads/iptocountry: <b>есть</b>', __FILE__);
			$form .= '<p><u>'.t('Преобразование csv базы в бинарную базу (от 10 сек до 2-3 мин.)', __FILE__).'</u></p>';
			$form .= '<p><input type="submit" name="csv2bin" value="' . t('Преобразовать', __FILE__) . '"></p>';
		}else{
			echo t('Наличие файла iptocountry.csv в uploads/iptocountry: <b>файл с таким именем не найден!</b>', __FILE__) ;
		}

		if (isset($form)){
			echo '<form action="" method="post">' . mso_form_session('f_session_id');
			echo $form;
			echo '</form>';
		}

?>