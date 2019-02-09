<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
*/

// Выводим блок последних картинок ______________________________

     if ($options['last_width']) $width = ' width="' . $options['last_width'] . '" '; //ширина обложки заносится в добавку к html 
     else $width = '';
     
     if ($options['last_img_class']) $class = ' class="' . $options['last_img_class'] . '"'; //ширина обложки заносится в добавку к html 
     else $class = '';
          
     $oar=array();
     $par['limit'] = $options['last_count']; // колличество в выдаче
     $par['sort_field'] = $options['date_field']; // что используем как дату
     
     $p = false;
     
     $last_pictures = taggallery_get_pictures($par , $p);
     
     if ($last_pictures)
     {
       $out .= $options['last_title'];
       $out .= $options['last_pictures_do'];
       foreach ($last_pictures as $cur_picture)
       {
          if (!$cur_picture['picture_mini_url'])
               $cur_picture['picture_mini_url'] = $siteurl . 'uploads' . '/' . $cur_picture['picture_dir'] . 'mini/' . $cur_picture['picture_file'];
		      $mini_link = '<img' . $class . ' src="' . $cur_picture['picture_mini_url'] . '" alt="' . $cur_picture['picture_title'] . '"' . $width . ' />';
		      $picture_page_url = $siteurl . $options['picture_slug'] . '/' . $options['picture_prefix'] . $cur_picture['picture_slug'];
		      $link = '<a href="' . $picture_page_url.'" title="' . $cur_picture['picture_title'] . '">' . $mini_link . '</a>'; 
		              
          $out .= $options['last_picture_do'] . $link . $options['last_picture_posle'];
       }
       $out .= $options['last_pictures_posle'];
     }
     

?>