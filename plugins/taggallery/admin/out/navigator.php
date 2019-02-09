<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

// выводим навигатор по альбомам и галереям_________________________________________________________
   $out = '';

    require($plugin_dir . '/admin/out/albums.php');
    $out .= '<H3>Неразобранные по альбомам галереи:</H3>';
    $gallerys = taggallery_get_gallerys(array('nerazobrannoe' => true  , 'hash_tags'=>true)); 
    require($plugin_dir . '/admin/out/gallerys.php');
    $out .= '<H3><a href="' . $plugin_url . 'gallerys/nerazobrannoe">' . 'Неразобранные картинки' . '</a></H3>';

echo $out;


?>


