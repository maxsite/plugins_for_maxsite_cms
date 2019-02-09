<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

function __m($mail, $type, $url)
{
	$subject = 'Приглашение на регистрацию на сайте';
	$message  = 'Здравствуйте.'.NR.'Вам отправлено приглашение на регистрацию на сайте '.getinfo('siteurl').NR;
	$message .= 'Чтобы зарегистрироваться на сайте в качестве '.$type.', посетите адрес'.NR;
	$message .= getinfo('siteurl').$url.NR;
	$message .= 'Этот адрес действует только для '.$mail;
	return mso_mail($mail, $subject, $message, false, array('invite' => true));
}

	mso_cur_dir_lang(__FILE__);
	$CI = & get_instance();
	$options_key = 'register';
	$options = mso_get_option($options_key, 'plugins', array());
	$options['comusers_invite'] = isset($options['comusers_invite']) ? (int)$options['comusers_invite'] : 0;
	$options['users_invite']    = isset($options['users_invite'])    ? (int)$options['users_invite']    : 0;
	$options['slug_comusers']   = isset($options['slug_comusers'])   ?      $options['slug_comusers']   : 'register';
	$options['slug_users']      = isset($options['slug_users'])      ?      $options['slug_users']      : 'register_users';



	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_mail_users')) and $options['users_invite'] )
	{
		mso_checkreferer();
		$mail = trim($post['f_mail_users']);
		if (mso_valid_email($mail))
		{
			$url = $options['slug_users'].'/'.mso_md5($mail.$options['slug_users']);
			if (__m($mail, 'пользователя', $url))
				echo '<div class="update"><h3>' . t('Обновлено!', 'plugins') . '</h3>В качестве инвайта на регистрацию пользователя на e-mail '.$mail.' должна будет отправиться строка '.$url.'</div>';
			else
				echo '<div class="error">Ошибка отправки инвайта</div>';
		}
	}
	if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_mail_comusers')) and $options['comusers_invite'] )
	{
		mso_checkreferer();
		$mail = trim($post['f_mail_comusers']);
		if (mso_valid_email($mail))
		{
			$url = $options['slug_comusers'].'/'.mso_md5($mail.$options['slug_comusers']);
			if (__m($mail, 'комментатора', $url))
				echo '<div class="update"><h3>' . t('Обновлено!', 'plugins') . '</h3>В качестве инвайта на регистрацию комментатора на e-mail '.$mail.' должна будет отправиться строка '.$url.'</div>';
			else
				echo '<div class="error">Ошибка отправки инвайта</div>';
		}
	}

	echo '<h1>'. t('Плагин Register'). '</h1><p class="info">'. t('С помощью этого плагина вы можете рассылать приглашения на регистрацию на этом сайте.'). '</p>';

	$form  = '<h2>' . t('Инвайты пользователям', 'plugins') . '</h2><form action="" method="post">' . mso_form_session('f_session_id');
	$form .= '<p>&nbsp;</p><p><label><input name="f_mail_users" type="text" value="" /> <strong>' . t('E-mail.') . '</strong></label></p>';
	$form .= '<input type="submit" name="f_submit" value="' . t('Отправить приглашение пользователю') . '" style="margin: 25px 0 5px 0;"></form>';

	if ( $options['users_invite'] ) echo $form;

	$form  = '<h2>' . t('Инвайты комментаторам', 'plugins') . '</h2><form action="" method="post">' . mso_form_session('f_session_id');
	$form .= '<p>&nbsp;</p><p><label><input name="f_mail_comusers" type="text" value="" /> <strong>' . t('E-mail.') . '</strong></label></p>';
	$form .= '<input type="submit" name="f_submit" value="' . t('Отправить приглашение комментатору') . '" style="margin: 25px 0 5px 0;"></form>';

	if ( $options['comusers_invite'] ) echo $form;
