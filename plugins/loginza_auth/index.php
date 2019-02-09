<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Dminty (d51x)
 * (c) http://d51x.ru/
 */


# функция автоподключения плагина
function loginza_auth_autoload()
{
	$options = mso_get_option('plugin_loginza_auth', 'plugins', array());
	$widget_fcomments_priority = (isset($options['widget_fcomments_priority'])) ? $options['widget_fcomments_priority'] : 1; 
	$widget_flogin_priority = (isset($options['widget_flogin_priority'])) ? $options['widget_flogin_priority'] : 1; 
	
	mso_hook_add('init', 'loginza_auth_init');
	mso_hook_add('page-comment-form', 'loginza_auth_page_comment_form', $widget_fcomments_priority); # хук на форму комментов
	mso_hook_add('login_form_auth', 'loginza_auth_login_form_auth', $widget_flogin_priority); # хук на форму логина
	mso_hook_add('admin_init', 'loginza_auth_admin_init'); # хук на админку
	mso_hook_add( 'head', 'loginza_auth_head');
	
	//mso_register_widget('loginza_auth_widget', t('Форма Loginza Auth', 'plugins')); 	
}

# функция выполняется при активации (вкл) плагина
function loginza_auth_activate($args = array())
{	
	mso_create_allow('loginza_auth_edit', t('Админ-доступ к настройкам Loginza', 'plugins') . ' ' . t('loginza_auth', __FILE__));
	return $args;
}

# функция выполняется при деинсталяции плагина
function loginza_auth_uninstall($args = array())
{	
	mso_delete_option('plugin_loginza_auth', 'plugins'); // удалим созданные опции
	mso_remove_allow('loginza_auth_edit'); // удалим созданные разрешения
	mso_delete_option_mask('loginza_auth_widget', 'plugins'); // 
	return $args;
}

# подключим страницу опций, как отдельную ссылку
function loginza_auth_admin_init($args = array()) 
{
    
	if ( mso_check_allow('loginza_auth_edit') ) 
	{
		$this_plugin_url = 'plugin_options/loginza_auth'; // url и hook
		mso_admin_menu_add('plugins', $this_plugin_url, t('Loginza Auth', 'plugins'));
		mso_admin_url_hook ($this_plugin_url, 'plugin_loginza_auth');
	}
	
	return $args;
}

# функция отрабатывающая миниопции плагина (function плагин_mso_options)
# если не нужна, удалите целиком
function loginza_auth_mso_options() 
{
	
	if ( !mso_check_allow('loginza_auth_edit') ) 
	{
		echo t('Доступ запрещен', 'plugins');
		return;
	}
	
	mso_admin_plugin_options('plugin_loginza_auth', 'plugins', 
		array(
				'widget_type' => array(
						'type' => 'select', 
						'name' => 'Ссылка авторизации для формы комментариев в виде:', 
						'description' => 'Отображение ссылки авторизации для формы комментариев в виде строки, либо в виде виджета <img src="' . getinfo('plugins_url'). '/loginza_auth/sign_in_button_gray.gif">',
						'values' => '0||виджет # 1||текстовая строка',
						'default' => '1'
					),	
				'auth_title' => array(
						'type' => 'text', 
						'name' => 'Текст ссылки авторизации для формы комментариев:', 
						'description' => 'Укажите текст ссылки авторизации для формы комментариев.<br>Работает html',
						'default' => 'Войти через Loginza'
					),					
				'widget_fcomments_priority' => array(
						'type' => 'text', 
						'name' => 'Приоритет ссылки авторизации для формы комментариев:', 
						'description' => 'Укажите приоритет ссылки авторизации для формы логина. Чем меньше приоритет, тем дальше от начала будет ссылка. Чем больше - тем первее. Например, 1 - самый конец, 99 - самое начало',
						'default' => '1'
					),	
					
				'widget_type_flogin' => array(
						'type' => 'select', 
						'name' => 'Ссылка авторизации для формы логина в виде:', 
						'description' => 'Отображение ссылки авторизации для формы логина в виде строки, либо в виде виджета <img src="' . getinfo('plugins_url'). '/loginza_auth/sign_in_button_gray.gif"><br>' . 
						' либо в виде большого виджета <img src="' . getinfo('plugins_url'). '/loginza_auth/sign_in_big_buttons.png">',
						'values' => '0||виджет # 1||текстовая строка # 2||Большой виджет',
						'default' => '1'
					),	
					
				'auth_title_flogin' => array(
						'type' => 'text', 
						'name' => 'Текст ссылки авторизации для формы логина:', 
						'description' => 'Укажите текст ссылки авторизации для формы логина.<br>Работает html',
						'default' => 'Loginza'
					),		
					
				'widget_flogin_priority' => array(
						'type' => 'text', 
						'name' => 'Приоритет ссылки авторизации для формы логина:', 
						'description' => 'Укажите приоритет ссылки авторизации для формы логина. Чем меньше приоритет, тем дальше от начала будет ссылка. Чем больше - тем первее. Например, 1 - самый конец, 99 - самое начало',
						'default' => '1'
					),	
					
				'providers_set' => array(
						'type' => 'text', 
						'name' => 'Доступные провайдеры:', 
						'description' => 'Укажите через запятую доступных провайдеров.<br>Оставьте поле пустым, если желаете отображать всех доступных провайдеров.<br>Вы можете использовать следующие провайдеры:<br>' .
						// оставим на будущее
						//google, yandex, mailruapi, mailru, vkontakte, facebook, twitter, loginza, myopenid, webmoney, rambler, flickr, lastfm, verisign, aol, steam, openid', 
						'google, yandex, facebook, twitter, loginza, myopenid, webmoney, openid', 
						'default' => ''
					),
					
			),
		'Настройки плагина Loginza Auth', // титул
		'Авторизация на сайте через сервис <a href="http://loginza.ru">Loginza</a><br><br>
		<b>Авторизация будет работать только в том случае, если выбранный провайдер будет возвращать e-mail адрес!!!</b>'   // инфо
	);	
	
}


function loginza_auth_head($args = array())
{
	echo '<script src="http://loginza.ru/js/widget.js" type="text/javascript"></script>';

	
	
}

# хук на форму логина
function loginza_auth_login_form_auth($text = '') 
{
	$text .= '';
	
	$options = mso_get_option('plugin_loginza_auth', 'plugins', array() ); // получаем опции
	if (!isset($options['widget_type_flogin'])) $options['widget_type_flogin'] = 1; 
    $widget_type =  $options['widget_type_flogin'];
	 
	if (!isset($options['auth_title_flogin']) or empty($options['auth_title_flogin'])) $options['auth_title_flogin'] = 'Loginza';  
	
	if (!isset($options['providers_set'])) $options['providers_set'] = 'google, yandex, facebook, twitter, loginza, myopenid, webmoney, openid';
	$providers_set = $options['providers_set'];
	
	$curpage = getinfo('siteurl') . mso_current_url();
	$current_url = getinfo('siteurl') . 'maxsite-loginza-auth?' . $curpage;
	
	$auth_url = "https://loginza.ru/api/widget?token_url=" .  urlencode( $current_url );
	if ( !empty($providers_set) ) {
		$providers_set = str_replace(' ', '', $providers_set);
		$auth_url .= '&providers_set=' . $providers_set;
	} else {
		// пока что так
		$auth_url .= '&providers_set=' . 'google,yandex,facebook,twitter,loginza,myopenid,webmoney,openid';
	}	
	
	if ( $widget_type == 0) 
	{
		$text .= '<a href="' .  $auth_url . '" class="loginza loginza_auth">';
		$text .= '<img src="http://loginza.ru/img/sign_in_button_gray.gif" alt="Войти через loginza"/></a>';
		
	} else if ($widget_type == 1) {
	    //$text .= '<script src="http://s1.loginza.ru/js/widget.js" type="text/javascript"></script>';
		$text .= '<a href="' .  $auth_url . '" class="loginza_auth">' . $options['auth_title_flogin'] . '</a>';
	} else if ($widget_type ==2 ) {
		$auth_url .= '&overlay=loginza';
		$text .= '<iframe src="' . $auth_url . '" style="width:359px;height:300px;" scrolling="no" frameborder="no"></iframe>';

	}
	$text .= '[end]';
	return $text;
}

# сообщение в форме комментариев
function loginza_auth_page_comment_form($args = array()) 
{
	$options = mso_get_option('plugin_loginza_auth', 'plugins', array() ); // получаем опции
	if (!isset($options['widget_type'])) $options['widget_type'] = 1; 
    $widget_type =  $options['widget_type'];
	
	if (!isset($options['auth_title']) or empty($options['auth_title'])) $options['auth_title'] = 'Войти через Loginza';  
	$auth_title = $options['auth_title'];
	
	if (!isset($options['providers_set'])) $options['providers_set'] = 'google, yandex, facebook, twitter, loginza, myopenid, webmoney, openid';
	$providers_set = $options['providers_set'];
	
	$curpage = getinfo('siteurl') . mso_current_url();
	$current_url = getinfo('siteurl') . 'maxsite-loginza-auth?' . $curpage;
	
	$auth_url = "https://loginza.ru/api/widget?token_url=" .  urlencode( $current_url . '#comments') ;
	if ( !empty($providers_set) ) {
		$providers_set = str_replace(' ', '', $providers_set);
		$auth_url .= '&providers_set=' . $providers_set;
	} else {
		// пока что так
		$auth_url .= '&providers_set=' . 'google,yandex,facebook,twitter,loginza,myopenid,webmoney,openid';
	}	
	
	if ( $widget_type == 0) 
	{
		echo '<span><a href="' .  $auth_url . '" class="loginza loginza_auth">';
		echo '<img src="http://loginza.ru/img/sign_in_button_gray.gif" alt="Войти через loginza"/></a></span>';
	} else {
	    echo '<script src="http://s1.loginza.ru/js/widget.js" type="text/javascript"></script>';
		echo '<span><a href="' .  $auth_url . '" class="loginza_auth">' . $auth_title . '</a></span>';
	}
	return $args;
}

# запросы через curl
function loginza_auth_request($url, $callbackurl = '')
{
	$ch = curl_init();
 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_REFERER, $callbackurl);
	curl_setopt($ch, CURLOPT_HEADER, 0); 
	curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/x-www-form-urlencoded;charset=UTF-8")); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);
 
	curl_setopt($ch, CURLOPT_URL, $url);
 
	$result = curl_exec($ch);
	if (empty($result)) 
	{
		die(curl_error($ch));
		curl_close($ch);
	}
 
	curl_close($ch);
	return $result;
}


# тут всё и происходит...
function loginza_auth_init($arg = array())
{
	if (mso_segment(1) == 'maxsite-loginza-auth') 
	{
		// тут придет token
		if( !empty($_POST['token']) )
		{
			// token пришел? делаем редрект на страницу авторизации
			$auth_url = "http://loginza.ru/api/authinfo?token=" . $_POST['token'];
			$profile = loginza_auth_request($auth_url);
			$profile = json_decode($profile);
			if (!is_object($profile) || !empty($profile->error_message) || !empty($profile->error_type)) {
				$res_profile = (array) $profile['error_type'];
				die ( $res_profile['error_type'] );
			}
			$curpage = mso_url_get();
			if ( $curpage == getinfo('site_url') ) $curpage = false;
			$email = (isset($profile->email) and mso_valid_email($profile->email)) ? $profile->email : null;
			$nick = (isset($profile->name->full_name) ) ? $profile->name->full_name : null;
		
			if (isset($profile->email) and mso_valid_email($profile->email))
			{
				require_once(getinfo('common_dir') . 'comments.php');
				mso_comuser_auth(array('email'=> $email,
  				                       'comusers_nik'=> $nick,
									   'redirect' => $curpage 
									   )
								);
								
				mso_redirect( getinfo('site_url') , true, 301 );
			} else {
				// ссылка на главную или на предыдущую
				pr( $profile );
				$txt = 'Не удалось авторизоваться с помощью выбранного сервиса.<br>Возможно это связано с тем, что в ответ на запрос 
				     сервис не возратил Ваш e-mail<br>';
				$txt .= 'Вернуться на <a href="' . getinfo('site_url') . $curpage. '">предыдущую страницу</a><br>'; 	 
				$txt .= 'Вернуться на <a href="' . getinfo('site_url') . '">главную страницу</a><br>';
				die( $txt );
			}			
			die();
		} 
	}	

	return $arg;
}

/**************** widget ***********************/
/* пока что отключим виджет */
/*
function loginza_auth_widget($num = 1) 
{
	$out = '';
	$widget = 'loginza_auth_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	//pr( $options  );
	if (is_login())
	{
	
	} elseif ($comuser = is_login_comuser())
	{
	} else {
		// тут вывод формы авторизации логинзы
		$after_form = (isset($options['after_form'])) ? $options['after_form'] : '';
		
		if ( !isset($options['widget_type']) ) $options['widget_type'] = '1';
		$widget_type = $options['widget_type'];
		
		$curpage = getinfo('siteurl') . mso_current_url();
		$current_url = getinfo('siteurl') . 'maxsite-loginza-auth?' . $curpage;
		
		$providers_set = '';
		
		if ( isset($options['provider_google']) && !empty($options['provider_google']) ) 		$providers_set .= 'google,';
		if ( isset($options['provider_yandex']) && !empty($options['provider_yandex']) ) 		$providers_set .= 'yandex,';
		//if ( isset($options['provider_mailruapi']) && !empty($options['provider_mailruapi']) ) 	$providers_set .= 'mailruapi,';
		//if ( isset($options['provider_mailru']) && !empty($options['provider_mailru']) ) 		$providers_set .= 'mailru,';
		//if ( isset($options['provider_vkontakte']) && !empty($options['provider_vkontakte']) ) 	$providers_set .= 'vkontakte,'; 
		if ( isset($options['provider_facebook']) && !empty($options['provider_facebook']) )		$providers_set .= 'facebook,';
		if ( isset($options['provider_twitter']) && !empty($options['provider_twitter']) ) 		$providers_set .= 'twitter,';
		if ( isset($options['provider_loginza']) && !empty($options['provider_loginza']) ) 		$providers_set .= 'loginza,';
		if ( isset($options['provider_myopenid']) && !empty($options['provider_myopenid']) ) 	$providers_set .= 'myopenid,';
		if ( isset($options['provider_webmoney']) && !empty($options['provider_webmoney']) ) 	$providers_set .= 'webmoney,';
		//if ( isset($options['provider_rambler']) && !empty($options['provider_rambler']) ) 		$providers_set .= 'rambler,';
		//if ( isset($options['provider_flickr']) && !empty($options['provider_flickr']) ) 		$providers_set .= 'flickr,';
		//if ( isset($options['provider_steam']) && !empty($options['provider_steam']) ) 		$providers_set .= 'steam,';
		//if ( isset($options['provider_lastfm']) && !empty($options['provider_lastfm']) ) 		$providers_set .= 'lastfm,';
		//if ( isset($options['provider_verisign']) && !empty($options['provider_verisign']) )     $providers_set .= 'verisign,';
		//if ( isset($options['provider_aol']) && !empty($options['provider_aol']) ) 			$providers_set .= 'aol,';
		if ( isset($options['provider_openid']) && !empty($options['provider_openid']) ) 		$providers_set .= 'openid,';
	
		$last = $providers_set{strlen($providers_set)-1};
		if ( $last == ',' ) $providers_set = substr($providers_set, 0, strlen($providers_set)-1);
		
		
		$auth_url = "https://loginza.ru/api/widget?token_url=" .  urlencode( $current_url );
		if ( !empty($providers_set) ) {
			$providers_set = str_replace(' ', '', $providers_set);
			$auth_url .= '&providers_set=' . $providers_set;
		}	
		
		if ( $widget_type == 0) 
		{
			// widget
			$out .= '<span><a href="' .  $auth_url . '" class="loginza">';
			$out .= '<img src="http://loginza.ru/img/sign_in_button_gray.gif" alt="Войти через loginza"/></span><br><br>
			</a>';
		
		} else {
		   // строка 
			echo '<script src="http://s1.loginza.ru/js/widget.js" type="text/javascript"></script>';
			$out .= '<span><a href="' .  $auth_url . '">Авторизация через Loginza</a></span><br> ';
			   
		}
	
    }

	if ($out)
	{	
		if ( isset($options['header']) and $options['header'] ) $out = '<h2 class="box"><span>' . $options['header'] . '</span></h2>' . $out;	
	}
	
	return $out;
}

# форма настройки виджета 
# имя функции = виджет_form
function loginza_auth_widget_form($num = 1) 
{
	$widget = 'loginza_auth_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['after_form']) ) $options['after_form'] = '';
	if ( !isset($options['widget_type']) ) $options['widget_type'] = '1';
	
	if ( !isset($options['provider_google']) ) $options['provider_google'] = TRUE;
	if ( !isset($options['provider_yandex']) ) $options['provider_yandex'] = TRUE;
	//if ( !isset($options['provider_mailruapi']) ) $options['provider_mailruapi'] = TRUE;
	//if ( !isset($options['provider_mailru']) ) $options['provider_mailru'] = TRUE;
	//if ( !isset($options['provider_vkontakte']) ) $options['provider_vkontakte'] = TRUE;
	if ( !isset($options['provider_facebook']) ) $options['provider_facebook'] = TRUE;
	if ( !isset($options['provider_twitter']) ) $options['provider_twitter'] = TRUE;
	if ( !isset($options['provider_loginza']) ) $options['provider_loginza'] = TRUE;
	if ( !isset($options['provider_myopenid']) ) $options['provider_myopenid'] = TRUE;
	if ( !isset($options['provider_webmoney']) ) $options['provider_webmoney'] = TRUE;
	//if ( !isset($options['provider_rambler']) ) $options['provider_rambler'] = TRUE;
	//if ( !isset($options['provider_flickr']) ) $options['provider_flickr'] = TRUE;
	//if ( !isset($options['provider_steam']) ) $options['provider_steam'] = TRUE;
	//if ( !isset($options['provider_lastfm']) ) $options['provider_lastfm'] = TRUE;
	//if ( !isset($options['provider_verisign']) ) $options['provider_verisign'] = TRUE;
	//if ( !isset($options['provider_aol']) ) $options['provider_aol'] = TRUE;
	if ( !isset($options['provider_openid']) ) $options['provider_openid'] = TRUE;
	
					
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Текст после формы:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'after_form', 'value'=>$options['after_form'] ) ) ;
	
	$form .= '<p><div class="t150">&nbsp;</div> '. t('Например, ссылка на регистрацию', 'plugins') ;
	
	$dropdown_items = array( 0 => 'виджет', 1 => 'текстовая строка');
	
	$form .= '<p><div class="t150">' . t('Ссылка авторизации в виде:', 'plugins') . '</div> '. form_dropdown(  $widget . 'widget_type', $dropdown_items, $options['widget_type'] ) ;
	
	$form .= '<p><div class="t150">&nbsp;</div> '. t('<b>Доступные провайдеры:</b>', 'plugins') ;
		
    $form .= '<p><div class="t150">&nbsp;Google</div> '. form_checkbox(array( 'name'=>$widget . 'provider_google', 'value'=>'provider_google', 'checked'=>$options['provider_google'])) ;	
	
    $form .= '<p><div class="t150">&nbsp;Yandex</div> '. form_checkbox(array( 'name'=>$widget . 'provider_yandex', 'value'=>'provider_yandex', 'checked'=>$options['provider_yandex'])) ;		
    //$form .= '<p><div class="t150">&nbsp;mailruapi</div> '. form_checkbox(array( 'name'=>$widget . 'provider_mailruapi', 'value'=>'provider_mailruapi', 'checked'=>$options['provider_mailruapi'])) ;		
    //$form .= '<p><div class="t150">&nbsp;Mail.ru</div> '. form_checkbox(array( 'name'=>$widget . 'provider_mailru', 'value'=>'provider_mailru', 'checked'=>$options['provider_mailru'])) ;		
    //$form .= '<p><div class="t150">&nbsp;Вконтакте</div> '. form_checkbox(array( 'name'=>$widget . 'provider_vkontakte', 'value'=>'provider_vkontakte', 'checked'=>$options['provider_vkontakte'])) ;	
	$form .= '<p><div class="t150">&nbsp;FaceBook</div> '. form_checkbox(array( 'name'=>$widget . 'provider_facebook', 'value'=>'provider_facebook', 'checked'=>$options['provider_facebook'])) ;	
	$form .= '<p><div class="t150">&nbsp;Twitter</div> '. form_checkbox(array( 'name'=>$widget . 'provider_twitter', 'value'=>'provider_twitter', 'checked'=>$options['provider_twitter'])) ;	
	$form .= '<p><div class="t150">&nbsp;Loginza</div> '. form_checkbox(array( 'name'=>$widget . 'provider_loginza', 'value'=>'provider_loginza', 'checked'=>$options['provider_loginza'])) ;	
	$form .= '<p><div class="t150">&nbsp;MyOpenID</div> '. form_checkbox(array( 'name'=>$widget . 'provider_myopenid', 'value'=>'provider_myopenid', 'checked'=>$options['provider_myopenid'])) ;	
	$form .= '<p><div class="t150">&nbsp;Webmoney</div> '. form_checkbox(array( 'name'=>$widget . 'provider_webmoney', 'value'=>'provider_webmoney', 'checked'=>$options['provider_webmoney'])) ;		
	//$form .= '<p><div class="t150">&nbsp;Rambler</div> '. form_checkbox(array( 'name'=>$widget . 'provider_rambler', 'value'=>'provider_rambler', 'checked'=>$options['provider_rambler'])) ;		
	//$form .= '<p><div class="t150">&nbsp;Flickr</div> '. form_checkbox(array( 'name'=>$widget . 'provider_flickr', 'value'=>'provider_flickr', 'checked'=>$options['provider_flickr'])) ;		
	//$form .= '<p><div class="t150">&nbsp;Last.fm</div> '. form_checkbox(array( 'name'=>$widget . 'provider_lastfm', 'value'=>'provider_lastfm', 'checked'=>$options['provider_lastfm'])) ;		
	//$form .= '<p><div class="t150">&nbsp;VeriSign</div> '. form_checkbox(array( 'name'=>$widget . 'provider_verisign', 'value'=>'provider_verisign', 'checked'=>$options['provider_verisign'])) ;		
	//$form .= '<p><div class="t150">&nbsp;AOL</div> '. form_checkbox(array( 'name'=>$widget . 'provider_aol', 'value'=>'provider_aol', 'checked'=>$options['provider_aol'])) ;		
	//$form .= '<p><div class="t150">&nbsp;Steam</div> '. form_checkbox(array( 'name'=>$widget . 'provider_steam', 'provider_steam', 'checked'=>$options['provider_steam'])) ;		
	$form .= '<p><div class="t150">&nbsp;OpenID</div> '. form_checkbox(array( 'name'=>$widget . 'provider_openid', 'value'=>'provider_openid', 'checked'=>$options['provider_openid'])) ;		
	
	
	return $form;
}

# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function loginza_auth_widget_update($num = 1) 
{
	$widget = 'loginza_auth_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['after_form'] = mso_widget_get_post($widget . 'after_form');
	
	$newoptions['widget_type'] = mso_widget_get_post($widget . 'widget_type');
	$newoptions['provider_google'] = mso_widget_get_post($widget . 'provider_google');
	$newoptions['provider_yandex'] = mso_widget_get_post($widget . 'provider_yandex');
	//$newoptions['provider_mailruapi'] = mso_widget_get_post($widget . 'provider_mailruapi');
	//$newoptions['provider_mailru'] = mso_widget_get_post($widget . 'provider_mailru');
	//$newoptions['provider_vkontakte'] = mso_widget_get_post($widget . 'provider_vkontakte');
	$newoptions['provider_facebook'] = mso_widget_get_post($widget . 'provider_facebook');
	$newoptions['provider_twitter'] = mso_widget_get_post($widget . 'provider_twitter');
	$newoptions['provider_loginza'] = mso_widget_get_post($widget . 'provider_loginza');
	$newoptions['provider_myopenid'] = mso_widget_get_post($widget . 'provider_myopenid');
	$newoptions['provider_webmoney'] = mso_widget_get_post($widget . 'provider_webmoney');
	//$newoptions['provider_rambler'] = mso_widget_get_post($widget . 'provider_rambler');
	//$newoptions['provider_flickr'] = mso_widget_get_post($widget . 'provider_flickr');
	//$newoptions['provider_steam'] = mso_widget_get_post($widget . 'provider_steam');
	//$newoptions['provider_lastfm'] = mso_widget_get_post($widget . 'provider_lastfm');
	//$newoptions['provider_verisign'] = mso_widget_get_post($widget . 'provider_verisign');
	//$newoptions['provider_aol'] = mso_widget_get_post($widget . 'provider_aol');	
	$newoptions['provider_openid'] = mso_widget_get_post($widget . 'provider_openid');
	
	if ( $options != $newoptions ) 
		mso_add_option($widget, $newoptions, 'plugins');
}
*/

# end file
