<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


    // получим лог юзера
    $log = dialog_get_log(array('user_id'=>$edit_profile['profile_user_id']));
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
	else echo 'Не совершались';
    
	
?>


