<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
// в этом файле инициализируются дефолтные опции шаблона 


     
   $default_options_array = array(

    'breadcumbs_razd' => ' >> ' ,



// Опции вывода главной	
	
	   'album_gallerys_razd' => ', ',
//опции вывода обложки альбома
		'album_width' => 170 ,
//опции вывода обложки галереи
		'gallery_width' => 180 ,

// опции вывода миниатюр картинок галереи						

		'gallery_img_class' => 'lightbox' ,
		'gallery_picture_width' => 180 ,
    'gallery_sort' => '' ,
    'gallery_sort_order' => '' ,

// опции вывода конкретной картинки
		'picture_do' => '<div>' ,
		'picture_posle' => '</div>' ,
		'picture_img_class' => 'lightbox' ,
		'picture_picture_width' => '600' ,
    'see_tags_razd' => ' | ' ,
    'title_posts_on_tag' => 'Статьи по меткам картинки.' ,
    'title_gallerys_on_tag' => 'Галереи в которых картинка.' ,		

// опции вывода блоков похожих статей 
		'similar_posts_count' => 5 , 
	  'similar_posts_width' => 150 ,
		'similar_posts_title' => '<h3>Cтатьи по теме:</h3>',
		'similar_posts_start' => '<div class="page_other_pages">',
		'similar_posts_do' => '<table width="100%" border="0"><tr>',
		'similar_posts_posle' => '</tr></table>',
		'similar_posts_end' => '</div>',
		'similar_posts_format' => '<td valign="bottom">[link]<br>[image]</td>',
		'similar_posts_full_text' => 'false', 	

// опции вывода карусели изображений
		'carousel_picture_do' => '<li><div style="width: 160px">' ,
		'carousel_picture_posle' => '</div></li>' ,
//		'carousel_img_class' => '' ,
		'carousel_picture_width' => '150' ,

// опции вывода блока последних добавленных картинок
		'last_pictures_do' => '<div class="pictures">' ,
		'last_pictures_posle' => '</div>' ,
		'last_picture_do' => '<div class="picture">' ,
		'last_picture_posle' => '</div>' ,		
		'last_title' => '<H2>Последние фото</H2>' ,
	  'last_count' => 3 ,
	  'last_width' => 100 ,
	  'last_img_class' => ''
		)   ; 

foreach ($default_options_array as $key => $val)
    if (!isset($options[$key])) $options[$key] = $val;

require($template_dir . 'text.php'); 
     
?>