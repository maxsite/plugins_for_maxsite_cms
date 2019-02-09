<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// обработчик аякс запросов для формы после редактора

	$return = array(
		'error_code' => 1,
		'error_description' => 'Неверные данные',
		'resp' => '0',
	);
	
	if ( $post = mso_check_post(array('type' , 'dir' , 's_t')) )
	{
	   if ($post['type'] == 'get_files')
	   {
	     global $CI;
	     $CI = & get_instance();
	     $CI->load->helper('file'); // хелпер для работы с файлами
	     $CI->load->helper('directory');	
       $CI->load->library('image_lib');
	          
	     $allowed_ext = array('jpg','jpeg','gif','png');
	     
	     $uploads_dir = getinfo('uploads_dir') . $post['dir'];
	     $uploads_url = getinfo('uploads_url') . $post['dir'];

	      $fn_mso_descritions = $uploads_dir . '_mso_i/_mso_descriptions.dat';

	      if (file_exists( $fn_mso_descritions )) 
		        $mso_descritions = unserialize( read_file($fn_mso_descritions) ); // получим из файла все описания
	      else $mso_descritions = array();
	
	     // все файлы в массиве $dirs
	     $dirs = @directory_map($uploads_dir, true); // только в текущем каталоге

      $files = array();
      if ($dirs) foreach ($dirs as $file)
      {
            if (@is_dir($uploads_dir . $file)) continue;
		        $ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		        if ( !in_array($ext, $allowed_ext) ) continue; // запрещенный тип файла 
		        
            $file_full_path = $uploads_dir . $file;
            $date_file = filemtime($file_full_path);
            
            $image_info = GetImageSize($file_full_path);
		        $width = $image_info[0];
		        $height = $image_info[1]; 
  	        if ($width > $height) $position = 1; // ladscape
	          elseif ($width < $height) $position = 2; // portail
		        else $position = 3;    // squire	  
	  
            $file_array = array();
            $file_array['file'] =  $file;
            $file_array['date'] =  $date_file;
            $file_array['width'] =  $width;
            $file_array['height'] =  $height;
            $file_array['position'] =  $position;
               
            $files[] = $file_array;
      }
          
       $sort_fn = 'files_sort_' . $post['s_t'];    
       if (function_exists($sort_fn)) uasort($files , $sort_fn);

	     $out = ''; // весь вывод

	    if ($files) foreach ($files as $cur)
	    {
	          $file = $cur['file'];

		       if (isset($mso_descritions[$file])) $title = $mso_descritions[$file]; else $title = '';

		       if (file_exists( $uploads_dir . 'mini/' . $file  )) $mini = 'mini/' . $file;
		       else $mini = $file;		     

		       if (file_exists( $uploads_dir . '_mso_i/' . $file  )) $_f = '_mso_i/' . $file;
		       else $_f = $mini;

           $predpr = $file . '#|#' . $mini . '#|#' . $_f . '#|#' . $title;
         
		      if ($out) $out .= '#,#' . $predpr;  
		      else $out .= $predpr;
	     }
		   
		   if ($out)
		   {
		     // эту часть нужно вынести в клиентскую часть______________
		     $arr = explode('#,#' , $out);
		     $out = '';
		     foreach ($arr as $key=>$img)
		     {
		       $key = $key+1; // чтобы не было id = 0
		       $in = explode('#|#' , $img);
		     
		       $cod = stripslashes(htmlspecialchars('<div class="gallery"><a href="' . $uploads_url . $in[0] . '" title="' . $in[3] . '"><img alt="' . $in[3] . '" src="' . $uploads_url . $in[1] . '"></a></div>'));
		     		     
		       $out .= '<div class="file_block"><a href="javascript:void(0)" onClick="addimg(' . $key. ') " title="' . $cod . '"><img alt="" src="' . $uploads_url . $in[2] . '"></a><input type="hidden" id="img_code' . $key .'" value="' . $cod . '"></div>';
  	  
  	      }

         $return['error_code'] = 0;
         $return['resp'] = $out;
       }
       else //if ($out)  
       {
         $return['error_code'] = 0;
         $return['resp'] = 'Пусто';       
       }
     }  
     
    
     
  }

	     	
	echo json_encode($return);	

function files_sort_1($a, $b) //date
{
	if ( $a['date'] == $b['date'] ) return 0;
	return ( $a['date'] < $b['date'] ) ? 1 : -1;
}

function files_sort_2($a, $b) //date обратно
{
	if ( $a['date'] == $b['date'] ) return 0;
	return ( $a['date'] > $b['date'] ) ? 1 : -1;
}

function files_sort_3($a, $b) //file
{
	if ( $a['file'] == $b['file'] ) return 0;
	return ( $a['file'] < $b['file'] ) ? 1 : -1;
}

function files_sort_4($a, $b) //file обратно
{
	if ( $a['file'] == $b['file'] ) return 0;
	return ( $a['file'] > $b['file'] ) ? 1 : -1;
}

function files_sort_5($a, $b) //width
{
	if ( $a['width'] == $b['width'] ) return 0;
	return ( $a['width'] < $b['width'] ) ? 1 : -1;
}

function files_sort_6($a, $b) //hight
{
	if ( $a['height'] == $b['height'] ) return 0;
	return ( $a['height'] < $b['height'] ) ? 1 : -1;
}

function files_sort_7($a, $b) //position
{
	if ( $a['position'] == $b['position'] ) return 0;
	return ( $a['position'] < $b['position'] ) ? 1 : -1;
}

?>