<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * For MaxSite CMS
 * SMF Integration
 * Author: (c) Bugo
 * Plugin URL: http://dragomano.ru/page/maxsite-cms-plugins
 */

# функция автоподключения плагина
function smf_integration_autoload()
{
	mso_hook_add('init', 'smf_integration_init'); # хук на инициализацию
	mso_hook_add('admin_init', 'smf_integration_admin_init'); # хук на админку
}

function smf_integration_mso_options() 
{
	mso_admin_plugin_options('plugin_smf_integration', 'plugins', 
		array(
			'smf_path' => array(
				'type' => 'text',
				'name' => t('Путь к форуму', __FILE__),
				'description' => t('Относительный путь. Например, если MaxSite в корне сайта, а форум в подпапке forum, так и указываем: /forum.', __FILE__),
				'default' => '/forum'
			),
			'smf_reg_type' => array(
				'type' => 'select',
				'name' => 'Используемый тип регистрации',
				'description' => '',
				'values' => 'nothing||' . t('Мгновенная', __FILE__) . '#activation||' . t('Активация по e-mail', __FILE__) . '#admin||' . t('Одобрение админом', __FILE__),
				'default' => 'nothing'
			),
		),
		t('Настройки интеграции с SMF', __FILE__),
		t('Укажите необходимые опции.', 'plugins')
	);
}

function smf_integration_uninstall()
{	
	mso_delete_option('plugin_smf_integration', 'plugins');
}

function smf_integration_init()
{
	global $MSO;

	$options = mso_get_option('smf_integration', 'plugins', array());
	$options['smf_path'] = isset($options['smf_path']) ? $options['smf_path'] : '/forum';
	$options['smf_reg_type'] = isset($options['smf_reg_type']) ? $options['smf_reg_type'] : 'nothing';
	$login = '';

	@ini_set('display_errors', 'off'); // Убираем ошибки
	require_once(getinfo('plugins_dir') . 'smf_integration/smf_2_api.php'); // Подключаем API

	if (is_login()) // Пользователь?
	{
		$login = $MSO->data['session']['users_login'];
		smfapi_login($login); // Для успешной авторизации логин пользователя на форуме должен совпадать с логином в CMS
	}
	elseif (is_login_comuser()) // Комментатор?
	{
		$comuser = is_login_comuser();
		$login = strtolower($comuser['comusers_email']);
		
		if (smfapi_login($login) == false)
		{
			$email = strtolower($comuser['comusers_email']);
			$pos = strpos($email, '@');
			$name = substr($email, 0, $pos); // Отсеиваем @домен, чтобы эльки не светились на форуме
			$name = $name . rand(1,10000);
			$regOptions = array(
				'member_name' => $name,
				'email' => $email,
				'password' => $comuser['comusers_password'],
				'password_check' => $comuser['comusers_password'],
				'require' => $options['smf_reg_type']
			);
			smfapi_registerMember($regOptions);
			smfapi_login($login);
		}

		$user = smfapi_getUserByEmail(strtolower($comuser['comusers_email']));
		$data = array();
		
		$nick = !empty($comuser['comusers_nik']) && $user['real_name'] != $comuser['comusers_nik'];
		$nick2 = true;
		if (!empty($comuser['comusers_nik']))
		{
			$user2 = smfapi_getUserByUsername($comuser['comusers_nik']);
			$nick2 = $user2['real_name'] != $comuser['comusers_nik'];
		}
		if ($nick && $nick2) $data['real_name'] = $comuser['comusers_nik']; // Копируем ник комментатора для учётки на форуме
		else $data['real_name'] = $comuser['comusers_nik'] . '#' . rand(1, 1000);
		if (!empty($comuser['comusers_url'])) {
			$data['website_url'] = $comuser['comusers_url']; // Копируем адрес сайта комментатора для учётки на форуме
			$data['website_title'] = $comuser['comusers_url'];
		}
		if (!empty($comuser['comusers_avatar_url'])) $data['avatar'] = $comuser['comusers_avatar_url']; // Копируем ссылку на аватар комментатора для учётки на форуме
		if (!empty($data)) smfapi_updateMemberData($user['id_member'], $data);
	}
	else
	{
		if (!empty($_POST['comments_email']) && !empty($_POST['comments_password']))
		{
			$email = strtolower($_POST['comments_email']);
			$password = $_POST['comments_password'];
			$user = smfapi_getUserByEmail($email); // Получаем сведения о юзере по его эльке

			if (!empty($user['id_member'])) // Если у юзера есть id, то логинимся
				smfapi_login($email);
		}
	}

	if (mso_segment(1) == 'logout')
		smfapi_logout($login); // Разлогиниваемся...
}

// При удалении комментаторов удаляем и их учётки на форуме
function smf_integration_admin_init() 
{
    if ($post = mso_check_post(array('f_session_id', 'f_delete_submit', 'f_check_comusers')))
	{
		$all_check_comusers = $post['f_check_comusers'];
		
		$del_ids = array();
		foreach ($all_check_comusers as $id_com => $val)
			if ($val) $del_ids[] = $id_com;

		if (isset($del_ids[0]))
		{
			$CI = & get_instance();
			$CI->db->select('comusers_email');
			$CI->db->from('comusers');
			$CI->db->order_by('comusers_id');
			$CI->db->limit(count($del_ids));
			
			$query = $CI->db->get();
			
			foreach ($query->result_array() as $row)
			{
				$user = smfapi_getUserByEmail(strtolower($row['comusers_email']));
				$del_users[] = $user['id_member'];
			}

			smfapi_deleteMembers($del_users);
		}
	}
}

### end file