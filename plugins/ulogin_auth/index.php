<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * 
 * 
 */


# функция автоподключения плагина
function ulogin_auth_autoload()
{
  mso_register_widget('ulogin_form_widget', t('Форма логина ulogin', 'plugins')); 

	// должна быть CURL
	if (function_exists('curl_init'))
	{
		$options = mso_get_option('plugin_ulogin_auth', 'plugins', array());
		$widget_fcomments_priority = (isset($options['widget_fcomments_priority'])) ? $options['widget_fcomments_priority'] : 10; 
		$widget_flogin_priority = (isset($options['widget_flogin_priority'])) ? $options['widget_flogin_priority'] : 10; 
		
		mso_hook_add('init', 'ulogin_auth_init');
		mso_hook_add('page-comment-form', 'ulogin_auth_page_comment_form', $widget_fcomments_priority); # хук на форму комментов
		mso_hook_add('login_form_auth', 'ulogin_auth_login_form_auth', $widget_flogin_priority); # хук на форму логина
		mso_hook_add('admin_init', 'ulogin_auth_admin_init'); # хук на админку
		mso_hook_add( 'head', 'ulogin_auth_head');
    mso_hook_add('custom_page_404', 'ulogin_auth_custom_page_404');		
	}
	//mso_register_widget('ulogin_auth_widget', t('Форма ulogin Auth')); 	
}

# функция выполняется при активации (вкл) плагина
function ulogin_auth_activate($args = array())
{	
	mso_create_allow('ulogin_auth_edit', t('Админ-доступ к настройкам ulogin'));
	return $args;
}

# функция выполняется при деинсталяции плагина
function ulogin_auth_uninstall($args = array())
{	
	mso_delete_option('plugin_ulogin_auth', 'plugins' ); // удалим созданные опции
	mso_remove_allow('ulogin_auth_edit'); // удалим созданные разрешения
	mso_delete_option_mask('ulogin_auth_widget', 'plugins' ); // 
	mso_delete_option_mask('ulogin_form_widget_', 'plugins'); // удалим созданные опции

	return $args;
}

# подключим страницу опций, как отдельную ссылку
function ulogin_auth_admin_init($args = array()) 
{
    
	if ( mso_check_allow('ulogin_auth_edit') ) 
	{
		$this_plugin_url = 'plugin_options/ulogin_auth'; // url и hook
		mso_admin_menu_add('plugins', $this_plugin_url, t('ulogin Auth'));
		mso_admin_url_hook ($this_plugin_url, 'plugin_ulogin_auth');
	}
	
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function ulogin_auth_mso_options() 
{
	
	if ( !mso_check_allow('ulogin_auth_edit') ) 
	{
		echo t('Доступ запрещен');
		return;
	}
	
	$curl = (!function_exists('curl_init')) ? '<span style="color:red">' . t('Для работы плагина требуется наличие включенной PHP-библиотеки CURL!') . '</span><br><br>' : '';
	
	mso_admin_plugin_options('plugin_ulogin_auth', 'plugins', 
		array(
				'widget_type' => array(
						'type' => 'select', 
						'name' => t('Ссылка авторизации для формы комментариев в виде:'), 
						'description' => t('Отображение ссылки авторизации для формы комментариев в виде'),
						'values' => t('small||Кнопки # panel||Большие кнопки # window||Одна кнопка'),
						'default' => '1'
					),	
				'auth_title' => array(
						'type' => 'text', 
						'name' => t('Текст ссылки авторизации для формы комментариев:'), 
						'description' => t('Укажите текст ссылки авторизации для формы комментариев. Работает html'),
						'default' => 'ulogin'
					),					
				'widget_fcomments_priority' => array(
						'type' => 'text', 
						'name' => t('Приоритет ссылки авторизации для формы комментариев:'), 
						'description' => t('Укажите приоритет ссылки авторизации для формы логина. Чем меньше приоритет, тем дальше от начала будет ссылка. Чем больше - тем первее. Например, 10 - самый конец, 99 - самое начало'),
						'default' => '10'
					),	
					
				'widget_type_flogin' => array(
						'type' => 'select', 
						'name' => t('Ссылка авторизации для формы логина в виде:'), 
						'description' => t('Отображение ссылки авторизации для формы логина в виде'),
						'values' => t('small||Кнопки # panel||Большие кнопки # window||Одна кнопка'),
						'default' => '1'
					),	
					
				'auth_title_flogin' => array(
						'type' => 'text', 
						'name' => t('Текст ссылки авторизации для формы логина:'), 
						'description' => t('Укажите текст ссылки авторизации для формы логина. Работает html'),
						'default' => 'ulogin'
					),		
					
				'widget_flogin_priority' => array(
						'type' => 'text', 
						'name' => t('Приоритет ссылки авторизации для формы логина:'), 
						'description' => t('Укажите приоритет ссылки авторизации для формы логина. Чем меньше приоритет, тем дальше от начала будет ссылка. Чем больше - тем первее. Например, 10 - самый конец, 99 - самое начало'),
						'default' => '10'
					),	
					
				'providers_set' => array(
						'type' => 'text', 
						'name' => t('Отображаемые провайдеры:'), 
						'description' => t('Укажите через запятую доступных провайдеров:<br>') .
						'vkontakte,odnoklassniki,mailru,facebook,twitter,google,yandex,livejournal, youtube', 
						'default' => 'vkontakte,odnoklassniki,mailru,facebook,twitter'
					),
					
				'providers_hidden' => array(
						'type' => 'text', 
						'name' => t('Скрытые провайдеры:'), 
						'description' => t('Укажите через запятую провайдеров, которые появятся при наведении:<br>'),
						'default' => 'google,yandex,livejournal,youtube'
					),		
								
				'fields' => array(
						'type' => 'text', 
						'name' => t('Обязательные поля:'), 
						'description' => t('Укажите через запятую поля, которые будут запрошены у автора в случае отсутствия:<br>(email)<br>'),
						'default' => ''
					),									

			),
		t('Настройки плагина ulogin Auth'), // титул
		t('Авторизация на сайте через сервис <a href="http://ulogin.ru">ulogin</a>')
		. $curl
		. t('<br><b>Авторизация может работать без e-mail адреса!</b>')   // инфо
	);	
	
}


function ulogin_auth_head($args = array())
{
	if (!is_login() and !is_login_comuser())
		echo '<script src="http://ulogin.ru/js/ulogin.js"></script>';

	return $args;
}

# хук на форму логина
function ulogin_auth_login_form_auth($text = '') 
{
	$text .= '';
	
	$options = mso_get_option('plugin_ulogin_auth', 'plugins', array() ); // получаем опции
	if (!isset($options['widget_type_flogin'])) $options['widget_type_flogin'] = 'small'; 
    $widget_type =  $options['widget_type_flogin'];
	 
	if (!isset($options['auth_title_flogin']) or empty($options['auth_title_flogin'])) $options['auth_title_flogin'] = 'ulogin';  
	
	if (!isset($options['providers_set'])) $options['providers_set'] = 'vkontakte,odnoklassniki,mailru,facebook,twitter';
	$providers_set = $options['providers_set'];
	
	if (!isset($options['providers_hidden'])) $options['providers_hidden'] = 'google,yandex,livejournal,youtube';
	$providers_hidden = $options['providers_hidden'];	

	if (!isset($options['fields'])) $options['fields'] = '';  
  if ($options['fields']) $fields = 'fields=' . $options['fields'] . ';';
  else $fields = '';
	
	$curpage = getinfo('siteurl') . mso_current_url();
	$current_url = getinfo('siteurl') . 'maxsite-ulogin-auth?' . $curpage;
	
	if ( ($widget_type == 'small') or ($widget_type == 'panel'))
     $text .= '<div id="uLogin1"x-ulogin-params="display='
        . $widget_type . ';'. $fields .'optional=first_name,last_name,photo,nickname,email;providers='.$providers_set.';hidden='.$providers_hidden.';redirect_uri='
        . urlencode( $current_url ) . '"></div>';
  else
      $text .= '<a href="#" id="uLogin1" x-ulogin-params="display=window;'. $fields .'optional=first_name,last_name,photo,nickname,email;redirect_uri='
        . urlencode( $current_url ) . '"><img src="http://ulogin.ru/img/button.png" width=187 height=30 alt="МультиВход"/></a>';   
	
	$text .= '[end]';

	return $text;
}

# сообщение в форме комментариев
function ulogin_auth_page_comment_form($args = array()) 
{
	$options = mso_get_option('plugin_ulogin_auth', 'plugins', array() ); // получаем опции
	if (!isset($options['widget_type'])) $options['widget_type'] = 'small'; 
    $widget_type =  $options['widget_type'];
	
	if (!isset($options['auth_title']) or empty($options['auth_title'])) $options['auth_title'] = 'ulogin';  
	$auth_title = $options['auth_title'];
	
	if (!isset($options['providers_set'])) $options['providers_set'] = 'vkontakte,odnoklassniki,mailru,facebook,twitter';
	$providers_set = $options['providers_set'];
	
	if (!isset($options['providers_hidden'])) $options['providers_hidden'] = 'google,yandex,livejournal,youtube';
	$providers_hidden = $options['providers_hidden'];	

	if (!isset($options['fields'])) $options['fields'] = '';  
  if ($options['fields']) $fields = 'fields=' . $options['fields'] . ';';
  else $fields = '';
	
	$curpage = getinfo('siteurl') . mso_current_url();
	$current_url = getinfo('siteurl') . 'maxsite-ulogin-auth?' . $curpage;
	
	if ( ($widget_type == 'small') or ($widget_type == 'panel'))
     echo '
       <div id="uLogin2"x-ulogin-params="display='
        . $widget_type . ';'. $fields .'optional=first_name,last_name,photo,nickname,email;providers='.$providers_set.';hidden='.$providers_hidden.';redirect_uri='
        . urlencode( $current_url ) . '"></div>';
  else
      echo '
       <a href="#" id="uLogin2" x-ulogin-params="display=window;'. $fields .'optional=first_name,last_name,photo,nickname,email;redirect_uri='
        . urlencode( $current_url ) . '"><img src="http://ulogin.ru/img/button.png" width=187 height=30 alt="МультиВход"/></a>'; 
	
	return $args;
}



# тут всё и происходит...
function ulogin_auth_init($arg = array())
{
	if (mso_segment(1) == 'maxsite-ulogin-auth') 
    require (getinfo('plugins_dir') . 'ulogin_auth/auth.php');
	return $arg;
}


function ulogin_auth_custom_page_404($args=false)
{
 $options = mso_get_option('ulogin_auth', 'plugins', array());
 if (!isset($options['profile_slug'])) $options['profile_slug'] = 'profile'; 
 if (!isset($options['ulogin_slug'])) $options['ulogin_slug'] = 'social'; 
 
 $plugin_dir = getinfo('plugins_dir') . 'ulogin_auth/';
 $plugin_url = getinfo('plugins_url') . 'ulogin_auth/';
 
 // для профайлов отдельно
 if ( (mso_segment(1) == $options['profile_slug']) and (mso_segment(2) == $options['ulogin_slug']) ) 
    if ($comuser=is_login_comuser())
    {
      require($plugin_dir . 'comuser_profile.php');
      return true;
    }
    else die (t('Вход не произведен', 'plugins'));
 
 return $args; // true не возвращаем, чтобы мог обработать 
} 


function ulogin_form_widget($num = 1) 
{
	$out = '';
	
	$widget = 'ulogin_form_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
  		
	if (is_login())
	{
		$out = '<p><strong>' . t('Привет,', 'plugins') . ' ' . getinfo('users_nik') . '!</strong><br>
				[<a href="' . getinfo('siteurl') . 'admin">' . t('управление', 'plugins') . '</a>]
				[<a href="' . getinfo('siteurl') . 'logout'.'">' . t('выйти', 'plugins') . '</a>] 
				</p>';	
	}
	elseif ($comuser = is_login_comuser())
	{
  	$options_plugin = mso_get_option('plugin_ulogin_auth', 'plugins', array() ); // получаем опции
    if (!isset($options['profile_slug'])) $options['profile_slug'] = 'profile'; 
    if (!isset($options['ulogin_slug'])) $options['ulogin_slug'] = 'social'; 

		if (!$comuser['comusers_nik']) $cun = t('Привет!', 'plugins');
			else $cun = t('Привет,', 'plugins') . ' ' . $comuser['comusers_nik'] . '!';
			
		$out .= '<p><strong>' . $cun . '</strong><br>';
    if (isset($comuser['provider_key'])) $out .=	t('Вход с помощью', 'plugins') . ': <a href="' . $comuser['provider_user_url'] . 
    '" title="' . $comuser['provider_user_url'] . '"><img src="'.getinfo('plugins_url').'ulogin_auth/img/'.$comuser['provider_key'].'.png"></a><br>';
		$out .= '
				[<a href="' . getinfo('siteurl') . $options['profile_slug'] . '/' . $options['ulogin_slug'] . '">' . t('Управление', 'plugins') . '</a>]
				[<a href="' . getinfo('siteurl') . 'logout'.'">' . t('выйти', 'plugins') . '</a>] 
				</p>';				
	}
	else
	{
		$after_form = (isset($options['after_form'])) ? $options['after_form'] : '';

		$out = mso_login_form(array('login'=>t('Логин (email):', 'plugins') . ' ', 'password'=>t('Пароль:', 'plugins') . ' ', 'submit'=>'', 'form_end'=>$after_form ), getinfo('siteurl') . mso_current_url(), false);
	}
	
	if ($out)
	{
		if ( isset($options['header']) and $options['header'] ) $out = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>') . $out;
	}
	
	
	
	return $out;
}


# форма настройки виджета 
# имя функции = виджет_form
function ulogin_form_widget_form($num = 1) 
{
	$widget = 'ulogin_form_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['after_form']) ) $options['after_form'] = '';
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Текст после формы:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'after_form', 'value'=>$options['after_form'] ) ) ;
	
	$form .= '<p><div class="t150">&nbsp;</div> '. t('Например, ссылка на регистрацию', 'plugins') ;
	
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function ulogin_form_widget_update($num = 1) 
{
	$widget = 'ulogin_form_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['after_form'] = mso_widget_get_post($widget . 'after_form');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}

# end file
