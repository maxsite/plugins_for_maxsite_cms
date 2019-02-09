<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */



function profile_autoload()
{
	mso_create_allow('profile_edit', t('Админ-доступ к редактированию profile', __FILE__));
	mso_hook_add( 'admin_init', 'profile_admin_init');
	//mso_profile_widget('profile_widget', t('profile', __FILE__)); //
	mso_hook_add('custom_page_404', 'profile_custom_page_404');
	mso_hook_add('init', 'profile_init');
	$options = mso_get_option('profile', 'plugins', array());
	$widget_fcomments_priority = (isset($options['widget_fcomments_priority'])) ? $options['widget_fcomments_priority'] : 10; 
	$widget_flogin_priority = (isset($options['widget_flogin_priority'])) ? $options['widget_flogin_priority'] : 10; 	
	mso_hook_add('page-comment-form', 'profile_page_comment_form', $widget_fcomments_priority); # хук на форму комментов
	mso_hook_add('login_form_auth', 'profile_login_form_auth', $widget_flogin_priority); # хук на форму логина	
	
	mso_hook_add( 'head', 'profile_auth_head');
	
    mso_register_widget('profile_widget', t('Форма Auth')	);
    mso_register_widget('use_uploads_widget', t('Используемые загрузки')	);
    
}

function profile_auth_head($args = array())
{
	 if (!is_login_comuser())
		echo '<script src="http://ulogin.ru/js/ulogin.js"></script>';
	return $args;
}


function profile_head($arg = array())
{
	echo '<link rel="stylesheet" type="text/css" href="' . getinfo('plugins_url') . 'profile/style.css">';
}

function profile_activate($args = array())
{
	return $args;
}


function profile_uninstall($args = array())
{
	mso_delete_option_mask('profile', 'plugins'); // удалим созданные опции
	mso_remove_allow('profile_edit'); // удалим созданные разрешения
	mso_delete_option_mask('profile_widget', 'plugins' );	
	mso_delete_option_mask('use_uploads_widget', 'plugins' );	

	return $args;
}



function profile_admin_init($args = array())
{
	if ( mso_check_allow('profile_edit') )
	{
		mso_admin_menu_add('plugins', 'profile', t('Личный кабинет', __FILE__));
		mso_admin_url_hook ('profile', 'profile_admin_page');
	}
	return $args;
}



function profile_admin_page($args = array())
{
	# выносим админские функции отдельно в файл
	if ( !mso_check_allow('profile_edit') )
	{
		echo t('Доступ запрещен', 'plugins');
		return $args;
	}
	# выносим админские функции отдельно в файл
	mso_hook_add_dinamic( 'mso_admin_header', ' return $args . "' . t('profile', __FILE__) . '"; ' );
	mso_hook_add_dinamic( 'admin_title', ' return "' . t('profile', __FILE__) . ' - " . $args; ' );
	require(getinfo('plugins_dir') . 'profile/admin/index.php');
}


function profile_custom_page_404($args = false)
{
  // вывод приватной страницы профиля пользователя (личного кабинета)

  $options = mso_get_option('profile', 'plugins', array());
  $segment1 = mso_segment(1);
  $segment2 = mso_segment(2);
  $segment3 = mso_segment(3);
  $site_url = getinfo('siteurl');
  
  $subdir = 'userfile'; // подкаталог для загрузок комюзерами

	$options['profile_slug'] = isset($options['profile_slug']) ? $options['profile_slug'] : 'profile';
	$options['profile_user_slug'] = isset($options['profile_user_slug']) ? $options['profile_user_slug'] : 'profile-user';
	$options['profiles_slug'] = isset($options['profiles_slug']) ? $options['profiles_slug'] : 'profiles';
	$options['profiles_user_slug'] = isset($options['profiles_user_slug']) ? $options['profiles_user_slug'] : 'profiles-users';
    	
	$options['register_slug'] = isset($options['register_slug']) ? $options['register_slug'] : 'registration';

    if (!isset($options['profile_plugins'])) $options['profile_plugins'] = array('profile'); // плагины, подключающие свои элементы

    $error = '';
	// регистрация (свой файл или файл maxsite шаблона default)
	if ( (mso_segment(1) == $options['register_slug']) and ($options['register_slug'] != 'registration'))
	{
	  require( getinfo('plugins_dir') . 'profile/register.php' ); // подключили страницу регистрации
	  return true; // выходим с true
	}  
	
	  // публичные страницы пользователя (профиль пользователя)
	if ( (mso_segment(1) == $options['profiles_slug']) or (mso_segment(1) == $options['profiles_user_slug'])) // если сегмент профиля
    {
        require( getinfo('plugins_dir') . 'profile/pub_pages/index.php' );
        if (!$error)  return true;
    }    
	 
	// приватные страницы пользователя (личный кабинет)
	if ( (mso_segment(1) == $options['profile_slug']) or (mso_segment(1) == $options['profile_user_slug'])) 
	{
	    require( getinfo('plugins_dir') . 'profile/priv_pages/index.php' ); 
        if (!$error)  return true;
	}    

	return $args;
}

# хук на форму логина
function profile_login_form_auth($text = '') 
{
	//$text .= '';
	
	$options = mso_get_option('profile', 'plugins', array() ); // получаем опции
   // $u_id = '2';
	
	require (getinfo('plugins_dir') . 'profile/auth_providers/ulogin/ulogin.php');
	$text .= $out1 . '[end]' ;

	return $text;
}

# сообщение в форме комментариев
function profile_page_comment_form($args = array()) 
{
	$options = mso_get_option('profile', 'plugins', array() ); // получаем опции
   // $u_id = '2';

	require (getinfo('plugins_dir') . 'profile/auth_providers/ulogin/ulogin.php');

    echo $out1;
	
	return $args;
}



# тут всё и происходит...
function profile_init($arg = array())
{
	if (mso_segment(1) == 'maxsite-ulogin-auth') 
    require (getinfo('plugins_dir') . 'profile/auth_providers/ulogin/auth.php');
	return $arg;
}



function profile_widget($num = 1) 
{
	$out = '';
	
	$widget = 'profile_widget_' . $num; // имя для опций = виджет + номер
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
  		

	if ($comuser = is_login_comuser())
	{
      if (!isset($options['profile_slug'])) $options['profile_slug'] = 'profile'; 
      $options['ulogin_slug'] = 'logininfo'; 
      if (!isset($options['default_nik'])) $options['default_nik'] = 'Привет'; 
      if (!isset($options['title'])) $options['title'] = 'Личный кабинет'; 
	
      if (!isset($options['format_title_prov'])) $options['format_title_prov'] = '<p><strong>#NIK#</strong> #PROV#</p>'; 
      if (!isset($options['format_title'])) $options['format_title'] = '<p><strong>#NIK#</strong></p>'; 
    
		if (!$comuser['comusers_nik']) $nik = $options['default_nik'];
		else $nik = $comuser['comusers_nik'];
					
		// если вход через провайдера
		if (isset($comuser['provider_key']))
		{
		  $prov = '<a href="' . $comuser['provider_user_url'] . 
    '" title="' . $comuser['provider_user_url'] . '"><img src="'.getinfo('plugins_url').'profile/img/'.$comuser['provider_key'].'.png"></a>';
      $out .= str_replace(array('#NIK#' , '#PROV#'), 
							array($nik, $prov), $options['format_title_prov']);
		}
		else // вход логином и паролем
       $out .= str_replace('#NIK#',$nik, $options['format_title']);
    
		$out .= '<p>
				[<a href="' . getinfo('siteurl') . $options['profile_slug'] . '/' . $options['ulogin_slug'] . '">' . $options['title'] . '</a>]
				[<a href="' . getinfo('siteurl') . 'logout'.'">' . t('выйти', 'plugins') . '</a>] 
				</p>';				
	}
	elseif (is_login())
	{
		$out .= '<p><strong>' . t('Привет,', 'plugins') . ' ' . getinfo('users_nik') . '!</strong><br>
				[<a href="' . getinfo('siteurl') . 'admin">' . t('управление', 'plugins') . '</a>]
				[<a href="' . getinfo('siteurl') . 'logout'.'">' . t('выйти', 'plugins') . '</a>] 
				</p>';	
	}	
	else
	{
        $after_form = (isset($options['after_form'])) ? $options['after_form'] : '';
        if (!isset($options['login_email'])) $options['login_email'] = 'Email'; 
        if (!isset($options['login_password'])) $options['login_password'] = 'Пароль'; 
		$out .= mso_login_form(array('login'=>$options['login_email'] . ' ', 'password'=>$options['login_password'] . ' ', 'submit'=>'', 'form_end'=>$after_form ), getinfo('siteurl') . mso_current_url(), false);
	   // $u_id = '';
		//require (getinfo('plugins_dir') . 'profile/auth_providers/ulogin/ulogin.php');
		//$out .= $out1;
	}
	
	
	if ($out)
	{
		if ( isset($options['header']) and $options['header'] ) $out .= mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>') . $out;
	}
	
	return $out;
}


# форма настройки виджета 
# имя функции = виджет_form
function profile_widget_form($num = 1) 
{
	$widget = 'profile_widget_' . $num; // имя для формы и опций = виджет + номер
	
	// получаем опции 
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) ) $options['header'] = '';
	if ( !isset($options['after_form']) ) $options['after_form'] = '';
    if (!isset($options['default_nik'])) $options['default_nik'] = 'Привет'; 
    if (!isset($options['format_title_prov'])) $options['format_title_prov'] = '<p><strong>#NIK#</strong> #PROV#</p>'; 
    if (!isset($options['format_title'])) $options['format_title'] = '<p><strong>#NIK#</strong></p>'; 	
    if (!isset($options['login_email'])) $options['login_email'] = 'Email'; 
    if (!isset($options['login_password'])) $options['login_password'] = 'Пароль'; 	
	
	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = '<p><div class="t150">' . t('Заголовок', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Текст после формы входа:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'after_form', 'value'=>$options['after_form'] ) ) ;
	$form .= '<p><div class="t150">&nbsp;</div> '. t('Например, ссылка на регистрацию', 'plugins') ;

	$form .= '<p><div class="t150">' . t('Вход с провайдером:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'format_title_prov', 'value'=>$options['format_title_prov'] ) ) ;
	$form .= '<p><div class="t150">&nbsp;</div>  (#NIK# #PROV#)';

	$form .= '<p><div class="t150">' . t('Вход email+пароль:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'format_title', 'value'=>$options['format_title'] ) ) ;
	$form .= '<p><div class="t150">&nbsp;</div>  (#NIK#)';

	$form .= '<p><div class="t150">' . t('Имя, если нет:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'default_nik', 'value'=>$options['default_nik'] ) ) ;

	$form .= '<p><div class="t150">' . t('Поле email:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'login_email', 'value'=>$options['login_email'] ) ) ;
	
	$form .= '<p><div class="t150">' . t('Поле password:', 'plugins') . '</div> '. form_input( array( 'name'=>$widget . 'login_password', 'value'=>$options['login_password'] ) ) ;		
		
	return $form;
}


# сюда приходят POST из формы настройки виджета
# имя функции = виджет_update
function profile_widget_update($num = 1) 
{
	$widget = 'profile_widget_' . $num; // имя для опций = виджет + номер
	
	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());
	
	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['after_form'] = mso_widget_get_post($widget . 'after_form');
	$newoptions['format_title_prov'] = mso_widget_get_post($widget . 'format_title_prov');
	$newoptions['format_title'] = mso_widget_get_post($widget . 'format_title');
	$newoptions['default_nik'] = mso_widget_get_post($widget . 'default_nik');
	$newoptions['login_password'] = mso_widget_get_post($widget . 'login_password');
	$newoptions['login_email'] = mso_widget_get_post($widget . 'login_email');
	
	if ( $options != $newoptions ) 
		 mso_add_option($widget, $newoptions, 'plugins');
}


function use_uploads_widget($num = 1) 
{
	$widget = 'use_uploads_widget_' . $num; // имя для опций = виджет + номер
    if ( $k = mso_get_cache($widget) ) return $k; // да есть в кэше	
    
    require_once(getinfo('plugins_dir') . 'profile/functions_userfile.php');
	$out = '';
    $width = '';
	$subdir = 'userfile';
	$options = mso_get_option($widget, 'plugins', array() ); // получаем опции
	
	if ( !isset($options['img_prev_attr'])) 	$options['img_prev_attr'] = 'class="left"';
	if ( !isset($options['header']) )	$options['header'] = 'Картинки пользователей';
	if ( !isset($options['text_do']) ) 		$options['text_do'] = '';
	if ( !isset($options['text_posle']) ) 		$options['text_posle'] = '<div class="clearfix"></div>';
	if ( !isset($options['block_start']) ) 		$options['block_start'] = '';
	if ( !isset($options['block_end']) ) 		$options['block_end'] = '';
	if ( !isset($options['count']) ) 		$options['count'] = 4;
	if ( !isset($options['go']) ) 		$options['go'] = 'Перейти к дискуссии';
	
    
    // получим все userfiles/id
    $CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	$CI->load->helper('directory');

	$path = getinfo('uploads_dir') . $subdir . '/';

	$dirs = directory_map($path, true); // только в текущем каталоге
	if (!$dirs) $dirs = array();

    $userfiles = array();
	foreach ($dirs as $dir)
	{
		if (!is_dir($path . $dir)) continue; // это не каталог
		if (!is_numeric($dir)) continue;
		$cur_userfiles = get_userfiles($dir , $subdir , 1 , true , $options['count']);
		if ($cur_userfiles) $userfiles = array_merge($userfiles , $cur_userfiles);
     }

    uasort($userfiles , '_sort_use_userfiles');

	$uploads_url = getinfo('uploads_url');
	$i=0;
	foreach ($userfiles as $file)
	   if (isset($file['use'][0]) )
	   {
	     	$subpath = $subdir . '/' . $file['comuser_id'] . '/';
	      
	        $prev = '<img ' . $options['img_prev_attr'] . ' alt="" src="' . $uploads_url . $subpath . $file['prev'] . $file['file'] . '">';
	        $url = getinfo('siteurl') . 'goto/disc/' . $file['use'][0]['discussion_id'] . '/comm/' . $file['use'][0]['comment_id'];
            $link = '<a href="' . $url . '" target="_blank" title="' . $options['go'] . ' ' . $file['use'][0]['discussion_title'] . '">' . $prev . '</a>';
            $out .= $options['block_start'] . $link	 . $options['block_end'];
            $i++;
            if ($i==$options['count']) break;
	   }  

	if ($out)
	{
	    $out = $options['text_do'] . $out . $options['text_posle'];
	   
		if ( isset($options['header']) and $options['header'] )
		   $out = mso_get_val('widget_header_start', '<h2 class="box"><span>') . $options['header'] . mso_get_val('widget_header_end', '</span></h2>') . $out;
	}
	
	mso_add_cache($widget, $out);
	return $out;
}

function _sort_use_userfiles($a, $b) //date
{
	if ( $a['use_date'] == $b['use_date'] ) return 0;
	return ( $a['use_date'] < $b['use_date'] ) ? 1 : -1;
}

function use_uploads_widget_form($num = 1)
{
	$widget = 'use_uploads_widget_' . $num; // имя для формы и опций = виджет + номер

	// получаем опции
	$options = mso_get_option($widget, 'plugins', array());
	
	if ( !isset($options['header']) )	$options['header'] = 'Картинки пользователей';
	if ( !isset($options['text_do']) ) 		$options['text_do'] = '';
	if ( !isset($options['text_posle']) ) 		$options['text_posle'] = '<div class="clearfix"></div>';
	if ( !isset($options['block_start']) ) 		$options['block_start'] = '';
	if ( !isset($options['block_end']) ) 		$options['block_end'] = '';
	if ( !isset($options['img_prev_attr'])) 	$options['img_prev_attr'] = 'class="left"';
	if ( !isset($options['count'])) 	$options['count'] = 4;
	if ( !isset($options['go'])) 	$options['go'] = 'Перейти к дискуссии';

	// вывод самой формы
	$CI = & get_instance();
	$CI->load->helper('form');
	
	$form = mso_widget_create_form(t('Заголовок'), form_input( array( 'name'=>$widget . 'header', 'value'=>$options['header'] ) ), '');
	$form .= mso_widget_create_form(t('Текст до'), form_input( array( 'name'=>$widget . 'text_do', 'value'=>$options['text_do'] ) ), '');
	$form .= mso_widget_create_form(t('Текст после'), form_input( array( 'name'=>$widget . 'text_posle', 'value'=>$options['text_posle'] ) ), '');
	$form .= mso_widget_create_form(t('До блока'), form_input( array( 'name'=>$widget . 'block_start', 'value'=>$options['block_start'] ) ), '');
	$form .= mso_widget_create_form(t('После блока'), form_input( array( 'name'=>$widget . 'block_end', 'value'=>$options['block_end'] ) ), '');
	$form .= mso_widget_create_form(t('Стиль'), form_input( array( 'name'=>$widget . 'img_prev_attr', 'value'=>$options['img_prev_attr'] ) ), '');
	$form .= mso_widget_create_form(t('Колличество'), form_input( array( 'name'=>$widget . 'count', 'value'=>$options['count'] ) ), '');
	$form .= mso_widget_create_form(t('title ссылки'), form_input( array( 'name'=>$widget . 'go', 'value'=>$options['go'] ) ), '');
	
	return $form;
}


function use_uploads_widget_update($num = 1)
{
	$widget = 'use_uploads_widget_' . $num; // имя для опций = виджет + номер

	// получаем опции
	$options = $newoptions = mso_get_option($widget, 'plugins', array());

	# обрабатываем POST
	$newoptions['header'] = mso_widget_get_post($widget . 'header');
	$newoptions['text_do'] = mso_widget_get_post($widget . 'text_do');
	$newoptions['text_posle'] = mso_widget_get_post($widget . 'text_posle');
	$newoptions['block_start'] = mso_widget_get_post($widget . 'block_start');
	$newoptions['block_end'] = mso_widget_get_post($widget . 'block_end');
	$newoptions['img_prev_attr'] = mso_widget_get_post($widget . 'img_prev_attr');
	$newoptions['count'] = mso_widget_get_post($widget . 'count');
	$newoptions['go'] = mso_widget_get_post($widget . 'go');
	
	if ( $options != $newoptions )
		mso_add_option($widget, $newoptions, 'plugins' );
}
