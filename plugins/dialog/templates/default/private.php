<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// страница приватных дискуссий пользователя



mso_head_meta('title', $options['news_title']); 
mso_head_meta('description', $options['news_desc'] ); 

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
    echo '<div class="breadcrumbs">' . $main_link  . $options['breadcrumbs_razd'] . $options['title_private'] . '</div>'; 
 
   //Выводим  заголовок и описание  ______________________________ 
   echo '<H1>' . $options['title_private']  . '</H1>';
   echo '<p>' . $options['desc_private']  . '</p>';



   $discussions = dialog_get_discussions(array('private' => true , 'user_id' => $comuser_id , 'role_id' => $comuser_role) , $options);

   // есть 
   if ($discussions)
   {    
     //   $flag_subscribe_button = true; // флаг о том что ужно выводить Пописаться/Отписаться
        $out = '';
        // вывод дискуссий, что в массиве $discussions
        $fn = 'out-discussions.php';
        if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
        else 
          require($template_default_dir . $fn);            
        echo $out;
   }
   else echo 'Нет доступных приватных дискуссий.';


 	 
 $error = false; // тут ошибки невозможны
 
$fn = 'posle.php';
if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
   require($template_dir . $fn);
else 
   require($template_default_dir . $fn);
   
// конец вывода
echo NR . '</div><!-- class="dialog_page" -->' . NR;
require(getinfo('shared_dir') . 'main/main-end.php');
 
?>