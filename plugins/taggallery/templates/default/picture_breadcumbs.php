<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// хлебные крошки страницы картинки __________________________________________________________

$breadcumbs_gallery = '';
$breadcumbs_album = '';

if (isset($picture['gallerys'][0]) )
{
    $gallery = $picture['gallerys'][0];
    $gallery_link = '<a href ="' . $siteurl . $options['gallery_slug'] . '/' . $options['gallery_prefix'] . $gallery['gallery_slug'] . '" tite ="' . $options['text_gallery_title'] . $gallery['gallery_title'] . '">' . $gallery['gallery_title'] . '</a>';
    $breadcumbs_gallery = $options['breadcumbs_razd'] . $gallery_link;
    $albums = taggallery_get_albums(array('gallery_id' => $gallery['gallery_id']));
    if (isset($albums[0]) )
    {
      $album = $albums[0];
      $gallery_albom_link = '<a href ="' . $siteurl . $options['album_slug'] . '/' . $options['album_prefix'] . $album['album_slug'] . '" tite ="' . $options['text_album_title'] . $album['album_title'] . '">' . $album['album_title'] . '</a>';
      $breadcumbs_album = $options['breadcumbs_razd'] . $gallery_albom_link;
    }
}
    
     $out .= '<div class="breadcrumbs">' . $main_link  . $breadcumbs_album . $breadcumbs_gallery . $options['breadcumbs_razd'] . $title . '</div>' . NR;
  
?>