<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
 
 // Главный файл дефолтного шаблона форума Dialog
 // подключим файл предварительной подготовки общих данных перед выводом 
 
 $fn = 'prepare.php'; 
 if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
     require($template_dir . $fn);
 else 
     require($template_default_dir . $fn);
  
  
  // выясним - какой файл шаблона подключать

 // Если у нас главная страница
 if ($segment1 == $options['main_slug'])
 {
   if (!$segment2)
   {
     $fn = 'main.php'; 
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);
   } 
   elseif ($segment2 == $options['all-comments_slug'])
   {
     $fn = 'all-comments.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);     
   }
   elseif ($segment2 == $options['all-discussions_slug'])
   {
     $fn = 'all-discussions.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);     
   }   
   elseif ($segment2 == $options['news_slug'])
   {
     $fn = 'news.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);     
   }   
   elseif ($segment2 == $options['log_slug'])
   {
     $fn = 'log.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);    
   }   
   elseif ($segment2 == $options['new_private_slug'])
   {
     $fn = 'new_private.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);    
   } 
   elseif ($segment2 == $options['private_slug'])
   {
     $fn = 'private.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);    
   } 

   else
   {
     $fn = 'category.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);     
   }     
 }
 

 // Если у нас страница дискуссии
 elseif ($segment1 == $options['discussion_slug'])
 {
   $fn = 'discussion.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);
 }
 
 
 
 // Если у нас страница редактирования дискуссии
 elseif ($segment1 == $options['edit_discussion_slug'])
 {
   $fn = 'edit_discussion.php';
   if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
       require($template_dir . $fn);
   else 
       require($template_default_dir . $fn);
 } 
 
 // Если у нас страница комментария
 elseif ($segment1 == $options['comment_slug'])
 {
   $fn = 'comment.php';
   if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
   else 
        require($template_default_dir . $fn);
 } 



 // Если у нас страница профайлов
 elseif ($segment1 == $options['profile_slug'])
 { 
      if ($segment2 == $options['main_slug'])
      {
         $fn = 'profiles.php';
         if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
             require($template_dir . $fn);
         else 
             require($template_default_dir . $fn);      
      }
      else
      {
         // конкретный профайл
         $fn = 'user.php';
         if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
             require($template_dir . $fn);
         else 
             require($template_default_dir . $fn);       
      }   
 } 
 
 
 
  //если редирект
   elseif ($segment1 == $options['goto_slug'])
   {
     $fn = 'goto.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);     
   }  
   
   
  //если отписка
   elseif ($segment1 == $options['unsubscribe_slug'])
   {
     $fn = 'unsubscribe.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);     
   } 



 // Если у нас страница личного кабинета и кто-то залогинен
 elseif ($segment1 == $options['comuser_profile_slug'])
 {
    //  выясним про код активации
   if ($comuser['comusers_activate_string'] != $comuser['comusers_activate_key']) // нет активации 
         return $args;
         
    $fn = 'comuser_profile.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn);    
 } 



 if ($error) // если не найдено что выводить
 {
	header('HTTP/1.0 404 Not Found');
		
	# начальная часть шаблона
	require(getinfo('shared_dir') . 'main/main-start.php');
	echo NR . '<div class="dialog_page">' . NR;
	
	echo '<h1>' . t('404. Ничего не найдено...') . '</h1>';
	echo '<p>' . $error . '</p>';
    echo '<p>' . $options['text_go_to_main'] . $main_link . '</p>';
	//	echo mso_hook('page_404');
	echo '</div>';
		# конечная часть шаблона
	require(getinfo('shared_dir') . 'main/main-end.php');	
	}



?>