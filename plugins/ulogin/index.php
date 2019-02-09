<?php
/**
 * Plugin Name: uLogin - виджет авторизации через социальные сети
 * Plugin URI:  https://ulogin.ru/
 * Description: uLogin — это инструмент, который позволяет пользователям получить единый доступ к различным
 * Интернет-сервисам без необходимости повторной регистрации, а владельцам сайтов — получить дополнительный
 * приток клиентов из социальных сетей и популярных порталов (Google, Яндекс, Mail.ru, ВКонтакте, Facebook и др.)
 * Version:     2.0.0
 * Author:      uLoginTeam
 * Author URI:  https://ulogin.ru/
 * License:     GPL2
 */

if(!defined('BASEPATH'))
	exit('No direct script access allowed');
function ulogin_autoload() {
	if(function_exists('curl_init')) {
		mso_hook_add('init', 'ulogin_init');
		mso_hook_add('login_form_auth', 'ulogin_login_form_auth');
		mso_hook_add('page-comment-form', 'ulogin_auth_page_comment_form');
		mso_hook_add('users_add_out', 'ulogin_sync_form');
		mso_hook_add('admin_init', 'ulogin_admin_init');
		mso_hook_add('head', 'ulogin_head');
	}
}

function ulogin_activate($args = array()) {
	mso_create_allow('ulogin_edit', t('Админ-доступ к настройкам ulogin'));
	$CI = &get_instance();
	if(!$CI->db->table_exists('ulogintable')) {
		$charset = $CI->db->char_set ? $CI->db->char_set : 'utf8';
		$collate = $CI->db->dbcollat ? $CI->db->dbcollat : 'utf8_general_ci';
		$charset_collate = ' DEFAULT CHARACTER SET ' . $charset . ' COLLATE ' . $collate;
		$sql = "
		CREATE TABLE " . $CI->db->dbprefix . "ulogintable (
		`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
		`comusers_id` int(10) unsigned NOT NULL,
        `identity` varchar(255)  NOT NULL,
        `network` varchar(255)  NOT NULL,
		PRIMARY KEY (id)
		)" . $charset_collate;
		$CI->db->query($sql);
	}

	return $args;
}

function ulogin_head() {
	echo '<script src="//ulogin.ru/js/ulogin.js"></script>' . NR;
	$page = mso_current_url();
	$page = explode('/',$page);
	if($page[0] == 'users') {
		echo '<link type="text/css" rel="stylesheet" href="//ulogin.ru/css/providers.css">' . NR;
		echo '<script src="'.getinfo('plugins_url') . 'ulogin/js/ulogin_sync.js""></script>' . NR;
	}
}

function ulogin_mso_options() {
	mso_admin_plugin_options('plugin_ulogin', 'plugins', array(
		'ulogin_id1' => array('type' => 'text', 'name' => t('uLogin ID форма входа:'), 'description' => t('Идентификатор виджета в окне входа и регистрации. Пустое поле - виджет по умолчанию'), 'default' => ''),
		'ulogin_id2' => array('type' => 'text', 'name' => t('uLogin ID форма синхронизации:'), 'description' => t('Идентификатор виджета для синхронизации. Пустое поле - виджет по умолчанию'), 'default' => ''),
		'soclink' => array('type' => 'checkbox', 'name' => t('Сохранять ссылку на профиль:'), 'description' => t('Сохранять ссылку на страницу пользователя в соцсети при авторизации через uLogin'), 'default' => 1),
//		'notify' => array('type' => 'checkbox', 'name' => t('Отправлять письмо при регистрации нового пользователя:'), 'description' => t('Уведомляет по почте администратора сайта о регистрации нового пользователя и отправляет пользователю письмо с логином и паролем для авторизации'), 'default' => 0)
	));
}

function ulogin_uninstall($args = array()) {
	mso_delete_option('plugin_ulogin', 'plugins');
	mso_remove_allow('ulogin_edit');
	mso_delete_option_mask('ulogin_widget', 'plugins');

	return $args;
}

function ulogin_admin_init($args = array()) {
	if(mso_check_allow('ulogin_edit')) {
		$this_plugin_url = 'plugin_options/ulogin';
		mso_admin_menu_add('plugins', $this_plugin_url, t('uLogin'));
		mso_admin_url_hook($this_plugin_url, 'plugin_ulogin');
	}

	return $args;
}

function ulogin_login_form_auth($text = '') {
	require_once "ulogin.class.php";
	$text .= ulogin::getPanelCode(0);
	$text .= '[end]';

	return $text;
}

function ulogin_sync_form() {
	require_once(getinfo('common_dir') . 'common.php');
	$comuser_info = mso_get_comuser(mso_segment(2));
	if (getinfo('comusers_id') == $comuser_info[0]['comusers_id'] ){
		echo '<h2>'.t('Синхронизация аккаунтов').'</h2>';
		require_once "ulogin.class.php";
		echo ulogin::getPanelCode(1);
		echo '<p>'.t('Привяжите ваши аккаунты соц. сетей к личному кабинету для быстрой авторизации через любой из них').'</p>';
		echo '<h2>'.t('Привязанные аккаунты').'</h2>';
		echo ulogin::getuloginUserAccountsPanel();
		echo t('Вы можете удалить привязку к аккаунту, кликнув по значку');
	}
}

function ulogin_auth_page_comment_form($args = array()) {
	require_once "ulogin.class.php";
	echo ulogin::getPanelCode();
	return $args;
}

function ulogin_init($arg = array()) {
	if(isset($_POST['token'])) {
		require_once "ulogin.class.php";
		$user = ulogin::uloginGetUserFromToken($_POST['token']);
		if(!$user) {
			die(t('Ошибка работы uLogin:Не удалось получить данные о пользователе с помощью токена.') . ulogin::_get_back_url());
		}
		$u_user = json_decode($user, true);
		$check = ulogin::uloginCheckTokenError($u_user);
		if(!$check) {
			return false;
		}
		$ulogin = new Ulogin($u_user);
		$ulogin->initAutorith();
		unset($_POST['token']);
		mso_redirect('');
	}
	if(isset($_POST['identity'])) {
		try
		{
			$DB =& get_instance();
			$DB->db->query("DELETE FROM " . $DB->db->dbprefix . "ulogintable WHERE identity='" . $_POST['identity']. "'");
			echo json_encode(array(
				'answerType' => 'ok',
				'msg' => "Удаление привязки аккаунта ".$_POST['network']." успешно выполнено"
			));
			unset($_POST['identity']);
			unset($_POST['network']);
			exit;
		} catch (Exception $e) {
			echo json_encode(array(
				'answerType' => 'error',
				'msg' => "Ошибка при удалении аккаунта \n Exception: " . $e->getMessage()
			));
			unset($_POST['identity']);
			unset($_POST['network']);
			exit;
		}
	}

	return $arg;
}

