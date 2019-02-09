<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Плагин для MaxSite CMS
 * работа с Web Money
 * (c) http://kerzoll.org.ua/
 */

# функция автоподключения плагина
function wm_control_autoload($args = array())
{
	mso_create_allow('wm_control_edit', t('Админ-доступ к редактированию WM', __FILE__));
	mso_hook_add( 'admin_init', 'wm_control_admin_init'); # хук на адинку
	mso_hook_add( 'init', 'wm_control_init'); # хук на инициализацию
	mso_hook_add( 'content', 'wm_control_content'); # хук на контент
	mso_hook_add( 'custom_page_404', 'wm_control_custom_page_404');

	return $args;
}

# функция выполняется при активации (вкл) плагина
function wm_control_activate($args = array())
{

	$CI = & get_instance();

	$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
	$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
	$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;

	if ( !$CI->db->table_exists('wm_check_trade')) // нет таблицы wm_check_trade
	{
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "wm_check_trade (
		num bigint(20) NOT NULL auto_increment,
		orderid bigint(20) NOT NULL default '0',
		wminvid bigint(20) NOT NULL default '0',
		customerwmid bigint(14) NOT NULL default '0',
		storepurse varchar(14) NOT NULL default '',
		amount varchar(25) NOT NULL default '',
		desc_ varchar(255) NOT NULL default '',
		state int(1) NOT NULL default '0',
		time varchar(255) NOT NULL default '',
		ip bigint(20) NOT NULL default '0',
		PRIMARY KEY (num)
		)" . $charset_collate;

		$CI->db->query($sql);
	}

	if ( !$CI->db->table_exists('wm_give_money')) // нет таблицы wm_give_money
	{
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "wm_give_money (
		num bigint(20) NOT NULL auto_increment,
		trunid bigint(20) NOT NULL default '0',
		opertype int(1) NOT NULL default '0',
		period int(3) NOT NULL default '0',
		wminvid bigint(20) NOT NULL default '0',
		pursesrc varchar(14) NOT NULL default '',
		pursedest varchar(14) NOT NULL default '',
		amount varchar(25) NOT NULL default '',
		comiss varchar(25) NOT NULL default '',
		desc_ varchar(255) NOT NULL default '',
		time bigint(20) NOT NULL default '0',
		ip bigint(20) NOT NULL default '0',
		PRIMARY KEY (num)
		)" . $charset_collate;

		$CI->db->query($sql);
	}

	return $args;
}

# функция выполняется при деинсталяции плагина
function wm_control_uninstall($args = array())
{
	mso_remove_allow('wm_control_edit'); // удалим созданные разрешения

	// удалим таблицу
	$CI = &get_instance();
	$CI->load->dbforge();
	$CI->dbforge->drop_table('wm_check_trade');
	$CI->dbforge->drop_table('wm_give_money');
	return $args;
}

# функция выполняется при указаном хуке init
function wm_control_init($args = array()){
	global $MSO;

	$CI = & get_instance();

	$key_options = 'plugin_wm_control';
	$options = mso_get_option($key_options, 'plugins', array());

}

# функция выполняется при указаном хуке admin_init
function wm_control_admin_init($args = array())
{
	if ( mso_check_allow('plugin_wm_control') )//Проверяем на вкл/выкл плагина
	{
		$this_plugin_url = 'plugin_wm_control'; // url и hook
		mso_admin_menu_add('plugins', $this_plugin_url, t('Управление Web Money', __FILE__));
		mso_admin_url_hook ($this_plugin_url, 'wm_control_admin_page');
	}

	return $args;
}


# функция вызываемая при хуке, указанном в mso_admin_url_hook
function wm_control_admin_page($args = array()){
	$CI = &get_instance();
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('plugin_wm_control') )
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('Управление Web Money', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('Управление Web Money', __FILE__) . ' - " . $args; ' );

	require(getinfo('plugins_dir') . 'wm_control/head.php');
	require(getinfo('plugins_dir') . 'wm_control/config.php');

	switch (mso_segment(3)){		case 'utilits':		require(getinfo('plugins_dir') . 'wm_control/utilits.php');
							break;
		case 'edit':		require(getinfo('plugins_dir') . 'wm_control/edit.php');
							break;
		default:			require(getinfo('plugins_dir') . 'wm_control/utilits.php');
							break;	}
}

function wm_control_content_callback($match){
	return "Форма для мерчанта ".$match[1];}

function wm_control_content($text){	$preg = '~\[wm\](.*?)\[\/wm\]~si';
	$text = preg_replace_callback($preg, "wm_control_content_callback" , $text);
	$text = str_ireplace('[wm]', '', $text);
	$text = str_ireplace('[/wm]', '', $text);
	return $text;
}

function wm_control_custom_page_404($text){	global $MSO;

	if (mso_segment(1) == 'pay_form' or mso_segment(1) == 'pay_good' or mso_segment(1) == 'pay_fail' or mso_segment(1) == 'pay_valid'){
		$file = mso_segment(1);
		$old_template = $MSO->config['template'];
		$old_path_templates = $MSO->config['templates_dir'];

		$MSO->config['template'] = 'skin';
		$MSO->config['templates_dir'] = getinfo('plugins_dir').'wm_control/';

		require(getinfo('plugins_dir').'wm_control/skin/index.php');

		return mso_segment(1);
	}else{		return;	}
}

?>