<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// выводятся дискуссии, в которых есть новые комменты



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

 // для каждого комюзера свои новости
 $cache_key = 'dialog_news_' . $comuser_id;
 if ($options['cache_flag']) $out = mso_get_cache($cache_key);
 else $out = '';

 if ($out) echo $out;
 else 
 {
   // хлебные крошки
    $out .= '<div class="breadcrumbs">' . $main_link  . $options['breadcrumbs_razd'] . $options['news'] . '</div>'; 
 
   //Выводим  заголовок и описание  ______________________________ 
   $out .= '<H1>' . $options['news_title']  . '</H1>';
   if ($options['news_desc']) $out .= '<p>' . $options['news_desc']  . '</p>';

    $discussions = dialog_get_discussions(array('user_id' => $comuser_id , 'news' => true , 'role_id' => $comuser_role) , $options);
    if ($discussions)
    {
       $fn = 'out-discussions.php';
       if ( ($template_default_dir != $template_dir) and (file_exists($template_dir . $fn)) )
          require($template_dir . $fn);
       else 
          require($template_default_dir . $fn);        
    }
    else $out .= 'Нет непросмотренных дискуссий';
 	 if ($options['cache_flag']) mso_add_cache($cache_key, $out); // сразу в кэш добавим
 	 
   echo($out);
 }

 	 
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