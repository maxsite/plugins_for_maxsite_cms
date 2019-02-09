<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 /**
 * MaxSite CMS
 */
 
// функция создает миниатюру для картинки
function taggallery_create_mini($r = array())
{
	$CI = & get_instance();
	$CI->load->library('image_lib');
	$err = false;
	
	// если манипуляцию проводим с картинкой в базе
	if (isset($r['picture_id']) and $r['picture_id'])
	{
	   $CI->db->select('picture_dir , picture_file');
		 $CI->db->where('picture_id', $r['picture_id']);
		 $query=$CI->db->get('pictures');
	   if ($query->num_rows()) // если есть картинка
	   {
	     $pictures = $query->result_array();
	     if (isset($pictures[0]['picture_dir']))
	     {
	       $r['file_path'] = getinfo('uploads_dir') . $pictures[0]['picture_dir'];
	       $r['file_name'] = $pictures[0]['picture_file'];
	     }
	   }
	}



if (isset($r['file_path']) and isset($r['file_name'])) 
{
  // создадим директорию mini в указанном $r['file_path'], если нет
  $mini_dir_name = getinfo('uploads_dir') . $r['file_path'] . 'mini';
  if (!is_dir($mini_dir_name)) @mkdir($mini_dir_name, 0777); 


  $full_path = $r['file_path'] .  $r['file_name'];
	// миниатюры всегда хранятся в подкаталоге mini
	if (!isset($r['userfile_mini_size'])) $r['userfile_mini_size'] = mso_get_option('size_image_mini', 'general', 150); // размер миниатюры
	if (!isset($r['mini_type'])) $r['mini_type'] = mso_get_option('image_mini_type', 'general', 1);	
; // тип миниатюры
	
			$image_info = GetImageSize($full_path);
			$image_width = $image_info[0];
			$image_height = $image_info[1];
			
			
			# теперь нужно сделать миниатюру указанного размера в mini
			if ($r['userfile_mini_size'])
			{
				$size = abs((int) $r['userfile_mini_size']);

				($image_width >= $image_height) ? ($max = $image_width) : ($max = $image_height);
				if ( $size > 1 and $size < $max ) // корректный размер
				{
					$r_conf = array(
						'image_library' => 'gd2',
						'source_image' => $full_path,
						'new_image' => $r['file_path'] . 'mini/' . $r['file_name'],
						'maintain_ratio' => true,
						'width' => $size,
						'height' => $size,
					);


					$mini_type = $r['mini_type']; // тип миниатюры
					/*
					1 Пропорционального уменьшения
					2 Обрезки (crop) по центру
					3 Обрезки (crop) с левого верхнего края
					4 Обрезки (crop) с левого нижнего края
					5 Обрезки (crop) с правого верхнего края
					6 Обрезки (crop) с правого нижнего края
					7 Уменьшения и обрезки (crop) в квадрат
					*/

					if ($mini_type == 2) // Обрезки (crop) по центру
					{
						$r_conf['x_axis'] = round($image_width / 2 - $size / 2);
						$r_conf['y_axis'] = round($image_height / 2 - $size / 2);
						$CI->image_lib->initialize($r_conf );
						if (!$CI->image_lib->crop()) $err = $CI->image_lib->display_errors();
						else $err = false;
					}
					elseif ($mini_type == 3) // Обрезки (crop) с левого верхнего края
					{
						$r_conf['x_axis'] = 0;
						$r_conf['y_axis'] = 0;

						$CI->image_lib->initialize($r_conf );
						if (!$CI->image_lib->crop()) $err = $CI->image_lib->display_errors();
						else $err = false;
					}
					elseif ($mini_type == 4) // Обрезки (crop) с левого нижнего края
					{
						$r_conf['x_axis'] = 0;
						$r_conf['y_axis'] = round($image_height - $size * $image_height/$image_width);

						$CI->image_lib->initialize($r_conf );
						if (!$CI->image_lib->crop()) $err = $CI->image_lib->display_errors();
						else $err = false;
					}
					elseif ($mini_type == 5) // Обрезки (crop) с правого верхнего края
					{
						$r_conf['x_axis'] = $image_width - $size;
						$r_conf['y_axis'] = 0;

						$CI->image_lib->initialize($r_conf );
						if (!$CI->image_lib->crop()) 	$err = $CI->image_lib->display_errors();
						else $err = false;

					}
					elseif ($mini_type == 6) // Обрезки (crop) с правого нижнего края
					{
						$r_conf['x_axis'] = $image_width - $size;
						$r_conf['y_axis'] = $image_height - $size;

						$CI->image_lib->initialize($r_conf );
						if (!$CI->image_lib->crop()) $err = $CI->image_lib->display_errors();
						else $err = false;
					}
					elseif ($mini_type == 7) // Уменьшения и обрезки (crop) в квадрат
					{
						if ($image_width > $image_height) // Если ширина больше высоты
						{
							$resize = round($size * $image_width / $image_height); // Для ресайза по минимальной стороне
							$r_conf['width'] = $resize;
							
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->resize()) $err = $CI->image_lib->display_errors();
						  else // если ресайз выполнен то продолжим
						  {
							  $r_conf['x_axis'] = round(($resize - $size) / 2);
							  $r_conf['y_axis'] = 0;
							  $r_conf['width'] = $size;
							  $r_conf['maintain_ratio'] = false;
							  $r_conf['source_image'] = $r_conf['new_image'];
							
							  $CI->image_lib->initialize($r_conf );
							  if (!$CI->image_lib->crop()) $err = $CI->image_lib->display_errors();
							  else $err = false;
						  }
						}
						elseif ($image_width < $image_height) // Если высота больше ширины
						{
							$resize = round($size * $image_height / $image_width);
							$r_conf['height'] = $resize;
							
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->resize()) $err = $CI->image_lib->display_errors();
						  else // если ресайз выполнен то продолжим
						  {
							   $r_conf['x_axis'] = 0;
							   $r_conf['y_axis'] = round(($resize - $size) / 2);
							   $r_conf['height'] = $size;
							   $r_conf['maintain_ratio'] = false;
							   $r_conf['source_image'] = $r_conf['new_image'];
							
							   $CI->image_lib->initialize($r_conf );
						     if (!$CI->image_lib->crop()) $err = $CI->image_lib->display_errors();
							   else $err = false;
							}
						}
						else // Равны
						{
							$CI->image_lib->initialize($r_conf );
							if (!$CI->image_lib->resize()) $err = $CI->image_lib->display_errors();
              else $err = false;
						}					
					}
					else // ничего не указано - Пропорционального уменьшения
					{
						$CI->image_lib->initialize($r_conf );
						if (!$CI->image_lib->resize()) $err = $CI->image_lib->display_errors();
						else $err = false;
					}
				}
				else $err = 'Миниатюра не создана - размер некорректный.';
			}
			else $err = 'Миниатюра не создана - размер не задан.';	
}
else $err = 'файл картинки не задан корректно.';	

return $err;	
}




function taggallery_get_photo_data($filename = '')

{
    if (!file_exists($filename)) return false;
    
		$Toolkit_Dir = getinfo('plugins_dir') . 'taggallery/admin/functions/exif/'; // Ensure dir name includes trailing slash
		// Hide any unknown EXIF tags
		$GLOBALS['HIDE_UNKNOWN_TAGS'] = TRUE;

		include_once ( $Toolkit_Dir . 'Toolkit_Version.php' );          // Change: added as of version 1.11
		include_once ( $Toolkit_Dir . 'JPEG.php' );                     // Change: Allow this example file to be easily relocatable - as of version 1.11
		include_once ( $Toolkit_Dir . 'JFIF.php' );
		include_once ( $Toolkit_Dir . 'PictureInfo.php' );
		include_once ( $Toolkit_Dir . 'XMP.php' );
		include_once ( $Toolkit_Dir . 'Photoshop_IRB.php' );
		include_once ( $Toolkit_Dir . 'EXIF.php' );    
    
    $DateTimeOriginal = '';

		$exif = get_EXIF_JPEG( $filename );
		
		if ( isset($exif[0][34665]['Data'][0]) )
		{
			$exif = $exif[0][34665]['Data'][0];
			if (isset($exif[36867]['Text Value'])) $DateTimeOriginal = $exif[36867]['Text Value'];
    }
    
    return $DateTimeOriginal;
} 
?>