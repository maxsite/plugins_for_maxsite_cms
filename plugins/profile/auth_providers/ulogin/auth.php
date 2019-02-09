<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Залогиневание ulogin_auth 
 */

	  $curpage = mso_url_get();
		if ( $curpage == getinfo('site_url') ) $curpage = false;
		// тут придет token
		if( !empty($_POST['token']) )
		{
		  require (getinfo('plugins_dir') . 'profile/auth_providers/ulogin/functions.php');
		  
		  $s = file_get_contents('http://ulogin.ru/token.php?token=' . $_POST['token'] . '&host=' . $_SERVER['HTTP_HOST']);
      $profile = json_decode($s, true);
      
      $profile = get_fields($profile);   
      
      $profile['redirect'] = $curpage;
      
	    // получим всех доступных провайдеров
	    $options = mso_get_option('plugin_ulogin_auth', 'plugins', array() ); // получаем опции
	    if (!isset($options['providers_set'])) $options['providers_set'] = 'vkontakte,odnoklassniki,yandex,mailru,twitter';
	    if (!isset($options['providers_hidden'])) $options['providers_hidden'] = 'google,facebook,livejournal,youtube';
      $profile['all_providers'] = $options['providers_set'] . ',' . $options['providers_hidden'];
     // $profile['email'] = 'no';
       
      ulogin_comuser_auth($profile);
      
			mso_redirect( $curpage , true, 301 );
	  }
		
		$txt = t('Не удалось авторизоваться с помощью выбранного сервиса.') . '<br>';
		if ($curpage != getinfo('site_url')) $txt .= t('Вернуться на') . ' <a href="' . getinfo('site_url') . $curpage. '">' . t('предыдущую страницу') . '</a><br>'; 	 
		$txt .= t('Вернуться на') . ' <a href="' . getinfo('site_url') . '">' . t('главную страницу') . '</a><br>';
		die( $txt );




# end file
