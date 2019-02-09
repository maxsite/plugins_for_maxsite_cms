<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

// обработчик аякс запросов

	$return = array(
		'error_code' => 1,
		'error_description' => 'Неверные данные',
		'resp' => '0',
	);
	
	
	if ( ($post = mso_check_post(array('type' , 'dir' , 'sort'))) and  ($comuser = is_login_comuser()) )
	{
	   if ($post['type'] == 'get_files')
	   {
	        $comusers_id = $comuser['comusers_id'];
            require (getinfo('plugins_dir') . 'profile/functions_userfile.php' );	   
	        $files = get_userfiles($comusers_id, $post['dir'], $post['sort']);
	        $out = ''; // весь вывод

            $subdir = $post['dir'];
	        $uploads_url = getinfo('uploads_url');
	        $subpath = $subdir . '/' . $comusers_id . '/';
	        $path = getinfo('uploads_dir') . $subpath;

           $width = '';
           $id = 0;
	       if ($files) foreach ($files as $file)
	       {
		      $file_arr = explode("." , $file['file']);

	          // ключ файла для формы
              $file_form_key = $file_arr[0] . '_ext_' . $file_arr[1];
	          $prev = '<img class="uploads_img" alt="" src="' . $uploads_url . $subpath . $file['prev'] . $file['file'] . '">';
	          $url = $uploads_url . $subdir . '/' . $comusers_id . '/' . $file['file'];
	          
	          $out .= '<div class="uploads_picture">';
	  
          //  $out .= '<a class="lightbox cboxElement" href="' . $url . '">' . $prev . '</a>';
              $out .= '<a class="lightbox" href="' . $url . '">' . $prev . '</a>';

              if ($file['use']) 
              {
                 $id++;
                 $out .= '<p><a href="#" data-dropdown="#usepics-'.$id.'" class="dropdown" title="Показать">Использовано (' . count($file['use']) . ')...</a></p>';
                 $out .= '<div id="usepics-'.$id.'" class="dropdown-menu has-tip " style="display: none;"><ul>';
                 foreach ($file['use'] as $use)
                 {
                     $link = getinfo('siteurl') . 'goto/disc/' . $use['discussion_id'] . '/comm/' . $use['comment_id'];
                     $out .= '<li><a href="' . $link . '" target="_blank" title="Перейти к сообщению, в котором использован файл">' . $use['discussion_title'] . '</a></li>';
                 }
                 $out .= '</ul></div>';            
               }  
               else if ($file['file'] == 'avatar.jpg') $out .= '<span class="uploads_avatar">Аватар</span>';
           
               $out .= '</div>'; // uploads_picture
            }
            else $out .= 'Нет файлов';
       
         $return['error_code'] = 0;
         $return['resp'] = $out;
       }  
  }

	echo json_encode($return);	


?>