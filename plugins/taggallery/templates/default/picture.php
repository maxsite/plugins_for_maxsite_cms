<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
//Страница вывода одиночной картинки для плагина taggallery.



   function carousel_css($a = array())
   {
     $options = mso_get_option('taggallery', 'plugins', array());
     if (!isset($options['template'])) $options['template'] = 'default';

	   $plugin_dir = getinfo('plugins_dir') . 'taggallery/';
     $this_template_dir = $plugin_dir . 'templates/' . $options['template'] . '/';
     $carousel_dir = $this_template_dir . 'carousel/';

     $fn_carousel_head = $carousel_dir . 'carousel_head.php';
	   if (file_exists($fn_carousel_head)) require ($fn_carousel_head);
	
	   return $a;
   }   

  $fn_functions = $plugin_dir . 'functions/function_view_count.php';
	if (file_exists($fn_functions)) require ($fn_functions);

 // определим ключ для получения картинки
 if ($options['picture_prefix']) $picture_slug = str_replace($options['picture_prefix'] , "" , $segment2);
 else $picture_slug = $segment2;
 if ($picture_slug)
 {
 
  
  // получаем картинку
  $pag = false;
  $pictures = taggallery_get_pictures(array('picture_slug' => $picture_slug) , $pag);
  if (isset($pictures[0]))
  {
    $picture = $pictures[0];
    taggallery_view_count_first($options['picture_prefix']);
  
  
    if ($picture['picture_title']) $title = $picture['picture_title'];
    else 
    {
      $title = 'Фотография ' . implode(", " , $picture['tags']); 
      $picture['picture_title'] = $title; // для того чтобы назвать сущность в комментариях     
    }  
    mso_head_meta('title', $title); // meta title страницы
    mso_head_meta('description', $picture['picture_desc']); // meta description страницы
    mso_head_meta('keywords', implode(", " , $picture['tags'])); // meta keywords страницы
    
    // подключим карусель
    mso_hook_add('head', 'carousel_css');  
	  mso_hook_add('head', 'taggallery_css');  

    require(getinfo('template_dir') . 'main-start.php');
  
    $cache_key = 'taggallery_picture_' . $picture_slug;
    
    if ($options['cache_flag']) $out = mso_get_cache($cache_key);
    else $out = '';
    
    if ($out) echo $out;
    else
    {   
      $out .= NR . '<div class="galery_page">' . NR;
    
      $template_options = mso_get_option('taggallery_' . $options['template'] , 'taggallery', array());
      $options = array_merge($template_options, $options);

      // подключим файл инициализации дефолтных опций шаблона
      require($template_dir . 'options_default.php'); 

      require($template_dir . 'picture_breadcumbs.php'); // хлебные крошки
      
      $edit_url = getinfo('site_admin_url') . 'taggallery/picture/' . $picture['picture_id'];
      if (is_login()) $edit_link = '<a href = "' . $edit_url . '"><img src="' .$template_url . 'images/edit.png" width="16" height="16" alt="" title="Edit picture" class="right"></a>';
      else $edit_link = '';
      
      $out .= '<h1>' . $title . $edit_link . '</h1>' . NR;
 
     require($template_dir . 'picture_info_top.php'); 
      
      if ($picture['picture_content'])
      {
         $out .=  '<div class="gallery-content">' . NR; 
         $out .= $picture['picture_content'];
         $out .= '</div>' . NR;
      }

      require($template_dir . 'picture_picture.php'); // сама картинка
     
      require($template_dir . 'carousel/carousel_go.php');  // карусель изображений текущей галереи
       
      $out .= '<div class="break"></div>' . NR;
      
      require($template_dir . 'add_zalkadki.php');  // закладки на соцсервисы
  
      require($template_dir . 'picture_similar_posts.php'); // похожие статьи по меткам картинки

      require($template_dir . 'gallery-posle.php'); 

      echo $out;
 	    if ($options['cache_flag']) mso_add_cache($cache_key, $out); // сразу в кэш добавим
    }
 
     // подключаем комментарии плагина other_comments
    if ($options['comments_plugin'])
    {
       // установлен ли плагин комментирования
       global $MSO;
       if (in_array($options['comments_plugin'], $MSO->active_plugins) ) 
       {   
          $element = array(
             'element_id_in_table' => $picture['picture_id'],
             'table_name' => 'pictures',
             'title' => $picture['picture_title']
           );
	         $fn_comments = getinfo('plugins_dir') . $options['comments_plugin'] . '/other_comments.php'; // путь
           if ( file_exists($fn_comments) ) require($fn_comments); // если есть, подключаем 
        }   
    }

    echo NR . '</div><!-- class="type galery_page" -->' . NR;
    require(getinfo('template_dir') . 'main-end.php');

    $error = false;
  }  
  else $error = $options['text_not_pictures']; 	
 }
 else $error = $options['text_not_pictures']; 	
	
?>