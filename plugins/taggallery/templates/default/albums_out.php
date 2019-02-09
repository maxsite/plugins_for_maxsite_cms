<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// вывод альбомов
 $out .= '<table>'; 
 foreach ($albums as $album)
 {
   // сформируем список галерей текущего альбома
   $gallerys_out = '';
   if (isset($album['gallerys']) and $album['gallerys'])
     foreach ($album['gallerys'] as $cur_gal)
     {
        $gallery_url = $siteurl . $options['gallery_slug'] . '/' . $options['gallery_prefix'] . $cur_gal['gallery_slug'];
        $gallery_link = '<a href="' . $gallery_url . '" title = "' . $cur_gal['gallery_desc'] . '">' . $cur_gal['gallery_name'] . '</a>';
        if ($gallerys_out) $gallerys_out .= ', ' . $gallery_link;
        else $gallerys_out .= $gallery_link;
     }
       
    $album_url = $siteurl . $options['album_slug'] . '/' . $options['album_prefix'] . $album['album_slug'];
    $album_link = '<a href="' . $album_url . '" title = "' . $album['album_desc'] . '">' . $album['album_title'] . '</a>'; 
    
    $out .= '<tr><td>' . $album_link . '</td><td>' . $gallerys_out . '</td></tr>';
 }
 $out .= '</table>'; 

?>