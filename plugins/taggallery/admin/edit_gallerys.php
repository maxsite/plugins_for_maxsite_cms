<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
?>

<h2><?= t('Управление галереями изображений') ?></h2>

<?php

 
	$CI = & get_instance();
	
	$this_tag_slug = mso_segment(4);
	$this_url = $plugin_url . 'gallerys/';
  
  global $MSO;

 

  $gallery_mody = false; // флаг модификации галереи


  if (!$this_tag_slug) $gallery = false;
  elseif ($this_tag_slug != 'nerazobrannoe')
  {
     $gallerys = taggallery_get_gallerys(array('gallery_slug' => $this_tag_slug, 'hash_tags'=>true)); 
     if (isset($gallerys[0])) $gallery = $gallerys[0];
     else $gallery = false;
  }   
  else $gallery = false;


  if ($gallery)
  {
  
      // изменение параметров галереи
	    if ( $post = mso_check_post(array('f_session_id', 'f_gallery_title', 'f_gallery_desc', 'f_gallery_content',	'f_gallery_date','f_edit_gallery_submit_' . $this_tag_slug)) and isset($gallery['gallery_name']) )
	    {
		    mso_checkreferer();

		   $par['gallery_title'] = $post['f_gallery_title'];
		   $par['gallery_desc'] = $post['f_gallery_desc']	;
		   $par['gallery_content'] = $post['f_gallery_content'];
		   $par['gallery_date'] = $post['f_gallery_date'];
		
		   $par['gallery_name'] = $gallery['gallery_name'];
		   $res_id = taggallery_add_gallery($par);
		
       if ($res_id)  echo '<div class="update">Галерея ' . $res_id . ' изменена.</div>';
       else echo '<div class="error">Ошибка id галереи.</div>';
       
       $gallery_mody = true;
	   }
  
  
  
    //если нажата кнопка создать миниатюру
    if ( $post = mso_check_post(array('f_session_id', 'f_create_mini_submit')) )
    {
	     mso_checkreferer();
	     $picture_id = mso_array_get_key($post['f_create_mini_submit']);
       $err = taggallery_create_mini(array('picture_id' => $picture_id ));
 
       if (!$err)  echo '<div class="update">Миниатюра создана.</div>';
       else echo '<div class="error">' .  $err . '</div>';    
     
       $gallery_mody = true;
    }  
  
    //если нажата кнопка удалить (удалить можно только пустую галерею)
    if ( $post = mso_check_post(array('f_session_id', 'f_delete_gallery_submit_' . $this_tag_slug)) ) 
    {
	    mso_checkreferer();
      $err = taggallery_delete_gallery(array('gallery_slug' => $this_tag_slug));
      
      if (!$err) echo '<div class="update">Галерея удалена.</div>';
      else echo '<div class="error">' . $err . '</div>';
       
      $gallery_mody = true;      
    }
    
    //если нажата кнопка удалить все картинки из галереи
    if ( $post = mso_check_post(array('f_session_id', 'f_delete_all_files_submit')) ) 
    {
	     mso_checkreferer();
       $err =  taggallery_empty_gallery(array('gallery_slug' => $this_tag_slug));
       
       if (!$err)  echo '<div class="update">Все картинки удалены из альбома.</div>';
       else echo '<div class="error">' .  $err . '</div>';
    }
    
    //если нажата кнопка выбрать самую новую картинку обложкой
    if ( $post = mso_check_post(array('f_session_id', 'f_new_thumb_submit')) ) 
    {
	     mso_checkreferer();
       $err = taggallery_generate_gallery_thumb(array('gallery_slug' => $this_tag_slug));
       
       if (!$err)  echo '<div class="update">Обложка установлена.</div>';
       else echo '<div class="error">' .  $err . '</div>';
       
       $gallery_mody = true;
    }    


    //если нажата кнопка удалить картинку из галереи
    if ( $post = mso_check_post(array('f_session_id', 'f_del_from_gallery_submit')) ) 
    {
	     mso_checkreferer();
	     $picture_id = mso_array_get_key($post['f_del_from_gallery_submit']);
       $err = taggallery_delete_picture_from_gallery(array('picture_id' => $picture_id , 'gallery_slug' => $this_tag_slug));
       
       if (!$err) echo '<div class="update">Картинка удалена из галереи.</div>';
       else echo '<div class="error">' . $err . '</div>';
    }

    //если нажата кнопка изменить обложку
    if ( $post = mso_check_post(array('f_session_id', 'f_thumbnail_submit')) ) 
    {
	    mso_checkreferer();
	    $picture_id = mso_array_get_key($post['f_thumbnail_submit']);
	    
	    $err = taggallery_set_gallery_thumb(array('gallery_slug' => $this_tag_slug , 'picture_id' => $picture_id));

      if (!$err) echo '<div class="update">Обложка изменена.</div>';
      else echo '<div class="error">' . $err . '</div>';
      
      $gallery_mody = true;
    }
  
/*
    //если нажата кнопка редактирования даты картинки
    if ( $post = mso_check_post(array('f_session_id', 'f_date' , 'f_date_submit')) ) 
    {
	    mso_checkreferer();
	    
	    $picture_id = mso_array_get_key($post['f_date_submit']);
	    $date = $post['f_date'][$picture_id];
	    
	    $picture = array();
	    $picture['picture_id'] = $picture_id;	    
	    $picture['picture_date'] = $date;
	    
	    $res = taggallery_add_pictures(array($picture));
	    
      if (!$res['errors']) echo '<div class="update">Дата изменена.</div>';
      else echo '<div class="error">' .  $res['errors'] . '</div>';
    }  
*/

  }
 
    //если нажата кнопка редактирования меток и описаний
    if ( $post = mso_check_post(array('f_session_id', 'f_tags', 'f_desc' , 'f_tags_submit')) ) 
    {
	    mso_checkreferer();
	    
	    $picture_id = mso_array_get_key($post['f_tags_submit']);
	    $tags = $post['f_tags'][$picture_id];
	    
	    $picture = array();
	    $picture['picture_id'] = $picture_id;	    
	    $picture['picture_tags'] = mso_explode($tags, false, false, true);
	    $picture['picture_title'] = $post['f_desc'][$picture_id];
	    
	    $res = taggallery_add_picture($picture);
	    
      if (!$res['errors']) echo '<div class="update">Метки и описание картинки изменены.</div>';
      else echo '<div class="error">' .  $res['errors'] . '</div>';
    } 
    
    //если нажата кнопка удалить картинку из базы
    if ( $post = mso_check_post(array('f_session_id', 'f_del_picture_submit')) ) 
    {
	     mso_checkreferer();
	     $picture_id = mso_array_get_key($post['f_del_picture_submit']);
       $err = taggallery_delete_picture(array('picture_id' => $picture_id));
       
       if (!$err) echo '<div class="update">Картинка удалена из базы данных.</div>';
       else echo '<div class="error">' . $err . '</div>';
    }

// если галерея изменена - переполучим редактируемую галерею
if ($gallery_mody)
{
  $gallerys = taggallery_get_gallerys(array('gallery_slug' => $this_tag_slug, 'hash_tags'=>true)); 
  if (isset($gallerys[0])) $gallery = $gallerys[0];
  else $gallery = false; 
}  
 
 
// теперь выводим___________________________________________________________________________________
 
 
/*
  // сформируем если ндо ссылку на неразобранны картинки
  $pictures = taggallery_get_nerazobrannoe();
  if ($pictures) echo '<p><a href = "' . $this_url . 'nerazobrannoe">Неразобранные по галереям картинки</а>.</p>';
*/


 $pag = FALSE;
 if ($gallery) $pictures = taggallery_get_pictures($par = array('gallery_id' => $gallery['gallery_id']) , $pag);
 elseif ($this_tag_slug == 'nerazobrannoe') $pictures = taggallery_get_pictures(array('nerazobrannoe' => true) , $pag);
 else $pictures = false;
 // если  ($this_tag_slug != 'nerazobrannoe'), то неразобранные картинки мы уже получили ранее

// выводим навигатор по альбомам и галереям_________________________________________________________
    $out = '<table>';
    $out .= '<tr><td>';
    $out .= '<H3>Неразобранные по альбомам:</H3>';
    $gallerys = taggallery_get_gallerys(array('nerazobrannoe' => true, 'hash_tags'=>true)); 
    require($plugin_dir . '/admin/out/gallerys.php');
    $out .= '</td><td>';
    require($plugin_dir . '/admin/out/albums.php');
    $out .= '<H3><a href="' . $plugin_url . 'gallerys/nerazobrannoe">' . 'Неразобранные картинки' . '</a></H3>';
    $out .= '</td></tr></table>';
    echo $out;
    
 // echo  '<div style="clear:both">'; 

 
 echo '<form action="" method="post">' . mso_form_session('f_session_id');

// выводим форму редактирования атрибутов галереи
 if ($gallery)
 {
   if (!$gallery['thumb_url']) $gallery['thumb_url'] = $options['default_gallery_thumb_url'];  
		echo '<div class="admin_plugin_options">';
   echo '<table><tr>';
   echo '<td>';
   echo '<H1>Имя галереи: ' . $gallery['gallery_name'] . '</H1>';
   echo '<H3>Id : ' . $gallery['gallery_id'] . '</H3>';
   echo '<H3>Слуг : ' . $this_tag_slug . '</H3>';
   echo '<H3>Обложка :</H3>';
   echo '<img src="' . $gallery['thumb_url'] . '"><br/>';
   echo '(' . $gallery['thumb_url'] . ')';
   echo '<p><input type="submit" name="f_new_thumb_submit" value="' . t('Установить последнюю картинку обложкой', 'plugins') . '" style="margin: 25px 0 5px 0;" /></p>';
   	
   echo '</td>';
   echo '<td>';
		

   echo '<strong>' . t('Заголовок: ') . '</strong>' . '<input type="text" name="f_gallery_title" value="' . $gallery['gallery_title'] . '">';
   echo '<strong>' . t('Дата добавления: ') . '</strong>' . '<input type="text" name="f_gallery_date" value="' . $gallery['gallery_date'] . '">';
   echo '<strong>' . t('Описание: ') . '</strong>' . '<textarea rows="3" name="f_gallery_desc">'  . $gallery['gallery_desc'] . '</textarea>';
   echo '<strong>' . t('Контент: ') . '</strong>' . '<textarea rows="10" name="f_gallery_content">' . $gallery['gallery_content'] . '</textarea>'; 
   echo '<p><input type="submit" name="f_edit_gallery_submit_'. $this_tag_slug . '" value="' . t('Именить параметры галереи', 'plugins') . '" style="margin: 25px 0 5px 0;" /></p>';
	
		   echo '</td></tr></table>';
		echo '</div>';
 }
 elseif ($this_tag_slug == 'nerazobrannoe') echo '<H1>Неразобранное</H1>';
	
 if ($pictures) 
 {
     // установлены ли плагины комментирования и водных меток
   // if (in_array($options['comments_plugin'], $MSO->active_plugins) ) $comments_plugin = true;  else $comments_plugin = false;
  $comments_plugin = false; 
    
	  $CI->load->library('table');
	  $tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="100%"><colgroup width="100">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );

	  $CI->table->set_template($tmpl); // шаблон таблицы
	  // заголовки
	  $CI->table->set_heading('Картинка', 'Инфо' , 'Действия' , 'Метки и описание');
  
    foreach ($pictures as $picture_id => $picture)
    {
		   // если эта картинка из uploads/
       if (/*$picture['picture_dir'] and */ $picture['picture_file'] and !$picture['picture_source_id']) 
       { 
          // миниатюра 
          $mini_url = $uploads_url . $picture['picture_dir'] . 'mini/' . $picture['picture_file'];
          $mini_dir = $uploads_dir . $picture['picture_dir'] . 'mini/' . $picture['picture_file'];
          if (file_exists($mini_dir)) $prev = '<img class="file_img" alt="" src="' . $mini_url . '" width = "' . $options_admin['admin_picture_width'] .'">'; 
          else $prev = '<img alt="" src="' . $uploads_url . $picture['picture_dir'] . '' . $picture['picture_file'] . '" width = "' . $options_admin['admin_picture_width'] .'">' . '<br/>' . 'Нет миниатюры'; 
          
         
         if ($picture['picture_url']) $url = $picture['picture_url'];
         else $url = $uploads_url . $picture['picture_dir'] . '' . $picture['picture_file'];
         
         $prev = '<a class="lightbox" href="' . $url . '">' . $prev . '</a>';

          
          // сформируем ключ однозначной идентефикации файла в uploads/
          $file_arr = explode("." , $picture['picture_file']);
          $file_full_key = str_replace("/","_DDD_",$picture['picture_dir']) . '_FFF_' . $file_arr[0] . '_EEE_' . $file_arr[1];
       
          // ссылка на наложения ватермарк
          $watermark = '<a href="' . $plugin_url . 'watermark/' . $file_full_key .  '">Watermark</a>';
          
          // ссылка на страницу директории этой картинки
          $dir_link = '<a href="' . $admin_url . 'taggallery/' . $picture['picture_dir'] . '" title="Каталог изображения" >' . '/uploads/' . $picture['picture_dir'] . '</a> ';
          $file = $picture['picture_file'];
          $source = $dir_link . '<br/>' . $file;
          $submit_create_mini = '<input type="submit" name="f_create_mini_submit[' . $picture['picture_id'] . ']" value="Создать миниатюру">';
       }   
       elseif ($picture['picture_source_id'])
       {
          $watermark = '';
          $prev = '<img class="file_img" alt="" src="' . $picture['picture_mini_url'] . '" width = "' . $options_admin['admin_picture_width'] .'">';
          $prev = '<a class="lightbox" href="' . $picture['picture_url'] . '">' . $prev . '</a>';
          $submit_create_mini = '';
          //$source = taggallery_get_import_source($picture['picture_source_id']);
          $source = $picture['picture_source_id'];
          //if ($picture['source_link'])
          //   $source .= '<br/>' . '<a href="' . $picture['source_link'] . '" title="Страница картинки" >' . $picture['source_link'] . '</a> ';
       }   
       else 
       {
          $prev = '';
          $watermark = '';
          $submit_create_mini = '';       
          $source = 'Неизвестный источник';
       }   
       
       
      $picture_edit = '<a href="' . $admin_url . 'taggallery/picture/' . $picture['picture_id'] . '" title="Редактировать атрибуты картинки" >Редактировать</a> '; 
      $picture_link = $picture_edit . ' | ' . '<a href="' . $siteurl . $options['picture_slug'] . '/' .  $options['picture_prefix'] .$picture['picture_slug'] . '" title="Просмотр страницы картинки" >Просмотр</a>';  

       
       // если это не "Неразобранные"
       if ($this_tag_slug != 'nerazobrannoe')
       {
         $set_thumbnail = '<input type="submit" name="f_thumbnail_submit[' . $picture['picture_id'] . ']" value="Обложкой">';
         $delete = '<input type="submit" name="f_del_from_gallery_submit[' . $picture['picture_id'] . ']" value="Удалить из галереи">';
       }  
       // если это "Неразобранные"
       else
       {
         $set_thumbnail = '';
         $delete = '<input type="submit" name="f_del_picture_submit[' . $picture['picture_id'] . ']" value="Удалить из базы">';       
       }
       
       $picture_tags = '';
       if ($picture['tags']) 
       {
         foreach ($picture['tags'] as $val) 
         {
           if ($picture_tags) $picture_tags .= ', ' . $val; 
           else $picture_tags .= $val; 
         }
       }
       
       $tags = 'Метки: <input type="text" name="f_tags[' . $picture['picture_id'] . ']" value="' . $picture_tags . '">';
       $desc = 'Заголовок: <input type="text" name="f_desc[' . $picture['picture_id'] . ']" value="' . $picture['picture_title'] . '">';
       $tags_submit = '<input type="submit" name="f_tags_submit[' . $picture['picture_id'] . ']" value="Изменить">';
       
      $picture_date = taggallery_date($picture['picture_date'], $date_format, '', '' , false);
       
      $picture_date_file = taggallery_date($picture['picture_date_file'], $date_format, '', '' , false);
      
      $picture_date_photo = taggallery_date($picture['picture_date_photo'], $date_format, '', '' , false);  
    
      $position = str_replace(array('1','2','3') , array('Ландшафт', 'Портрет', 'Кварат') , $picture['picture_position']);

       //параметры для получения ссылки на редактирование комментариев комментарии
       $par = array(
                 'picture_slug' => $picture['picture_slug'],
                 'picture_prefix' => $options['picture_prefix'],
                 'picture_slug' => $options['picture_slug'],
               );
    //   if ($comments_plugin) $comments = taggallery_get_edit_comments_link($par);
     //  else $comments = '';
     $comments = '';  
      
      if ($picture['picture_full_size_url']) $source .= '<br />' . '<a href = "' . $picture['picture_full_size_url'] . '" target = "_blank">' . $picture['picture_full_size_url'] . '</a>';
      
       $info = 
               $source . '<br/>' .
               'Дата файла: ' . $picture_date_file . '<br/>' .  
               'Дата снимка: ' . $picture_date_photo . '<br/>' .
               'Дата добавления: ' . $picture_date . '<br/>' .
               $picture['picture_width'] . '*' . $picture['picture_height'] . ' ' . $position
               ;  
      
      
      $functions =
                    $picture_link . '<br/>' . // страница редактирования фото
                    $set_thumbnail . '<br/>' . // кнопка "Обложкой"  
                    $delete . '<br/>' . // кнопка "Удалить из галереи" 
                    $submit_create_mini . '<br/>' . // создать миниатюру         
                    $comments . '<br/>' . // комментарии    
                    $watermark // наложение wotermark          
                    ;
                
       $CI->table->add_row($prev , $info , $functions , $desc . '<br />' . $tags . '<br />' . $tags_submit);
     }
     
	   echo $CI->table->generate(); // вывод подготовленной таблицы
	   
	}
	if ($gallery and $pictures) echo '<p><input type="submit" name="f_delete_all_files_submit" value="' . t('Удалить все картинки из галереи', 'plugins') . '" style="margin: 25px 0 5px 0;" /></p>';

	// если картинок нет (галерея пустая), то галерею можно удалить
	elseif ($gallery and ($this_tag_slug != 'nerazobrannoe')) 
	{
	  echo '<p>Нет картинок в этой галерее.</p>';
		echo '<p><input type="submit" name="f_delete_gallery_submit_'. $this_tag_slug . '" value="' . t('Удалить эту галерею', 'plugins') . '" style="margin: 25px 0 5px 0;" /></p>';
	}
	
  echo '</form>';	   

 if (!$gallery and ($this_tag_slug != 'nerazobrannoe') and $this_tag_slug) echo 'Нет такой галереи';





?>