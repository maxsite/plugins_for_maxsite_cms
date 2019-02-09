<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
//Главная страница галерей (альбомов) для плагина taggallery.
//Выводятся все альбомы и все галереи в каждом из них
//если альбомов нет то выводятся все галереи

if ($options['all_gallerys_desc']) $desc = $options['all_gallerys_desc']; else $desc = $options['gallery_desc'];

mso_head_meta('title', $options['all_gallerys_text']); 
mso_head_meta('description', $desc ); 
mso_hook_add('head', 'taggallery_css');  

// Начало вывода_______________________________________________________________
require(getinfo('template_dir') . 'main-start.php');
echo NR . '<div class="gallery_page">' . NR;

//Выводим  заголовок и описание галереи изображений ______________________________ 
 echo '<div class="breadcrumbs">' . $main_link . $options['breadcumbs_razd'] . $options['all_gallerys_text'] . '</div>';
 echo '<H1>' . $options['all_gallerys_text'] . '</H1>'; 

 $cache_key = 'taggallery_all_gallery';
// $out = mso_get_cache($cache_key);
 $out = '';
 if (!$out)
 {
     if ($options['all_gallerys_desc'])
     {
         $out .=  '<div class="gallery-content">' . NR; 
         $out .= $options['all_gallerys_desc'];
         $out .= '</div>' . NR;
     }
 
   $template_options = mso_get_option('taggallery_' . $options['template'] , 'taggallery', array());
   $options = array_merge($template_options, $options);
   // подключим файл инициализации дефолтных опций шаблона
   require($template_dir . 'options_default.php'); 
   
   $gallerys = taggallery_get_gallerys();
   require($template_dir . 'gallerys_out.php'); 
   
   // блок последних страниц
   require($template_dir . 'blok-last-pictures.php');    
   
   require($template_dir . 'gallery-posle.php'); 

 }

 echo($out);

 $error = false; // тут ошибки невозможны


// конец вывода
echo NR . '</div><!-- class="gallery_page" -->' . NR;
require(getinfo('template_dir') . 'main-end.php');
 
?>