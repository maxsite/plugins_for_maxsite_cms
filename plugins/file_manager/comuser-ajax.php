<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// обработчик аякс запросов

	$return = array(
		'error_code' => 1,
		'error_description' => 'Неверные данные',
		'resp' => '0',
	);
	
	
	if ( ($post = mso_check_post(array('type' , 'dir'))) and  ($comuser = is_login_comuser()) )
	{
	   if ($post['type'] == 'get_files')
	   {
	     $uploads_dir = getinfo('uploads_dir') . 'userfile/' . $comuser['comusers_id'] . '/';
	     if (@is_dir($uploads_dir)) 
	     {
	        global $CI;
	        $CI = & get_instance();
	        $CI->load->helper('file'); // хелпер для работы с файлами
	        $CI->load->helper('directory');	
	          
	        $allowed_ext = array('jpg','jpeg','gif','png');
	     
	        $uploads_url = getinfo('uploads_url') . 'userfile/' . $comuser['comusers_id'] . '/';

	        $fn_mso_descritions = $uploads_dir . '_mso_i/_mso_descriptions.dat';

	        if (file_exists( $fn_mso_descritions )) 
		         $mso_descritions = unserialize( read_file($fn_mso_descritions) ); // получим из файла все описания
	        else $mso_descritions = array();
	
	        // все файлы в массиве $dirs
	        $dirs = directory_map($uploads_dir, true); // только в текущем каталоге
          
          $files = array();
          if ($dirs) foreach ($dirs as $file)
          {
            if (@is_dir($uploads_dir . $file)) continue;
		        $ext = strtolower(str_replace('.', '', strrchr($file, '.'))); // расширение файла
		        if ( !in_array($ext, $allowed_ext) ) continue; // запрещенный тип файла 
		        
            $file_full_path = $uploads_dir . $file;
            $date_file = date ("Y-m-d" , filemtime($file_full_path));
            $files[] = array('file'=>$file, 'date'=>$date_file);
          }
          
          
          uasort($files , 'files_sort_date');

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
		           $in = explode('#|#' , $img);
		     
		     /*
		           $cod = stripslashes(htmlspecialchars('<div class="gallery"><a href="' . $uploads_url . $in[0] . '" title="' . $in[3] . '"><img alt="' . $in[3] . '" src="' . $uploads_url . $in[1] . '"></a></div>'));*/
		           
		     		   $cod = stripslashes(htmlspecialchars('[myfile]' . $in[0] . '[/myfile]'));
		     		     
		           $out .= '<div class="file_block"><a href="javascript:void(0)" onClick="addimg(\'img_' . $key. '\') " title="Добавить в коммент"><img alt="" src="' . $uploads_url . $in[2] . '"></a><input type="hidden" id="img_' . $key .'" value="' . $cod . '"></div>';
  	         }
  	         // _________________________________________________________
          }
          else $out = 'Нет файлов';
       }
       else $out = 'Нет файлов';
       
       $return['error_code'] = 0;
       $return['resp'] = $out;
     }  
  }


	echo json_encode($return);	

function files_sort_date($a, $b) 
{
	if ( $a['date'] == $b['date'] ) return 0;
	return ( $a['date'] < $b['date'] ) ? 1 : -1;
}
?>