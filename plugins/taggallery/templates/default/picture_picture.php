<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

 // Картинка__________________________________________________________________________
 
   $out .= $options['picture_do'] ;

    if ($picture)
    {
      if ($options['picture_picture_width']) $html_adds = ' width="' . $options['picture_picture_width'] . '"'; 
      else $html_adds = '';
      if ($options['picture_img_class']) $class = 'class = "' .$options['picture_img_class'] . '"'; else $class = '';
   
      if ($picture['picture_url']) $picture_url = $picture['picture_url'];
      elseif ($picture['picture_file']) $picture_url = $uploads_url . $picture['picture_dir'] . $picture['picture_file'];
      else $picture_url = 'Нет файла';
      
      $picture_link = '<img src="' . $picture_url . '" title="' . $picture['picture_desc'] . '" ' . 'alt="' . implode(", " , $picture['tags']) . '"' . $html_adds . ' >';
      
      $out .= '<a href="' . $picture_url  . '"' . $class . '>' . $picture_link . '</a>';
    }
    else $out .= $options['text_not_pictures'];

    $out .= $options['picture_posle'] ;


?>