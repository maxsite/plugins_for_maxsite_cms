<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 


   $default_text = array(
      'text_album_empty' => 'Этот альбом еще пуст' ,
      'text_not_album' => 'Нет такого альбома' ,
      'text_album_error' => 'Альбом не указан' ,
      'text_album_title' => 'Раздел ' ,
      'text_gallery_title' => 'Галерея ' ,
      'text_not_pictures' => 'Нет картинок' ,
      'text_count_pictures' => 'Всего картинок' ,
      'text_not_gallery' => 'Нет такой галереи изображений' ,
      'text_picture_view' => 'Просмотров картинки' ,
      'text_go_to_main' => 'Перейти на главную страницу галерей изображений:' ,
      
        );

foreach ($default_text as $key => $val)
    if (!isset($options[$key])) $options[$key] = $val;
    
?>