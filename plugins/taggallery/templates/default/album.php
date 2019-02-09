<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
//Страница отображения групп галерей (альбомов) для плагина taggallery.


// определим ключ-слуг для получения альбома
if ($options['album_prefix']) $album_slug = str_replace($options['album_prefix'] , "" , $segment2);
else $album_slug = $segment2;

if ($album_slug)
{  
  // получаем альбом
  $albums = taggallery_get_albums(array('album_slug' => $album_slug));
  if (isset($albums[0]) and $albums[0]) // если альбом есть
  {
     $album = $albums[0];
     
     // получим опции шаблона
     $template_options = mso_get_option('taggallery_' . $options['template'] , 'taggallery', array());
     $options = array_merge($template_options, $options);

     // подключим файл инициализации дефолтных опций шаблона
     require($template_dir . 'options_default.php'); 

     // формируем титл страницы___________________________________________________________________________________
     mso_head_meta('title', $album['album_title'] , $album['album_title']); // meta title страницы
     mso_head_meta('description', $album['album_desc'] ); // meta title страницы
     mso_hook_add('head', 'taggallery_css');  


     // Начало вывода_______________________________________________________________
     require(getinfo('template_dir') . 'main-start.php');

    $cache_key = 'taggallery_album' . $album_slug;
    if ($options['cache_flag']) $out = mso_get_cache($cache_key);
    else $out = '';

    if ($out) echo $out;
	  else
	  {
	  
	  	$out .= NR . '<div class="gallery_page">' . NR;

     // хлебные крошки
     $out .= '<div class="breadcrumbs">' . $main_link . $options['breadcumbs_razd'] . $album['album_title'] . '</div>';
     
     // Выводим  заголовок и описание альбома ______________________________ 
     
    $edit_url = getinfo('site_admin_url') . 'taggallery/albums/';
    if (is_login()) $edit_link = '<a href = "' . $edit_url . '"><img src="' . $template_url . 'images/edit.png" width="16" height="16" alt="" title="Edit albums" class="right"></a>';
    else $edit_link = '';     
     
     $out .= '<H1>' . $options['text_album_title'] . $album['album_title'] . $edit_link . '</H1>'; 
     
      if ($album['album_desc'])
      {
         $out .=  '<div class="gallery-content">' . NR; 
         $out .= $album['album_desc'];
         $out .= '</div>' . NR;
      }

     if ($gallerys = taggallery_get_gallerys(array('album_id' => $album['album_id']))) // если альбом содержит галереи
     {
       require($template_dir . 'gallerys_out.php');
     }
     else $out .= $options['text_album_empty'];
     
     $out .= '<div class="break"></div>';

     // блок последних картинок
     require($template_dir . 'blok-last-pictures.php'); 

     require($template_dir . 'gallery-posle.php'); 



     $out .= '</div><!-- div class=gallery_page -->';
     
     echo $out;
 	   if ($options['cache_flag']) mso_add_cache($cache_key, $out); // сразу в кэш добавим
   }  
     require(getinfo('template_dir') . 'main-end.php');
     
     $error = false; 
  }
  else $error = $options['text_not_album'];
}  
else $error = $options['text_album_error'];

 
?>