<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
   
//Вывод галерей



  if ($gallerys)
  {
     $pictures_count = taggallery_get_pictures_count();
  
     $out .= '<div class="gallerys-all">' . NR;
     
     if ($options['gallery_width']) $width = ' width="' . $options['gallery_width'] . '" '; //ширина обложки заносится в добавку к html 
     else $width = '';  
         
     foreach ($gallerys as $cur_gal)
     {
       if (!$cur_gal['thumb_url']) $cur_gal['thumb_url'] = $options['default_gallery_thumb_url'];
     
       $gallery_url = $siteurl . $options['gallery_slug'] . '/' . $options['gallery_prefix'] . $cur_gal['gallery_slug'];
       $gallery_link = '<span><a href="' . $gallery_url . '" title = "' . $options['text_gallery_title'] . $cur_gal['gallery_title'] . '">' . $cur_gal['gallery_title'] . '</a></span>';
       $gallery_thumb_url = '<img src="' . $cur_gal['thumb_url'] . '" alt = "' . $options['text_gallery_title'] . $cur_gal['gallery_title'] . '"' .$width . '>';
       $gallery_thumb_link = '<a href="' . $gallery_url . '" title = "' . $options['text_gallery_title'] . $cur_gal['gallery_title'] . '">' . $gallery_thumb_url . '</a>';
       
       if (isset($pictures_count[$cur_gal['gallery_id']])) $gallery_count = $pictures_count[$cur_gal['gallery_id']];
       else $gallery_count = 0;
       
       $gallery_count = '<span><img src="' .$template_url . 'images/count.gif" width="16" height="16" alt="" title="' . $options['text_count_pictures'] . '" >' . $gallery_count . '</span>' . NR;
       
       $out .= '<div class="gallery-all">' . NR;
       
 
       $out .= '<div class="gallery-all-info">' . NR;
       $out .= $gallery_link . $gallery_count;
       $out .= '</div>' . NR;    
       
       $out .= '<div class="gallery-all-thumb">' . NR;
       $out .= $gallery_thumb_link;
       $out .= '</div>' . NR;
    
       $out .= '</div>' . NR;
       
     }
     $out .= '</div>' . NR;
  } 
  else $out .= $options['text_album_empty'];
  
 
?>

