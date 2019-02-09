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
require($plugin_dir . 'plugin_options_default.php');



	if ( $post = mso_check_post(array('f_session_id', 'f_submit')) )
	{
		mso_checkreferer();
	  
		if ( isset($post['f_template']) ) $options['template'] = $post['f_template'];
		
		if ( isset($post['f_editor_name']) ) $options['editor_name'] = $post['f_editor_name'];
		
		if ( isset($post['f_rate_alow_send']) ) $options['rate_alow_send'] = $post['f_rate_alow_send']; 
		
		
		if ( isset($post['f_name']) ) $options['name'] = trim($post['f_name']);
		if ( isset($post['f_desc']) ) $options['desc'] = trim($post['f_desc']); 
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
		
		if ( isset($post['f_comments_on_page']) ) $options['comments_on_page'] = trim($post['f_comments_on_page']);
		if ( isset($post['f_count_activity']) ) $options['count_activity'] = trim($post['f_count_activity']);
		
		if ( isset($post['f_bad_count']) ) $options['bad_count'] = trim($post['f_bad_count']);

		if ( isset($post['f_comments_count_allow_private']) ) $options['comments_count_allow_private'] = trim($post['f_comments_count_allow_private']);

		if ( isset($post['f_css']) ) $options['css'] = mso_explode($post['f_css'] , false , false);
		if ( isset($post['f_tags']) ) $options['tags'] = trim($post['f_tags']);
		if ( isset($post['f_email_tags']) ) $options['email_tags'] = trim($post['f_email_tags']);
		
		if ( isset($post['f_noword']) ) $options['noword'] = trim($post['f_noword']);		
		if ( isset($post['f_delta_time']) ) $options['delta_time'] = trim($post['f_delta_time']);
		if ( isset($post['f_moderate']) ) $options['moderate'] = trim($post['f_moderate']);
		if ( isset($post['f_rate_func']) ) $options['rate_func'] = trim($post['f_rate_func']);
		if ( isset($post['f_allow_edit_time']) ) $options['allow_edit_time'] = (int) $post['f_allow_edit_time'];
		if ( isset($post['f_admin_email']) ) $options['admin_email'] = trim($post['f_admin_email']);

    $options['cache_flag']  = isset($post['f_cache_flag']) ? TRUE : FALSE;
    $options['discussion_free']  = isset($post['f_discussion_free']) ? TRUE : FALSE;
    $options['xss_clean']  = isset($post['f_xss_clean']) ? TRUE : FALSE;
    $options['xss_clean_die']  = isset($post['f_xss_clean_die']) ? TRUE : FALSE;
    $options['check_repeat']  = isset($post['f_check_repeat']) ? TRUE : FALSE;
  //  $options['comment_plugins']  = isset($post['f_comment_plugins']) ? TRUE : FALSE;
    $options['answers']  = isset($post['f_answers']) ? TRUE : FALSE;
    $options['style_button']  = isset($post['f_style_button']) ? TRUE : FALSE;
    $options['flag_redirect']  = isset($post['f_flag_redirect']) ? TRUE : FALSE;
    
    if ( isset($post['f_adds_forum']) ) $options['adds_forum'] = mso_explode($post['f_adds_forum']);

		mso_add_option('dialog', $options , 'plugins');

  	$message = 'Обновлено!' ;
		echo '<div class="update">' . t($message, 'plugins') . '</div>';
	}

	
		
	# получим имеющиеся шаблоны 
	$templates = directory_map(getinfo('plugins_dir') . 'dialog/templates', true); // 
	$list_templates = array();
	
	if ($templates)
	 foreach($templates as $template)
   {
	  $template_dir = getinfo('plugins_dir') . 'dialog/templates/' . $template;
		if (is_dir($template_dir) and file_exists($template_dir . '/info.php'))
		{
		  $list_templates[$template] = $template;
		} 
	 }		
	 
	# получим имеющиеся редакторы 
	$editors = directory_map(getinfo('plugins_dir') . 'dialog/editors', true); // 
	$list_editors = array();
	
	if ($editors)
	 foreach($editors as $editor)
   {
	  $editor_dir = getinfo('plugins_dir') . 'dialog/editors/' . $editor;
		if (is_dir($editor_dir) and file_exists($editor_dir . '/go.php'))
		{
		  $list_editors[$editor] = $editor;
		} 
	 }	

	 
	         
		$form = '';
		$form .= '<H3>' . t('Настройки функционирования форума.', 'plugins') . '</H3>';
		
		$form .= '<H3>' . 'Основные'  . '</H3>';
		
		$form .= '<table>';

		$form .= '<tr><td>' . t('Шаблон форума:', 'plugins') . ' </td><td>' . form_dropdown('f_template', $list_templates , $options['template']) . '</td></tr>';		

 // сформируем ссылку на настройки шаблона
	//	$form .= '<tr><td><strong><a href="' . $template_setting_url .'" target="_blank">Опции шаблона</a></strong></td></tr>';
		
		$form .= '<tr><td>' . t('Название форума', 'plugins') . ' </td>' . '<td><input name="f_name" type="text" value="' . $options['name'] . '"></td></tr>';
		
		$form .= '<tr><td>' . t('Описание форума', 'plugins') . ' </td>' . '<td><textarea rows="3" name="f_desc">' . $options['desc'] . '</textarea></td></tr>';
		
		$form .= '<tr><td>' . t('Редактор форума:', 'plugins') . ' </td><td>' . form_dropdown('f_editor_name', $list_editors , $options['editor_name']) . '</td></tr>';		

		$form .= '<tr><td>' . t('Номера сообщений, после которых<br />выводится ушка "adds_forum"<br />разделить пробелами/запятыми', 'plugins') . ' </td>' . '<td><input name="f_adds_forum" type="text" value="' . implode(", " , $options['adds_forum']) . '"></td></tr>';
		
		$form .= '<tr><td>' . t('Начально сообщений на странице<br />(может быть персональным)', 'plugins') . ' </td>' . '<td><input name="f_comments_on_page" type="text" value="' . $options['comments_on_page'] . '"></td></tr>';
		
		$form .= '<tr><td>' . t('Активных дискуссий', 'plugins') . ' </td>' . '<td><input name="f_count_activity" type="text" value="' . $options['count_activity'] . '"></td></tr>';

		$form .= '<tr><td>' . t('Кол-во жалоб<br />отправляющих на модерацию', 'plugins') . ' </td>' . '<td><input name="f_bad_count" type="text" value="' . $options['bad_count'] . '"></td></tr>';
		
		$form .= '<tr><td>' . t('Сколько первых комментариев модерировать<br />Если 0 - не модерировать', 'plugins') . ' </td>' . '<td> <input name="f_moderate" type="text" value="' . $options['moderate'] . '"></td></tr>';

		$form .= '<tr><td>' . t('Кол-во комментов, разрешающее начинать приват', 'plugins') . ' </td>' . '<td><input name="f_comments_count_allow_private" type="text" value="' . $options['comments_count_allow_private'] . '"></td></tr>';
		
		$form .= '<tr><td>' . t('Рейтинг, разрешающий отправлять письма пользователям', 'plugins') . ' </td>' . '<td><input name="f_rate_alow_send" type="text" value="' . $options['rate_alow_send'] . '"></td></tr>';		
		
     $chckout = ''; 
 //    if (!isset($options['cache_flag']))  $options['cache_flag'] = false;
     if ( (bool)$options['cache_flag'] )
        {
            $chckout = 'checked="true"';
        } 	
	
	  $form .= '<tr><td>' . t('Кешировать страницы форума', 'plugins') . '</td>' . '<td> <input name="f_cache_flag" type="checkbox" ' . $chckout . '></td></tr>';


     $chckout = ''; 
  //   if (!isset($options['comment_plugins']))  $options['comment_plugins'] = false;
     if ( (bool)$options['comment_plugins'] )
        {
            $chckout = 'checked="true"';
        } 	
/*	
	  $form .= '<tr><td>' . t('JS плагинов комментов<br />(для comment_buttons) ', 'plugins') . '</td>' . '<td> <input name="f_comment_plugins" type="checkbox" ' . $chckout . '></td></tr>';
*/
     $chckout = ''; 
//     if (!isset($options['discussion_free']))  $options['discussion_free'] = false;
     if ( (bool)$options['discussion_free'] )
        {
            $chckout = 'checked="true"';
        } 	
	
	  $form .= '<tr><td>' . t('Разрешить ли дискуссии без категорий', 'plugins') . '</td>' . '<td> <input name="f_discussion_free" type="checkbox" ' . $chckout . '></td></tr>';


     $chckout = ''; 
 //    if (!isset($options['answers']))  $options['answers'] = false;
     if ( (bool)$options['answers'] )
        {
            $chckout = 'checked="true"';
        } 	
	
	  $form .= '<tr><td>' . t('Подключать ответы<br />(увеличит запросы)', 'plugins') . '</td>' . '<td> <input name="f_answers" type="checkbox" ' . $chckout . '></td></tr>';


     $chckout = ''; 
  //   if (!isset($options['style_button']))  $options['style_button'] = false;
     if ( (bool)$options['style_button'] )
        {
            $chckout = 'checked="true"';
        } 	
	
	  $form .= '<tr><td>' . t('Выводить кнопку переключающую стили', 'plugins') . '</td>' . '<td> <input name="f_style_button" type="checkbox" ' . $chckout . '></td></tr>';
	  
	  $form .= '<tr><td>' . t('Время доступности коммента для изменения<br>в секундах', 'plugins') . '</td><td><input name="f_allow_edit_time" type="text" value="' . $options['allow_edit_time'] . '"></td></tr>';
	  	  
		$form .= '<tr><td>' . t('Имена файлов стилей<br />(два файла, разделенные запятой или пробелом)', 'plugins') . ' </td><td><input name="f_css" type="text" value="' . implode(", " , $options['css']) . '"></td></tr>';
		
		$form .= '<tr><td>' . t('Адрес для писем админу', 'plugins') . ' </td>' . '<td><input name="f_admin_email" type="text" value="' . $options['admin_email'] . '"></td></tr>';
		
		
		$form .= '</table>';
		
		
		$form .= '<H3>' . 'Формирование адреса страниц форума.'  . '</H3>';
		
		$form .= '<table>';

		$form .= '<tr><td>' . t('Slug для главной', 'plugins') . ' </td>' . '<td><input name="f_main_slug" type="text" value="' . $options['main_slug'] . '"></td></tr>';

		$form .= '<tr><td>' . t('Slug новостей', 'plugins') . ' </td>' . '<td> <input name="f_news_slug" type="text" value="' . $options['news_slug'] . '"></td></tr>';		

		$form .= '<tr><td>' . t('Slug для сообщения', 'plugins') . ' </td>' . '<td> <input name="comment_slug" type="text" value="' . $options['comment_slug'] . '"></td></tr>';
		    
		$form .= '<tr><td>' . t('Slug всех дискуссий', 'plugins') . ' </td>' . '<td> <input name="f_all-discussions_slug" type="text" value="' . $options['all-discussions_slug'] . '"></td></tr>';
				
		$form .= '<tr><td>' . t('Slug всех сообщений', 'plugins') . ' </td>' . '<td> <input name="f_all-comments_slug" type="text" value="' . $options['all-comments_slug'] . '"></td></tr>';				
				


		$form .= '<tr><td>' . t('Slug дискуссии', 'plugins') . ' </td>' . '<td> <input name="f_discussion_slug" type="text" value="' . $options['discussion_slug'] . '"></td></tr>';		

	
		$form .= '<tr><td>' . t('Slug подписки', 'plugins') . ' </td>' . '<td> <input name="f_subscribe_slug" type="text" value="' . $options['subscribe_slug'] . '"></td></tr>';				
				
		$form .= '<tr><td>' . t('Slug отписки', 'plugins') . ' </td>' . '<td> <input name="f_unsubscribe_slug" type="text" value="' . $options['unsubscribe_slug'] . '"></td></tr>';	

		$form .= '<tr><td>' . t('Slug редактирования дискуссии', 'plugins') . ' </td>' . '<td> <input name="f_edit_discussion_slug" type="text" value="' . $options['edit_discussion_slug'] . '"></td></tr>';		

		$form .= '<tr><td>' . t('Slug редиректа на коммент', 'plugins') . ' </td>' . '<td> <input name="f_goto_slug" type="text" value="' . $options['goto_slug'] . '"></td></tr>';

		$form .= '</table>';

		
		
			
		$form .= '<H3>' . 'Опции обработки сообщения при добавлении.'  . '</H3>';

		$form .= '<div class="admin_plugin_options">';
		$form .= '<p>' . t('Разрешенные теги', 'plugins') . ' </p>' . '<input name="f_tags" type="text" value="' . $options['tags'] . '">';
	  $form .= '</div>';
		$form .= '<div class="admin_plugin_options">';
		$form .= '<p>' . t('Разрешенные теги в письмах', 'plugins') . ' </p>' . '<input name="f_email_tags" type="text" value="' . $options['email_tags'] . '">';
	  $form .= '</div>';
	  		
		$form .= '<div class="admin_plugin_options">';
		     $chckout = ''; 
  //   if (!isset($options['xss_clean']))  $options['xss_clean'] = false;
     if ( (bool)$options['xss_clean'] )
        {
            $chckout = 'checked="true"';
        } 	
	  $form .= '<p>' . t('Очищать от xss', 'plugins') . ' <input name="f_xss_clean" type="checkbox" ' . $chckout . '></p>';
	  $form .= '</div>';
	  
		$form .= '<div class="admin_plugin_options">';
		     $chckout = ''; 
  //   if (!isset($options['xss_clean_die']))  $options['xss_clean_die'] = false;
     if ( (bool)$options['xss_clean_die'] )
        {
            $chckout = 'checked="true"';
        } 	
	  $form .= '<p>' . t('Рубить при xss', 'plugins') . ' <input name="f_xss_clean_die" type="checkbox" ' . $chckout . '></p>';
	$form .= '</div>';

		$form .= '<div class="admin_plugin_options">';
		     $chckout = ''; 
  //   if (!isset($options['check_repeat']))  $options['check_repeat'] = false;
     if ( (bool)$options['check_repeat'] )
        {
            $chckout = 'checked="true"';
        } 	
	  $form .= '<p>' . t('Проверять ли коммент на повтор (проверка автора, текста, дискуссии)', 'plugins') . ' <input name="f_check_repeat" type="checkbox" ' . $chckout . '></p>';		
	$form .= '</div>';
		
		$form .= '<div class="admin_plugin_options">';
		$form .= '<p>' . t('Запрещенные слова и фразы (разелить запятыми или пробелами)', 'plugins') . '</p><input name="f_noword" type="text" value="' . $options['noword'] . '">';
	$form .= '</div>';

		$form .= '<div class="admin_plugin_options">';
		$form .= '<p>' . t('Ф-я вычисления рейтинга (php без ошибок), например:<br />$rate = (int) ( ##comments_count##/20 + ##dankes_count##/4 + ##votes##/2 );<br />##comments_count## - кол-во комментариев пользователя<br />##dankes_count## - кол-во спасибо этому пользователю<br />##votes## - сумма положительных и отрицательных оценок комментариев', 'plugins') . '</p> <input name="f_rate_func" type="text" value="' . $options['rate_func'] . '">';	
	$form .= '</div>';
	
		$form .= '<div class="admin_plugin_options">';
		$form .= '<p>' . t('min время между комментами (в секундах)<br>(если 0 - проверки нет)', 'plugins') . '</p><input name="f_delta_time" type="text" value="' . $options['delta_time'] . '">';
	$form .= '</div>';

     $chckout = ''; 
   //  if (!isset($options['flag_redirect']))  $options['flag_redirect'] = false;
     if ( (bool)$options['flag_redirect'] )
        {
            $chckout = 'checked="true"';
        } 	
	
	  $form .= '<p>' . t('Редирект включить (для отладки выключить)', 'plugins') . ' <input name="f_flag_redirect" type="checkbox" ' . $chckout . '></p>';

   $form .= '<input type="submit" name="f_submit" value="' . t('Сохранить изменения', 'plugins') . '" style="margin: 25px 0 5px 0;" />';
		
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
        echo $form;
		echo '</form>';

?>