<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// обработчик аякс запросов для формы после редактора

	$return = array(
		'error_code' => 1,
		'error_description' => 'Неверные данные',
		'resp' => '0',
	);
	
	if ( $post = mso_check_post(array('type' , 'dir' , 'meta')) )
	{
	   if ($post['type'] == 'get_files')
	   {
	     global $CI;
	     $CI = & get_instance();
	     $CI->load->helper('file'); // хелпер для работы с файлами
	     $CI->load->helper('directory');	
	          
	     $allowed_ext = array('jpg','jpeg','gif','png');
	     
	     $uploads_dir = getinfo('uploads_dir') . $post['dir'];
	     $uploads_url = getinfo('uploads_url') . $post['dir'];

	      $fn_mso_descritions = $uploads_dir . '_mso_i/_mso_descriptions.dat';

	      if (file_exists( $fn_mso_descritions )) 
		        $mso_descritions = unserialize( read_file($fn_mso_descritions) ); // получим из файла все описания
	      else $mso_descritions = array();
	
	     // все файлы в массиве $dirs
	     $dirs = directory_map($uploads_dir, true); // только в текущем каталоге

	     if (!$dirs) $dirs = array();

	     sort($dirs);

	     $out = ''; // весь вывод

	     foreach ($dirs as $file)
	     {
		     if (@is_dir($uploads_dir . $file)) continue; // это каталог

		     $ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		     if ( !in_array($ext, $allowed_ext) ) continue; // запрещенный тип файла

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
		       $in = explode('#|#' , $img);
		     
		      // $cod = stripslashes(htmlspecialchars('<div class="gallery"><a href="' . $uploads_url . $in[0] . '" title="' . $in[3] . '"><img alt="' . $in[3] . '" src="' . $uploads_url . $in[1] . '"></a></div>'));
		    
		       $cod = stripslashes(htmlspecialchars($uploads_url . $in[0]));
		     		     
		       $out .= '<div class="file_block"><a href="javascript:void(0)" onClick="selectfile' . $post['meta'] . '(\''.$post['dir'] . $in[0].'\') " title="' . $cod . '"><img alt="" src="' . $uploads_url . $in[2] . '"></a><input type="hidden" id="img_code' . $key .'" value="' . $cod . '"></div>';
  	  // _________________________________________________________
  	  
  	  
  	      }
         $return['error_code'] = 0;
         $return['resp'] = $out;
       }
       else
       {
         $return['error_code'] = 0;
         $return['resp'] = 'Пусто';       
       }
       
     }  
  }

  	
	echo json_encode($return);	


?>