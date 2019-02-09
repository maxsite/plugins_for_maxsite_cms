<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
	
// страница лога форума

  
   mso_head_meta('title', $options['all_log_title']); 
   mso_head_meta('description', $options['all_log_desc']); 
   
    $fn = 'head.php';
     if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
        require($template_dir . $fn);
     else 
        require($template_default_dir . $fn); 
        
  // Начало вывода_______________________________________________________________
  require(getinfo('shared_dir') . 'main/main-start.php');
  echo NR . '<div class="dialog_page">' . NR;    

  $fn = 'do.php';
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
      require($template_dir . $fn);
  else 
      require($template_default_dir . $fn); 
        
  // хлебные крошки
   $profiles_link = '<a href="' . $siteurl . $options['profile_slug'] . '">' . $options['profiles'] . '</a>';
  echo '<div class="breadcrumbs">' . $main_link  . $options['breadcrumbs_razd'] . $profiles_link . $options['breadcrumbs_razd'] . $options['all_log_title'] . '</div>';

  
  echo  '<H1>' . $options['all_log_title'] . '</H1>';
  if ($options['all_log_desc']) echo  '<H3>' . $options['all_log_desc'] . '</H3>';

    // получим весь лог форума
    $log = dialog_get_log();
    if ($log)
    {
      echo NR . '<div class="log_comment">';
      echo  '<H3>' . $options['all_log'] . '</H3>';
      // подключим вывод из массива $log 
      $fn = 'out_log.php';
      if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
         require($template_dir . $fn);
      else 
         require($template_default_dir . $fn);      
      echo '</div>' . NR;
    }
    
    
  $fn = 'posle.php';
  if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
      require($template_dir . $fn);
  else 
      require($template_default_dir . $fn);
    
    // конец вывода
    echo NR . '</div><!-- class="dialog_page" -->' . NR;
    require(getinfo('shared_dir') . 'main/main-end.php');		
    
    $error = false;




?>