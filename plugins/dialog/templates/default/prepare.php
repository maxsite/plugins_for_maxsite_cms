<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */

// осуществим здесь предварительную подготовку вывода шаблона

 // подключим функции дефолтного шаблона
 // require($template_default_dir . 'functions.php'); 
 
 // подключим файл интерпритации сообщений 
 $fn = 'info_messages.php'; 
 if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
     require($template_dir . $fn);
 else 
     require($template_default_dir . $fn);
     
 // добавим сообщения в опции 
 $options = array_merge($options , $options_messages);
 
 // функции, используемые при выводе
 require ($plugin_dir . 'functions/access_db.php');
 require ($plugin_dir . 'functions/view_count.php');
 require ($plugin_dir . 'functions/functions.php');
    
  // получим пользователя 
  $comuser = dialog_get_login_profile($options);
  if ($comuser)
  {
    $comuser_id = $comuser['comusers_id'];
    $comuser_role = $comuser['profile_user_role_id'];
  }
  else // дефолтные параметры, если никто не залогинен
  {
    $comuser_id =0;
    $comuser_role = 0;
    $comuser['profile_vid'] = 1;
    $comuser['profile_comments_on_page'] = $options['comments_on_page'];
    $comuser['profile_user_role_id'] = $comuser_role;
    $comuser['profile_comments_count'] = 0;
    $comuser['profile_rate'] = 0;
    $comuser['profile_font_size'] = 12;
  }           
  

 // ссылка на главную
 $main_link = '<a href = "' . $siteurl . $options['main_slug'] . '" title = "' . $options['name'] . '">' . $options['name'] . '</a>';
 
 // дефолтная ошибка
 $error = $options['out_of_element'];

 // где обрабатывается аякс комментов
 $ajax_path = getinfo('ajax') . base64_encode('plugins/dialog/functions/ajax-ajax.php');
 $get_ajax_path = getinfo('ajax') . base64_encode('plugins/dialog/functions/getinfo-ajax.php');
    
 
 // получим ссылку на пользователя или, если никто не залогинен, ссылку на login/register
 if ($comuser_id)
{
  $profile_url = $siteurl . $options['comuser_profile_slug'] . '/' . $options['settings_slug'];
	if (!$comuser['profile_psevdonim']) $login_name = $options['form_hello']; 
	else $login_name = $comuser['profile_psevdonim'];
	$link_login = '<a href="'. $profile_url . '">' . $login_name . '</a>';
	$link_login .= ' (<a href="'. getinfo('siteurl') . 'logout">' . $options['form_exit'] . '</a>)';
}
else
  $link_login = '<a href="' . $siteurl . 'loginform">' . $options['login'] . '</a>' . 
     ' | <a href="' . $siteurl . 'registration">' . $options['register'] . '</a>'; 
  


?>