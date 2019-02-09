<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

	$CI = & get_instance();
	$CI->load->helper('form');
	$CI->load->helper('directory');
	
 //опции 
  
$options = mso_get_option('dialog' , 'plugins', array());
require($plugin_dir . 'plugin_options_default.php'); // инициализация дефолтных настроек

	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
	  
		if ( isset($post['f_main_slug']) ) $options['main_slug'] = trim($post['f_main_slug']); 
		if ( isset($post['f_news_slug']) ) $options['news_slug'] = trim($post['f_news_slug']);
		if ( isset($post['f_all-comments_slug']) ) $options['all-comments_slug'] = trim($post['f_all-comments_slug']);
		if ( isset($post['f_all-discussions_slug']) ) $options['all-discussions_slug'] = trim($post['f_all-discussions_slug']);
		if ( isset($post['f_comments_slug']) ) $options['comments_slug'] = trim($post['f_comments_slug']);
		if ( isset($post['f_discussion_slug']) ) $options['discussion_slug'] = trim($post['f_discussion_slug']);
		if ( isset($post['f_comment_slug']) ) $options['comment_slug'] = trim($post['f_comment_slug']);
		if ( isset($post['f_profile_slug']) ) $options['profile_slug'] = trim($post['f_profile_slug']);
		if ( isset($post['f_comuser_profile_slug']) ) $options['comuser_profile_slug'] = trim($post['f_comuser_profile_slug']);
		if ( isset($post['f_settings_slug']) ) $options['settings_slug'] = trim($post['f_settings_slug']);
		if ( isset($post['f_settings_subscribe_slug']) ) $options['settings_subscribe_slug'] = trim($post['f_settings_subscribe_slug']);

		if ( isset($post['f_comments_slug_profile']) ) $options['comments_slug_profile'] = trim($post['f_comments_slug_profile']);
		if ( isset($post['f_dankes_slug_profile']) ) $options['dankes_slug_profile'] = trim($post['f_dankes_slug_profile']);
		
		if ( isset($post['f_subscribe_slug']) ) $options['subscribe_slug'] = trim($post['f_subscribe_slug']);
		if ( isset($post['f_edit_discussion_slug']) ) $options['edit_discussion_slug'] = trim($post['f_edit_discussion_slug']);
		if ( isset($post['f_unsubscribe_slug']) ) $options['unsubscribe_slug'] = trim($post['f_unsubscribe_slug']);
		if ( isset($post['f_goto_slug']) ) $options['goto_slug'] = trim($post['f_goto_slug']);
		if ( isset($post['f_log_slug']) ) $options['log_slug'] = trim($post['f_log_slug']);


		$menu_pages_profiles = explode("\n", $post['f_pages_profiles']); // разбиваем по строкам
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
		
		$menu_pages_profiles = explode("\n", $post['f_pages_profile']); // разбиваем по строкам
		$options['pages_profile'] = array();
		foreach ($menu_pages_profiles as $menu_pages_profile)
		{
		   $arr = explode("|", $menu_pages_profile);
		   if (isset($arr[1]) and trim($arr[1]))
		   {
		     $arr[0] = trim($arr[0]);
             if ($arr[0])  $options['pages_profile'][$arr[0]] = trim($arr[1]);
             else $options['pages_profile']['0'] = trim($arr[1]);     
           }
		}		
				
		mso_add_option('dialog', $options , 'plugins');

  	    $message = 'Обновлено!' ;
		echo '<div class="update">' . t($message, 'plugins') . '</div>';
	}
	
	$options_pages_profiles = '';
	foreach ($options['pages_profiles'] as $slug=>$title)
    {
      if ($slug=='0') $options_pages_profiles .= ' | ' . $title . NR;
      else $options_pages_profiles .= $slug . ' | ' . $title . NR;
    }	

	$options_pages_profile = '';
	foreach ($options['pages_profile'] as $slug=>$title)
    {
      if ($slug=='0') $options_pages_profile .= ' | ' . $title . NR;
      else $options_pages_profile .= $slug . ' | ' . $title . NR;
    }
	
		$form = '';

		$form .= '<H2>' . 'Согласование с плагином Личного Кабинета' . '</H2>';
		$form .= '<H3>' . 'Формирование адресов страниц личного кабинета (приватных страниц).'  . '</H3>';
		
		$form .= '<table><tr><td>';
		
		$form .= '<table>';
		$form .= '<tr><td>' . t('Slug личного кабинета', 'plugins') . ' </td>' . '<td> <input name="f_comuser_profile_slug" type="text" value="' . $options['comuser_profile_slug'] . '"></td></tr>';
		$form .= '<tr><td>' . t('Slug настроек форума', 'plugins') . ' </td>' . '<td> <input name="f_settings_slug" type="text" value="' . $options['settings_slug'] . '"></td></tr>';
		$form .= '<tr><td>' . t('Slug подписок', 'plugins') . ' </td>' . '<td> <input name="f_settings_subscribe_slug" type="text" value="' . $options['settings_subscribe_slug'] . '"></td></tr>';	
		$form .= '<tr><td>'. t('Slug log', 'plugins'). ' </td>' . '<td><input name="f_log_slug" type="text" value="'. $options['log_slug']. '"></td></tr>';		
		$form .= '</table>';
		
		$form .= '</td><td>';
		$form .= '<p>В плагине личного кабинета нужно добавить <b>Пункты меню личного кабинета</b> (соответствующие настройкам слева):</p>';
		$form .= '<p><b>' . $options['settings_slug'] . ' | Настройки форума</b></p>';
		$form .= '<p><b>' . $options['settings_subscribe_slug'] . ' | Подписки форума</b></p>';
		$form .= '<p><b>' . $options['log_slug'] . ' | Админдействия</b></p>';
		$form .= '<p>Слуг личного кабинета должен совпадать: ' . $options['comuser_profile_slug'] . '</p>';
		$form .= '</td></tr></table>';

		$form .= '<H3>' . 'Формирование адресов страниц пользователя (публичных страниц).'  . '</H3>';
		$form .= '<table><tr><td>';
		$form .= '<table>';
		$form .= '<tr><td>' . t('Slug профайлов', 'plugins') . ' </td>' . '<td> <input name="f_profile_slug" type="text" value="' . $options['profile_slug'] . '"></td></tr>';
		$form .= '<tr><td>' . t('Slug сообщений', 'plugins') . ' </td>' . '<td><input name="f_comments_slug" type="text" value="' . $options['comments_slug'] . '"></td></tr>';		
		$form .= '<tr><td>'. t('Slug репутаций', 'plugins'). ' </td>' . '<td><input name="f_guds_slug" type="text" value="'. $options['guds_slug']. '"></td></tr>';		
				
		$form .= '</table>';
		$form .= '</td><td>';
		$form .= '<p>В плагине личного кабинета нужно добавить <b>Пункты меню Публичных Страниц Профиля</b> (соответствующие настройкам слева):</p>';
		$form .= '<p><b>' . $options['comments_slug'] . ' | Сообщения на форуме</b></p>';
		$form .= '<p><b>' . $options['guds_slug'] . ' | Репутация</b></p>';
		
		$form .= '<p>Слуг публичных страниц профиля пользователя должен совпадать: ' . $options['profile_slug'] . '</p>';
		$form .= '</td></tr></table>';		

	    $form .= '<p><strong>' . t('Пункты меню Публичных Страниц Профиля (если нет плагина profile):', 'plugins') . '</strong></p> <textarea name="f_pages_profiles" rows="10" cols="40">' . $options_pages_profiles . '</textarea>' ;
	    $form .= '<p><strong>' . t('Пункты меню Приватных Страниц Профиля (если нет плагина profile):', 'plugins') . '</strong></p> <textarea name="f_pages_profiles" rows="10" cols="40">' . $options_pages_profile . '</textarea>' ;
		

		$form .= '<H3>' . 'Формирование ссылок на элементы профиля пользователя из сообщения (блок под аватаром).' . '</H3>';
		$form .= '<p>Должны совпадать со слугом соответствующего элемента, задаваемым на странице <b>Элементы профиля</b> настроек форума</p>';
		$form .= '<table><tr><td>';
		$form .= '<table>';
		$form .= '<tr><td>' . t('Slug сообщений', 'plugins') . ' </td>' . '<td><input name="f_comments_slug_profile" type="text" value="' . $options['comments_slug_profile'] . '"></td></tr>';		
		$form .= '<tr><td>'. t('Slug благодарили', 'plugins'). ' </td>' . '<td><input name="f_dankes_slug_profile" type="text" value="'. $options['dankes_slug_profile']. '"></td></tr>';		
				
		$form .= '</table>';
		
		$form .= '</td></tr></table>';		
		
		
			

   $form .= '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;" />';
		
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
        echo $form;
		echo '</form>';

?>