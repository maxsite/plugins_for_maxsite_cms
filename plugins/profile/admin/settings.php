<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	mso_cur_dir_lang(__FILE__);
	$CI = & get_instance();

	$options_key = 'profile';
	$options_key2 = 'profile_profiles';

	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
		$options = array();
		$options['upload_avatar']       = isset( $post['f_upload_avatar'])       ? 1 : 0;
		$options['no_cache']       = isset( $post['f_no_cache'])       ? 1 : 0;
		$options['profiles_slug']   = isset( $post['f_profiles_slug'])   ? $post['f_profiles_slug']   : 'profiles';
		$options['profile_slug']   = isset( $post['f_profile_slug'])   ? $post['f_profile_slug']   : 'profile';
		$options['register_slug']      = isset( $post['f_register_slug'])      ? $post['f_register_slug']      : 'register';
		$options['title'] = isset( $post['f_title']) ? $post['f_title'] : 'Личный кабинет';
		$options['profiles_title'] = isset( $post['f_profiles_title']) ? $post['f_profiles_title'] : 'Пользователи';
		$options['exit']    = isset( $post['f_exit'])    ? $post['f_exit']    : 'Выход';
		$options['hello']    = isset( $post['f_hello'])    ? $post['f_hello']    : 'Привет';
		$options['events_count']    = isset( $post['f_events_count'])    ? $post['f_events_count']    : 10;
        $options['all_events_title'] = isset($post['f_all_events_title']) ? $post['f_all_events_title'] : 'Все события';
		
		$menu_pages = explode("\n", $post['f_pages']); // разбиваем по строкам
		//$empty_slug = false;
		$options['pages'] = array();
		foreach ($menu_pages as $menu_page)
		{
		   $arr = explode("|", $menu_page);
		   if (isset($arr[1]) and trim($arr[1]))
		   {
		     $arr[0] = trim($arr[0]);
             if ($arr[0])  $options['pages'][$arr[0]] = trim($arr[1]);
             else $options['pages']['0'] = trim($arr[1]);     
           }
		}

		$menu_pages_profiles = explode("\n", $post['f_pages_profiles']); // разбиваем по строкам
		//$empty_slug = false;
		$options['pages_profiles'] = array();
		foreach ($menu_pages_profiles as $menu_pages_profile)
		{
		   $arr = explode("|", $menu_pages_profile);
		   if (isset($arr[1]) and trim($arr[1]))
		   {
		     $arr[0] = trim($arr[0]);
             if ($arr[0])  $options['pages_profiles'][$arr[0]] = trim($arr[1]);
             else $options['pages_profiles']['0'] = trim($arr[1]);     
           }
		}

		$menu_main = explode("\n", $post['f_pages_main']); // разбиваем по строкам
		//$empty_slug = false;
		$options['pages_main'] = array();
		foreach ($menu_main as $menu_page)
		{
		   $arr = explode("|", $menu_page);
		   if (isset($arr[1]) and trim($arr[1]))
		   {
		     $arr[0] = trim($arr[0]);
         if ($arr[0])  $options['pages_main'][$arr[0]] = trim($arr[1]);
         else $options['pages_main']['0'] = trim($arr[1]);     
       }
		}
		
		$profile_plugins = explode("," , $post['f_profile_plugins']);
		$options['profile_plugins'] = array();
		foreach ($profile_plugins as $profile_plugin) $options['profile_plugins'][] = trim($profile_plugin);
				
		mso_add_option($options_key, $options, 'plugins');

		$element['title'] = isset($post['f_e_title']) ? $post['f_e_title'] : 'Комментарий к статье';
		$element['name'] = isset($post['f_name']) ? $post['f_name'] : 'Комментарий';
		$element['all'] = isset($post['f_all']) ? $post['f_all'] : 'Все комментарии';
		$element['title_go'] = isset($post['f_title_go']) ? $post['f_title_go'] : 'Перейти к статье';
		$element['all_link'] = isset($post['f_all_link']) ? $post['f_all_link'] : 'comments';
		$element['img'] = isset($post['f_img']) ? $post['f_img'] : getinfo('plugins_url') . 'profile/img/comment.png';
		$element['filename'] = isset($post['f_filename']) ? $post['f_filename'] : 'comments';
		$element['slug'] = isset($post['f_slug']) ? $post['f_slug'] : 'comments';
		
		$elements =array($element);
		mso_add_option($options_key2, $elements, 'plugins');

		mso_flush_cache();
		echo '<div class="update">' . t('Обновлено!', 'plugins') . '</div>';
	}

	echo '<h1>'. t('Плагин profile'). '</h1><p class="info">'. t('С помощью этого плагина вы можете настраивать регистрацию на сайте.'). '</p>';

	$options = mso_get_option($options_key, 'plugins', array());
	$options['upload_avatar']    = isset($options['upload_avatar'])    ? (int)$options['upload_avatar']    : 1;
	$options['no_cache']    = isset($options['no_cache'])    ? (int)$options['no_cache']    : 0;
	$options['profiles_slug']   = isset($options['profiles_slug'])   ?      $options['profiles_slug']   : 'profiles';
	$options['profile_slug']   = isset($options['profile_slug'])   ?      $options['profile_slug']   : 'profile';
	$options['register_slug']      = isset($options['register_slug'])      ?      $options['register_slug']      : 'registration';
	$options['title'] = isset($options['title']) ?      $options['title'] : 'Личный кабинет';
	$options['profiles_title'] = isset($options['profiles_title']) ?      $options['profiles_title'] : 'Пользователи';
	$options['exit']    = isset($options['exit'])    ?      $options['exit']    : 'Выход';
	$options['hello']    = isset($options['hello'])    ?      $options['hello']    : 'Привет';
	$options['events_count']    = isset($options['events_count'])    ?      $options['events_count']    : 10;
	
  $options['pages'] = isset($options['pages']) ? $options['pages'] : array('0'=>'Основные настройки' , 'avatar' => 'Аватар' , 'logininfo' => 'Социализация' , 'commenting' => 'Комментированное' , 'files' => 'Загрузки');
  
  $options['pages_profiles'] = isset($options['pages_profiles']) ? $options['pages_profiles'] : array('0'=>'Информация' , 'all' => 'Все действия' , 'files' => 'Загрузки' , 'comments' => 'Комментарии');
  
  $options['pages_main'] = isset($options['pages_main']) ? $options['pages_main'] : array('0'=>'Все пользователи' , 'all' => 'Все действия');	  
  $options['all_events_title'] = isset($options['all_events_title']) ? $options['all_events_title'] : 'Все события';
  
	$options['profile_plugins']   = isset($options['profile_plugins'])   ?      $options['profile_plugins']   : array('profile');

	$form = '';

	$form .= '<h2>' . t('Настройки', 'plugins') . '</h2>';

	$chk = $options['upload_avatar'] ? ' checked="checked"  ' : '';
	$form .= '<p><label><input name="f_upload_avatar" type="checkbox" ' . $chk . '> <strong>' . t('Загрузка аватара.') . '</strong></label><br />';
	$form .= t('Если отмечено, разрешаем загружать аватар комюзерами.'). '</p>';

	$form .= '<p><strong>' . t('Слуг публичной страницы профилей пользователей') . '</strong><input name="f_profiles_slug" type="text" value="' . $options['profiles_slug'] . '" /></p>';
	$form .= '<p><strong>' . t('Слуг страницы личного кабинета') . ' </strong><input name="f_profile_slug" type="text" value="' . $options['profile_slug'] . '" /></p>';
	$form .= '<p><strong>' . t('Ссылка на регистрацию<br>(registration - использовать форму MaxSite)') . '</strong> <input name="f_register_slug" type="text" value="' . $options['register_slug'] . '" /></p>';
	$form .= '<p><strong>' . t('Заголовок личного кабинета') . '</strong> <input name="f_title" type="text" value="' . $options['title'] . '" /></p>';	
	$form .= '<p><strong>' . t('Заголовок "Пользователи"') . '</strong> <input name="f_profiles_title" type="text" value="' . $options['profiles_title'] . '" /></p>';	
	$form .= '<p><strong>' . t('Заголовок "Все действия"') . '</strong> <input name="f_all_events_title" type="text" value="' . $options['all_events_title'] . '" /></p>';		
	$form .= '<p><strong>' . t('Выход') . '</strong> <input name="f_exit" type="text" value="' . $options['exit'] . '" /></p>';	
	$form .= '<p><strong>' . t('Привет') . '</strong> <input name="f_hello" type="text" value="' . $options['hello'] . '" /></p>';	

	$form .= '<p><strong>' . t('Кол-во получения событий за шаг') . '</strong> <input name="f_events_count" type="text" value="' . $options['events_count'] . '" /></p>';	

	// преобразуем массив
	$options_pages = '';
	foreach ($options['pages'] as $slug=>$title)
  {
    if ($slug=='0') $options_pages .= ' | ' . $title . NR;
    else $options_pages .= $slug . ' | ' . $title . NR;
  }

	$options_pages_profiles = '';
	foreach ($options['pages_profiles'] as $slug=>$title)
  {
    if ($slug=='0') $options_pages_profiles .= ' | ' . $title . NR;
    else $options_pages_profiles .= $slug . ' | ' . $title . NR;
  }
  
	$options_pages_main = '';
	foreach ($options['pages_main'] as $slug=>$title)
  {
    if ($slug=='0') $options_pages_main .= ' | ' . $title . NR;
    else $options_pages_main .= $slug . ' | ' . $title . NR;
  }

	$form .= '<p><strong>' . t('Пункты меню Линого Кабинета:', 'plugins') . '</strong></p> <textarea name="f_pages" rows="10" cols="40">' . $options_pages . '</textarea>' ;

	$form .= '<p><strong>' . t('Пункты меню Публичных Страниц Профиля:', 'plugins') . '</strong></p> <textarea name="f_pages_profiles" rows="10" cols="40">' . $options_pages_profiles . '</textarea>' ;
	
	$form .= '<p><strong>' . t('Пункты меню главных страниц всех профайлов:', 'plugins') . '</strong></p> <textarea name="f_pages_main" rows="10" cols="40">' . $options_pages_main . '</textarea>' ;
	
	$chk = $options['no_cache'] ? ' checked="checked"  ' : '';
	$form .= '<p><label><input name="f_no_cache" type="checkbox" ' . $chk . '> <strong>' . t('Не кешировать списки событий.') . '</strong></label><br />';
	$form .= t('Если отмечено, списки событий не будут кешироваться (например, для отладки).'). '</p>';	
	
	$form .= '<h2>' . t('Плагины, которые подключают свои элементы (в папках указанных плагинов ищутся файлы с именем элемента в папке profile плагина , в котором содержится информация об элементах плагина.)', 'plugins') . '</h2>';

	$form .= '<p><strong>' . t('Список плагинов:') . '</strong> <input name="f_profile_plugins" type="text" value="' . implode(", " , $options['profile_plugins']) . '" /></p>';	




	echo '<form action="" method="post">' . mso_form_session('f_session_id');
	echo $form;
	echo '<br><input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;"></form>';

