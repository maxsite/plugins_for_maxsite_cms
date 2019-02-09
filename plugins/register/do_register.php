<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require('functions.php');

function do_register($post, $type, $args = array())
{
	// Это на будущее
	if ( !isset($args['page_title']) )	$args['page_title'] = '';
	if ( !isset($args['css_ok']) )		$args['css_ok'] = 'comment-ok';
	if ( !isset($args['css_error']) )	$args['css_error'] = 'comment-err';

	if (!mso_checksession($post[$type.'_session']) )
		return '<div class="' . $args['css_error']. '">'. t('Ошибка сессии! Обновите страницу'). '</div>';

	// если этот хук возвращает false, значит капча неверная
	if (!mso_hook('comments_new_captcha', true))
	{
		// если определен хук на неверную капчу, отдаем его
		if (mso_hook_present('comments_new_captcha_error'))
		{
			return mso_hook('comments_new_captcha_error');
		}
		else
		{
			return '<div class="' . $args['css_error']. '">'. t('Ошибка! Неверно введены нижние символы!'). '</div>';
		}
	}

	$email = mso_strip(trim($post[$type.'_email']));
	$nik = trim(mso_strip($post[$type.'_nik']));
	if ($type == 'users') $login = trim(mso_strip($post[$type.'_login']));
	$password = mso_strip($post[$type.'_password']);

	if ( $type == 'users' and !$login )
		return '<div class="' . $args['css_error']. '">'. t('Нужно указать Логин'). '</div>';

	if ( !$email )
		return '<div class="' . $args['css_error']. '">'. t('Нужно указать Email'). '</div>';

	if ( !$password )
		return '<div class="' . $args['css_error']. '">'. t('Нужно указать пароль'). '</div>';

	if ( !mso_valid_email($email) )
		return '<div class="' . $args['css_error']. '">'. t('Ошибочный Email'). '</div>';

	if (is_email_exists($email, $type))
		return '<div class="' . $args['css_error']. '">'. t('Такой E-mail уже зарегистрирован'). '</div>';
		//TODO на будущее давать ссылку на профиль… Или не надо, бо так смогут проверять мыло.
		//Что ли пробовать логинить при этом? Проверять мейл и пароль, и при удаче логинить и перебрасывать на профиль.
		//Да, когда в одной функции юзер и комюзер, есть некоторые сложности.

/*
	$row = $query->row_array(1);

		// пароль не нужно шифровать mso_md5
		if (isset($post['comuser_password_md']) and $post['comuser_password_md'])
		{
			if ($row['comusers_password'] != $comuser_password) // пароль неверный
				return '<div class="' . $args['css_error']. '">'. t('Неверный пароль'). '</div>';
		}
		else
		{
			if ($row['comusers_password'] != mso_md5($comuser_password)) // пароль неверный
				return '<div class="' . $args['css_error']. '">'. t('Неверный пароль'). '</div>';
		}

		$comusers_id = $row['comusers_id']; // получаем номер комюзера
*/

	if ($type == 'users' and is_users_login_exist($login) )
		return '<div class="' . $args['css_error']. '">'. t('Такой логин уже зарегистрирован'). '</div>';

	if ($nik and is_nik_exist($nik, $type))
		return '<div class="' . $args['css_error']. '">'. t('Такое имя уже зарегистрировано'). '</div>';

	$ins_data = array (
		$type.'_email' => $email,
		$type.'_password' => mso_md5($password) // Т.е. можно было не очищать пароль, но хрен с ним.
		);
	if ($type == 'users')
	{
		if (!$nik) $nik = $login;
		$ins_data[$type.'_login'] = $login;
		$ins_data[$type.'_levels_id'] = 1; // Ну а полномочия и группу пользователя пропишем жёстко.
		$ins_data[$type.'_groups_id'] = 2;
	}
	if ($nik) $ins_data[$type.'_nik'] = $nik;

	// генерируем случайный ключ активации
	$ins_data[$type.'_activate_key'] = mso_md5(rand());
	$ins_data[$type.'_date_registr'] = date('Y-m-d H:i:s');
	$ins_data[$type.'_last_visit'] = date('Y-m-d H:i:s');
	$ins_data[$type.'_ip_register'] = $_SERVER['REMOTE_ADDR'];

	$CI = &get_instance();
	$res = ($CI->db->insert($type, $ins_data)) ? '1' : '0';

	if ($res)
	{
		$id = $CI->db->insert_id(); // номер добавленной записи
		if ($type == 'comusers') // todo сделать точно так же одну функцию на регистрацию. Юзеру тоже неплохо бы слать сообщение.
		{
			//require_once( getinfo('common_dir') . 'comments.php' ); //Можно было бы просто функу перенести
			mso_email_message_new_comuser($id, $ins_data); // отправляем ему уведомление с кодом активации
		}
		if (!$nik) $CI->db->insert($type, array( $type.'_nik' => t('Комментатор ' . ' ' . $id) ));
	}
	else
		return '<div class="' . $args['css_error']. '">'. t('Ошибка регистрации'). '</div>';
	if ($type == 'comusers')
		return '<div class="' . $args['css_ok']. '">'. t('Поздравляем! Вы успешно зарегистрировались. Теперь на ваш e-mail придут дальнейшие инструкции по активации.', 'template'). '</div>';
	else
		return '<div class="' . $args['css_ok']. '">'. t('Поздравляем! Вы успешно зарегистрировались. Теперь вы можете залогиниться используя указанный вами логин и пароль.', __FILE__). '</div>';
}
