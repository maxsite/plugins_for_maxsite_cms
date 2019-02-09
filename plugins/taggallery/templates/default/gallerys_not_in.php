<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
//Страница отображения галерей не в альбоме для плагина taggallery.


$gallerys = taggallery_get_gallerys(array('nerazobrannoe' => true));

if ($gallerys)
{  

     $template_options = mso_get_option('taggallery_' . $options['template'] , 'taggallery', array());
     $options = array_merge($template_options, $options);

     // подключим файл инициализации дефолтных опций шаблона
     require($template_dir . 'options_default.php'); 

     $title = $options['gallerys_not_in_text'];
     if ($options['gallerys_not_in_desc']) $desc = $options['gallerys_not_in_desc']; else $desc = $options['gallery_desc'];

     // формируем титл страницы___________________________________________________________________________________
     mso_head_meta('title', $options['gallery_name'] , $title); // meta title страницы
     mso_head_meta('description', $desc ); // meta title страницы
     mso_hook_add('head', 'taggallery_css');  


     // Начало вывода_______________________________________________________________
     require(getinfo('template_dir') . 'main-start.php');

    $cache_key = 'taggallery_gallery_not_in';
    if ($options['cache_flag']) $out = mso_get_cache($cache_key);
    else $out = '';

    if ($out) echo $out;
	  else
	  {
	  
	  	$out .= NR . '<div class="gallery_page">' . NR;

     // хлебные крошки
     $out .= '<div class="breadcrumbs">' . $main_link . $options['breadcumbs_razd'] . $title . '</div>';
     
     // Выводим  заголовок и описание альбома ______________________________ 
     
     $out .= '<H1>' . $title . '</H1>'; 
     
      if ($options['gallerys_not_in_desc'])
      {
         $out .=  '<div class="gallery-content">' . NR; 
         $out .= $options['gallerys_not_in_desc'];
         $out .= '</div>' . NR;
      }

     require($template_dir . 'gallerys_out.php');
     
     $out .= '<div class="break"></div>';
     
     require($template_dir . 'gallery-posle.php'); 

     $out .= '</div><!-- div class=gallery_page -->';
     
     echo $out;
 	   if ($options['cache_flag']) mso_add_cache($cache_key, $out); // сразу в кэш добавим
   }  
   
   require(getinfo('template_dir') . 'main-end.php');
     
   $error = false; 

}  
else $error = 'Галерей, не вошедших в альбом, нет';

 
?>