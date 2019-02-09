<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 * Создания миниатюры.
 */


# функция проставляет вотермарку
// $file_path - полное имя файла кртинки, которой проставляется вотермарка
// $water_type - как проставляетсявотермарк
// $userfile_water_file - файл ватермарк
function add_water_create_water($file_path , $water_type = 1, $userfile_water_file = '')
{
	$allowed_types = (isset($options['allowed_types'])) ? $options['allowed_types'] : 'gif|jpg|jpeg|png';
	
	if (!$userfile_water_file) $userfile_water_file = getinfo('uploads_dir') . 'watermark.png';

	$CI = & get_instance();
	$CI->load->library('image_lib');
	$CI->image_lib->clear();

			if (!file_exists($userfile_water_file))
			{
					echo '<div class="error">' . t('Водяной знак:', 'admin') . ' ' . t('файл водяного знака не найден! Загрузите его в каталог uploads/', 'admin') . '</div>';
					return false;
			}
			else
			{
					$hor = 'right'; //Инитим дефолтом.
					$vrt = 'bottom'; //Инитим дефолтом.
					if (($water_type == 2) or ($water_type == 4)) $hor = 'left';
					if (($water_type == 2) or ($water_type == 3)) $vrt = 'top';
					if ($water_type == 1) {$hor = 'center'; $vrt = 'middle';}

					$r_conf = array(
						'image_library' => 'gd2',
						'source_image' => $file_path,
						'new_image' => $file_path,
						'wm_type' => 'overlay',
						'wm_vrt_alignment' => $vrt,
						'wm_hor_alignment' => $hor,
						'wm_overlay_path' => $userfile_water_file //Жёстко, а что делать?
					);

					$CI->image_lib->initialize($r_conf );
					if (!$CI->image_lib->watermark())
					{
						echo '<div class="error">' . t('Водяной знак:', 'admin') . ' ' . $CI->image_lib->display_errors() . '</div>';
						return false;
					}	
					else return $file_path;
				}

}

function add_water_pictures_array(&$pictures , $dir='' , $url='', $curdir='' , $bad_file_names = array('tmp_without_water.jpg' , 'demo_all_water.jpg' , 'tmp_tmp_tmp.jpg') ) //рекурентная 
{
  $allowed_ext = array('gif', 'jpg', 'jpeg', 'png');
  $all_dirs = directory_map($dir, true); 
  
	if ($all_dirs)
	foreach ($all_dirs as $d)  // для содержимого этого каталога
	{
	  $subdir = $dir.$d;
	  $suburl = $url.$d;
	  if (is_dir($subdir)) // это каталог?
      {
  		   if ($d != '_mso_float' and $d != 'mini' and $d != '_mso_i' and $d != 'smiles') //  и это не служебный каталог
	       {
           add_water_pictures_array($pictures , $subdir.'/' , $suburl.'/' , $curdir . $d . '/' , $bad_file_names);  // переходим в него
         }
      }  
	  else //это файл - осуществляем подготовку и добавление в массив информации о файле
		{
			$ext = strtolower(str_replace('.', '', strrchr($d, '.'))); // расширение файла
			if ( in_array($ext, $allowed_ext) and !in_array($d, $bad_file_names))  // не запрещенный ли тип файла?
			{	
			  $date = filemtime($dir . $d);
			 	$file_arr = explode("." , $d);
        $file_full_key = str_replace("/","_DDD_",$curdir) . '_FFF_' . $file_arr[0] . '_EEE_' . $file_arr[1];
        $pictures[$file_full_key] = $date;
		  }  
		}
	} 

}

?>
<h1><?= t('Управление файлами картинок галереи изображений') ?></h1>
<p class="info"><?= t('Здесь вы можете наложить на картинку Watermark.') ?></p>
<?php

  $tmp_file_name = 'tmp_without_water.jpg';
  $tmp_file_name2 = 'tmp_tmp_tmp.jpg';
  $tmp_demo_name = 'demo_all_water.jpg';
  $tmp_file_path = getinfo('uploads_dir') . $tmp_file_name;
  $tmp_file_path2 = getinfo('uploads_dir') . $tmp_file_name2;
  $userfile_water_file = getinfo('uploads_dir') . 'watermark.png';
  $tmp_demo_path = getinfo('uploads_dir') . $tmp_demo_name;
  $demo_url = getinfo('uploads_url') . $tmp_demo_name;
  $img_width = '600';
  $img_add = ' width="' . $img_width . '"';
  
  $bad_file_names = array($tmp_file_name , $tmp_demo_name , $tmp_file_name2);
  
   $cache_key = 'add_water_all_pictures';
   $pictures_array = mso_get_cache($cache_key);
   if (!$pictures_array)
   {
      $pictures_array = array();
      $uploads_dir = getinfo('uploads_dir');
      $uploads_url = getinfo('uploads_url');
      add_water_pictures_array($pictures_array , $uploads_dir, $uploads_url , '' , $bad_file_names);
      asort($pictures_array);
    	mso_add_cache($cache_key, $pictures_array);
   }
  
   $img_navi_width = '200';
   $img_navi_add = ' width="' . $img_navi_width . '"';
   echo '<p class="info">Навигация по картинкам:</p>';  
  
  echo '<table><tr>';
  // выводим ссылку на первую картинку
  reset($pictures_array);
  $file_key = key($pictures_array);
  $path_array = explode("_FFF_" , $file_key);
  $ok = false;
  if (isset($path_array[0]) and isset($path_array[1]))  
  {
    $cur_dir = str_replace("_DDD_", "/", $path_array[0]);
    $file_array = explode("_EEE_" , $path_array[1]);
    if (isset($file_array[0]) and isset($file_array[1]))  
    {
      $file_name = $file_array[0] . '.' . $file_array[1];
      $file_path = getinfo('uploads_dir') . $cur_dir;
      $full_path = $file_path . $file_name;
      $picture_url = getinfo('uploads_url') . $cur_dir . $file_name;
      $picture_tmp_url = getinfo('uploads_url') . $tmp_file_name;
      if (file_exists($full_path)) $ok = true; // full_path первой картинки собран
   	  $wotermark_edit_link = '<a href="' . $plugin_url . 'watermark/' . $file_key .  '">Перейти</a>';
    }
  } 
  echo '<td>Первая картинка :<br>';  
  echo '<img src = "' . $picture_url . '"' . $img_navi_add . '><br>';    
  echo  $wotermark_edit_link . '</td>';   

  $file_current_key = mso_segment(4);

  if ($file_current_key == key($pictures_array)) // если текущая картинка первая
  {
      // выводим ссылку на вторую картинку
     next($pictures_array);
     $file_key = key($pictures_array);
     $path_array = explode("_FFF_" , $file_key);
     $ok = false;
     if (isset($path_array[0]) and isset($path_array[1]))  
     {
       $cur_dir = str_replace("_DDD_", "/", $path_array[0]);
       $file_array = explode("_EEE_" , $path_array[1]);
       if (isset($file_array[0]) and isset($file_array[1]))  
       {
         $file_name = $file_array[0] . '.' . $file_array[1];
         $file_path = getinfo('uploads_dir') . $cur_dir;
         $full_path = $file_path . $file_name;
         $picture_url = getinfo('uploads_url') . $cur_dir . $file_name;
         $picture_tmp_url = getinfo('uploads_url') . $tmp_file_name;
         if (file_exists($full_path)) $ok = true; // full_path второй картинки собран
   	     $wotermark_edit_link = '<a href="' . $plugin_url . 'watermark/' . $file_key .  '">Перейти</a>';
       }
     } 
     echo '<td>Вторая картинка :<br>';  
     echo '<img src = "' . $picture_url . '"' . $img_navi_add . '><br>';    
     echo  $wotermark_edit_link . '</td>'; 
  }
  
  while (each($pictures_array))
  {
     if ($file_current_key == key($pictures_array)) break;
  }
 
  if ($file_current_key == key($pictures_array) and $file_current_key)
  {
     prev($pictures_array);
       // выводим ссылку на предыдущую
       $file_key = key($pictures_array);
       $path_array = explode("_FFF_" , $file_key);
       $ok = false;
       if (isset($path_array[0]) and isset($path_array[1]))  
       {
         $cur_dir = str_replace("_DDD_", "/", $path_array[0]);
         $file_array = explode("_EEE_" , $path_array[1]);
         if (isset($file_array[0]) and isset($file_array[1]))  
         {
           $file_name = $file_array[0] . '.' . $file_array[1];
           $file_path = getinfo('uploads_dir') . $cur_dir;
           $full_path = $file_path . $file_name;
           $picture_url = getinfo('uploads_url') . $cur_dir . $file_name;
           $picture_tmp_url = getinfo('uploads_url') . $tmp_file_name;
           if (file_exists($full_path)) $ok = true; // full_path предыдущей картинки собран
   	       $wotermark_edit_link = '<a href="' . $plugin_url . 'watermark/' . $file_key .  '">Перейти</a>';
       } 
       echo '<td>Предыдущая картинка:<br>';  
       echo '<img src = "' . $picture_url . '"' . $img_navi_add . '><br>';    
       echo  $wotermark_edit_link . '</td>';   
     }
     
     next($pictures_array); 
     if (next($pictures_array) and next($pictures_array))
     { 
        prev($pictures_array);
        // выводим ссылку на следующую
       $file_key = key($pictures_array);
       $path_array = explode("_FFF_" , $file_key);
       $ok = false;
       if (isset($path_array[0]) and isset($path_array[1]))  
       {
         $cur_dir = str_replace("_DDD_", "/", $path_array[0]);
         $file_array = explode("_EEE_" , $path_array[1]);
         if (isset($file_array[0]) and isset($file_array[1]))  
         {
           $file_name = $file_array[0] . '.' . $file_array[1];
           $file_path = getinfo('uploads_dir') . $cur_dir;
           $full_path = $file_path . $file_name;
           $picture_url = getinfo('uploads_url') . $cur_dir . $file_name;
           $picture_tmp_url = getinfo('uploads_url') . $tmp_file_name;
           if (file_exists($full_path)) $ok = true; // full_path следующей картинки собран
   	       $wotermark_edit_link = '<a href="' . $plugin_url . 'watermark/' . $file_key .  '">Перейти</a>';
         }
       } 
       echo '<td>Следующая картинка:<br>';  
       echo '<img src = "' . $picture_url . '"' . $img_navi_add . '><br>';    
       echo  $wotermark_edit_link . '</td>';  
     }
  }
  // выводим ссылку на последнюю картинку
  end($pictures_array);
  $file_key = key($pictures_array);
  $path_array = explode("_FFF_" , $file_key);
  $ok = false;
  if (isset($path_array[0]) and isset($path_array[1]))  
  {
    $cur_dir = str_replace("_DDD_", "/", $path_array[0]);
    $file_array = explode("_EEE_" , $path_array[1]);
    if (isset($file_array[0]) and isset($file_array[1]))  
    {
      $file_name = $file_array[0] . '.' . $file_array[1];
      $file_path = getinfo('uploads_dir') . $cur_dir;
      $full_path = $file_path . $file_name;
      $picture_url = getinfo('uploads_url') . $cur_dir . $file_name;
      $picture_tmp_url = getinfo('uploads_url') . $tmp_file_name;
      if (file_exists($full_path)) $ok = true; // full_path последней картинки собран
   	  $wotermark_edit_link = '<a href="' . $plugin_url . 'watermark/' . $file_key .  '">Перейти</a>';
    }
  } 
  echo '<td>Последняя картинка:<br>';  
  echo '<img src = "' . $picture_url . '"' . $img_navi_add . '><br>';    
  echo  $wotermark_edit_link . '</td>';  

  echo '</tr></table>';

    
  // "_DDD_" заменим на /
  // "_EEE_" на .
  // "_FFF_" - разделитель между dir и file
  $file_key = mso_segment(4);
  $path_array = explode("_FFF_" , $file_key);
  $ok = false;
  if (isset($path_array[0]) and isset($path_array[1]))  
  {
    $cur_dir = str_replace("_DDD_", "/", $path_array[0]);
    $file_array = explode("_EEE_" , $path_array[1]);
    if (isset($file_array[0]) and isset($file_array[1]))  
    {
      $file_name = $file_array[0] . '.' . $file_array[1];
      $file_path = getinfo('uploads_dir') . $cur_dir;
      $full_path = $file_path . $file_name;
      $picture_url = getinfo('uploads_url') . $cur_dir . $file_name;
      $picture_tmp_url = getinfo('uploads_url') . $tmp_file_name;
      if (file_exists($full_path)) $ok = true; // full_path картинки собран
    }
  }
  
  if (!$file_key) echo '<H3>Вы пожете перебирать картинки по дате изменения файлов.</H3>';
  elseif (!$ok) echo '<div class="error">Файл картинки не найден.</div>';
  else
  {
    $wotermark_type = false;
    if ( $post = mso_check_post(array('f_session_id', 'f_type_1')) ) //нажата кнопка тип вотермарк 1
    {
	    mso_checkreferer();
	    $wotermark_type = 1;
    }
    if ( $post = mso_check_post(array('f_session_id', 'f_type_2')) ) //нажата кнопка тип вотермарк 2
    {
	    mso_checkreferer();
	    $wotermark_type = 2;
    }  
    if ( $post = mso_check_post(array('f_session_id', 'f_type_3')) ) //нажата кнопка тип вотермарк 3
    {
	    mso_checkreferer();
	    $wotermark_type = 3;
    }  
    if ( $post = mso_check_post(array('f_session_id', 'f_type_4')) ) //нажата кнопка тип вотермарк 4
    {
	    mso_checkreferer();
	    $wotermark_type = 4;
    }  
    if ( $post = mso_check_post(array('f_session_id', 'f_type_5')) ) //нажата кнопка тип вотермарк 5
    {
	    mso_checkreferer();
	    $wotermark_type = 5;
    }  
    if ( $post = mso_check_post(array('f_session_id', 'f_return')) ) //нажата кнопка возврата
    {
			copy($tmp_file_path , $full_path);	
      echo '<div class="update">Откат произведен.</div>';
    }  
    
    if ($wotermark_type)
    {
			//Перед изменением создадим копию картинки для отката
			if (file_exists($tmp_file_path)) unlink($tmp_file_path);
			copy($full_path, $tmp_file_path);	    
      $res_path = add_water_create_water($full_path , $wotermark_type , $userfile_water_file , $tmp_file_path);
      if ($res_path == $full_path) echo '<div class="update">Вотермарк наложена. Если не устраивает - произведите откат.</div>';
      else echo '<div class="error">Ошибка создания Watermark.</div>';
    }
  
    // выводим файл картинки
    echo '<p class="info">Файл: ' . $file_name . '</p>';  
    
   // ссылка на страницу директории этой картинки
   $dir_link = '<a href="' . $admin_url . 'taggallery/' . $cur_dir . '" title="Каталог изображения" >' . '/uploads/' . $cur_dir . '</a> ';
    echo '<p class="info">Перейти в директорий этой картинки: ' . $dir_link . '</p>';  

    // выводим картинку
    echo '<p class="info">Текущая картинка:</p>';  
    echo '<img src = "' . $picture_url . '"' . $img_add . '>';


    if (file_exists($tmp_demo_path)) unlink($tmp_demo_path);
  
      $f = '';
  
	 if (file_exists($userfile_water_file)) 
	 {
	    // создадим демо-картинку
	    copy($full_path, $tmp_demo_path);	   
      add_water_create_water($tmp_demo_path , 1 , $userfile_water_file);
      add_water_create_water($tmp_demo_path , 2 , $userfile_water_file);
      add_water_create_water($tmp_demo_path , 3 , $userfile_water_file);
      add_water_create_water($tmp_demo_path , 4 , $userfile_water_file);
      add_water_create_water($tmp_demo_path , 5 , $userfile_water_file);

      // выводим демо-картинку
      echo '<p class="info">Все варианты установки Watermark<br>(посмотрите какой лучше и выберите):</p>';  
      echo '<img src = "' . $demo_url . '"' . $img_add . '>';
    
      // форма
      echo '<p class="info">Выберете вариант установки Watermark:</p>';  
    
      // выводим кнопки
 		  $f .= '<table><tr>';
      $f .= '<td><p><input type="submit" name="f_type_2" value="' . t('Верх, лево', 'plugins') . '" style="margin: 25px 0 5px 0;" /></p></td>';
      $f .= '<td></td>';
      $f .= '<td><p><input type="submit" name="f_type_3" value="' . t('Верх, право', 'plugins') . '" style="margin: 25px 0 5px 0;" /></p></td>';
      $f .= '</tr><tr>';
      $f .= '<td></td><td><p><input type="submit" name="f_type_1" value="' . t('Центр', 'plugins') . '" style="margin: 25px 0 5px 0;" /></p></td><td></td>';
      $f .= '</tr><tr>';
      $f .= '<td><p><input type="submit" name="f_type_4" value="' . t('Низ, лево', 'plugins') . '" style="margin: 25px 0 5px 0;" /></p></td>';
      $f .= '<td></td>';
      $f .= '<td><p><input type="submit" name="f_type_5" value="' . t('Низ, право', 'plugins') . '" style="margin: 25px 0 5px 0;" /></p></td>';  
 		  $f .= '</tr></table>';
   }
   else echo '<div class="error">файл водяного знака не найден! Загрузите его в каталог uploads/</div>';
       
    // выводим возможность отката
    $f .= '<p class="info">Содержание файла временной картинки,<br>которая будет возвращена в текущую при нажатии кнопки Восстановить:</p>';
    $f .= '<img src = "' . $picture_tmp_url . '"' . $img_add . '>';
    $f .= '<p><input type="submit" name="f_return" value="' . t('Восстановить', 'plugins') . '" style="margin: 25px 0 5px 0;" /></p>';  

    echo '<form action="" method="post">' . mso_form_session('f_session_id') . $f . '</form>';
  
  }

?>