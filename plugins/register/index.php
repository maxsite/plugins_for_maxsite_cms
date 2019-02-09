<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */



function register_autoload()
{
	mso_create_allow('register_edit', t('Админ-доступ к редактированию register', __FILE__));
	mso_create_allow('register_invite', t('Рассылка инвайтов', __FILE__));
	mso_hook_add( 'admin_init', 'register_admin_init');
	//mso_register_widget('register_widget', t('register', __FILE__)); // Виджет не доделан. Главным образом потому, что я не знаю, что в нём должно быть.
	mso_hook_add('custom_page_404', 'register_custom_page_404');
}



function register_activate($args = array())
{
	//Нам надо что-нибудь с этим делать?
	return $args;
}



function register_uninstall($args = array())
{
	mso_delete_option_mask('register_widget_', 'plugins'); // удалим созданные опции
	mso_remove_allow('register_edit'); // удалим созданные разрешения
	return $args;
}



function register_admin_init($args = array())
{
	if ( mso_check_allow('register_edit') )
	{
		$this_plugin_url = 'register';
		mso_admin_menu_add('plugins', $this_plugin_url, t('Регистрация на сайте', __FILE__));
		mso_admin_url_hook ($this_plugin_url, 'register_admin_page');
	}
	if ( mso_check_allow('register_invite') )
	{
		$this_plugin_url = 'invite';
		mso_admin_menu_add('plugins', $this_plugin_url, t('Рассылка инвайтов', __FILE__));
		mso_admin_url_hook ($this_plugin_url, 'register_invite');
	}

	return $args;
}



function register_admin_page($args = array())
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('register_edit') )
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('register', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('register', __FILE__) . ' - " . $args; ' );
	require(getinfo('plugins_dir') . 'register/admin.php');
}



function register_invite($args = array())
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('register_invite') )
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('invite', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('invite', __FILE__) . ' - " . $args; ' );
	require(getinfo('plugins_dir') . 'register/invite.php');
}



# функция, которая берет настройки из опций виджетов
function register_widget($num = 1)
{
	$widget = 'register_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции

	// заменим заголовок, чтобы был в  h2 class="box"
	if ( isset($options['header']) and $options['header'] )
		$options['header'] = '<h2 class="box"><span>' . $options['header'] . '</span></h2>';
	else $options['header'] = '';

	return register_widget_custom($options, $num);
}



# форма настройки виджета
# имя функции = виджет_form
function register_widget_form($num = 1)
{
	$widget = 'register_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = mso_get_option($widget, 'plugins', array());

	if ( !isset($options['header']) ) $options['header'] = '';

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');

	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '.
			form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;

	return $form;
}



# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function register_widget_update($num = 1)
{
	$widget = 'register_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');

	if ( $options != $newoptions )
		mso_add_option($widget, $newoptions, 'plugins');
}



function register_widget_custom($options = array(), $num = 1)
{
	// кэш
	$cache_key = 'register_widget_custom' . serialize($options) . $num;
	$k = mso_get_cache($cache_key);
	if ($k) return $k; // да есть в кэше

	$out = '';
	if ( !isset($options['header']) ) $options['header'] = '';

	mso_add_cache($cache_key, $out); // сразу в кэш добавим

	return $out;
}



function register_custom_page_404($args = false)
{
	$options = mso_get_option('register', 'plugins', array());
	$options['reg_comusers']    = isset($options['reg_comusers'])    ? (int)$options['reg_comusers']    : 1;
	$options['reg_users']       = isset($options['reg_users'])       ? (int)$options['reg_users']       : 0;
	$options['comusers_invite'] = isset($options['comusers_invite']) ? (int)$options['comusers_invite'] : 0;
	$options['users_invite']    = isset($options['users_invite'])    ? (int)$options['users_invite']    : 0;
	$options['slug_comusers']   = isset($options['slug_comusers'])   ?      $options['slug_comusers']   : 'register';
	$options['slug_users']      = isset($options['slug_users'])      ?      $options['slug_users']      : 'register_users';
	$options['comusers_legend'] = isset($options['comusers_legend']) ?      $options['comusers_legend'] : '<h3>Регистрация комментатора</h3>';
	$options['users_legend']    = isset($options['users_legend'])    ?      $options['users_legend']    : '<h3>Регистрация пользователя</h3>';

	if ( (mso_segment(1) == $options['slug_comusers']) or (mso_segment(1) == $options['slug_users']) )
	{
		require( getinfo('plugins_dir') . 'register/register.php' ); // подключили свой файл вывода
		return true; // выходим с true
	}

	return $args;
}


