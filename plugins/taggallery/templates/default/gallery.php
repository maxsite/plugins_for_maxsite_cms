
<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
//Страница вывода галлереи картинок для плагина taggallery.

  // определим ключ-слуг для получения галереи
  if ($options['gallery_prefix']) $gallery_slug = str_replace($options['gallery_prefix'] , "" , $segment2);
  else $gallery_slug = $segment2;
  // получаем галерею
  if ($gallery_slug)
     $gallerys = taggallery_get_gallerys(array('gallery_slug' => $gallery_slug));
  else $gallerys = false;
  

  // если галерею удалось получить
  if (isset($gallerys[0]) and $gallerys[0])
  { 
    $gallery = $gallerys[0];

    if ($gallery['gallery_desc'])  $desc = $gallery['gallery_desc']; else $desc = $options['gallery_desc'];
	  mso_head_meta('title', $options['gallery_name'], $gallery['gallery_title']);
	  mso_head_meta('description', $desc);
	
	 $title = $gallery['gallery_title'];
	
	  mso_hook_add('head', 'taggallery_css');  
	
    require(getinfo('template_dir') . 'main-start.php');
    echo NR . '<div class="gallery_page">' . NR;

    // хлебные крошки
    // если галерея состоит в альбоме
    echo '<div class="breadcrumbs">';
    if ($gallery['albums'])
    {
      $album = $gallery['albums'][0];
      $gallery_albom_link = '<a href ="' . $siteurl . $options['album_slug'] . '/' . $options['album_prefix'] . $album['album_slug'] . '" title = "' . $options['text_album_title'] . $album['album_title'] . '">' . $album['album_title'] . '</a>';
      echo $main_link . $options['breadcumbs_razd'] . $gallery_albom_link . $options['breadcumbs_razd'] . $gallery['gallery_title'];
    }
    else echo $main_link . $options['breadcumbs_razd'] . $gallery['gallery_title'];
    echo '</div>';
  
    $edit_url = getinfo('site_admin_url') . 'taggallery/gallerys/' . $gallery['gallery_name'];
    if (is_login()) $edit_link = '<a href = "' . $edit_url . '"><img src="' . $template_url . 'images/edit.png" width="16" height="16" alt="" title="Edit gallery" class="right"></a>';
    else $edit_link = '';
    
    echo '<h1>' . $options['text_gallery_title'] . $gallery['gallery_title'] . $edit_link . '</h1>';
    
    echo '<div class="info">';
    echo '</div>';

    if ($gallery['gallery_content'])
    {
      echo '<div class="gallery-content">';
      echo $gallery['gallery_content'];
      echo '</div>';
    }
    
    //получаем блок картинок, принадлежащих данной галерее, к выводу______________________________
    $par = array();
    $par['sort'] = $options['gallery_sort'];
    $par['sort_order'] = $options['gallery_sort_order']; 
    $par['gallery_id'] = $gallery['gallery_id']; 
    $par['count'] = $options['gallery_pag_count']; 
    if ($par['count']) $par['pag'] = true;
    if ($options['comments_plugin']) $par['comments_count'] = true;
    
    $pictures = taggallery_get_pictures($par , $pagination);
    
    if ($pictures)
    {
      if ($pagination) mso_hook('pagination', $pagination);	

       $out = '';
	     $out .=  '<div class="pictures">';
	     
       if ($options['gallery_picture_width']) 
       {
            $width = ' width="' . $options['gallery_picture_width'] . '"';
       }
       else $width ='';

	     foreach ( $pictures as $picture ) 
	     {
		      extract( $picture );
	        $out .=  '<div class="picture">';
		      // разберемся с миниатюрой
		      if (!$picture_mini_url) $picture_mini_url = $siteurl . 'uploads' . '/' . $picture_dir . 'mini/' . $picture_file;

		       
		      $mini_link = '<img src="' . $picture_mini_url . '" alt="' . $picture_title . '"' . $width . ' />';
		      $picture_page_url = $siteurl . $options['picture_slug'] . '/' . $options['picture_prefix'] . $picture_slug;
		      $link = '<a href="' . $picture_page_url.'" title="' . $picture_title . '">' . $mini_link . '</a>';
		      

		      $out .= '<div class="picture-title">' . $picture_title . '</div>';
		      $out .= '<div class="picture-link">' . $link . '</div>';
		      		      
		      $info = '';

		      $picture_view_count = $picture['picture_view_count'];
			    $info .= '<span class="picture-view-count">' . $picture_view_count . '</span>';

		      $info .= '<span class="picture-comments-count">' . $picture['comments_count'] . '</span>';


			    $picture_galerys = '';
			    if ($picture['gallerys']) foreach ($picture['gallerys'] as $cur_gal)
			    {
			        $gallery_url = $siteurl . $options['gallery_slug'] . '/' . $options['gallery_prefix'] . $cur_gal['gallery_slug'];
              $gallery_link = '<a href="' . $gallery_url . '">' . $cur_gal['gallery_name'] . '</a>';
			        if ($picture_galerys) $picture_galerys .= ', ' . $gallery_link;
			        else $picture_galerys .= $gallery_link;
			    }
			    if ($picture_galerys) $info .= '<br/><span class="picture-tags">' . $picture_galerys . '</span>';	
		      
		      $out .= '<div class="picture-info">' . $info . '</div>';
		      
		      $out .= '</div>';
	     }      
       $out .=  '</div>';
       
      
    }
    else  $out = $options['text_not_pictures'];
  
    $out .=  '<div class="break"></div>';

    require($template_dir . 'add_zalkadki.php');  // закладки на соцсервисы
   
    // выводим блок страниц по этой метке
    require($template_dir . 'pages_on_tag.php');

    require($template_dir . 'gallery-posle.php'); 

    echo $out;

    echo NR . '</div><!--class="gallery_page" -->' . NR;  

    require(getinfo('template_dir') . 'main-end.php');
  
    $error = false;
  }
  else $error = $options['text_not_gallery'];
   
	
?>