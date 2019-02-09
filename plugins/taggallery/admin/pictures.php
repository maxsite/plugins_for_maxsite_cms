<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>

<h1><?= t('Управление картинками') ?></h1>
<p class="info"><?= t('Здесь вы можете просматривать и редактировать все картинки.') ?></p>

<?php
	$CI = & get_instance();
	$CI->load->helper('form');
	
	global $MSO;
	
 // сформируем массив сортировок
 $sort_fields = array(
       'picture_date' => 'Дата добавления',
       'picture_date_file' => 'Дата файла',
       'picture_date_photo' => 'Дата снимка',
       'picture_file' => 'Файл',
       'picture_width' => 'Ширина',
       'picture_position' => 'Положение',
       'picture_view_count' => 'Просмотров',
       'picture_source_id' => 'Источник',
       
         );         
 // если нажата кнопка сортировки
 if ( $post = mso_check_post(array('f_session_id', 'f_sort_submit')) ) 
 {
	  mso_checkreferer();
	  $sort_field = mso_array_get_key($post['f_sort_submit']);
    if (isset($sort_fields[$sort_field]))
    {
       $options_admin['sort_field'] = $sort_field;
   		 mso_add_option('taggallery_admin', $options_admin , 'plugins');
   		 echo '<div class="update">Порядок сортировки изменен: ' . $sort_fields[$sort_field] . '</div>';
    }
    else 
    {
       $sort_field = false;
       echo '<div class="error">Ошибка сортировки</div>';
    }
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

//_________________________________________________________________________________
//_________________________________________________________________________________
//_________________________________________________________________________________
//_________________________________________________________________________________


	echo '<div>';
  require($plugin_dir . '/admin/out/navigator.php');
	echo '</div>';
	
//  echo  '<div style="clear:both">'; 

  $pictures = taggallery_get_pictures(array('sort_field' => $options_admin['sort_field']) , $pagination);
  echo '<h1>Все картинки.</H1>';
  echo '<h1>Всего  '. count($pictures) . ' картинок.</H1>';

	//Список файлов 
	
	$CI->load->library('table');
	$tmpl = array (
					'table_open'		  => '<table class="page" border="0" width="100%"><colgroup width="100">',
					'row_alt_start'		  => '<tr class="alt">',
					'cell_alt_start'	  => '<td class="alt">',
			  );

	$CI->table->set_template($tmpl); // шаблон таблицы
	// заголовки
	$CI->table->set_heading('Картинка', 'Инфо<br/><EM>* в () значение на диске, отличное от БД</EM>' , 'Действия' , 'Описание и метки' );


  //***************************************************************************************
  //***************************************************************************************
  //***************************************************************************************
  //***************************************************************************************

      
if ($pictures) 
 {
     // установлены ли плагины комментирования
  //  if (in_array($options['comments_plugin'], $MSO->active_plugins) ) $comments_plugin = true;  else $comments_plugin = false;
    
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
		   if ($picture['picture_mini_url']) 
		   {
		     $prev = '<img class="file_img" alt="" src="' . $picture['picture_mini_url'] . '" width = "' . $options_admin['admin_picture_width'] .'">';
         $prev = '<a class="lightbox" href="' . $picture['picture_url'] . '">' . $prev . '</a>';
		    }
		    
		   // если эта картинка из uploads/
       if (/*$picture['picture_dir'] and */ $picture['picture_file'] and !$picture['picture_source_id']) 
       { 
          // миниатюра 
          if ($picture['picture_mini_url']) 
          {
             $mini_url = $picture['picture_mini_url'];
             $prev = '<img class="file_img" alt="" src="' . $mini_url . '" width = "' . $options_admin['admin_picture_width'] .'">';
             $url = $picture['picture_url'];
          }   
          else
          {
             $mini_url = $uploads_url . $picture['picture_dir'] . 'mini/' . $picture['picture_file'];
             $mini_dir = $uploads_dir . $picture['picture_dir'] . 'mini/' . $picture['picture_file'];
             if (file_exists($mini_dir)) $prev = '<img class="file_img" alt="" src="' . $mini_url . '" width = "' . $options_admin['admin_picture_width'] .'">'; 
             else $prev = '<img class="file_img" alt="" src="' . $uploads_url . $picture['picture_dir'] . '' . $picture['picture_file'] . '" width = "' . $options_admin['admin_picture_width'] .'">' . '<br/>' . 'Нет миниатюры'; 
          
             if ($picture['picture_url']) $url = $picture['picture_url'];
             else $url = $uploads_url . $picture['picture_dir'] . '' . $picture['picture_file'];
          }    
          
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
          $submit_create_mini = '';
          //$source = taggallery_get_import_source($picture['picture_source_id']);
          $source = $picture['picture_source_id'];
          //if ($picture['source_link'])
          //   $source .= '<br/>' . '<a href="' . $picture['source_link'] . '" title="Страница картинки" >' . $picture['source_link'] . '</a> ';
       }   
       else 
       {
          $watermark = '';
          $submit_create_mini = '';       
          $source = 'Неизвестный источник';
       }   
       
       
      $picture_edit = '<a href="' . $admin_url . 'taggallery/picture/' . $picture['picture_id'] . '" title="Редактировать атрибуты картинки" >Редактировать</a> '; 
      $picture_link = $picture_edit . ' | ' . '<a href="' . $siteurl . $options['picture_slug'] . '/' .  $options['picture_prefix'] .$picture['picture_slug'] . '" title="Просмотр страницы картинки" >Просмотр</a>';  

       

        $delete = '<input type="submit" name="f_del_picture_submit[' . $picture['picture_id'] . ']" value="Удалить из базы">';       
       
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
               $picture['picture_width'] . '*' . $picture['picture_height'] . ' ' . $position . '<br/>' . 
               'Просмотров: ' . $picture['picture_view_count']
               ;  
      
      
      $functions =
                    $picture_link . '<br/>' . // страница редактирования фото
                    $delete . '<br/>' . // кнопка "Удалить из галереи" 
                    $submit_create_mini . '<br/>' . // создать миниатюру         
                    $comments . '<br/>' . // комментарии    
                    $watermark // наложение wotermark          
                    ;
                
       $CI->table->add_row($prev , $info , $functions , $desc . '<br />' . $tags . '<br />' . $tags_submit);
     }
     
  
	// добавляем форму, а также текущую сессию
  echo '<form action="" method="post">' . mso_form_session('f_session_id');  
        //выведем сортировку
   echo 'Сортировка:';
   foreach ($sort_fields as $key=>$val)
   {
      if ($options_admin['sort_field'] == $key) $disabled = ' disabled ';
      else $disabled = '';
      echo '<input type="submit" name="f_sort_submit[' . $key . ']" value="' . $val . '"' . $disabled . '>';
   }	
	
	  echo $CI->table->generate(); // вывод подготовленной таблицы
	  echo '<p><input type="submit" name="f_submit" value="Сохранить изменения"></p>';
     
   
	}// if pictures
	else echo 'Нет картинок.';
	
  echo '</form>';	   


       
?>