<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * плагин для MaxSite CMS
 * (c) http://max-3000.com/
 */
   
   function taggallery_css($a = array())
   {
      $options = mso_get_option('taggallery', 'plugins', array());
      if (!isset($options['template'])) $options['template'] = 'default';

      $taggallery_css = getinfo('plugins_url') . 'taggallery/templates/' . $options['template'] . '/' . 'taggallery.css';

	     echo '<link rel="stylesheet" type="text/css" href="'. $taggallery_css . '">';

	    return $a;
   }   
      
    
     $template_options = mso_get_option('taggallery_' . $options['template'] , 'taggallery', array());
     $options = array_merge($template_options, $options);
     
   
     // подключим файл инициализации дефолтных опций шаблона
     if (file_exists($template_dir . 'options_default.php')) require($template_dir . 'options_default.php'); 
     
     
 require ($plugin_dir . 'functions/access_db.php');
 require ($plugin_dir . 'functions/functions.php');
 
 $main_link = '<a href = "' . $siteurl . $options['main_slug'] . '" title = "' . $options['gallery_desc'] . '">' . $options['gallery_name'] . '</a>';
 
 $segment2 = mso_segment(2);

 $error = 'Не найден элемент для вывода.';

 // Если у нас главная страница
 if (!$segment2 and ($segment1 == $options['main_slug']))
 {
   $fn = $template_dir . 'main.php';
   if (file_exists($fn)) require($fn); 
   else $error = 'template_error';
 }
 
  // Если у нас страница всех галерей
 if ( ($segment2 == $options['all_gallerys_slug'])  and ($segment1 == $options['main_slug']))
 {
   $fn = $template_dir . 'all-gallerys.php';
   if (file_exists($fn)) require($fn); 
   else $error = 'template_error';
 }

 // Если у страница неразобранных галерей
 if (($segment2 == $options['gallerys_not_in_slug']) and ($segment1 == $options['main_slug']))
 {
   $fn = $template_dir . 'gallerys_not_in.php';
   if (file_exists($fn)) require($fn); 
   else $error = 'template_error';
 }

 
 else
 {
   $gallery_prefix = true;
   $picture_prefix = true;
   $album_prefix = true;
   
  if ($options['gallery_prefix'])
   {
     if ( substr($segment2 ,0 , strlen($options['gallery_prefix'])) == $options['gallery_prefix']) $gallery_prefix = true;
     else $gallery_prefix = false;
   }  
   else $gallery_prefix = true;
 
   if ($options['picture_prefix'])
   {
     if ( substr($segment2 ,0 , strlen($options['picture_prefix'])) == $options['picture_prefix']) $picture_prefix = true;
     else $picture_prefix = false;
   }  
   else $picture_prefix = true;
   
   if ($options['album_prefix'])
   {
     if ( substr($segment2 ,0 , strlen($options['album_prefix'])) == $options['album_prefix']) $album_prefix = true;
     else $album_prefix = false;
   }  
   else $album_prefix = true;
   
   
   if (($segment1 == $options['gallery_slug']) and $gallery_prefix)
   {
      $fn = $template_dir . 'gallery.php';
      if (file_exists($fn)) require($fn);  
      else $error = 'template_error';
   } 

   elseif (($segment1 == $options['picture_slug']) and $picture_prefix)
   {
      $fn = $template_dir . '/picture.php';
      if (file_exists($fn)) require($fn);  
      else $error = 'template_error';
   } 
      
   elseif (($segment1 == $options['album_slug']) and $album_prefix)
   {
      $fn = $template_dir . '/album.php';
      if (file_exists($fn)) require($fn); 
      else $error = 'template_error';
   }       
 }

 

 if ($error) // если не найдено что выводить
 {
		header('HTTP/1.0 404 Not Found');
		
		# начальная часть шаблона
	  require(getinfo('template_dir') . 'main-start.php');
	  echo NR . '<div class="type type_foto">' . NR;
	
		echo '<h1>' . t('404. Ничего не найдено...') . '</h1>';
		echo '<p>' . $error . '</p>';

		if ($segment2 or ($segment1 != $options['main_slug']))
        echo '<p>' . $options['text_go_to_main'] . $main_link . '</p>';
		echo mso_hook('page_404');
		echo '</div>';
		# конечная часть шаблона
	  require(getinfo('template_dir') . 'main-end.php');	
	}

?>