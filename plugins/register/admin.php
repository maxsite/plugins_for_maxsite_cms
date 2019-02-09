<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	mso_cur_dir_lang(__FILE__);
	$CI = & get_instance();

	$options_key = 'register';

	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		$options = array();
		$options['reg_comusers']    = isset( $post['f_reg_comusers'])    ? 1 : 0;
		$options['reg_users']       = isset( $post['f_reg_users'])       ? 1 : 0;
		$options['comusers_invite'] = isset( $post['f_comusers_invite']) ? 1 : 0;
		$options['users_invite']    = isset( $post['f_users_invite'])    ? 1 : 0;
		$options['slug_comusers']   = isset( $post['f_slug_comusers'])   ? $post['f_slug_comusers']   : 'register';
		$options['slug_users']      = isset( $post['f_slug_users'])      ? $post['f_slug_users']      : 'register_users';
		$options['comusers_legend'] = isset( $post['f_comusers_legend']) ? $post['f_comusers_legend'] : '<h3>Регистрация комментатора</h3>';
		$options['users_legend']    = isset( $post['f_users_legend'])    ? $post['f_users_legend']    : '<h3>Регистрация пользователя</h3>';

		mso_add_option($options_key, $options, 'plugins');
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}

	echo '<h1>'. t('Плагин Register'). '</h1><p class="info">'. t('С помощью этого плагина вы можете настраивать регистрацию на сайте.'). '</p>';

	$options = mso_get_option($options_key, 'plugins', array());
	$options['reg_comusers']    = isset($options['reg_comusers'])    ? (int)$options['reg_comusers']    : 1;
	$options['reg_users']       = isset($options['reg_users'])       ? (int)$options['reg_users']       : 0;
	$options['comusers_invite'] = isset($options['comusers_invite']) ? (int)$options['comusers_invite'] : 0;
	$options['users_invite']    = isset($options['users_invite'])    ? (int)$options['users_invite']    : 0;
	$options['slug_comusers']   = isset($options['slug_comusers'])   ?      $options['slug_comusers']   : 'register';
	$options['slug_users']      = isset($options['slug_users'])      ?      $options['slug_users']      : 'register_users';
	$options['comusers_legend'] = isset($options['comusers_legend']) ?      $options['comusers_legend'] : '<h3>Регистрация комментатора</h3>';
	$options['users_legend']    = isset($options['users_legend'])    ?      $options['users_legend']    : '<h3>Регистрация пользователя</h3>';

	$form = '';

	$form .= '<h2>' . t('Настройки', 'plugins') . '</h2>';

	$chk = $options['reg_comusers'] ? ' checked="checked"  ' : '';
	$form .= '<p><label><input name="f_reg_comusers" type="checkbox" ' . $chk . '> <strong>' . t('Регистрация комментаторов.') . '</strong></label><br />';
	$form .= t('Если отмечено, разрешаем регистрацию комментаторов.'). '</p>';
	$chk = $options['comusers_invite'] ? ' checked="checked"  ' : '';
	$form .= '<p><label><input name="f_comusers_invite" type="checkbox" ' . $chk . '> <strong>' . t('Комментаторы по инвайтам') . '</strong></label><br />';
	$form .= t('Если отмечено, комментаторы могут зарегистрироваться только по инвайтам.'). '</p>';

	$chk = $options['reg_users'] ? ' checked="checked"  ' : '';
	$form .= '<p>&nbsp;</p><p><label><input name="f_reg_users" type="checkbox" ' . $chk . '> <strong>' . t('Регистрация пользователей.') . '</strong></label><br />';
	$form .= t('Если отмечено, разрешаем регистрацию пользователей, имеющих доступ в админку.'). '</p>';
	$chk = $options['users_invite'] ? ' checked="checked"  ' : '';
	$form .= '<p><label><input name="f_users_invite" type="checkbox" ' . $chk . '> <strong>' . t('Пользователи по инвайтам.') . '</strong></label><br />';
	$form .= t('Если отмечено, пользователи могут зарегистрироваться только по инвайтам.'). '</p>';

	$form .= '<p>&nbsp;</p><p><label><input name="f_slug_comusers" type="text" value="' . $options['slug_comusers'] . '" /> <strong>' . t('Ссылка страницы регистрации для комментаторов.') . '</strong></label></p>';
	$form .= '<p>&nbsp;</p><p><label><input name="f_slug_users" type="text" value="' . $options['slug_users'] . '" /> <strong>' . t('Ссылка страницы регистрации для пользователей. Может совпадать с предыдущей ссылкой.') . '</strong></label></p>';

	$form .= '<h3>'.t('Текст, предваряющий регистрацию комментатора',__FILE__).'</h3><textarea name="f_comusers_legend" rows="7" style="width: 99%;">';
	$form .= htmlspecialchars($options['comusers_legend']);
	$form .= '</textarea>';

//t('Если вы уже зарегистрированы как комментатор или хотите зарегистрироваться, укажите пароль и свой действующий email. <br />(<i>При регистрации на указанный адрес придет письмо с кодом активации и ссылкой на ваш персональный аккаунт, где вы сможете изменить свои данные, включая адрес сайта, ник, описание, контакты и т.д.</i>)')
	$form .= '<h3>'.t('Текст, предваряющий регистрацию пользователя',__FILE__).'</h3><textarea name="f_users_legend" rows="7" style="width: 99%;">';
	$form .= htmlspecialchars($options['users_legend']);
	$form .= '</textarea>';

//t('Если вы или хотите зарегистрироваться как пользователь, укажите пароль и свой действующий email.')
	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<br><input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;"></form>';

