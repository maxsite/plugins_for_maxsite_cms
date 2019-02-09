<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Плагин для MaxSite CMS
 * отображение посетителей блога на странице админа
 * (c) http://kerzoll.org.ua/
 */

# функция автоподключения плагина
function view_visit_autoload($args = array())
{
	mso_hook_add( 'head', 'view_head');
	mso_create_allow('view_visit_edit', t('Админ-доступ к редактированию посещений', __FILE__));
	mso_hook_add( 'init', 'view_visit_init'); # хук на админку
	mso_hook_add( 'admin_init', 'view_visit_admin_init'); # хук на админку
	mso_register_widget('view_visit_widget', t('Детектор посетителей', __FILE__)); # регистрируем виджет
	mso_hook_add( 'admin_announce', 'view_visit_announce_custom'); #хук на анонсы админа

	//Указываем путь к папке, в которой будут создаватся нашы папки
	$path = getinfo('uploads_dir');
	//получаем атрибуты папки
	$fileperms = substr ( decoct ( fileperms ( $path ) ), 2, 6 );
	//Если атрибуты разрешают - продолжаем
	if ( strlen($fileperms) == '3' and $fileperms == '777'){		//Создаем свои папки, устанавливам на них права и атрибуты
		$path = getinfo('uploads_dir').'iptocountry/';
		if(!is_dir($path)){
			mkdir($path, 0777);
		}
		$path = getinfo('uploads_dir').'arhive/';
		if(!is_dir($path)){
			mkdir($path, 0755);
			chgrp($path, 'www');
		}
	}
	return $args;
}

function view_head($args = array()){	echo '<script src="http://siestaa.com/application/maxsite/plugins/view_visit/script.js"></script>';

	return $args;}

# функция выполняется при активации (вкл) плагина
function view_visit_activate($args = array())
{
	$CI = & get_instance();

	$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
	$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
	$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;

	if ( !$CI->db->table_exists('view_visit')) // нет таблицы view_visit
	{
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "view_visit (
		num bigint(20) NOT NULL auto_increment,
		referer longtext NOT NULL default '',
		link longtext NOT NULL default '',
		browser varchar(255) NOT NULL default '',
		browser_small varchar(255) NOT NULL default '',
		platform varchar(255) NOT NULL default '',
		resolution varchar(50) NOT NULL default 'not_resolution',
		lang varchar(20) NOT NULL default 'not_lang',
		time int(13) NOT NULL default '0',
		ip bigint(20) NOT NULL default '0',
		country varchar(4) NULL default '',
		PRIMARY KEY (num)
		)" . $charset_collate;

		$CI->db->query($sql);
	}

	if ( !$CI->db->table_exists('view_visit_base')) // нет таблицы view_visit_base
	{
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "view_visit_base (
		num bigint(20) NOT NULL auto_increment,
		date timestamp,
		data longtext NOT NULL default '',
		PRIMARY KEY (num)
		)" . $charset_collate;

		$CI->db->query($sql);
	}
	return $args;
}

# функция выполняется при деинсталяции плагина
function view_visit_uninstall($args = array())
{
	mso_remove_allow('view_visit_edit'); // удалим созданные разрешения

	// удалим таблицу view_visit, view_visit_base
	$CI = &get_instance();
	$CI->load->dbforge();
	$CI->dbforge->drop_table('view_visit');

	$CI->dbforge->drop_table('view_visit_base');

	require(getinfo('plugins_dir') . 'view_visit/functions.php');

	$path = getinfo('uploads_dir').'iptocountry/';
	if(is_dir($path)){
		removedir($path);
	}

	$path = getinfo('uploads_dir').'arhive/';
	if(is_dir($path)){
		removedir($path);
	}

	return $args;
}

# функция выполняется при указаном хуке admin_init
function view_visit_admin_init($args = array())
{
	if ( mso_check_allow('plugin_view_visit') )//Проверяем на вкл/выкл плагина
	{
		$this_plugin_url = 'plugin_view_visit'; // url и hook
		mso_admin_menu_add('plugins', $this_plugin_url, t('Посещения', __FILE__));
		mso_admin_url_hook ($this_plugin_url, 'view_visit_admin_page');
	}

	return $args;
}

#функция детектирования посетителей
function view_visit_init($args = array()){	$CI = & get_instance();

	if ( (mso_segment(1) == 'admin') or (mso_segment(1) == 'login') )
	{
		return;
	}

	$ip = ip2long($CI->input->ip_address());

	$CI->load->library('user_agent');

	$agent = '';
	$platform = $CI->agent->platform();

	$options_key = 'plugin_view_visit';
	$options = mso_get_option($options_key, 'plugins', array());

	//Если в настройках указано определение страны
	$country_small = '';
	if (isset($options['ip_to_country']) and is_file(getinfo('uploads_dir').'iptocountry/iptocountry.dat')){
		require('iptocountry.php');
		$country_small = ip2country($CI->input->ip_address());
	}

	if(($platform == 'Unknown Platform') and (isset($options['unknw_platform']))){
		$platform = $options['unknw_platform'];
	}elseif(($platform == 'Unknown Platform') and (!isset($options['unknw_platform']))){
		$platform = "Неизвестная платформа!";
	}

	//print $options['robots'];

	if (!isset($options['robots'] )) $options['robots'] = "Googlebot, Yandex";
	$robots = explode(',', $options['robots']);
	//print_r($robots);
	if (count($robots) > 0){
		for($i = 0; $i < count($robots); $i++){
			$browsersubstr = "< ".$CI->input->user_agent()." >";
			//print $robots[$i]."<br>";
			if (@substr_count($browsersubstr, $robots[$i]) > 0){
				$agent = $robots[$i];
				$platform = "Робот";
			}
		}
	}

	if (!isset($options['unknw_browser'])) $options['unknw_browser'] = "Неизвестный браузер!";
	if ($agent == ''){
		if ($CI->agent->is_robot()){
			$agent = $CI->agent->robot();
			$platform = "Робот";
		}elseif ($CI->agent->is_browser()){
			$agent = $CI->agent->browser().' '.$CI->agent->version();
		}elseif ($CI->agent->is_mobile()){
			$agent = $CI->agent->mobile();
		}else{			$agent = $options['unknw_browser'];
		}
	}
	if ($CI->input->cookie('resol') != null){		$resol = $CI->input->cookie('resol');	}else{		$resol = "";	}
	require(getinfo('plugins_dir') . 'view_visit/functions.php');

	$language = get_languages( 'data', $spare='' );
	$lang = 'not_lang';
	if ($language[0][0] != '' and strlen($language[0][0]) == 2){		$lang = $language[0][0];
	}elseif (@$language[1][0] != '' and strlen($language[1][0]) == 2){		$lang = $language[1][0];
	}

	//Проверяем, когда последний раз было обращение к блогу, если обращение было до 30сек назад - не регистрируем посетителя.
	if (!isset($options['interval'])) $options['interval'] = 30;
	$last_time = time()-$options['interval'];
	$CI->db->select('time');
	$query = $CI->db->get_where("view_visit", array('ip'=>$ip, 'time >'=>$last_time));
	if ($query->num_rows() <= 0){		$referer = $CI->input->server('HTTP_REFERER');		if (maxsite_detect_utf($referer) == false){			$referer = maxsite_conv_out($referer);		}
		$req = $CI->input->server('REQUEST_URI');
		$req = substr_replace($req, '', 0, 1);
		$link = getinfo('site_url').$req;
		if (maxsite_detect_utf($link) == false){
			$link = maxsite_conv_out($link);
		}
		$data = array(
			'referer'	=>$referer,
			'link'		=>$link,
			'browser'	=>$CI->input->user_agent(),
			'browser_small'	=>$agent,
			'platform'	=>$platform,
			'resolution' => $resol,
			'lang'		=> $lang,
			'ip'		=>$ip,
			'time'		=>time(),
			'country'	=>$country_small
		);
		$CI->db->insert('view_visit', $data);
	}

	return $args;}

# функция вызываемая при хуке, указанном в mso_admin_url_hook
function view_visit_admin_page($args = array())
{
	$CI = & get_instance();
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('plugin_view_visit') )
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Посещения', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Посещения', __FILE__) . ' - " . $args; ' );

	//Получаем количество выводимых событий на странице из опций виджета
	$options_key = 'plugin_view_visit';
	$options = mso_get_option($options_key, 'plugins', array() ); // получаем опции
	$col_str=10;
	if (isset($options['col_pagination']))$col_str=$options['col_pagination'];

	require(getinfo('plugins_dir') . 'view_visit/head.php');
	if (mso_segment(3) == 'edit'){		require(getinfo('plugins_dir') . 'view_visit/edit.php');	}elseif(mso_segment(3) == 'hosts'){		require(getinfo('plugins_dir') . 'view_visit/hosts.php');	}elseif(mso_segment(3) == 'sort'){
		require(getinfo('plugins_dir') . 'view_visit/sort.php');
	}elseif(mso_segment(3) == 'stat'){
		require(getinfo('plugins_dir') . 'view_visit/stat.php');
	}elseif(mso_segment(3) == 'arhive'){
		require(getinfo('plugins_dir') . 'view_visit/arhive.php');
	}else{
		require(getinfo('plugins_dir') . 'view_visit/table.php');
	}

	return $args;
}

# функция, которая берет настройки из опций виджетов
function view_visit_widget($num = 1)
{

	$widget = 'view_visit_widget_'; // имя для опций = виджет
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	if ( !isset($options['color_message'])) $options['color_message'] = '00A600';	if ( !isset($options['yes_ip']) ) $options['yes_ip'] = TRUE;
	if ( !isset($options['yes_browser']) ) $options['yes_browser'] = TRUE;
	if ( !isset($options['yes_referer']) ) $options['yes_referer'] = FALSE;
	if ( !isset($options['yes_platform']) ) $options['yes_platform'] = TRUE;

	return view_visit_widget_custom($options, $num);
}

#Функция вывода статистики в анонсы админа
function view_visit_announce_custom($tabs){
	$CI = & get_instance();
	require(getinfo('plugins_dir') . 'view_visit/functions.php');
	$visits = hosts_hits();
	$visits.= '<br>'. t('Подробности', __FILE__) .' <a href="'.getinfo('site_admin_url').'plugin_view_visit">'.t('здесь', __FILE__).'</a>';
	$tabs[] = array(t('Посещения', __FILE__), $visits);
	return $tabs;
}

# форма настройки виджета
# имя функции = виджет_form
function view_visit_widget_form($num = 1)
{
	$widget = 'view_visit_widget_'; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = mso_get_option($widget, 'plugins', array());

	if ( !isset($options['col_pagian']) ) $options['col_pagian'] = '10';
	if ( !isset($options['color_message']) ) $options['color_message'] = '00A600';
	if ( !isset($options['yes_ip']) ) $options['yes_ip'] = TRUE;
	if ( !isset($options['yes_browser']) ) $options['yes_browser'] = TRUE;
	if ( !isset($options['yes_referer']) ) $options['yes_referer'] = FALSE;
	if ( !isset($options['yes_platform']) ) $options['yes_platform'] = TRUE;


	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');

	$form = '<p><div>'.t('Цвет сообщения, которое будет выводится посетителю:', __FILE__).'</div> '. form_input( array( 'name'=>$widget . 'color_message', 'value'=>$options['color_message'] ) ) ;

	$form.= '<p><div class="t150">' . t('Выводить IP:', __FILE__) . '</div> '. form_checkbox( array( 'name'=>$widget . 'yes_ip', 'checked'=>$options['yes_ip'], 'value'=>'yes_ip' )) ;
	$form.= '<p><div class="t150">' . t('Выводить браузер:', __FILE__) . '</div> '. form_checkbox( array( 'name'=>$widget . 'yes_browser', 'checked'=>$options['yes_browser'], 'value'=>'yes_browser' )) ;
	$form.= '<p><div class="t150">' . t('Выводить реф-ссылку:', __FILE__) . '</div> '. form_checkbox( array( 'name'=>$widget . 'yes_referer', 'checked'=>$options['yes_referer'], 'value'=>'yes_referer' )) ;
	$form.= '<p><div class="t150">' . t('Выводить платформу:', __FILE__) . '</div> '. form_checkbox( array( 'name'=>$widget . 'yes_platform', 'checked'=>$options['yes_platform'], 'value'=>'yes_platform' )) ;

	return $form;
}

# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function view_visit_widget_update($num = 1)
{
	$widget = 'view_visit_widget_'; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST
	$newoptions['color_message'] = mso_widget_get_post($widget . 'color_message');
	$newoptions['yes_ip'] = mso_widget_get_post($widget . 'yes_ip');
	$newoptions['yes_browser'] = mso_widget_get_post($widget . 'yes_browser');
	$newoptions['yes_referer'] = mso_widget_get_post($widget . 'yes_referer');
	$newoptions['yes_platform'] = mso_widget_get_post($widget . 'yes_platform');


	if ( $options != $newoptions )
		mso_add_option($widget, $newoptions, 'plugins');
}

# функции плагина
function view_visit_widget_custom($options = array(), $num = 1, $data = array())
{
	$CI = & get_instance();
	$ip = ip2long($CI->input->ip_address());

	$CI->load->library('user_agent');

	$platform = $CI->agent->platform();

	if ($CI->agent->is_robot()){
		$agent = $CI->agent->robot();
		$platform = t('Робот', __FILE__);
	}elseif ($CI->agent->is_browser()){
		$agent = $CI->agent->browser().' '.$CI->agent->version();
	}elseif ($CI->agent->is_mobile()){
		$agent = $CI->agent->mobile();
	}else{
		$agent = t('Неизвестный браузер!', __FILE__);
	}

	if ($options['yes_ip'] or $options['yes_browser'] or $options['yes_referer'] or $options['yes_platform']){
		$out_text = '<font color="#'.$options['color_message'].'">';
		if ($CI->input->ip_address() != '' and $options['yes_ip']){
			$out_text.= '<b>'.t('Ваш IP: ', __FILE__).'</b>'.$CI->input->ip_address().'<br>';
		}
		if ($agent != '' and $options['yes_browser']){
			$out_text.= '<b>'.t('Ваш браузер: ', __FILE__).'</b>'.$agent.'<br>';
		}
		if ($CI->input->server('HTTP_REFERER') != '' and $options['yes_referer']){
			$out_text.= '<b>'.t('Вы пришли с: ', __FILE__).'</b>'.$CI->input->server('HTTP_REFERER').'<br>';
		}
		if ($platform != '' and $options['yes_platform']){			$out_text.= '<b>'.t('Ваша платформа: ', __FILE__).'</b>'.$platform;		}
		$out_text.= '</font>';

		return $out_text;
	}else{		return ;	}
}
?>