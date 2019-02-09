<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 
/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */
 
/* в файле Функции для работы с масивами данных (для плагина галереи картинок taggallery )

*/

// например, при включении плагина восстановим БД из uploads
function taggallery_start()
{
  $CI = & get_instance();
	$CI->load->helper('file'); // хелпер для работы с файлами
	$CI->load->helper('directory');
  
  //$taggallery_pictures = array();
  $uploads_dir = getinfo('uploads_dir');
  $uploads_url = getinfo('uploads_url');
  
  // построим массив картинок в uploads, которым присвоены метки
  $res = taggallery_recalc_subdir($uploads_dir, $uploads_url , '');
  return $res;
}

// Функция строит массив картинок, которые нужно добавить/модифицировать в галереи
// для построения используется файлы mso_descriptions.dat
// рекурентная для taggallery_start()
function taggallery_recalc_subdir($dir='' , $url='', $curdir='') 
{
  $allowed_ext = array('gif', 'jpg', 'jpeg', 'png');
  $all_dirs = directory_map($dir, true); 
  $pictures = array();

  $res = '';

 if ($all_dirs)
	foreach ($all_dirs as $d)  // для содержимого этого каталога
	{
	  $subdir = $dir.$d;
	  $suburl = $url.$d;
	  if (is_dir($subdir)) // это каталог?
      {
  		   if ($d != '_mso_float' and $d != 'mini' and $d != '_mso_i' and $d != 'smiles') //  и это не служебный каталог
	       {
           $res .= taggallery_recalc_subdir($subdir.'/' , $suburl.'/' , $curdir . $d . '/');  // переходим в него
         }
      }  
	  else //это файл - осуществляем подготовку и добавление в массив информации о файле
		{
			$ext = strtolower(str_replace('.', '', strrchr($d, '.'))); // расширение файла
			if ( in_array($ext, $allowed_ext) )  // не запрещенный ли тип файла?
			{	
				$fn_mso_descriptions = $dir . '_mso_i/_mso_descriptions.dat';
	     	if (file_exists( $fn_mso_descriptions )) 
		    {
		      	// массив данных: fn => описание 
			     $mso_descriptions = unserialize( read_file($fn_mso_descriptions) ); // получим из файла все описания
		    }
	    	else $mso_descriptions = array();
	    	
		   $desc = '';
		   $tags = '';
		   if (isset($mso_descriptions[$d]))
		   {
         if (strpos($mso_descriptions[$d],"|") !== false ) //есть ли разделитель в дополнении?
         {
            $opis = explode ("|" , $mso_descriptions[$d]);
            $desc = trim($opis[0]);
            $tags = trim($opis[1]);
         }
         else  
         {
            $desc = trim($mso_descriptions[$d]);
            $tags = '';
         }		
        }	    	
	        
	       // у картинки есть метки 
		     if ($tags)
		     {
		       // if (file_exists($dir . 'mini/' . $d)) $file_mini = $url . 'mini/' . $d; else $file_mini = false;  // существует миниатюра
		     
		       $file_arr = explode("." , $d);
           $file_key = str_replace("/","_",$curdir) . $file_arr[0] . '_' . $file_arr[1];
    
		       $picture = array(
		           'picture_slug' => $file_key, 
		           'picture_title' => $desc, 
		           'picture_dir'=>$curdir ,
		           'picture_file'=>$d ,
		           'picture_tags'=>mso_explode($tags, false, false) 
		                       );
		        // добавляем картинку
		        // модифицировать файлы mso_descriptions не надо             
           $res_add = taggallery_add_picture($picture , false);
           if ($res_add['errors']) $res .= $res_add['errors'];
        }		       

		  }  
		}
	} 
	
	return $res;
}

// функция добавляет или изменяет картинку в базу данных 
// где $pictures_add массив картинки
// $descriptions_file_mody - корректировать ли файлы mso_descriptions
//$razd - разделитель сообщений о результате
function taggallery_add_picture($picture = array(), $descriptions_file_mody = true , $razd = ' -> ')
{
	$CI = & get_instance();

  

  $errors = false; // были ли ошибки при добавлении
  $add_descs = array(); // массив информацмм о том как надо изменить файлы mso_descriptions.dat
  
  $picture_id =  false;  // номер найденной или добавленной картинки
  
  $flag_add = false;
  $flag_mody = false;
  $uploads_dir = getinfo('uploads_dir');
  $file_error = false; // флаг присутсвия на диске файла
  $file_full_path = false; // файл картинки
  $message = '';
  
	// если заданы параметры файла на диске 
	// проверим - есть ли файл  
	//имеем ввиду, что если картинка из стороннего источника, то в picture_file хранится id картинки в стороннем источнике
	if (isset($picture['picture_dir']) and isset($picture['picture_file']) and (!isset($picture['picture_source_id']) or !$picture['picture_source_id']) )
	{
	   $file_full_path = $uploads_dir . $picture['picture_dir'] . $picture['picture_file'];
     if (!file_exists($file_full_path)) $file_error = true;
  }
  
  // если прямо указан picture_id то мы хотим отредактировать данные существующей картинки
  if( isset($picture['picture_id']) and $picture['picture_id'] )
  {
	    // проверим наличие картинки
	    $CI->db->select('*');
	    $CI->db->where('picture_id', $picture['picture_id']);
	    if ($query = $CI->db->get('pictures'))
	    {
	      if ($query->num_rows() > 0) // если запись найдена
	      {
	        $picture_id = $picture['picture_id'];
          $flag_mody = true;
          $message .= 'Картинка id=' . $picture_id . ' есть в бд' . $razd;
	      }
	      // если нет картинки 
	      if (!$picture_id) $errors = 'Картинка с id=' . $picture['picture_id'] . ' не найдена.';
	    } 
	}


  // если указан picture_source, то картинка из другого источника
  // тогда в поле picture_dir будем хранить id картинки в рамках другого источника
  elseif(isset($picture['picture_source_id']) and $picture['picture_source_id'])
  {
        // проверим наличие источника
        $CI->db->select('*');
	      $CI->db->where('source_id', $picture['picture_source_id']);
	      $query = $CI->db->get('source');
	      if ($query->num_rows()) // если источник есть
	      {
	         // проверим наличие картинки
	         if(!isset( $picture['picture_dir'])) $picture['picture_dir'] = '';
	         $CI->db->select('*');
	         $CI->db->where('picture_source_id', $picture['picture_source_id']);
	         $CI->db->where('picture_file', $picture['picture_file']);
	         $query = $CI->db->get('pictures');
	         if ($query->num_rows()>0) // если файл картинки найден, то изменяем
	         {
	            $row = $query->row_array(1);
	            $picture_id = $row['picture_id'];	
	            $flag_mody = true;
              $message .= 'Картинка ' . $picture['picture_file'] . ' есть в бд' . $razd;
	         }
	         else // если нет такого файла картинки, то добавляем
	         {
	           $flag_add = true;
             $message .= 'Картинки ' . $picture['picture_file'] . ' нет в бд' . $razd;	             
	         }
	      }   
	      else $errors .= 'Ошибка id источника: ' . $picture['picture_source_id']  . $razd;   
	  }

  // если картинка из uploads, то должны быть dir и file
  // dir может быть пустой, file-нет
  
  elseif (isset($picture['picture_dir']) and isset($picture['picture_file']) and $picture['picture_file'])
  {
	   // проверим наличие такой картинки в БД
	   $CI->db->select('*');
	   $CI->db->where('picture_dir', $picture['picture_dir']);
	   $CI->db->where('picture_file', $picture['picture_file']);
	   if ($query = $CI->db->get('pictures'))
	   {
	      if ($query->num_rows() > 0) 
	      {
	        $row = $query->row_array(1);
	        $picture_id = $row['picture_id'];	
	        $flag_mody = true;
          $message .= 'Картинка ' . $file_full_path . ' есть в бд' . $razd;
	      }  
	   }
	   // если файл картинки найден, то изменяем
	   if (!$flag_mody)
	   {
	      $flag_add = true;
        $message .= 'Картинки ' . $file_full_path . ' нет в бд' . $razd;
	   }  
	}  

	   
	 // теперь подготовим данные в зависимости от ситуации
	   
	 // если нужно модифицировать данные существующей в БД картинки
	 if ($flag_mody)
	 {
	      $upd_data = array();

        if (isset($picture['picture_title'])) $upd_data['picture_title'] = $picture['picture_title'];
        if (isset($picture['picture_slug'])) $upd_data['picture_slug'] = $picture['picture_slug'];
        if (isset($picture['picture_desc'])) $upd_data['picture_desc'] = $picture['picture_desc'];
        if (isset($picture['picture_exif'])) $upd_data['picture_exif'] = $picture['picture_exif'];
        if (isset($picture['picture_source_id'])) $upd_data['picture_source_id'] = $picture['picture_source_id'];
        
        if (isset($picture['picture_dir'])) $upd_data['picture_dir'] = $picture['picture_dir'];
        if (isset($picture['picture_file'])) $upd_data['picture_file'] = $picture['picture_file'];
        
        if (isset($picture['picture_full_size_url'])) $upd_data['picture_full_size_url'] = $picture['picture_full_size_url'];
        if (isset($picture['picture_url'])) $upd_data['picture_url'] = $picture['picture_url'];
        if (isset($picture['picture_mini_url'])) $upd_data['picture_mini_url'] = $picture['picture_mini_url'];
        if (isset($picture['picture_date'])) $upd_data['picture_date'] = $picture['picture_date'];
        if (isset($picture['picture_date_photo'])) $upd_data['picture_date_photo'] = $picture['picture_date_photo'];
        if (isset($picture['picture_date_file'])) $upd_data['picture_date_file'] = $picture['picture_date_file'];
        if (isset($picture['picture_view_count'])) $upd_data['picture_view_count'] = $picture['picture_view_count'];
        if (isset($picture['picture_rate_plus'])) $upd_data['picture_rate_plus'] = $picture['picture_rate_plus'];
        if (isset($picture['picture_rate_minus'])) $upd_data['picture_rate_minus'] = $picture['picture_rate_minus'];
        if (isset($picture['picture_rate_count'])) $upd_data['picture_rate_count'] = $picture['picture_rate_count'];
        if (isset($picture['picture_width'])) $upd_data['picture_width'] = $picture['picture_width'];
        if (isset($picture['picture_height'])) $upd_data['picture_height'] = $picture['picture_height'];
        
        if (isset($upd_data['picture_width']) and isset($upd_data['picture_width']) )
        {
           if ($upd_data['picture_width'] > $upd_data['picture_height']) $picture_position = 1;
	  	     elseif ($upd_data['picture_width'] < $upd_data['picture_height']) $picture_position = 2;
		       else $picture_position = 3;
		       $upd_data['picture_position'] = $picture_position;
        }

        if (isset($picture['picture_position'])) $upd_data['picture_position'] = $picture['picture_position'];
        
        if (isset($picture['picture_content'])) $upd_data['picture_content'] = $picture['picture_content'];

	      $message .= 'Изменяем' . $razd;
	      $CI->db->where('picture_id', $picture_id);
 			  $res = ($CI->db->update('pictures', $upd_data)) ? '1' : '0';
			  if (!$res) $errors .= 'Ошибка изменения картинки id=' . $picture_id . $razd; 
			  else $message .= 'Изменено' . $razd;
	 }
	 elseif ($flag_add) // если нужно файл добавить
	 {
	    // подготовим данные
	    $ins_data = array();
	        
	    // слуг должен быть и должен быть уникальным
      if (!isset($picture['picture_slug']) or !$picture['picture_slug'])
      {
         if (isset($picture['picture_dir']) and isset($picture['picture_file']) and $picture['picture_file'])
         {
            // создадим слуг из dir и file
		        $file_arr = explode("." , $picture['picture_file']);
            $picture['picture_slug'] = str_replace("/","_",$picture['picture_dir']) . $file_arr[0] . '_' . $file_arr[1]; 
         }  
         else $picture['picture_slug'] = '_TMP_SLG_'; // позже сделаем picture_slug = picture_id
      }
          
      if ($picture['picture_slug'])
      {
         // проверим слуг на уникальность - а вдруг...
	       $CI->db->select('*');
	       $CI->db->where('picture_slug', $picture['picture_slug']);
	       if ($query = $CI->db->get('pictures') )         
             if ($query->num_rows() > 0 ) // если есть такой слуг то
             {
               $row = $query->row_array(1);
               if ($row['picture_slug'] = '_TMP_SLG_')
               {
                 // что-то совсем плохо
                 $errors .= 'Слуг картинки "_TMP_SLG_" уже есть в БД' . $razd;
                 $flag_add = false;
                 $falag_mody = false;
               }
               $picture['picture_slug'] = '_TMP_SLG_';
             }
       } 
       
       // на всякий случай
       if (!$picture['picture_slug']) $picture['picture_slug'] = '_TMP_SLG_';
       
        
       // теперь данные, по файлу
       // если имеется такой файл
       if ($file_full_path and !$file_error)
       {
          // разберемся с датами
          // дата файла
          
          $ins_data['picture_date_file'] = date ("Y-m-d",filemtime($file_full_path));
          // дата снимка
          $picture_date_photo = taggallery_get_photo_date($file_full_path);
          
          
          $ins_data['picture_date_photo'] = $picture_date_photo ? $picture_date_photo : '';
                 
                 
          //определим ориентацию картинки в пространстве
          $image_info = GetImageSize($file_full_path);
		      $picture_width = $image_info[0];
		      $picture_height = $image_info[1]; 
  		    if ($picture_width > $picture_height) $picture_position = 1;
	  	    elseif ($picture_width < $picture_height) $picture_position = 2;
		      else $picture_position = 3;
          
          $ins_data['picture_exif'] = ''; // будет позже
          $ins_data['picture_width'] = $picture_width;
          $ins_data['picture_height'] = $picture_height;
          $ins_data['picture_position'] = $picture_position;
       }
       // если файла нет
       // и данные картинки не переданные - делаем их нулевыми
       else
       {
          $ins_data['picture_width'] = (isset($picture['picture_width'])) ? $picture['picture_width'] : 0;
          $ins_data['picture_height'] = (isset($picture['picture_height'])) ? $picture['picture_height'] : 0;
          
          if ($ins_data['picture_width'] > $ins_data['picture_height']) $picture_position = 1;
	  	    elseif ($ins_data['picture_width'] < $ins_data['picture_height']) $picture_position = 2;
		      else $picture_position = 3;
          $ins_data['picture_position'] = (isset($picture['picture_position'])) ? $picture['picture_position'] : $picture_position;
          
          $ins_data['picture_exif'] = (isset($picture['picture_exif'])) ? $picture['picture_exif'] : '';
          $ins_data['picture_date_photo'] = (isset($picture['picture_date_photo'])) ? $picture['picture_date_photo'] : date('Y-m-d');
          $ins_data['picture_date_file'] = (isset($picture['picture_date_file'])) ? $picture['picture_date_file'] : date('Y-m-d');        
       }  

        
       // не получаемые из файла данные
        $ins_data['picture_slug'] = (isset($picture['picture_slug'])) ? $picture['picture_slug'] : '';
        
        $ins_data['picture_dir'] = (isset($picture['picture_dir'])) ? $picture['picture_dir'] : '';
        $ins_data['picture_file'] = (isset($picture['picture_file'])) ? $picture['picture_file'] : '';
        $ins_data['picture_content'] = (isset($picture['picture_content'])) ? $picture['picture_content'] : '';

        // дата добавления
        $ins_data['picture_date'] = (isset($picture['picture_date'])) ? $picture['picture_date'] : date('Y-m-d');
        
        $ins_data['picture_title'] = (isset($picture['picture_title'])) ? $picture['picture_title'] : '';
        $ins_data['picture_desc'] = (isset($picture['picture_desc'])) ? $picture['picture_desc'] : '';
        $ins_data['picture_source_id'] = (isset($picture['picture_source_id'])) ? $picture['picture_source_id'] : 0;
        
          $ins_data['picture_full_size_url'] = (isset($picture['picture_full_size_url'])) ? $picture['picture_full_size_url'] : '';
          $ins_data['picture_url'] = (isset($picture['picture_url'])) ? $picture['picture_url'] : '';
          $ins_data['picture_mini_url'] = (isset($picture['picture_mini_url'])) ? $picture['picture_mini_url'] : '';
          $ins_data['picture_content'] = (isset($picture['picture_content'])) ? $picture['picture_content'] : '';

          $ins_data['picture_view_count'] = (isset($picture['picture_view_count'])) ? $picture['picture_view_count'] : 0;
          $ins_data['picture_rate_plus'] = (isset($picture['picture_rate_plus'])) ? $picture['picture_rate_plus'] : 0;
          $ins_data['picture_rate_minus'] = (isset($picture['picture_rate_minus'])) ? $picture['picture_rate_minus'] : 0;
          $ins_data['picture_rate_count'] = (isset($picture['picture_rate_count'])) ? $picture['picture_rate_count'] : 0;

		      // вставим данные
		      $message .= 'Добавляем' . $razd;
				  $res = ($CI->db->insert('pictures', $ins_data)) ? '1' : '0';
		      if ($res)
		      {
		         $message .= 'Добавлено' . $razd;
		         $picture_id = $CI->db->insert_id();	
		         if (($picture['picture_slug'] == '_TMP_SLG_') and $picture_id) 
		         {
		           // заменим временный слуг на id 
               $upd_data = array('picture_slug' => $picture_id);
	             $CI->db->where('picture_id', $picture_id);
 			         $res = ($CI->db->update('pictures', $upd_data)) ? '1' : '0';
			         if (!$res) $message .= 'Не удалось восстановить слуг кртинки id=' . $picture_id . $razd; 
		         }
		      }  
		      else
		      {
		         $picture_id = false;	
		         $errors .= 'Ошибка добавления новой картинки' . $razd;    		      
		      } 
	      }
	      
    
    // если картинка не добавлена, пропускаем
    if (!$picture_id) $errors .= 'Картинка не добавлена' . $razd;  
    elseif (isset($picture['picture_tags']))
    { 
       // если переданы новые метки обнулим привязку картинки к галерее
       // если метки не переданы, то оставляем как было
      $err = taggallery_delete_picture_from_all_gallerys(array('picture_id' => $picture_id));
      if ($err) $errors .= $err . $razd;

      // а теперь добавим картинку в нужные галереи
      foreach ($picture['picture_tags'] as $key => $tag)
      {
				$gallery_id = taggallery_add_gallery(array('gallery_name' => $tag));
				if (!$gallery_id) $errors .= 'Галерея "' . $tag . '" не добавлена.' . $razd;
        else // добавим текущую картику в галерею
        {
           $err .= taggallery_add_picture_in_gallery($picture_id , $gallery_id);
           if ($err) $errors .= $err . $razd;
        }   
      }
    }  
   
  // измененим описания в mso_descriptiond.dat файлах 
    if ($picture_id and $descriptions_file_mody)
         taggallery_descriptions_file_mody(array($picture_id)); 

  
  return (array('messages' => $message , 'errors' => $errors , 'id' => $picture_id ));
}



// если галерея есть - возвращается ее номер
// иначе создается и возвращается ее номер
function taggallery_add_gallery($par = array())
{
	 $CI = & get_instance();

   if (!isset($par['gallery_name']) or !trim($par['gallery_name']) ) return false;
   
   $par['gallery_name'] = trim($par['gallery_name']);
   
   $tag_slug = mso_slug($par['gallery_name']);
   $gallery_id = false;

    // проверим наличие такой галереи и если нет - создадим
	 $CI->db->select('*');
	 $CI->db->where('gallery_name', $par['gallery_name']);
	 if ($query = $CI->db->get('gallerys'))  
	  if ($query->num_rows() >0) // если есть такая галерея
	  {
	    // модифицируем, если нужно
	    $row = $query->row_array(1);
	    $gallery_id = $row['gallery_id'];
	    $upd_data = array();
			if (isset($par['gallery_title']))	$upd_data['gallery_title'] = $par['gallery_title'];
			if (isset($par['gallery_slug']))	$upd_data['gallery_slug'] = $par['gallery_slug'];
			if (isset($par['gallery_desc']))	$upd_data['gallery_desc'] = $par['gallery_desc'];
			if (isset($par['gallery_thumb_id']))	$upd_data['gallery_thumb_id'] = $par['gallery_thumb_id'];
			if (isset($par['gallery_date']))	$upd_data['gallery_date'] = $par['gallery_date'];
			if (isset($par['gallery_content']))	$upd_data['gallery_content'] = $par['gallery_content'];
			// если переданы какие либо параметры для изменения
			if($upd_data) 
			{
	      $CI->db->where('gallery_id', $gallery_id);
			  $res = ($CI->db->update('gallerys', $upd_data)) ? '1' : '0';
			}  
	  }
	  
	 if (!$gallery_id) // добавим такую галерею
	 {     
		  $ins_data = array();	
      $ins_data['gallery_name'] = isset($par['gallery_name']) ? $par['gallery_name'] : '';
      $ins_data['gallery_title'] = isset($par['gallery_title']) ? $par['gallery_title'] : $ins_data['gallery_name'];
      $ins_data['gallery_slug'] = isset($par['gallery_slug']) ? $par['gallery_slug'] : $tag_slug;
      $ins_data['gallery_desc'] = isset($par['gallery_desc']) ? $par['gallery_desc'] : '';
      $ins_data['gallery_thumb_id'] = isset($par['gallery_thumb_id']) ? $par['gallery_thumb_id'] : 0;
      $ins_data['gallery_date'] = isset($par['gallery_date']) ? $par['gallery_date'] : date('Y-m-d');
      $ins_data['gallery_content'] = isset($par['gallery_content']) ? $par['gallery_content'] : '';
			$res = ($CI->db->insert('gallerys', $ins_data)) ? '1' : '0';
		  if ($res) $gallery_id = $CI->db->insert_id();	
		  else $gallery_id = false;
   }
   
   return $gallery_id;		   
}




function taggallery_add_picture_in_gallery($picture_id = 0, $gallery_id = 0)
{
	 $CI = & get_instance();

   $err = false;
   
   if (!$picture_id ) return 'Нет picture_id.';
   if (!$gallery_id ) return 'Нет gallery_id.';
   
    // проверим наличие такой галереи
	 $CI->db->select('gallery_id , gallery_thumb_id');
	 $CI->db->where('gallery_id', $gallery_id);
	 if ($query = $CI->db->get('gallerys')) 
	 {
	   if ($query->num_rows() > 0) // если есть такая галерея
	   {
	     $result_gallerys = $query->result_array();
	     $gallery_thumb_id = $result_gallerys[0]['gallery_thumb_id'];
       // проверим наличие такой картинки
	     $CI->db->select('*');
	     $CI->db->where('picture_id', $picture_id);
	     if ($query = $CI->db->get('pictures'))
	     {   
	       if ($query->num_rows() >0) // если есть такая картинка
	       {
				   $ins_data = array(
				     	'picgal_picture_id' => $picture_id , 
				     	'picgal_gallery_id' => $gallery_id
				     	);
				   $res = ($CI->db->insert('picgal', $ins_data)) ? '1' : '0';
				   if ($res)
				   {
				     //если у галереи нет обложки, селаем добавляемую сейчас картинку обложкой
				     if (!$gallery_thumb_id) 
				     {
				        $upd_data = array('gallery_thumb_id' => $picture_id);	
				        $CI->db->where('gallery_id', $gallery_id);
				        $res = ($CI->db->update('gallerys', $upd_data)) ? '1' : '0';
				     }
				   }
				   else $err = 'Не удалось добавить картинку в галерею.';

	       }	 
	       else $err = 'Картинка не найдена.';
	     }
	     else $err = 'Картинка не найдена.';
	   }
	   else $err = 'Галерея не найдена.'; 
	 } 
	 else $err = 'Галерея не найдена.'; 
	 
	 if ($picture_id) taggallery_descriptions_file_mody(array($picture_id));
	 return $err; 
}



// нужно удалить картинку из галереи
function taggallery_delete_picture_from_gallery($par = array())
{
	 $CI = & get_instance();
   $gallery_id = 0;
   $picture_id = 0;
   
  // какая галерея?
	if (isset($par['gallery_id']))
	{
    // проверим наличие такой галереи
	  $CI->db->select('gallery_id');
	  $CI->db->where('gallery_id', $par['gallery_id']);
	  if ($query = $CI->db->get('gallerys'))   
	   if ($query->num_rows() >0 ) // если есть такая галерея
	   {  	
	     $row = $query->row_array(1);
	     $gallery_id = $row['gallery_id'];	
     }
     
    if (!$gallery_id) return 'Нет галереи с номером: ' . $par['gallery_id'] . '.';
  }
	elseif (isset($par['gallery_slug']))
	{
    // проверим наличие такой галереи
	  $CI->db->select('gallery_id');
	  $CI->db->where('gallery_slug', $par['gallery_slug']);
	  if ($query = $CI->db->get('gallerys'))   
	   if ($query->num_rows()) // если есть такая галерея
	   {  	
	     $row = $query->row_array(1);
	     $gallery_id = $row['gallery_id'];	
     }
     
    if (!$gallery_id) return 'Нет галереи со слугом: ' . $par['gallery_slug'] . '.';
  }  
  else return 'Не указана галерея.'; // не указана галерея


  // какая картинка?
	if (isset($par['picture_id']))
	{
    // проверим наличие такой картинки
	  $CI->db->select('picture_id');
	  $CI->db->where('picture_id', $par['picture_id']);
	  if ($query = $CI->db->get('pictures'))   
	   if ($query->num_rows()) 
	   {  	
	     $row = $query->row_array(1);
	     $picture_id = $row['picture_id'];	
     }
    
    if (!$picture_id) return 'Нет картинки с номером: ' . $par['picture_id'] . '.';
  }
	elseif (isset($par['picture_slug']))
	{
    // проверим наличие такой картинки
	  $CI->db->select('picture_id');
	  $CI->db->where('picture_slug', $par['picture_slug']);
	  if ($query = $CI->db->get('pictures'))   
	   if ($query->num_rows()) 
	   {  	
	     $row = $query->row_array(1);
	     $picture_id = $row['picture_id'];	
     }
    
    if (!$picture_id) return 'Нет картинки со слугом: ' . $par['picture_slug'] . '.';
  }
  else return 'Не указана картинка.'; 
  
	$CI->db->where('picgal_gallery_id', $gallery_id);
	$CI->db->where('picgal_picture_id', $picture_id);
  $res = ($CI->db->delete('picgal')) ? '1' : '0';	
  
  // обнуляем обложку галереи
  if ($res) taggallery_generate_gallery_thumb(array('gallery_id' => $gallery_id));
  
  //корректируем mso_descriptions.dat файлы
  if ($picture_id) taggallery_descriptions_file_mody(array($picture_id)); // маслом кашу не испортить?
  
  if ($res) return false; // нет ошибки
  else return 'Ошибка удаления картинки id="' . $picture_id . '" из галереи id="' . $picture_id . '"';
}




// функция отвечает за изменение файлов описаний картинок mso_descriptions_dat 
// функция предназначена для вызова из taggallery_add_pictures
// $pictures_id = массив номеров картинок которые надопроверить
function taggallery_descriptions_file_mody($pictures_id_array = array())
{
	 $CI = & get_instance();
	 $CI->load->helper('file'); // хелпер для работы с файлами

   $pag = false;
	 $pictures = taggallery_get_pictures(array('pictures_id' => $pictures_id_array) , $pag);
	 
  
   if ($pictures)
   {
     $uploads_dir = getinfo('uploads_dir');
     foreach ($pictures as $picture)
     {
       if ($picture['picture_source_id']) continue;


       $file_path = $uploads_dir . $picture['picture_dir'] . '/' . $picture['picture_file'];
       
			 $fn_mso_descriptions = $uploads_dir . $picture['picture_dir'] . '/_mso_i/_mso_descriptions.dat';
	     if (file_exists( $fn_mso_descriptions )) 
		   {
		      // массив данных: fn => описание 
			    $mso_descriptions = unserialize( read_file($fn_mso_descriptions) ); // получим из файла все описания
		   }
	     else $mso_descriptions = array();       
       
       if (file_exists($file_path))
       {
         //получим все галереи, где картинка
         $tags = '';

           foreach ($picture['tags'] as $tag)
           {
             if ($tags) $tags .= ', ' . $tag;
             else $tags = $tag;
           }
           
         if ($tags) $description = $picture['picture_title'] . ' | ' . $tags;
         else $description = $picture['picture_title']; 

	    	 $mso_descriptions[$picture['picture_file']] = $description;
       }
       else
       {
         if (isset($mso_descriptions[$picture['picture_file']])) unset ($mso_descriptions[$picture['picture_file']]);
       }
	     write_file($fn_mso_descriptions, serialize($mso_descriptions)); // записываем в него массив	 
     } //foreach (pictures as picture)
   }

   return true;
   // теперь бы поудалять инфу о картинках, которых нет в БД
   // но не будем пока
   
}


// удаляет картинку из базы данных
function taggallery_delete_picture($par = array())
{
	$CI = & get_instance();

  $picture_id = 0;
  $err = '';
  // какая картинка?
	if (isset($par['picture_id']))
	{
    // проверим наличие такой картинки
	  $CI->db->select('picture_id');
	  $CI->db->where('picture_id', $par['picture_id']);
	  if($query = $CI->db->get('pictures'))   
	   if ($query->num_rows()) 
	   {  	
	     $row = $query->row_array(1);
	     $picture_id = $row['picture_id'];	
     }
     
     if (!$picture_id) return 'Нет картинки с номером: ' . $par['picture_id'] . '.';
  }
	elseif (isset($par['picture_slug']))
	{
    // проверим наличие такой картинки
	  $CI->db->select('picture_id');
	  $CI->db->where('picture_slug', $par['picture_slug']);
	  $query = $CI->db->get('pictures');   
	  if ($query->num_rows()) 
	  {  	
	    $row = $query->row_array(1);
	    $picture_id = $row['picture_id'];	
    }
    
    if (!$picture_id) return 'Нет картинки со слугом: ' . $par['picture_slug'] . '.';
  }  
  else return 'Не указана картинка.'; 
  
  
  $err = taggallery_delete_picture_from_all_gallerys(array('picture_id' => $picture_id));
  if (!$err)	
  {
    // теперь удалим картинку
    $CI->db->where('picture_id', $picture_id);
    $res = ($CI->db->delete('pictures')) ? '1' : '0';
    if (!$res) $err = 'Ошибка удаления картинки из БД.';
    else $err = false;
  }
  
	if ($picture_id) taggallery_descriptions_file_mody(array($picture_id)); // на всякий случай

  return $err;  
}





function taggallery_delete_picture_from_all_gallerys($par = array())
{
	 $CI = & get_instance();

  $err = '';
  $picture_id = false;
  
  // какая картинка?
	if (isset($par['picture_id']))
	{
    // проверим наличие такой картинки
	  $CI->db->select('picture_id');
	  $CI->db->where('picture_id', $par['picture_id']);
	  if ($query = $CI->db->get('pictures'))   
	    if ($query->num_rows()) 
	    {  	
	      $row = $query->row_array(1);
	      $picture_id = $row['picture_id'];	
      }
      
    if (!$picture_id) return 'Нет картинки с номером: ' . $par['picture_id'] . '.';
  }
	elseif (isset($par['picture_slug']))
	{
    // проверим наличие такой картинки
	  $CI->db->select('picture_id');
	  $CI->db->where('picture_slug', $par['picture_slug']);
	  if ($query = $CI->db->get('pictures'))  
	    if ($query->num_rows()) 
	    {  	
	      $row = $query->row_array(1);
	      $picture_id = $row['picture_id'];	
      }
    
    if (!$picture_id) return  'Нет картинки со слугом: ' . $par['picture_slug'] . '.';
  }  
  else return 'Не указана картинка.'; 
  
  // сперва удалим картинку из всех галерей
  $CI->db->where('picgal_picture_id', $picture_id);
  $res = ($CI->db->delete('picgal')) ? '1' : '0';
  if (!$res) $err = 'Ошибка удаления картинки ' . $picture_id . ' из всех галерей.';	
  
  // теперь сбросим обложку галереи, если эта картинка есть обложка в галерее
	$CI->db->select('gallery_id');
	$CI->db->where('gallery_thumb_id', $picture_id);
	if ($query = $CI->db->get('gallerys') )
	  // получили галереи, где эта картинка обложка 
	  if ($query->num_rows()) 
	  { 
	     $gallerys = $query->result_array();
	     foreach ($gallerys as $gallery)
	     {
	       // установм обложкой самую новую картинку
	       taggallery_generate_gallery_thumb(array('gallery_id' => $gallery['gallery_id']));
	     }	
	  }   
  
  
    //корректируем mso_descriptions.dat файлы
  if ($picture_id) taggallery_descriptions_file_mody(array($picture_id)); 
  
  return $err;
}


function taggallery_delete_gallery($par = array())
{
	$CI = & get_instance();
  $gallery_id = false;
  $err = false;

  // какая галерея?
	if (isset($par['gallery_id']))
	{
    // проверим наличие такой галереи
	  $CI->db->select('gallery_id');
	  $CI->db->from('gallerys');
	  $CI->db->where('gallery_id', $par['gallery_id']);
	  if ($query = $CI->db->get() )
	    if ($query->num_rows()) // если есть такая галерея
	    {  	
	      $row = $query->row_array(1);
	      $gallery_id = $row['gallery_id'];	
      }
      
    if (!$gallery_id) return 'Нет галереи с номером: ' . $par['gallery_id'] . '.';
  }
	elseif (isset($par['gallery_slug']))
	{
    // проверим наличие такой галереи
	  $CI->db->select('gallery_id');
	  $CI->db->from('gallerys');
	  $CI->db->where('gallery_slug', $par['gallery_slug']);
	  if ($query = $CI->db->get() )   
	    if ($query->num_rows()) // если есть такая галерея
	    {  	
	      $row = $query->row_array(1);
	      $gallery_id = $row['gallery_id'];	
      }
      
    if (!$gallery_id) return 'Нет галереи со слугом: ' . $par['gallery_slug'] . '.';
  }  
  else return 'Не указана галерея.'; // не указана галерея
  
  // проерим, чтобы галерея была пуста.
	$CI->db->select('*');
	$CI->db->where('picgal_gallery_id', $gallery_id);
	if ($query = $CI->db->get('picgal'))   
	  if ($query->num_rows()) return 'Можно удалить только пустую галерею. Очистите галерею перед удалением.';  	 
  // else return 'Ошибка получения картинок в удаляемой галерее. Галерея не удалена.';
  
	// удалим эту галерею из альбомов
	$CI->db->where('galalb_gallery_id', $gallery_id);
  $res = ($CI->db->delete('galalb'))  ? '1' : '0';
  if(!$res) return 'Ошибка удаления галереи из альбомов.';	  
  
  // теперь удалим галерею
  $CI->db->where('gallery_id', $gallery_id);
  $res = ($CI->db->delete('gallerys')) ? '1' : '0';	
  if (!$res) return 'Ошибка удаления галереи.';	

	mso_flush_cache(); // сбросим кэш
	 
	return $err; 

}


function taggallery_empty_gallery($par = array())
{
	$CI = & get_instance();
  $gallery_id = false;
  $err = false;
  $array_of_id = array();// id картинок, которые удаляются из галереи
  
  // какая галерея?
	if (isset($par['gallery_id']))
	{
    // проверим наличие такой галереи
	  $CI->db->select('gallery_id');
	  $CI->db->from('gallerys');
	  $CI->db->where('gallery_id', $par['gallery_id']);
	  if ($query = $CI->db->get())   
	    if ($query->num_rows()) // если есть такая галерея
	    {  	
	      $row = $query->row_array(1);
	      $gallery_id = $row['gallery_id'];	
      }
      
    if (!$gallsey_id) return 'Нет галереи с номером: ' . $par['gallery_id'] . '.';
  }
	elseif (isset($par['gallery_slug']))
	{
    // проверим наличие такой галереи
	  $CI->db->select('gallery_id');
	  $CI->db->from('gallerys');
	  $CI->db->where('gallery_slug', $par['gallery_slug']);
	  if ($query = $CI->db->get())  
	    if ($query->num_rows()) // если есть такая галерея
	    {  	
	      $row = $query->row_array(1);
	      $gallery_id = $row['gallery_id'];	
      }
      
    if (!$gallery_id) return 'Нет галереи со слугом: ' . $par['gallery_slug'] . '.';
  }  
  else return 'Не указана галерея.'; // не указана галерея

  // создадим массив картинок, которые были в галерее
	$CI->db->select('picgal_picture_id');
	$CI->db->where('picgal_gallery_id', $gallery_id);
	if ($query = $CI->db->get('picgal'))   
	  if ($query->num_rows())
	  {
	    $result_array = $query->result_array();
	    foreach ($result_array as $val) $array_of_id[] = $val['picgal_picture_id'];
	  } 


  //обнулим обложку
  $CI->db->where('gallery_id', $gallery_id);
  $upd_data = array('gallery_thumb_id' => 0);
  $res = ($CI->db->update('gallerys', $upd_data)) ? '1' : '0';
  if (!$res) $err = 'Ошибка обнуления обложки<br />';
  
  $CI->db->where('picgal_gallery_id', $gallery_id);
  $res = ($CI->db->delete('picgal')) ? '1' : '0';	
  if ($res) $err = false;
  else $err .= 'Ошибка удаления всех картинок из галереи.';
  
    //корректируем mso_descriptions.dat файлы
  if ($array_of_id) taggallery_descriptions_file_mody($array_of_id);   
  
  return $err;
}




// установить каринку обложкой галереи
function taggallery_set_gallery_thumb($par = array())
{
	$CI = & get_instance();
  $err = false;
  $gallery_id = false;
  $picture_id = false;
  
  // какая галерея?
	if (isset($par['gallery_id']))
	{
    // проверим наличие такой галереи
	  $CI->db->select('gallery_id');
	  $CI->db->where('gallery_id', $par['gallery_id']);
	  if($query = $CI->db->get('gallerys'))
	    if ($query->num_rows()) // если есть такая галерея
	    {  	
	      $row = $query->row_array(1);
	      $gallery_id = $row['gallery_id'];	
      }
      
    if(!$gallery_id) return 'Нет галереи с номером: ' . $par['gallery_id'] . '.';
  }
	elseif (isset($par['gallery_slug']))
	{
    // проверим наличие такой галереи
	  $CI->db->select('gallery_id');
	  $CI->db->where('gallery_slug', $par['gallery_slug']);
	  if ($query = $CI->db->get('gallerys'))   
	    if ($query->num_rows()) // если есть такая галерея
	    {  	
	      $row = $query->row_array(1);
	      $gallery_id = $row['gallery_id'];	
      }
      
    if(!$gallery_id) return 'Нет галереи со слугом: ' . $par['gallery_slug'] . '.';
  }  
  else return 'Не указана галерея.'; // не указана галерея


  // какая картинка?
	if (isset($par['picture_id']))
	{
    // проверим наличие такой картинки
	  $CI->db->select('picture_id');
	  $CI->db->where('picture_id', $par['picture_id']);
	  if ($query = $CI->db->get('pictures'))   
	    if ($query->num_rows()) 
	    {  	
	      $row = $query->row_array(1);
	      $picture_id = $row['picture_id'];	
      }
      
    if(!$picture_id) return 'Нет картинки с номером: ' . $par['picture_id'] . '.';
  }
	elseif (isset($par['picture_slug']))
	{
    // проверим наличие такой картинки
	  $CI->db->select('picture_id');
	  $CI->db->from('pictures');
	  $CI->db->where('picture_slug', $par['picture_slug']);
	  if ($query = $CI->db->get())   
	    if ($query->num_rows()) 
	    {  	
	      $row = $query->row_array(1);
	      $picture_id = $row['picture_id'];	
      }
    
    if(!$picture_id) return 'Нет картинки со слугом: ' . $par['picture_slug'] . '.';
  }  
  else return 'Не указана картинка.'; 

  $CI->db->where('gallery_id', $gallery_id);
  $upd_data = array('gallery_thumb_id' => $picture_id);
  $res = ($CI->db->update('gallerys', $upd_data)) ? '1' : '0';
	if (!$res) return 'Ошибка установки обложки галреи.';

  return $err; 
}




// выбрать обложку галереи из картинок этой галереи по правилу
// берется первая картинка по sort_order и sort_asc
// можно указать: 
// $par['sort_order'] = 'picture_date' , 'picture_date_file' , 'picture_date_photo' , 'picture_rate'
// $par['sort_asc'] = 'asc','desc'
function taggallery_generate_gallery_thumb($par = array())
{
	$CI = & get_instance();

  $err = false;
  $gallery_id = false;
  $picture_id = false;


  $sort_fields = array('picture_date' , 'picture_date_file' , 'picture_date_photo' , 'picture_rate');
  if (!isset($par['sort_field']) or !in_array($par['sort_field'] , $sort_fields)) $par['sort_field'] = 'picture_date';
  if (!isset($par['sort_order']) or !in_array($par['sort_order'] , array('asc','desc'))) $par['sort_order'] = 'desc';
  
  // какая галерея?
	if (isset($par['gallery_id']))
	{
    // проверим наличие такой галереи
	  $CI->db->select('gallery_id');
	  $CI->db->where('gallery_id', $par['gallery_id']);
	  if($query = $CI->db->get('gallerys'))   
	    if ($query->num_rows()) // если есть такая галерея
	    {  	
	      $row = $query->row_array(1);
	      $gallery_id = $row['gallery_id'];	
      }
      
    if(!$gallery_id) return 'Нет галереи с номером: ' . $par['gallery_id'] . '.';
  }
	elseif (isset($par['gallery_slug']))
	{
    // проверим наличие такой галереи
	  $CI->db->select('gallery_id');
	  $CI->db->where('gallery_slug', $par['gallery_slug']);
	  if ($query = $CI->db->get('gallerys'))   
	    if ($query->num_rows()) // если есть такая галерея
	    {  	
	      $row = $query->row_array(1);
	      $gallery_id = $row['gallery_id'];	
      }
      
    if (!$gallery_id) return 'Нет галереи со слугом: ' . $par['gallery_slug'] . '.';
  }  
  else return 'Не указана галерея.'; // не указана галерея

  // какая картинка?
	$CI->db->select('picture_id');
	$CI->db->join('picgal', 'picgal.picgal_picture_id = pictures.picture_id');	
	$CI->db->where('picgal_gallery_id', $gallery_id);
	$CI->db->order_by($par['sort_field'] , $par['sort_order']);
	$CI->db->limit(1);
	if ($query = $CI->db->get('pictures') )  
	  if ($query->num_rows()) 
	  {  	
	     $row = $query->row_array(1);
	     $picture_id = $row['picture_id'];	
    }
  
  if (!$picture_id) return 'Ошибка получения картинки.';
  
  $CI->db->where('gallery_id', $gallery_id);
  $upd_data = array('gallery_thumb_id' => $picture_id);
  $res = ($CI->db->update('gallerys', $upd_data)) ? '1' : '0';
	if (!$res) return 'Ошибка установки обложки галреи.';

  return $err; 
}


function taggallery_add_album($par = array())
{
	 $CI = & get_instance();
	 
	 $err = ''; 
  
  if (!isset($par['album_slug'])) $par['album_slug'] = '';
  if (!isset($par['album_title'])) $par['album_title'] = '';
  if (!isset($par['album_desc'])) $par['album_desc'] = '';
  if (!isset($par['album_tags'])) $par['album_tags'] = false;
  if (!isset($par['album_thumb'])) $par['album_thumb'] = '';
  if (!isset($par['album_date'])) $par['album_date'] = date('Y-m-d');
  if (!isset($par['album_parent_id'])) $par['album_parent_id'] = 0;
  
	$ins_data = array(	
				 'album_slug' => $par['album_slug'],
				 'album_title' => $par['album_title'], 
				 'album_desc' => $par['album_desc'], 
				 'album_date' => $par['album_date'], 
				 'album_thumb' => $par['album_thumb'],
				 'album_parent_id' => $par['album_parent_id']	
			 );
			 
	$res = ($CI->db->insert('albums', $ins_data))	? '1' : '0';	 
	if ($res)
	{
	  $album_id = $CI->db->insert_id();	
	  
	  //если есть галереи в альбоме
	  if ($par['album_tags'])
	  {
	    foreach ($par['album_tags'] as $tag_name)
	    {
	      // определим номер галереи
	      $gallery_id = false;
	      $CI->db->select('gallery_id');
        $CI->db->where('gallery_name', $tag_name);
	      if ($query = $CI->db->get('gallerys'))  
	        if ($query->num_rows())
	        {
	           $gallery = $query->row_array(1);
	           $gallery_id = $gallery['gallery_id'];
	           // добавим галерею в альбом
	           $ins_data = array(	
				       'galalb_gallery_id' => $gallery_id,
				       'galalb_album_id' => $album_id);
				     $res = ($CI->db->insert('galalb', $ins_data)) ? '1' : '0';	    
	           if (!$res) $err .= ' Галерея ' . $tag_name . ' не добавлена в альбом.';
				  }
				  
				if (!$gallery_id) $err .= ' Галерея ' . $tag_name . ' не найдена. ';
	    }
	  }
	}
	else $err = 'Добавить новый альбом не получилось.';
	
	return $err;
}


function taggallery_edit_album($par = array())
{
	 $CI = & get_instance();

  $err = ''; 

  $upd_data = array();
  if (!isset($par['album_id'])) return 'Нет id альбома.';
  if (isset($par['album_slug'])) $upd_data['album_slug'] = $par['album_slug'];
  if (isset($par['album_title'])) $upd_data['album_title'] = $par['album_title'];
  if (isset($par['album_desc'])) $upd_data['album_desc'] = $par['album_desc'];
  if (isset($par['album_thumb'])) $upd_data['album_thumb'] = $par['album_thumb'];
  if (isset($par['album_date'])) $upd_data['album_date'] = $par['album_date'];
  if (isset($par['album_parent_id'])) $upd_data['album_parent_id'] = $par['album_parent_id'];

  // проверим - уникальный ли слуг
	$CI->db->select('album_id');
  $CI->db->where('album_slug', $upd_data['album_slug']);
  $CI->db->where_not_in('album_id', array($par['album_id']));
	if ($query = $CI->db->get('albums') )
	   if ($query->num_rows()) return 'Слуг ' . $upd_data['album_slug'] . ' не уникальный.';  

  // сперва найдем редактируемый альбом
	$CI->db->select('album_id , album_title');
  $CI->db->where('album_id', $par['album_id']);
	if ($query = $CI->db->get('albums'))
	{   
	   if (!($query->num_rows() >0)) return 'Нет такого альбома: Id=' . $par['album_id'];
	}   
	else return 'Ошибка получения редактируемого альбома:  Id=' . $par['album_id'];
		 
  $CI->db->where('album_id', $par['album_id']);
  $res = ($CI->db->update('albums', $upd_data)) ? '1' : '0';
  if (!$res) return 'Не удалось модифицировать альбом.';

  // добавим галереи в альбом
  if (isset($par['album_tags'])) 
  {
  
     // удалим все галереи из альбома
 			$CI->db->where('galalb_album_id', $par['album_id']);
			$CI->db->delete('galalb');    
     
     
     if ($par['album_tags'])
     foreach ($par['album_tags'] as $tag_name)
	   {
	      // определим номер галереи
	      $gallery_id = false;
	      $CI->db->select('gallery_id');
        $CI->db->where('gallery_name', $tag_name);
	      if ($query = $CI->db->get('gallerys'))  
	        if ($query->num_rows())
	        {
	           $gallery = $query->row_array(1);
	           $gallery_id = $gallery['gallery_id'];
	           // добавим галерею в альбом
	           $ins_data = array(	
				       'galalb_gallery_id' => $gallery_id,
				       'galalb_album_id' => $par['album_id']);
				     $res = ($CI->db->insert('galalb', $ins_data)) ? '1' : '0';	    
	           if (!$res) $err .= 'Галерея ' . $tag_name . ' не добавлена в альбом. ';
				  }
				 if (!$gallery_id) $err .= 'Галерея ' . $tag_name . ' не найдена. ';
				  
	   }     
  }
	
	return $err;
}


function taggallery_delete_album($par =array())
{
	 $CI = & get_instance();

  $err = false;
  $album_id = false;
  
  // какой альбом?
	if (isset($par['album_id']))
	{
    // проверим наличие такого альбома
	  $CI->db->select('album_id');
	  $CI->db->where('album_id', $par['album_id']);
	  if ($query = $CI->db->get('albums'))  
	    if ($query->num_rows()) // если есть
	    {  	
	      $row = $query->row_array(1);
	      $album_id = $row['album_id'];	
      }
    
    if (!$album_id) return 'Нет альбома с номером: ' . $par['album_id'] . '.';
  }
	elseif (isset($par['album_slug']))
	{
    // проверим наличие
	  $CI->db->select('album_id');
	  $CI->db->where('album_slug', $par['album_slug']);
	  if ($query = $CI->db->get('albums'))  
	    if ($query->num_rows()) // если есть такая галерея
	    {  	
	      $row = $query->row_array(1);
	      $album_id = $row['album_id'];	
      }
    
    if (!$album_id) return 'Нет альбома со слугом: ' . $par['album_slug'] . '.';
  }  
  else return 'Не указан альбом.';


	// удалим из этого альбома все галереи
	$CI->db->where('galalb_album_id', $album_id);
  $res = ($CI->db->delete('galalb')) ? '1' : '0';	
 // if (!$res) $err = 'Ошибка удаления галерей из альбома.';	  
  
  // теперь удалим альбом
  $CI->db->where('album_id', $album_id);
  $res = ($CI->db->delete('albums')) ? '1' : '0';	
  if (!$res) return 'Ошибка удаления альбома.';
	 
	return $err; 
}


function taggallery_get_photo_date($filename = '')

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
			if (isset($exif[36867]['Text Value'])) 
			{
			  $DateTimeOriginal = $exif[36867]['Text Value'];
			}  
    }
    
    //подготовим чтобы дата в SQL формате
   $part = explode(' ' , $DateTimeOriginal);

	 if (isset($part[0])) $part[0] = str_replace(":", "-", $part[0]);
	 
	 $DateTimeOriginal = implode (" " , $part);
    
   return $DateTimeOriginal;
} 

?>