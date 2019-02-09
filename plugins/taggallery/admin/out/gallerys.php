<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
   
 if (!isset($razd)) $razd = '<br />';    

//Вывод галерей в админке
  if ($gallerys)
  {
     foreach ($gallerys as $cur_gal)
     {
       $gallery_url = $siteurl . $options['gallery_slug'] . '/' . $options['gallery_prefix'] . $cur_gal['gallery_slug'];
       $gallery_admin_url = $plugin_url . 'gallerys/' . $cur_gal['gallery_slug'];
       $gallery_link = $cur_gal['gallery_name'] . ': <a href="' . $gallery_admin_url . '">' . '(Редактировать)' . '</a>' . ' <a href="' . $gallery_url . '" target = "blank">(Просмотреть)</a>';
    
       $out .= $gallery_link . $razd;
     }
  } 
  else $out .= 'Галереи отсутствуют';
?>

