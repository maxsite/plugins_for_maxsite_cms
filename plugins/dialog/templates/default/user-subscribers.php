<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// страница управления подписками пользователя

// доступна только для хозяина

   // получаем дискуссии с подпиской
   
   $watch_id_array = dialog_get_subscribers(array('watch_user_id'=>$edit_profile['profile_user_id']) );
   if ($watch_id_array)
       $discussions = dialog_get_discussions(array('user_id' => $comuser_id , 'id_array' => $watch_id_array , 'role_id' => $comuser_role) , $options);
   else $discussions = false;
   // есть подписки
   if ($discussions)
   {    
        $flag_subscribe_button = true; // флаг о том что ужно выводить Пописаться/Отписаться
        $out = '';
        // вывод дискуссий, что в массиве $discussions
        $fn = 'out-discussions.php';
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
        else 
          require($template_default_dir . $fn);            
        echo $out;
   }
   else echo 'Нет подписок.';

 
?>