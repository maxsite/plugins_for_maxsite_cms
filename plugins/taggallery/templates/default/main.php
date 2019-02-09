<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
//Главная страница галерей (альбомов) для плагина taggallery.
//Выводятся все альбомы и все галереи в каждом из них
//если альбомов нет то выводятся все галереи


mso_head_meta('title', $options['gallery_name']); 
mso_head_meta('description', $options['gallery_desc'] ); 
mso_hook_add('head', 'taggallery_css');  

// Начало вывода_______________________________________________________________
require(getinfo('template_dir') . 'main-start.php');

echo NR . '<div class="gallery_page">' . NR;

//Выводим  заголовок и описание галереи изображений ______________________________ 

 echo '<H1>' . $options['gallery_name'] . '</H1>'; 

 $cache_key = 'taggallery_main';
 if ($options['cache_flag']) $out = mso_get_cache($cache_key);
 else $out = '';
 
 if ($out) echo $out;
 else 
 {
   $template_options = mso_get_option('taggallery_' . $options['template'] , 'taggallery', array());
   $options = array_merge($template_options, $options);
   // подключим файл инициализации дефолтных опций шаблона
   require($template_dir . 'options_default.php'); 
  
   // получаем альбомы
   $albums = taggallery_get_albums();   
   // есть альбомы (используется ли функционал альбомов)?
     if ($albums)
     {
        $out .= '<div class="albums">' . NR;
           
           $out .= '<div class="album-title">' . NR;
           $out .= '<div class="album-thumb"></div>';
           $out .= '<div class="album-desc">' . $options['text_album_title'] . '</div>';
           $out .= '<div class="album-gallerys">' . $options['text_gallery_title'] . '</div>';
           $out .= '</div>' . NR;
          
         if ($options['album_width']) $width = ' width="' . $options['album_width'] . '" '; //ширина обложки заносится в добавку к html 
         else $width = '';  
           
        foreach ($albums as $album)
        {
           $album_url = $siteurl . $options['album_slug'] . '/' . $options['album_prefix'] . $album['album_slug'];
           $album_link = '<a href="' . $album_url . '">' . $album['album_title'] . '</a>';    
           if (!$album['album_thumb']) $album['album_thumb'] = $options['default_album_thumb_url']; 
           $album_thumb = '<img src ="' . $album['album_thumb'] . '" alt = "' . $album['album_title'] . '"' . $width . '>';  
           $album_thumb_link = '<a href="' . $album_url . '">' . $album_thumb . '</a>';    

           $out .= '<div class="album">' . NR;
      
           $out .= '<div class="album-thumb">' . NR;
           $out .= $album_thumb_link;
           $out .= '</div>' . NR;
      
           $out .= '<div class="album-desc">' . NR;
           $out .= '<h3>' . $album_link . '</h3>' . $album['album_desc'];
           $out .= '</div>' . NR;
            
          $out .= '<div class="album-gallerys">' . NR;
           // сформируем список галерей текущего альбома
          $gallerys_out = '';
          if (isset($album['gallerys']) and $album['gallerys'])
          foreach ($album['gallerys'] as $cur_gal)
          {
            $gallery_url = $siteurl . $options['gallery_slug'] . '/' . $options['gallery_prefix'] . $cur_gal['gallery_slug'];
            $gallery_link = '<a href="' . $gallery_url . '">' . $cur_gal['gallery_name'] . '</a>';
            if ($gallerys_out) $gallerys_out .= $options['album_gallerys_razd'] . $gallery_link;
            else $gallerys_out .= $gallery_link;
          }
          $out .= $gallerys_out; 
          $out .= '</div>' . NR;
    
          $out .= '</div>' . NR; // <div class="album">
          
        }
       $out .= '</div>' . NR; // <div class="albums">
       
       $out .= '<div class="other_gallerys"><H3>';
        if ($options['gallerys_not_in_text'] and taggallery_get_galerys_not_in_album()) 
        {
           $gallerys_not_in_url = $siteurl . $options['main_slug'] . '/' . $options['gallerys_not_in_slug'];
           $out .= '<span class = "left"><a href="' . $gallerys_not_in_url . '" title = "' . $options['gallerys_not_in_desc'] . '">' . $options['gallerys_not_in_text'] . '</a></span>';
        }   
       if ($options['all_gallerys_text']) 
        {
           $all_gallerys_url = $siteurl . $options['main_slug'] . '/' . $options['all_gallerys_slug'];
           $out .= '<span class = "right"><a href="' . $all_gallerys_url . '" title = "' . $options['all_gallerys_desc'] . '">' . $options['all_gallerys_text'] . '</a></span>';
        }     
        $out .= '</H3></div>';
          
     }
     else // функционал альбомов не используется
     {
        $gallerys = taggallery_get_gallerys();
        $out .= '<H1>' . $options['all_gallerys_text'] . '</H1>';
        require($template_dir . 'gallerys_out.php'); 
     }
     
     // блок последних картинок
     require($template_dir . 'blok-last-pictures.php'); 
     
     require($template_dir . 'gallery-posle.php'); 
   
 	   if ($options['cache_flag']) mso_add_cache($cache_key, $out); // сразу в кэш добавим
     echo($out);
 }


 $error = false; // тут ошибки невозможны


// конец вывода
echo NR . '</div><!-- class="gallery_page" -->' . NR;
require(getinfo('template_dir') . 'main-end.php');
 
?>