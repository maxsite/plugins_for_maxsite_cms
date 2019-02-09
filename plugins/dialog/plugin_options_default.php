<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// в этом файле инициализируются дефолтные опции  

  $default_options_array = array(
    'name' => 'Форум' ,
    'desc' => 'Тестовый форум' ,
    'cache_flag' => false ,
    'comment_plugins' => false ,
    'bad_count' => 0,  
    'discussion_free' => true , // разрешить дискуссии без категории (свободные дискуссии)
    'template' => 'default' ,
    
    'main_slug' => 'forum' ,
      'news_slug' => 'news' ,
      'all-comments_slug' => 'all-comments' ,
      'all-discussions_slug' => 'all-discussions' ,
      
      'comments_slug' => 'forum-comments' ,
      'comments_slug_profile' => 'forum-comments' ,
      'dankes_slug_profile' => 'dankes1' ,
      
    
    'discussion_slug' => 'discussion' ,
    'comment_slug' => 'comment' ,
    
    'profile_slug' => 'profiles' ,
    'comuser_profile_slug' => 'profile' ,
      'settings_slug' => 'forum' ,
      'settings_subscribe_slug' => 'forum-subscribe' ,
      
      'subscribe_slug' => 'subscribe' ,
      'guds_slug' => 'dankes' ,
      'send_email_slug' => 'email',
      'private_slug' => 'private',
      'activity_slug' => 'activity',
    
    'edit_discussion_slug' => 'edit_discussion' ,
    'unsubscribe_slug' => 'unsubscribe' ,

    'goto_slug' => 'goto' ,
    
    'log_slug' => 'log' ,
    'new_private_slug' => 'new_private' ,
	
    'pages_profiles' => array(
	   '0'=>'Информация' , 
	   'forum-comments' => 'Сообщения' , 
	   'email' => 'Отправить сообщение' , 
	   'dankes' => 'Репутация'
	   ),
	
	'pages_profile' => array(
       'forum' => 'Настройки форума',
       'forum-subscribe' => 'Подписки форума',
       'log' => 'Админдействия'
       ),
	
    'comments_count_allow_private' => 1 ,
    
    'comments_on_page' => 10 , // 0 - пагинации нет
    'count_activity' => 10,
    'pag_activity' => 0, // 0 - пагинации нет
     'cat_disc_count'=>3,
     
     
    'style_button' => false,
    
    'css' => array('css1.css' , 'css2.css'),
    'answers' => true, 
    
    'rate_alow_send' => 1,
    'rate_func' =>'$rate = (int) ( ##comments_count##/20 + ##dankes_count##/4 + ##votes##/2);',

    'discussion_lenght' => 1, //  период в днях для вычисления плотности дискуссии 
    
    'editor_name' => 'default', 
    
    'adds_forum' => array(3,15,25),
    'allow_edit_time' => 12000,
    // опции обработки добавляемых комментов
    'tags' => '<ol><li><ul><a><p><blockquote><br><span><strong><strong><em><i><b><u><s><pre><code><img><div>' ,
    'email_tags' => '<ol><li><ul><a><p><blockquote><br><span><strong><strong><em><i><b><u><s><pre><code><img><div>' ,
    
    'xss_clean' => true ,
    'xss_clean_die' => false ,
    'noword' => '', 
    'check_repeat' => true,
    'delta_time' => 0,
    'moderate' => 0,
   
    // отладка
    'flag_redirect' => true,
    
    // альтернативный email
    'admin_email' => '',
		)   ; 

foreach ($default_options_array as $key => $val)
    if (!isset($options[$key])) $options[$key] = $val;

     
?>