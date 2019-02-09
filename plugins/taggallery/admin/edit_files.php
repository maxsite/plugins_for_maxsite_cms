<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>

<h1><?= t('Управление файлами картинок галереи изображений') ?></h1>
<p class="info"><?= t('Здесь вы можете задать описания и метки файлам изображений в директориях.') ?></p>

<?php
	$CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	
	$CI->load->helper('directory');
	$CI->load->helper('form');
	
	global $MSO;
	
  $table_error = false;
	if ( !$CI->db->table_exists('pictures')) $table_error .= 'нет pictures<br/>';
	if ( !$CI->db->table_exists('gallerys')) $table_error .= 'нет gallers<br/>';
	if ( !$CI->db->table_exists('albums')) $table_error .= 'нет albums<br/>';
	if ( !$CI->db->table_exists('galalb')) $table_error .= 'нет galalb<br/>';
	if ( !$CI->db->table_exists('picgal')) $table_error .= 'нет picgal<br/>';
	if ( !$CI->db->table_exists('source')) $table_error .= 'нет source<br/>';
  if ($table_error) echo '<div class="error"><H1>Проверка существования таблиц:</H1>' . $table_error . '</div>';

	// по сегменту определяем текущий каталог
	// если каталога нет, скидываем на дефолтный ''
	$current_dir = getCurDir();
	$path = getinfo('uploads_dir') . $current_dir;
	$source_pach = false; // здесь будет что-то, если юзаем не uploads/
	
	if (!is_dir($path)) // нет каталога
	{
		$path = getinfo('uploads_dir');
		$current_dir = $current_dir_h2 = '';
	}

	
 $fn_mso_descriptions = $path . '_mso_i/_mso_descriptions.dat';
 $pag = false;
 
 
 
  $pictures = taggallery_get_pictures(array('dir' => $current_dir , 'source_id' =>0) , $pagination);
  
  // приведем к виду чтобы можно было сразу узнать - вбазе данных ли картинка
  $pictures_in_db = array();
  if ($pictures) foreach ($pictures as $picture)
  {
		$file_arr = explode("." , $picture['picture_file']);
    $file_form_key = $file_arr[0] . '_ext_' . $file_arr[1];
    $pictures_in_db[$file_form_key] = $picture;
  } 
 
 
 // сформируем массив сортировок (он иной чем в других местах)
 $sort_fields = array(
       'date_file' => 'Дата файла',
       'date_photo' => 'Дата снимка',
       'file' => 'Файл',
       'width' => 'Ширина',
       'position' => 'Положение',
       'tags' => 'Метки',
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


 //если нажата кнопка удалить из базы данных
 if ( $post = mso_check_post(array('f_session_id', 'f_delete_from_db')) ) 
 {
	  mso_checkreferer();
	  $picture_id = mso_array_get_key($post['f_delete_from_db']);

    $err = taggallery_delete_picture(array('picture_id' => $picture_id));
    
    if ($err) echo '<div class="error">' . $err . '</div>';
    else echo '<div class="update">Картинка удалена из базы данных.</div>';
 } 


 //если нажата кнопка добавить картинку
 if ( $post = mso_check_post(array('f_session_id', 'f_desc' , 'f_tags' , 'f_add_to_db')) ) 
 {
	  mso_checkreferer();
	  
   if ( ($f_tags = $post['f_tags']) and ($f_desc = $post['f_desc']) )
	 { 
	  $picture_key = mso_array_get_key($post['f_add_to_db']);
    $file_arr = explode("_ext_" , $picture_key);
    $file_name = $file_arr[0] . '.' . $file_arr[1];	
    
    $tags = trim($f_tags[$picture_key]); // новый заголовок

    $tags_array = mso_explode($tags, false, false , true); // новые метки
    $desc = trim($f_desc[$picture_key]); // новый заголовок
    $picture = array(
                        'picture_dir' => $current_dir ,
                        'picture_file' => $file_name ,
                        'picture_tags' => $tags_array ,
                        'picture_title' => $desc
                   );
    
    $res = taggallery_add_picture($picture);
    
    if ($res['errors']) echo '<div class="error">' . $res['errors'] . '</div>';
    else echo '<div class="update">Картинка обавлена в БД.</div>';
    
    if ($res['messages']) echo '<div class="update">' . $res['messages'] . '</div>';
   }
   else echo '<div class="error">Ошибка.</div>';
 } 
 
 //если нажата кнопка создать миниатюру
 if ( $post = mso_check_post(array('f_session_id', 'f_create_mini_submit')) ) 
 {
	  mso_checkreferer();
	  $picture_key = mso_array_get_key($post['f_create_mini_submit']);
    $file_arr = explode("_ext_" , $picture_key);
    $file_name = $file_arr[0] . '.' . $file_arr[1];	
    // $file_key = str_replace("/","_",$current_dir) . $file_arr[0] . '_' . $file_arr[1];
    $r = array();
    $r['file_path'] = $path;
    $r['file_name'] = $file_name;
    $err = taggallery_create_mini($r);
    if ($err) echo '<div class="error">' . $err . '</div>';
    else echo '<div class="update">Миниатюра создана:<br /><img src="' . $uploads_url . $current_dir . 'mini/' . $file_name . '" /></div>';
 } 

 // кнопка соранить изменения 
 if ( $post = mso_check_post(array('f_session_id', 'f_submit', 'f_desc' , 'f_tags')) )
 {
   mso_checkreferer();
   if ( ($f_tags = $post['f_tags']) and ($f_desc = $post['f_desc']) )
   {
     $tags = array();
     $desc = array();
     // массив изменяемых картинок
     foreach ($f_tags as $key => $val)
     {
       $file_arr = explode("_ext_" , $key);
       $file_name = $file_arr[0] . '.' . $file_arr[1];
       $tags_array = mso_explode($val, false, false , true); // новые метки
       $desc = trim($f_desc[$key]); // новый заголовок

       // если меток нет и нет картинки в базе то ее и не добавляем
       if (!$val and !isset($pictures_in_db[$key])) continue;
       
       $picture = array(
          'picture_title' => $desc,
          'picture_tags' => $tags_array,
          'picture_dir' => $current_dir,
          'picture_file' => $file_name,
               );

       // if ($source_pach) $picture['source_path'] => $source_pach; // если картинка не из uploads/ 
      $res = taggallery_add_picture($picture);
      if ($res['errors']) echo '<div class="error">' . $res['errors'] . '</div>';
      else echo '<div class="update">Изменения сохранены: ' . $file_name . '</div>';

       if ($res['messages']) echo '<div class="update">' . $res['messages'] . '</div>';
     }  

   }
   else  echo '<div class="error">Ошибка ...</div>';
 }

  $pictures = taggallery_get_pictures(array('dir' => $current_dir , 'source_id' =>0) , $pagination);
  
  // приведем к виду чтобы можно было сразу узнать - вбазе данных ли картинка
  $pictures_in_db = array();
  if ($pictures)
    foreach ($pictures as $picture)
  {
		$file_arr = explode("." , $picture['picture_file']);
    $file_form_key = $file_arr[0] . '_ext_' . $file_arr[1];
    $pictures_in_db[$file_form_key] = $picture;
  }
  
  
	if (file_exists($fn_mso_descriptions )) 
	{
		$mso_descriptions = unserialize( read_file($fn_mso_descriptions) ); // получим из файла все описания
	}
	else 
	{
	  write_file($fn_mso_descriptions, serialize(array())); // записываем в него пустой массив
	  $mso_descriptions = array();
  }
//_________________________________________________________________________________
//_________________________________________________________________________________
//_________________________________________________________________________________
//_________________________________________________________________________________
  echo '<div style="float:left">';
	navigate_block();
	echo '</div>';
	echo '<div style="margin-left:270px;">';
  require($plugin_dir . '/admin/out/navigator.php');
	echo '</div>';
	
  echo '<h1>Картинки в директории:</H1>';

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


	// проходимся по каталогу аплоада и выводим их списком


	// все файлы в массиве $dirs
	$dirs = directory_map($path, true); // только в текущем каталоге

	if (!$dirs) $dirs = array();

	sort($dirs);

	$out_all = false; // весь вывод

     // установлены ли плагины комментирования
  //  if (in_array($options['comments_plugin'], $MSO->active_plugins) ) $comments_plugin = true;  else $comments_plugin = false;
    $comments_plugin = false;
     
  // здесь будут все галереи этой директории
  $gallerys_this_dir = array();   
      
      
  // создадим массив вайлов в текущей директории
  $files = array();
  
	foreach ($dirs as $file)
	{
	  $file_array = array();
	
		if (@is_dir($path . $file)) continue; // это каталог

		$ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		if ( !( $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'png')  ) continue; // запрещенный тип файла

 //   $title = '';
		$file_arr = explode("." , $file);
		
    $file_array['file'] =  $file;
    $file_array['file_full_path'] = $path . $file;
    
		// ключ файла для однозначного слуга картинки
    $file_array['file_key'] = str_replace("/","_",$current_dir) . $file_arr[0] . '_' . $file_arr[1];
    
		// ключ файла для формы
    $file_array['file_form_key'] = $file_arr[0] . '_ext_' . $file_arr[1];
    
    // ключ для слуга определения картинки в плагине weatermark
    $file_array['file_full_key'] = str_replace("/","_DDD_",$current_dir) . '_FFF_' . $file_arr[0] . '_EEE_' . $file_arr[1];
    
	  $file_form_key = $file_array['file_form_key'];
	  $file_full_key = $file_array['file_full_key'];
	  $file_key = $file_array['file_key'];
	  $file_full_path = $file_array['file_full_path'];
    
		$desc = '';
		$tags = '';
		if (isset($mso_descriptions[$file]))
		{
      if (strpos($mso_descriptions[$file],"|") !== false ) //есть ли разделитель в дополнении?
      {
        $opis = explode ("|" , $mso_descriptions[$file]);
        $desc = trim($opis[0]);
        $tags = trim($opis[1]);
      }
      else  
      {
        $desc = trim($mso_descriptions[$file]);
        $tags = '';
      }		
    } 
 
   $file_array['desc'] =  $desc;
   $file_array['tags'] =  $tags;
 
   $file_array['file_full_path'] = $path . $file;
   
   // разберемся с датами
   // дата файла
    $date_file = date ("Y-m-d" , filemtime($file_full_path));
   	$file_array['date_file'] = taggallery_date($date_file , $date_format, '', '' , false);
							    
    
    // дата снимка
    $date_photo = taggallery_get_photo_date($file_full_path);
   	$file_array['date_photo'] = taggallery_date($date_photo , $date_format, '', '' , false);
          
    //определим ориентацию картинки в пространстве
    $image_info = GetImageSize($file_full_path);
		$width = $image_info[0];
		$height = $image_info[1]; 
  	if ($width > $height) $position = 1; // ladscape
	  elseif ($width < $height) $position = 2; // portail
		else $position = 3;    // squire	  
	  
   $file_array['width'] =  $width;
   $file_array['height'] =  $height;
   $file_array['position'] =  $position;
   
   $files[] = $file_array;
	}  
  
  //***************************************************************************************
  //***************************************************************************************
  //***************************************************************************************
  //***************************************************************************************

 //сортировка
  if ($options_admin['sort_field'] != 'file' and function_exists('taggallery_sort_' . $options_admin['sort_field'])) uasort($files , 'taggallery_sort_' . $options_admin['sort_field']);
  
  

      
	foreach ($files as $file)
	{
	  $file_form_key = $file['file_form_key'];
	  $file_full_key = $file['file_full_key'];
	  $file_key = $file['file_key'];
	
    $title = '';
	  $watermark = '<a href="' . $plugin_url . 'watermark/' . $file['file_full_key'] .  '">Watermark</a>';

	  // если картинка уже в базе данных
	  if (isset($pictures_in_db[$file['file_form_key']])) 
	  {
	    $picture = $pictures_in_db[$file['file_form_key']];
	    
	    // сравним то что в базе и что на диске
	    // если не совпадает - сообщим об этом
	    
      $picture_date_file = $picture['picture_date_file'];
      $picture_date_file = taggallery_date($picture_date_file, $date_format, '', '' , false);
      if (!($picture_date_file == $file['date_file'])) 
          $date_file = $picture_date_file . ' (' . $file['date_file'] . ')';
      else $date_file = $picture_date_file;
      
      $picture_date_photo = $picture['picture_date_photo'];
      $picture_date_photo = taggallery_date($picture_date_photo, $date_format, '', '' , false);  
      if (!($picture_date_photo == $file['date_photo']))
          $date_photo = $picture_date_photo . ' (' . $file['date_photo'] . ')';
      else $date_photo = $picture_date_photo;



      if (!($picture['picture_height'] == $file['height']))
          $height = $picture['picture_height'] . ' (' . $file['height'] . ')';
      else $height = $picture['picture_height'];
      
      if (!($picture['picture_width'] == $file['width']))
          $width = $picture['picture_width'] . ' (' . $file['width'] . ')'; 
      else $width = $picture['picture_width'];

      if (!($picture['picture_position'] == $file['position']))
          $position = $picture['picture_position'] . ' (' . $file['position'] . ')';            
      else $position = $picture['picture_position'];

	    
	    // сравним описания
      if ($picture['picture_title'] == $file['desc'])
         $desc_edit = 'Титл: <input name="f_desc[' . $file_form_key . ']" type="text" value="' . $file['desc'] . '">';
      else 
      {
         $desc_edit = 'Титл в Бд: ' . $picture['picture_title'] . '<br />' .
                      'Новый титл (в desc в mso_descriptions.dat): <input name="f_desc[' . $file_form_key . ']" type="text" value="' . $file['desc'] . '">';
      }
      
      // сравним метки 
	    $picture_tags = array();
	    foreach ($picture['gallerys'] as $picture_gallery)
	    {
	      $picture_tags[] = $picture_gallery['gallery_name'];
	    }
	    
	    $tags_array = mso_explode($file['tags'], false, false);
	    
      if (!array_diff($tags_array , $picture_tags) and !array_diff($picture_tags , $tags_array) )
         $tags_edit = 'Метки: <input name="f_tags[' . $file_form_key . ']" type="text" value="' . $file['tags'] . '">';
      else 
      {
         $tags_edit = 'Метки в БД: ' . implode(", " , $picture['tags']) . '<br />' .
                      'Новые метки (в mso_descriptions.dat): <input name="f_tags[' . $file_form_key . ']" type="text" value="' . $file['tags'] . '">';
      }
      	
      	
      $picture_date = taggallery_date($picture['picture_date'], $date_format, '', '' , false);      	    
      $date =  'Дата добавления: ' . $picture_date .'<br/>';
	    
	    $in_db = '';
	    $in_db_submit = '<input type="submit" name="f_delete_from_db[' . $picture['picture_id'] . ']" value="Удалить из БД">';
	    
	    $picture_edit = '<a href="' . $admin_url . 'taggallery/picture/' . $picture['picture_id'] . '" title="Редактировать атрибуты картинки" >Редактировать</a> ';  
	    
	    $picture_link = $picture_edit . ' | ' . '<a href="' . $siteurl . $options['picture_slug'] . '/' .  $options['picture_prefix'] .$picture['picture_slug'] . '" title="Просмотр страницы картинки" >Просмотр</a>';  
	    
	    // запись о файле в db обработана, поэтому удалим за ненадобностью
	    unset($pictures_in_db[$file['file_form_key']]); // вычеркнем
	  }
	  else // если картинки нет в базе данных
	  {
         $tags_edit = 'Метки: <input name="f_tags[' . $file_form_key . ']" type="text" value="' . $file['tags'] . '">';
         $desc_edit = 'Титл: <input name="f_desc[' . $file_form_key . ']" type="text" value="' . $file['desc'] . '">';
   //      $date = '<input name="f_date[' . $file_form_key . ']" type="text" value="' . date("Y-m-d") . '">';
         $date = '';
	       $in_db = '<span class="red">Нет в базе</span>';
	       $picture_link = ''; 
	       $in_db_submit = '<input type="submit" name="f_add_to_db[' . $file_form_key . ']" value="Добавить в БД">';
	  }	  
	  
	  	    	
		// если миниатюры нет, то сформируем кнопку ее создания
		if (file_exists( $path . 'mini/' . $file['file']  ))
		{
	    	$submit_create_mini = '<input type="submit" name="f_create_mini_submit[' . $file_form_key . ']" value="Пере-Создать миниатюру">';
		}
		else
		{
	    	$submit_create_mini = '<input type="submit" name="f_create_mini_submit[' . $file_form_key . ']" value="Создать миниатюру">';
		}   

		// выберем превьюшку
		if (file_exists( $path . '_mso_i/' . $file['file']  ))
	    	$prev = '<img class="file_img" alt="" src="' . $uploads_url . $current_dir . '_mso_i/' . $file['file'] . '" width = "' . $options_admin['admin_picture_width'] .'">';
		elseif (file_exists( $path . 'mini/' . $file['file']  ))
	    	$prev = '<img  class="file_img" alt="" src="' . $uploads_url . $current_dir . 'mini/' . $file['file'] . '" width = "' . $options_admin['admin_picture_width'] .'">';
	  else	
	    	$prev = '<img class="file_img" alt="" src="' . $uploads_url . $current_dir . $file['file'] . '" width = "' . $options_admin['admin_picture_width'] .'">';
		if (!file_exists( $path . 'mini/' . $file['file']  )) $prev .= '<br />Нет миниатюры';

    
	 $url = $uploads_url . $current_dir . $file['file'];
   $prev = '<a class="lightbox" href="' . $url . '">' . $prev . '</a>';
   
    
    $comments = '';
   
     	  $position = str_replace(array('1','2','3') , array('Ландшафт', 'Портрет', 'Кварат') , $position);

   
       // информация о файле изображения
       $info = 
               $file['file'] . '<br/>' .
               'Дата файла: ' . $date_file . '<br/>' .  
               'Дата снимка: ' . $date_photo . '<br/>' .
               $date .
               $width . '*' . $height . ' ' . $position
               ;  
      
       // что можно сделать с файлом изображения
       $functions =
                    $in_db .  
                    $picture_link . '<br/>' . 
                    $submit_create_mini . '<br/>' . // создать миниатюру         
                    $watermark . '<br/>' . // наложение wotermark 
                    $in_db_submit   // удалить ли добавить в БД      
                    ;    
    
       // описание (титл) и метки
       $edit_opis = 
                    $desc_edit . '<br/>' .  
                    $tags_edit
                    ;    
    
    $CI->table->add_row($prev , $info , $functions , $edit_opis);
    
    $out_all = true;
	}



  
 // echo  '<div style="clear:both">'; 
  
	// добавляем форму, а также текущую сессию
  echo '<form action="" method="post">' . mso_form_session('f_session_id');
	
	if ($out_all) 
	{
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
	}
	else
	{
		echo '<div>' . t('Нет загруженных файлов', 'admin') . '</div>';
	}

  // если унас в БД есть несуществующие картинки
  if ($pictures_in_db) 
  {
    echo '<H2>' . 'В БД есть несуществующие в этом каталоге картинки.' . '</H2>';
    echo '<table>';
    foreach ($pictures_in_db as $picture)
    {
      echo '<tr>';
      echo '<td>' . $picture['picture_id'] . '</td>';
      echo '<td>' . $picture['picture_file'] . '</td>';
	    echo '<td><input type="submit" name="f_delete_from_db[' . $picture['picture_id'] . ']" value="Удалить"></td>';
      echo '</tr>';
    }
    echo '</table>';
  } 

	echo '</form>';
	
  echo  '</div>'; 
	

// Блок навигации
function navigate_block()
{
	require(getinfo('plugins_dir') . 'taggallery/admin/tree/tree.php');
}




// Получение пути активного каталога
function getCurDir(){
	$i = 3;
	$f_directory = '';
	while(($seg = mso_segment($i)) != ''){
		$f_directory .= $seg . '/';
		$i++;
	}
	return $f_directory;
}


function taggallery_sort_date($a, $b) 
{
	if ( $a['date'] == $b['date'] ) return 0;
	return ( $a['date'] < $b['date'] ) ? 1 : -1;
}

function taggallery_sort_date_file($a, $b) 
{
	if ( $a['date_file'] == $b['date_file'] ) return 0;
	return ( $a['date_file'] < $b['date_file'] ) ? 1 : -1;
}

function taggallery_sort_date_photo($a, $b) 
{
	if ( $a['date_photo'] == $b['date_photo'] ) return 0;
	return ( $a['date_photo'] < $b['date_photo'] ) ? 1 : -1;
}

function taggallery_sort_width($a, $b) 
{
	if ( $a['width'] == $b['width'] ) return 0;
	return ( $a['width'] < $b['width'] ) ? 1 : -1;
}

function taggallery_sort_position($a, $b) 
{
	if ( $a['position'] == $b['position'] ) return 0;
	return ( $a['position'] < $b['position'] ) ? 1 : -1;
}

function taggallery_sort_tags($a, $b) 
{
	if ( $a['tags'] == $b['tags'] ) return 0;
	return ( $a['tags'] < $b['tags'] ) ? 1 : -1;
}

       
?>