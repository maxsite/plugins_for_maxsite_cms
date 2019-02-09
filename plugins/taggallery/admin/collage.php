<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * MaxSite CMS
 * (c) http://max-3000.com/
 */

//***************************************************
	$CI = & get_instance();
	$CI->load->helper('form');
	$CI->load->helper('directory');
	
	
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
 $sort_orders = array(
       'asc' => 'По возрастанию',
       'desc' => 'По убыванию',
         );

    if ( $post = mso_check_post(array('f_session_id', 'f_collage_picture_count' , 'f_collage_picture_width' , 'f_collage_picture_height' , 'f_collage_angles', 'f_collage_borderx' , 'f_collage_bordery' , 'f_collage_stepx' ,'f_collage_dir' , 'f_collage_gallerys','f_collage_sort_field' , 'f_collage_sort_order' ,'f_collage_pointx' , 'f_collage_pointy', 'f_collage_limit' , 'f_save_submit')) ) //если нажата кнопка установить пользователя
    {
	    mso_checkreferer();
	    $options_admin['collage_picture_count'] = $post['f_collage_picture_count'];
	    $options_admin['collage_picture_width'] = $post['f_collage_picture_width'];
	    $options_admin['collage_picture_height'] = $post['f_collage_picture_height'];
	    $options_admin['collage_angles'] = $post['f_collage_angles'];
	    $options_admin['collage_dir'] = $post['f_collage_dir'];
	    
	    $options_admin['collage_pointx'] = $post['f_collage_pointx'];
	    $options_admin['collage_pointy'] = $post['f_collage_pointy'];
	    $options_admin['collage_borderx'] = $post['f_collage_borderx'];
	    $options_admin['collage_bordery'] = $post['f_collage_bordery'];	    
	    $options_admin['collage_stepx'] = $post['f_collage_stepx'];	
	    
	    $options_admin['collage_gallerys'] = $post['f_collage_gallerys'];
	    $options_admin['collage_sort_field'] = $post['f_collage_sort_field'];
	    $options_admin['collage_sort_order'] = $post['f_collage_sort_order'];
	        
	    $options_admin['collage_limit'] = $post['f_collage_limit'];

      mso_add_option('taggallery_admin', $options_admin, 'plugins');
      echo '<div class="update">Опции изменены.</div>';
    }
    
    
  if ( $post = mso_check_post(array('f_session_id', 'f_create_submit')) ) //если нажата кнопка сформировать картинки
  {
	    mso_checkreferer();
	    
	    //получим картинки
	    $pag=false;
	    $par = array();
      if ($options_admin['collage_sort_field']) $par['sort_field'] = $options_admin['collage_sort_field'];
      if ($options_admin['collage_sort_order']) $par['sort_order'] = $options_admin['collage_sort_order'];
      if ($options_admin['collage_gallerys']) $par['tags'] = mso_explode($options_admin['collage_gallerys'] , false, false);
      if ($options_admin['collage_limit']) $par['limit'] = $options_admin['collage_limit'];
      $par['source_id'] = 0;
	    $pictures = taggallery_get_pictures($par , $pag);
	    $picture_pages_url = array();
      $par = array();	    
	    $par['img_array'] = array();
	    if ($pictures)
	    {
	      echo '<div class="update">Найдено картинок ' . count($pictures) . '</div>';
	      $i=1;
	      foreach ($pictures as $picture)
	      {
	         if ($picture['picture_url']) $par['img_array'][$i] = $picture['picture_url'];
	         elseif($picture['picture_file']) $par['img_array'][$i] = $uploads_dir . $picture['picture_dir'] . $picture['picture_file'];
	         else continue;
	         $picture_pages_url[$i] = $options['picture_slug'] . '/' . $options['picture_prefix'] . $picture['picture_slug'];
	         $i++;
	      }
	    }
	    else echo '<div class="error">Нет картинок с таким критерием</div>';
	    
	    if ($par['img_array'])
	    {
	       echo '<div class="update">Подходящих картинок ' . count($pictures) . '</div>';
	    
         $par['source_dir'] = $options_admin['collage_dir']; // директорий в uploads, где картинки
         $par['width'] = $options_admin['collage_picture_width']; // ширина элемента коллажа
         $par['height'] = $options_admin['collage_picture_height']; // высота эллемента коллажа
         $par['angle'] = mso_explode($options_admin['collage_angles'] , false , true); // углы на которые будут повернуты элементы
         $par['borderx'] = $options_admin['collage_borderx']; // бордюр рамки за изображением по x
         $par['bordery'] = $options_admin['collage_bordery']; // бордюр рамки за изображением по y
      
         $res = create_header_collage_images($par);
         if (!$res) echo '<div class="error">Неудача создания набора изображений.</div>';
         else echo '<div class="update">Вероятно создано превьюшек ' . $res . '</div>';
         
         if ($res) mso_add_float_option('taggallery_picture_pages', $picture_pages_url , 'taggallery');
      }
	    elseif ($pictures) echo '<div class="error">Нет подходящих картинок</div>';
      
  }


  if ( $post = mso_check_post(array('f_session_id', 'f_collage_submit')) ) //если нажата кнопка сформировать коллаж
  {
	    mso_checkreferer();
	    
	    //получим картинки
	    $pag=false;
	    $par = array();
      if ($options_admin['collage_sort_field']) $par['sort_field'] = $options_admin['collage_sort_field'];
      if ($options_admin['collage_sort_order']) $par['sort_order'] = $options_admin['collage_sort_order'];
      if ($options_admin['collage_gallerys']) $par['tags'] = mso_explode($options_admin['collage_gallerys'] , false, false);
      if ($options_admin['collage_limit']) $par['limit'] = $options_admin['collage_limit'];
      
      $par['source_id'] = 0;
	    $pictures = taggallery_get_pictures($par , $pag);
	    
      $par = array();	    
	    $par['img_array'] = array();
	    if ($pictures)
	    {
	      echo '<div class="update">Найдено картинок ' . count($pictures) . '</div>';
	      foreach ($pictures as $picture)
	      {
	         if ($picture['picture_url']) $par['img_array'][] = $picture['picture_url'];
	         elseif($picture['picture_file']) $par['img_array'][] = $uploads_dir . $picture['picture_dir'] . $picture['picture_file'];
	         else continue;
	      }
	    }
	    else echo '<div class="error">Нет картинок с таким критерием</div>';
	    
	    if ($par['img_array'])
	    {
	       echo '<div class="update">Подходящих картинок ' . count($pictures) . '</div>';
	    
         $par['source_dir'] = $options_admin['collage_dir']; // директорий в uploads, где картинки
         $par['width'] = $options_admin['collage_picture_width']; // ширина элемента коллажа
         $par['height'] = $options_admin['collage_picture_height']; // высота эллемента коллажа
         $par['pointx'] = $options_admin['collage_pointx']; // начальная точка вывода элементов
         $par['pointy'] = $options_admin['collage_pointy']; // где будут выведены элементы по высоте
         $par['angle'] = mso_explode($options_admin['collage_angles'] , false , true); // углы на которые будут повернуты элементы
         $par['borderx'] = $options_admin['collage_borderx']; // бордюр рамки за изображением по x
         $par['bordery'] = $options_admin['collage_bordery']; // бордюр рамки за изображением по y
         $par['stepx'] = $options_admin['collage_stepx']; // шаг вывода элементов
         $par['count'] = $options_admin['collage_picture_count']; // кол-во эллементов
      
         $res = create_header_collage_images($par);
         if (!$res) echo '<div class="error">Неудача создания коллажа.</div>';
         else echo '<div class="update">Вероятно коллаж создан.</div>';
         
         create_header_collage($par);
      }
	    elseif ($pictures) echo '<div class="error">Нет подходящих картинок</div>';
      
  }
   
   
//*******************************************************
		$form = '<tr><td><H2>Параметры создания файлов картинок</H2></td></tr>';
		$form .= '<tr><td>' . t('Директорий в upoads') . '</td><td><input type="text" name="f_collage_dir" value="' . $options_admin['collage_dir'] . '"></td></tr>';
  
  
		$form .= '<tr><td><H2>Параметры получения картинок</H2></td></tr>';
		$form .= '<tr><td>' . t('Имена галерей через запятые<br />!Большие буквы в именах галерей учитываются<br/>Вы можете создать для коллажа служебную галлерею<br/>Имя служебной галереи начинается на "_"') . '</td><td><input type="text" name="f_collage_gallerys" value="' . $options_admin['collage_gallerys'] . '"></td></tr>';
	 $form .= '<tr><td>' . t('Поле сортировки', 'plugins') . ' </td><td>' . form_dropdown('f_collage_sort_field', $sort_fields , $options_admin['collage_sort_field']) . '</td></tr>';		
	 $form .= '<tr><td>' . t('Порядок сортировки', 'plugins') . ' </td><td>' . form_dropdown('f_collage_sort_order', $sort_orders , $options_admin['collage_sort_order']) . '</td></tr>';			
	
	
		$form .= '<tr><td><H2>Параметры создания набора миниатюр</H2></td></tr>';
		$form .= '<tr><td>' . t('Ширина') . '</td><td><input type="text" name="f_collage_picture_width" value="' . $options_admin['collage_picture_width'] . '"></td></tr>';
		$form .= '<tr><td>' . t('Высота') . '</td><td><input type="text" name="f_collage_picture_height" value="' . $options_admin['collage_picture_height'] . '"></td></tr>';		
		$form .= '<tr><td>' . t('Бордюр x') . '</td><td><input type="text" name="f_collage_borderx" value="' . $options_admin['collage_borderx'] . '"></td></tr>';
		$form .= '<tr><td>' . t('Бордюр y') . '</td><td><input type="text" name="f_collage_bordery" value="' . $options_admin['collage_bordery'] . '"></td></tr>';		
		$form .= '<tr><td>' . t('Углы') . '</td><td><input type="text" name="f_collage_angles" value="' . $options_admin['collage_angles'] . '"></td></tr>';
		$form .= '<tr><td>' . t('Лимит картинок') . '</td><td><input type="text" name="f_collage_limit" value="' . $options_admin['collage_limit'] . '"></td></tr>';
	
		
		$form .= '<tr><td><H2>Параметры создания коллажа</H2></td></tr>';
		$form .= '<tr><td>' . t('Кол-во для коллажа') . '</td><td><input type="text" name="f_collage_picture_count" value="' . $options_admin['collage_picture_count'] . '"></td></tr>';
		$form .= '<tr><td>' . t('Точка х') . '</td><td><input type="text" name="f_collage_pointx" value="' . $options_admin['collage_pointx'] . '"></td></tr>';
		$form .= '<tr><td>' . t('Точка y') . '</td><td><input type="text" name="f_collage_pointy" value="' . $options_admin['collage_pointy'] . '"></td></tr>';
		$form .= '<tr><td>' . t('Шаг') . '</td><td><input type="text" name="f_collage_stepx" value="' . $options_admin['collage_stepx'] . '"></td></tr>';
	   		
		
		$form .= '<tr><td><input type="submit" name="f_save_submit" value="' . t('Сохранить опции', 'plugins') . '"  /></td></tr>';
		
		$form .= '<tr><td><H2>На основе сохраненных опций вы можете:</H2></td></tr>';

		$form .= '<tr><td><input type="submit" name="f_create_submit" value="' . t('Создать набор изображений для последующего коллажа', 'plugins') . '"  /></td></tr>';
		$form .= '<tr><td><input type="submit" name="f_collage_submit" value="' . t('Создать изображение-коллаж для размещения в шапке', 'plugins') . '"  /></td></tr>';
		
		echo '<form action="" method="post">' . mso_form_session('f_session_id');
		echo '<table>';
        echo $form;
        echo '</table>';
		echo '</form>';

  echo '<H2>Текущие результаты</H2>';
  echo '<a href="' . $plugin_url . $options_admin['collage_dir'] . '/mini-rotate">' . 'Просмотреть итоговый набор .png изображений в /mini-rotate' .'</a>';
  $ramka = getinfo('uploads_url') . $options_admin['collage_dir'] . '/result.jpg';
	echo '<H3>Результат коллажа в файле resut.jpg:</H3>';
  echo '<img src = "' . $ramka . '"/>';
/////////////////////////////////////
//////////////////////////////////////
//////////////////////////////////////

//функции коллажа

// накладывает на рамку и поворачивает изображение
function rotate_img_collage($ramka , $img , $angle , $width , $height , $borderx , $bordery)
{
   $res = imagecreatetruecolor($width,$height);
   $trans = imagecolorat($res,0,0);
 
   imagecopyresized ($res,$ramka,
                  0,0,
                  0,0,
                  $width,$height,
                  imagesx($ramka),imagesy($ramka));
                  
  $trans = imagecolorat($res,0,0);
  imagecolortransparent($res,$trans);
  
 $trans = imagecolorat($res,0,0);
 imagecopyresized ($res,$img,
                  $borderx,$bordery,
                  0,0,
                  $width-2*$borderx,$height-2*$bordery,
                  imagesx($img),imagesy($img));
  
  $res = imagerotate($res, $angle ,-1);
  imagealphablending($res, true); 
  imagesavealpha($res, true);                  
   
  return $res;                
}


// создает изображения для коллажа и записывает их в директорий
function create_header_collage_images($par=array())
{
    $res_count = 0;

    if (!isset($par['img_array']) or !$par['img_array']) return false;
   
    if (!isset($par['source_dir'])) $par['source_dir'] = 'header_collage';
    if (!isset($par['width'])) $par['width'] = 150;
    if (!isset($par['height'])) $par['height'] = 113;
    if (!isset($par['pointx'])) $par['pointx'] = 40;
    if (!isset($par['pointy'])) $par['pointy'] = 30;
    if (!isset($par['angle'])) $par['angle'] = array(20,-15,25,10,20,-10,15,25,-15);;
    if (!isset($par['borderx'])) $par['borderx'] = 6;
    if (!isset($par['bordery'])) $par['bordery'] = 8;
    

	  $path = getinfo('uploads_dir') . $par['source_dir'] . '/';
	  
	  if (!is_dir($path . 'mini-rotate')) @mkdir($path . 'mini-rotate', 0777); 

	  
	  $header = $path . '/header.jpg';
	  $ramka = $path . '/ramka.png';
	  $result = $path . '/result.jpg';

    extract($par); 
	  
    //создадим превьюшки для переанных картинок
    $ramka = imagecreatefrompng($ramka); 
    $i=0;
	  foreach ($img_array as $key=>$image)
	  {
	     copy($image , $path . $key . '.jpg');
	     $img = imagecreatefromjpeg($image);
	     if (!isset($angle[$i])) $i=0;
       $res = rotate_img_collage($ramka , $img , $angle[$i] , $width , $height , $borderx , $bordery);	
       $image = str_replace(".jpg" , ".png" , $image);
       imagepng($res, $path . 'mini-rotate/' . $key . '.png'); 
       $i++;
       $res_count++;
    }	  
    
    return $res_count;   
}

	
// функция создает изображение с коллажом
function create_header_collage($par=array())
{
    if (!isset($par['source_dir'])) $par['source_dir'] = 'header_collage';
    if (!isset($par['width'])) $par['width'] = 150;
    if (!isset($par['height'])) $par['height'] = 113;
    if (!isset($par['pointx'])) $par['pointx'] = 40;
    if (!isset($par['pointy'])) $par['pointy'] = 30;
    if (!isset($par['angle'])) $par['angle'] = array(20,-15,25,10,20,-10,15,25,-15);;
    if (!isset($par['borderx'])) $par['borderx'] = 6;
    if (!isset($par['bordery'])) $par['bordery'] = 8;
    if (!isset($par['stepx'])) $par['stepx'] = 150;
    if (!isset($par['count'])) $par['count'] = 5;
    if (!isset($par['img_array'])) $par['img_array'] = false;

	  $header = getinfo('uploads_dir') . $par['source_dir'] . '/header.jpg';
	  $ramka = getinfo('uploads_dir') . $par['source_dir'] . '/ramka.png';
	  $result =  getinfo('uploads_dir') . $par['source_dir'] . '/result.jpg';

	  extract($par);
	  shuffle($img_array); 
	  if ($count<count($img_array)) $img_array = array_slice($img_array, 0, $count);
	  
	  taggallery_header_collage($img_array, $ramka, $header ,$result, $width, $height, $borderx, $bordery, $pointx, $pointy, $angle, $stepx);
}	  


// функция последовательно поворачивает и накладывает изображения на основу с шагом
function taggallery_header_collage($array_fn_img , $fn_ramka, $fn_header , $fn_result , $width, $height , $borderx , $bordery , $pointx , $pointy , $angle , $stepx)
{
  $ramka = imagecreatefrompng($fn_ramka); 
  $header = imagecreatefromjpeg($fn_header); 
 
  $i=0;
  foreach ($array_fn_img as $fn_img)
  {
     if (!isset($angle[$i])) $i=0;
     $img = imagecreatefromjpeg($fn_img); 
     $res = rotate_img_collage($ramka , $img , $angle[$i] , $width , $height , $borderx , $bordery);
  
     imagecopy ( 
            $header, 
            $res, 
            $pointx+$stepx*$i , $pointy, 
            0, 0, 
            imagesx($res),imagesy($res)
           );
      $i++;     
   }
   imagejpeg($header,$fn_result); 
}

?>