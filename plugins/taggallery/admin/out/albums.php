<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
// вывод альбомов в админке
 $albums_url = $plugin_url . 'albums/';

 $CI = & get_instance();

 // получаем альбомы
 $albums = taggallery_get_albums(); 

 if ($albums)
 {
   $out .= '<H3>Галереи в альбомах:</H3>';
   
	  $CI->load->library('table');
	  $tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="100%"><colgroup width="100">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );

	  $CI->table->set_template($tmpl); // шаблон таблицы
	  // заголовки
	  $CI->table->set_heading('Альбомы', 'Галереи в альбоме');   
   
   foreach ($albums as $album)
   {
     // сформируем список галерей текущего альбома
     $razd = '<br/>';
     $gallerys_out = '';
     if (isset($album['gallerys']) and $album['gallerys'])
     foreach ($album['gallerys'] as $cur_gal)
     {
        $gallery_url = $siteurl . $options['gallery_slug'] . '/' . $options['gallery_prefix'] . $cur_gal['gallery_slug'];
        $gallery_link = '<a href="' . $gallery_url . '">' . $cur_gal['gallery_name'] . '</a>';
        $gallery_admin_url = $plugin_url . 'gallerys/' . $cur_gal['gallery_slug'];
         $gallery_link = $cur_gal['gallery_name'] . ': <a href="' . $gallery_admin_url . '">' . '(Редактировать)' . '</a>' . ' <a href="' . $gallery_url . '" target = "blank">(Просмотреть)</a>'; 
        if ($gallerys_out) $gallerys_out .= $razd . $gallery_link;
        else $gallerys_out .= $gallery_link;
     }
       
     $album_url = $siteurl . $options['album_slug'] . '/' . $options['album_prefix'] . $album['album_slug'];
     $album_link = '<a href="' . $album_url . '">' . $album['album_title'] . '</a>'; 
    
	   $CI->table->add_row($album_link , $gallerys_out);
   }
   $out .= $CI->table->generate(); // вывод подготовленной таблицы
   
 }

?>