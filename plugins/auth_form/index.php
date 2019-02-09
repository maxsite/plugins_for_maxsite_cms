<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 *
 * Форк плагина "login_form"
 * Александр Шиллинг
 */

# функция автоподключения плагина
function auth_form_autoload()
{
	# регистрируем виджет
	mso_register_widget('auth_form_widget', t('Авторизация', __FILE__)); 
}

# функция выполняется при деинсталяции плагина
function auth_form_uninstall($args = array())
{	
	mso_delete_option_mask('auth_form_widget_', 'plugins' ); // удалим созданные опции

	return $args;
}

# функция, которая берет настройки из опций виджетов
function auth_form_widget($num = 1) 
{
	$out = '';
	$img_path = getinfo('plugins_url') . 'auth_form/img/';
	
	$widget = 'auth_form_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	// получаем доступ к CI
	$CI = &get_instance();

	$avatar_url = '';
	$grav_email = '';

	if (is_login())	{
		$CI->db->from('users');
		$CI->db->where('users_id', getinfo('users_id'));
		$CI->db->limit(1);
        	$query = $CI->db->get();

        	foreach ($query->result_array() as $rw) {
			if ($rw['users_avatar_url']) $avatar_url = $rw['users_avatar_url'];
			if ($rw['users_email']) $grav_email = $rw['users_email'];
		}
	} elseif (is_login_comuser()) {
		$CI->db->from('comusers');
		$CI->db->where('comusers_id', getinfo('comusers_id'));
		$CI->db->limit(1);
        	$query = $CI->db->get();

        	foreach ($query->result_array() as $rw) {
			if ($rw['comusers_avatar_url']) $avatar_url = $rw['comusers_avatar_url'];
			if ($rw['comusers_email']) $grav_email = $rw['comusers_email'];
		}
	}

	if (!$avatar_url) {
		if (!empty($_SERVER['HTTPS'])) {
			$avatar_url = "https://secure.gravatar.com/avatar.php?gravatar_id="
				. md5($grav_email)
				. "&amp;size=100";
		} else {
			$avatar_url = "http://www.gravatar.com/avatar.php?gravatar_id="
				. md5($grav_email)
				. "&amp;size=100";
		}
	}

	$avatar = $avatar_url;
		
	if (is_login())	{
		$out = '<p><strong>' . t('Здравствуйте,', __FILE__) . ' <a href="' . getinfo('siteurl') . 'admin' . '">' . getinfo('users_nik') . '</a></strong></p>';

		$out .= '<p><a href="' . getinfo('siteurl') . 'admin' . '"><img src="' . $avatar . '"></a></p>';

		$out .= '<p><a href="' . getinfo('siteurl') . 'admin/users/edit/' . getinfo('users_id') . '"><img src="' . $img_path . 'settings.png" alt="" title=""> ' . tf('профиль') . '</a> ';
		$out .= '<a href="' . getinfo('siteurl') . 'logout'.'"><img src="' . $img_path . 'logout.png" alt="" title=""></a> <a href="' . getinfo('siteurl') . 'logout'.'">' . t('Выйти', __FILE__) . '</a></p>';

	} elseif ($comuser = is_login_comuser()) {
		if (!$comuser['comusers_nik']) $cun = t('Здравствуйте!', __FILE__);
			else $out = '<p><strong>' . t('Здравствуйте,', __FILE__) . ' <a href="' . getinfo('siteurl') . 'users/' . $comuser['comusers_id'] . '">' . $comuser['comusers_nik'] . '</a></strong></p>';

		$out .= '<p><img src="' . $avatar . '"></p>';

		$out .= '<p><a href="' . getinfo('siteurl') . 'users/' . $comuser['comusers_id'] . '"><img src="' . $img_path . 'settings.png" alt="" title=""> ' . tf('профиль') . '</a> ';
		$out .= '<a href="' . getinfo('siteurl') . 'logout'.'"><img src="' . $img_path . 'logout.png" alt="" title=""></a> <a href="' . getinfo('siteurl') . 'logout'.'">' . t('Выйти', __FILE__) . '</a></p>';
	} else {
		$after_form = (isset($options['after_form'])) ? $options['after_form'] : '';
		$before_form = (isset($options['before_form'])) ? $options['before_form'] : '';

		$out = '<p><strong>' . t('Здравствуйте, гость!', __FILE__) . '</strong></p>';

		$out .= $before_form;
		
		$out .= mso_login_form(array( 
			'login' => t('Логин (e-mail):', __FILE__) . ' ', 
			'password' => t('Пароль:', __FILE__) . ' ', 
			'submit' => '', 
			'form_end' => '',
			'submit_end' => ''
			), 
			getinfo('siteurl') . mso_current_url(), false);

		if (isset($options['registration']) and $options['registration']) {
			$registration = '<p><img src="' . $img_path . 'registration.png" alt="" title=""> <a href="' . getinfo('siteurl') . 'registration">' .  t('Регистрация', __FILE__) . '</a></p>';
		} else $registration = '';

		if (isset($options['lostpassword']) and $options['lostpassword']) {
			$lostpassword = '<p><img src="' . $img_path . 'lostpassword.png" alt="" title=""> <a href="' . getinfo('siteurl') . 'password-recovery">' .  t('Забыли пароль?', __FILE__) . '</a></p>';
		} else $lostpassword = '';

		$out .= $registration . $lostpassword . $after_form;
	}
	
	if ($out) {
		if ( isset($options['header']) and $options['header'] ) 
			$out = mso_get_val('widget_header_start', '<div class="mso-widget-header"><span>')
			. '<image src="' . getinfo('siteurl') . 'application/maxsite/plugins/auth_form/img/user.png" /> ' 
			. $options['header'] . mso_get_val('widget_header_end', '</span></div>') . $out;
	}

	return $out;	
}


# форма настройки виджета 
# имя функции = виджет_form
function auth_form_widget_form($num = 1) 
{
	$widget = 'auth_form_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['after_form']) ) $options['after_form'] = '';
	if ( !isset($options['registration']) ) $options['registration'] = '0';
	if ( !isset($options['lostpassword']) ) $options['lostpassword'] = '0';
	if ( !isset($options['before_form']) ) $options['before_form'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок', __FILE__), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'])), t('Укажите заголовок виджета', __FILE__));
	
	$form .= mso_widget_create_form(t('Регистрация', __FILE__), form_dropdown( $widget . 'registration', 
			array( 
				'0' => t('Не показывать ссылку', __FILE__), 
				'1' => t('Показывать ссылку', __FILE__), 
				), 
				$options['registration']), t('Ссылка будет отображена под кнопкой входа', __FILE__));

	$form .= mso_widget_create_form(t('Забыли пароль?', __FILE__), form_dropdown( $widget . 'lostpassword', 
			array( 
				'0' => t('Не показывать ссылку', __FILE__), 
				'1' => t('Показывать ссылку', __FILE__), 
				), 
				$options['lostpassword']), t('Ссылка будет отображена под кнопкой входа', __FILE__));
	
	$form .= mso_widget_create_form(t('Текст до формы', __FILE__), form_input( array( 'name'=>$widget . 'before_form', 'value'=>$options['before_form'])), t('Можно использовать HTML', __FILE__));

	$form .= mso_widget_create_form(t('Текст после формы', __FILE__), form_input( array( 'name'=>$widget . 'after_form', 'value'=>$options['after_form'])), t('Можно использовать HTML', __FILE__));

	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function auth_form_widget_update($num = 1) 
{
	$widget = 'auth_form_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['after_form'] = mso_widget_get_post($widget . 'after_form');
	$newoptions['registration'] = mso_widget_get_post($widget . 'registration');
	$newoptions['lostpassword'] = mso_widget_get_post($widget . 'lostpassword');
	$newoptions['before_form'] = mso_widget_get_post($widget . 'before_form');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins' );
}

# End of file
